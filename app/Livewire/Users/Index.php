<?php

namespace App\Livewire\Users;

use App\Support\Audit;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Users')]
class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $roleFilter = '';
    public string $statusFilter = '';

    public string $name = '';
    public string $email = '';
    public string $role = 'teacher';
    public bool $isActive = true;
    public string $password = '';

    public ?int $editingUserId = null;
    public string $editRole = 'teacher';
    public bool $editIsActive = true;
    public string $newPassword = '';
    public array $editPermissions = [];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedRoleFilter(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function users()
    {
        $query = User::query()->orderBy('name');

        if ($this->search) {
            $q = trim($this->search);
            $query->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        if ($this->roleFilter) {
            $query->where('role', $this->roleFilter);
        }

        if ($this->statusFilter === 'active') {
            $query->where('is_active', true);
        } elseif ($this->statusFilter === 'inactive') {
            $query->where('is_active', false);
        }

        return $query->paginate(15);
    }

    public function createUser(): void
    {
        $authUser = auth()->user();
        abort_unless($authUser && $authUser->hasPermission('users.manage'), 403);

        $data = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
            'role' => ['required', Rule::in(['admin', 'bursar', 'teacher'])],
            'isActive' => ['boolean'],
            'password' => ['nullable', 'string', 'min:8'],
        ], [
            'isActive.boolean' => 'Invalid active status.',
        ]);

        $password = $data['password'] ?: Str::password(12);

        $created = User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'is_active' => (bool) $data['isActive'],
            'password' => $password,
        ]);

        Audit::log('user.created', $created, [
            'role' => $created->role,
            'is_active' => $created->is_active,
        ]);

        $this->reset(['name', 'email', 'role', 'isActive', 'password']);
        $this->role = 'teacher';
        $this->isActive = true;

        $this->dispatch('alert', message: 'User created.', type: 'success');
    }

    public function startEdit(int $userId): void
    {
        $authUser = auth()->user();
        abort_unless($authUser && $authUser->hasPermission('users.manage'), 403);

        $user = User::query()->findOrFail($userId);
        $this->editingUserId = (int) $user->id;
        $this->editRole = (string) $user->role;
        $this->editIsActive = (bool) $user->is_active;
        $this->newPassword = '';

        $definitions = (array) config('permissions.definitions', []);
        $overrides = $user->permissions;
        if (! is_array($overrides)) {
            $overrides = [];
        }
        $grant = $overrides['grant'] ?? [];
        if (! is_array($grant)) {
            $grant = [];
        }
        $revoke = $overrides['revoke'] ?? [];
        if (! is_array($revoke)) {
            $revoke = [];
        }

        $grant = array_values(array_unique(array_filter(array_map('strval', $grant))));
        $revoke = array_values(array_unique(array_filter(array_map('strval', $revoke))));

        $this->editPermissions = [];
        foreach (array_keys($definitions) as $permission) {
            if (in_array($permission, $revoke, true)) {
                $this->editPermissions[$permission] = 'revoke';
            } elseif (in_array($permission, $grant, true)) {
                $this->editPermissions[$permission] = 'grant';
            } else {
                $this->editPermissions[$permission] = 'default';
            }
        }
    }

    public function cancelEdit(): void
    {
        $authUser = auth()->user();
        abort_unless($authUser && $authUser->hasPermission('users.manage'), 403);

        $this->editingUserId = null;
        $this->newPassword = '';
        $this->editPermissions = [];
    }

    public function saveEdit(): void
    {
        $authUser = auth()->user();
        abort_unless($authUser && $authUser->hasPermission('users.manage'), 403);

        if (! $this->editingUserId) {
            return;
        }

        $user = User::query()->findOrFail($this->editingUserId);

        $data = $this->validate([
            'editRole' => ['required', Rule::in(['admin', 'bursar', 'teacher'])],
            'editIsActive' => ['boolean'],
            'newPassword' => ['nullable', 'string', 'min:8'],
        ]);

        $isSelf = Auth::id() && (int) Auth::id() === (int) $user->id;
        if ($isSelf && ! $data['editIsActive']) {
            $this->addError('editIsActive', 'You cannot deactivate your own account.');
            return;
        }

        $user->role = $data['editRole'];
        $user->is_active = (bool) $data['editIsActive'];
        if ($data['newPassword']) {
            $user->password = $data['newPassword'];
        }

        $definitions = array_keys((array) config('permissions.definitions', []));
        $states = is_array($this->editPermissions) ? $this->editPermissions : [];
        $grant = [];
        $revoke = [];

        foreach ($definitions as $permission) {
            $state = $states[$permission] ?? 'default';
            if ($state === 'grant') {
                $grant[] = $permission;
            } elseif ($state === 'revoke') {
                $revoke[] = $permission;
            }
        }

        $user->permissions = ($grant !== [] || $revoke !== [])
            ? ['grant' => $grant, 'revoke' => $revoke]
            : null;

        $user->save();

        Audit::log('user.updated', $user, [
            'role' => $user->role,
            'is_active' => $user->is_active,
            'password_reset' => (bool) ($data['newPassword'] ?? false),
            'permissions' => $user->permissions,
        ]);

        $this->editingUserId = null;
        $this->newPassword = '';
        $this->editPermissions = [];

        $this->dispatch('alert', message: 'User updated.', type: 'success');
    }

    public function render()
    {
        $user = auth()->user();
        abort_unless($user && $user->hasPermission('users.manage'), 403);

        return view('livewire.users.index');
    }
}
