<?php

namespace App\Livewire\Users;

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

        User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'is_active' => (bool) $data['isActive'],
            'password' => $password,
        ]);

        $this->reset(['name', 'email', 'role', 'isActive', 'password']);
        $this->role = 'teacher';
        $this->isActive = true;

        $this->dispatch('alert', message: 'User created.', type: 'success');
    }

    public function startEdit(int $userId): void
    {
        $user = User::query()->findOrFail($userId);
        $this->editingUserId = (int) $user->id;
        $this->editRole = (string) $user->role;
        $this->editIsActive = (bool) $user->is_active;
        $this->newPassword = '';
    }

    public function cancelEdit(): void
    {
        $this->editingUserId = null;
        $this->newPassword = '';
    }

    public function saveEdit(): void
    {
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
        $user->save();

        $this->editingUserId = null;
        $this->newPassword = '';

        $this->dispatch('alert', message: 'User updated.', type: 'success');
    }

    public function render()
    {
        return view('livewire.users.index');
    }
}
