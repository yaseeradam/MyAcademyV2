<?php

namespace App\Livewire\Messages;

use App\Models\Conversation;
use App\Models\InAppNotification;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Messages')]
class Index extends Component
{
    public ?int $conversationId = null;

    public string $userSearch = '';
    public ?int $recipientId = null;

    public string $body = '';

    public function mount(): void
    {
        $user = auth()->user();
        abort_unless($user, 403);
    }

    #[Computed]
    public function conversations()
    {
        $user = auth()->user();
        abort_unless($user, 403);

        $allowedRoles = $this->allowedRecipientRoles($user);
        if (empty($allowedRoles)) {
            return collect();
        }

        $pivots = DB::table('conversation_user')
            ->where('user_id', $user->id)
            ->get(['conversation_id', 'last_read_at'])
            ->keyBy('conversation_id');

        $conversations = Conversation::query()
            ->whereIn('id', $pivots->keys())
            ->with([
                'participants:id,name,role',
                'messages' => fn ($q) => $q->latest('id')->limit(1)->with('sender:id,name'),
            ])
            ->get();

        return $conversations
            ->filter(function (Conversation $c) use ($user, $allowedRoles) {
                $others = $c->participants->where('id', '!=', $user->id);
                if ($others->isEmpty()) {
                    return false;
                }

                return $others->every(fn (User $u) => in_array($u->role, $allowedRoles, true));
            })
            ->map(function (Conversation $c) use ($user, $pivots) {
                $pivot = $pivots->get($c->id);
                $lastReadAt = $pivot?->last_read_at ? Carbon::parse($pivot->last_read_at) : null;
                $lastMessage = $c->messages->first();
                $lastMessageAt = $lastMessage?->created_at;

                $unread = $lastMessageAt && (! $lastReadAt || $lastMessageAt->gt($lastReadAt));

                $others = $c->participants->where('id', '!=', $user->id)->values();
                $title = $others->isEmpty()
                    ? 'Conversation'
                    : ($others->count() === 1 ? $others->first()->name : $others->pluck('name')->take(3)->join(', '));

                return [
                    'id' => $c->id,
                    'title' => $title,
                    'other_user_id' => $others->count() === 1 ? (int) $others->first()->id : null,
                    'unread' => $unread,
                    'last_message' => $lastMessage?->body,
                    'last_message_at' => $lastMessageAt,
                ];
            })
            ->sortByDesc(fn ($row) => $row['last_message_at']?->timestamp ?? 0)
            ->values();
    }

    #[Computed]
    public function recipientOptions()
    {
        $user = auth()->user();
        abort_unless($user, 403);

        $allowedRoles = $this->allowedRecipientRoles($user);
        if (empty($allowedRoles)) {
            return collect();
        }

        $query = User::query()
            ->where('is_active', true)
            ->where('id', '!=', $user->id)
            ->whereIn('role', $allowedRoles)
            ->orderBy('name');

        $search = trim($this->userSearch);
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return $query->limit(20)->get(['id', 'name', 'role']);
    }

    #[Computed]
    public function chatMessages()
    {
        $user = auth()->user();
        abort_unless($user, 403);

        if (! $this->conversationId) {
            return collect();
        }

        $this->assertConversationAccess($this->conversationId, $user);

        return Message::query()
            ->where('conversation_id', $this->conversationId)
            ->with('sender:id,name')
            ->orderBy('id')
            ->limit(300)
            ->get();
    }

    public function openConversation(int $id): void
    {
        $user = auth()->user();
        abort_unless($user, 403);

        $this->assertConversationAccess($id, $user);

        $this->conversationId = $id;
        $this->markConversationRead($id);
    }

    public function startConversation(?int $recipientId = null): void
    {
        $user = auth()->user();
        abort_unless($user, 403);

        $recipientId = (int) ($recipientId ?? $this->recipientId ?? 0);
        if ($recipientId <= 0) {
            throw ValidationException::withMessages(['recipientId' => 'Select a user.']);
        }

        if ($recipientId === $user->id) {
            throw ValidationException::withMessages(['recipientId' => 'You cannot message yourself.']);
        }

        $recipient = User::query()->where('is_active', true)->findOrFail($recipientId);

        $allowedRoles = $this->allowedRecipientRoles($user);
        if (! in_array($recipient->role, $allowedRoles, true)) {
            throw ValidationException::withMessages(['recipientId' => 'You cannot message this user.']);
        }

        $existing = Conversation::query()
            ->whereHas('participants', fn ($q) => $q->where('users.id', $user->id))
            ->whereHas('participants', fn ($q) => $q->where('users.id', $recipient->id))
            ->whereDoesntHave('participants', fn ($q) => $q->whereNotIn('users.id', [$user->id, $recipient->id]))
            ->value('id');

        if ($existing) {
            $this->conversationId = (int) $existing;
            $this->markConversationRead($this->conversationId);
            $this->recipientId = null;
            return;
        }

        $conversation = DB::transaction(function () use ($user, $recipient) {
            $c = Conversation::query()->create(['created_by' => $user->id]);
            $c->participants()->attach([$user->id, $recipient->id]);
            return $c;
        });

        $this->conversationId = $conversation->id;
        $this->recipientId = null;
        $this->body = '';
        $this->dispatch('alert', message: 'Conversation started.', type: 'success');
    }

    public function send(): void
    {
        $user = auth()->user();
        abort_unless($user, 403);

        if (! $this->conversationId) {
            throw ValidationException::withMessages(['conversationId' => 'Select a conversation.']);
        }

        $this->assertConversationAccess($this->conversationId, $user);

        $data = $this->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $message = DB::transaction(function () use ($user, $data) {
            $m = Message::query()->create([
                'conversation_id' => $this->conversationId,
                'sender_id' => $user->id,
                'body' => trim($data['body']),
            ]);

            DB::table('conversation_user')
                ->where('conversation_id', $this->conversationId)
                ->where('user_id', $user->id)
                ->update(['last_read_at' => now()]);

            $recipientIds = DB::table('conversation_user')
                ->where('conversation_id', $this->conversationId)
                ->where('user_id', '!=', $user->id)
                ->pluck('user_id')
                ->all();

            foreach ($recipientIds as $rid) {
                InAppNotification::query()->create([
                    'user_id' => (int) $rid,
                    'title' => 'New message',
                    'body' => $user->name.': '.mb_strimwidth($m->body, 0, 120, '...'),
                    'link' => route('messages', ['c' => $this->conversationId]),
                ]);
            }

            return $m;
        });

        $this->body = '';
        $this->markConversationRead($this->conversationId);
    }

    private function markConversationRead(int $conversationId): void
    {
        $user = auth()->user();
        abort_unless($user, 403);

        DB::table('conversation_user')
            ->where('conversation_id', $conversationId)
            ->where('user_id', $user->id)
            ->update(['last_read_at' => now()]);
    }

    private function allowedRecipientRoles(User $user): array
    {
        return match ($user->role) {
            'admin' => ['admin', 'teacher', 'bursar'],
            'teacher' => ['admin', 'teacher', 'bursar'],
            'bursar' => ['admin', 'teacher', 'bursar'],
            default => [],
        };
    }

    private function assertConversationAccess(int $conversationId, User $user): void
    {
        $allowed = DB::table('conversation_user')
            ->where('conversation_id', $conversationId)
            ->where('user_id', $user->id)
            ->exists();

        abort_unless($allowed, 403);

        $allowedRoles = $this->allowedRecipientRoles($user);
        if (empty($allowedRoles)) {
            abort(403);
        }

        $otherRoles = DB::table('conversation_user')
            ->join('users', 'users.id', '=', 'conversation_user.user_id')
            ->where('conversation_id', $conversationId)
            ->where('users.id', '!=', $user->id)
            ->pluck('users.role')
            ->unique();

        if ($otherRoles->isEmpty()) {
            abort(403);
        }

        foreach ($otherRoles as $role) {
            if (! in_array($role, $allowedRoles, true)) {
                abort(403);
            }
        }
    }

    public function render()
    {
        $user = auth()->user();
        abort_unless($user, 403);

        if (! in_array($user->role, ['admin', 'teacher', 'bursar'], true)) {
            abort(403);
        }

        $defaultConversation = (int) request('c', 0);
        if (! $this->conversationId && $defaultConversation > 0) {
            $this->openConversation($defaultConversation);
        }

        return view('livewire.messages.index', [
            'me' => $user,
        ]);
    }
}
