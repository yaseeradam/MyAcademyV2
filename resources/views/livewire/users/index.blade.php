<div class="space-y-6">
    <x-page-header title="Users" subtitle="Create accounts, assign roles, and manage activation." accent="settings" />

    @php
        $permissionDefinitions = (array) config('permissions.definitions', []);
    @endphp

    <div class="card-padded">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-6">
            <div class="lg:col-span-3">
                <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Search</label>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Name or email" class="mt-2 input-compact" />
            </div>

            <div class="lg:col-span-2">
                <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Role</label>
                <select wire:model.live="roleFilter" class="mt-2 select">
                    <option value="">All</option>
                    <option value="admin">Admin</option>
                    <option value="bursar">Bursar</option>
                    <option value="teacher">Teacher</option>
                </select>
            </div>

            <div class="lg:col-span-1">
                <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Status</label>
                <select wire:model.live="statusFilter" class="mt-2 select">
                    <option value="">All</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
        </div>
    </div>

    <div class="card-padded">
        <div class="text-sm font-semibold text-gray-900">Create User</div>

        @if ($errors->any())
            <div class="mt-4 rounded-xl border border-orange-200 bg-orange-50/60 p-4">
                <div class="text-sm font-semibold text-orange-900">Please fix the following:</div>
                <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-orange-900">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form wire:submit="createUser" class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-6">
            <div class="lg:col-span-2">
                <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Name</label>
                <input wire:model.live="name" type="text" class="mt-2 input-compact" placeholder="Full name" />
            </div>

            <div class="lg:col-span-2">
                <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Email</label>
                <input wire:model.live="email" type="email" class="mt-2 input-compact" placeholder="user@school.local" />
            </div>

            <div class="lg:col-span-1">
                <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Role</label>
                <select wire:model.live="role" class="mt-2 select">
                    <option value="teacher">Teacher</option>
                    <option value="bursar">Bursar</option>
                    <option value="admin">Admin</option>
                </select>
            </div>

            <div class="lg:col-span-1">
                <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Active</label>
                <select wire:model.live="isActive" class="mt-2 select">
                    <option value="1">Yes</option>
                    <option value="0">No</option>
                </select>
            </div>

            <div class="lg:col-span-3">
                <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Password (optional)</label>
                <input wire:model.live="password" type="text" class="mt-2 input-compact" placeholder="Leave empty to auto-generate" />
                <div class="mt-1 text-xs text-gray-500">If blank, a strong password is generated.</div>
            </div>

            <div class="lg:col-span-3 flex items-end justify-end">
                <button type="submit" class="btn-primary w-full justify-center sm:w-auto">Create</button>
            </div>
        </form>
    </div>

    <div class="card-padded">
        <div class="flex items-center justify-between gap-4">
            <div class="text-sm font-semibold text-gray-900">All Users</div>
            <div class="text-xs text-gray-500">{{ number_format((int) $this->users->total()) }} total</div>
        </div>

        <div class="mt-4">
            <x-table>
                <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wider text-gray-500">
                    <tr>
                        <th class="px-5 py-3">Name</th>
                        <th class="px-5 py-3">Email</th>
                        <th class="px-5 py-3">Role</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($this->users as $user)
                        <tr class="bg-white hover:bg-gray-50">
                            <td class="px-5 py-4">
                                <div class="text-sm font-semibold text-gray-900">{{ $user->name }}</div>
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-700">{{ $user->email }}</td>
                            <td class="px-5 py-4 text-sm text-gray-700">{{ ucfirst($user->role) }}</td>
                            <td class="px-5 py-4">
                                <x-status-badge variant="{{ $user->is_active ? 'success' : 'warning' }}">{{ $user->is_active ? 'Active' : 'Inactive' }}</x-status-badge>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <button type="button" wire:click="startEdit({{ $user->id }})" class="btn-ghost">Edit</button>
                            </td>
                        </tr>
                        @if ($editingUserId === $user->id)
                            <tr class="bg-slate-50">
                                <td colspan="5" class="px-5 py-4">
                                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-6">
                                        <div class="lg:col-span-2">
                                            <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Role</label>
                                            <select wire:model.live="editRole" class="mt-2 select">
                                                <option value="teacher">Teacher</option>
                                                <option value="bursar">Bursar</option>
                                                <option value="admin">Admin</option>
                                            </select>
                                        </div>
                                        <div class="lg:col-span-2">
                                            <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Active</label>
                                            <select wire:model.live="editIsActive" class="mt-2 select">
                                                <option value="1">Yes</option>
                                                <option value="0">No</option>
                                            </select>
                                            @error('editIsActive')
                                                <div class="mt-1 text-xs font-semibold text-orange-700">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="lg:col-span-2">
                                            <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">New Password (optional)</label>
                                            <input wire:model.live="newPassword" type="text" class="mt-2 input-compact" placeholder="Min 8 characters" />
                                        </div>

                                        <div class="lg:col-span-6">
                                            <div class="mt-2 flex items-center justify-between gap-3">
                                                <div>
                                                    <div class="text-sm font-semibold text-gray-900">Permissions</div>
                                                    <div class="mt-1 text-xs text-gray-600">Default permissions come from the selected role. You can override per user.</div>
                                                </div>
                                            </div>

                                            <div class="mt-3 overflow-hidden rounded-2xl border border-gray-200 bg-white">
                                                <div class="grid grid-cols-1 divide-y divide-gray-100">
                                                    @forelse ($permissionDefinitions as $key => $def)
                                                        @php
                                                            $label = (string) ($def['label'] ?? $key);
                                                            $roles = (array) ($def['roles'] ?? []);
                                                            $state = (string) ($editPermissions[$key] ?? 'default');
                                                            $defaultAllowed = in_array($editRole, $roles, true);
                                                            $effectiveAllowed = $state === 'revoke' ? false : ($state === 'grant' ? true : $defaultAllowed);
                                                        @endphp
                                                        <div class="flex flex-col gap-3 p-4 sm:flex-row sm:items-center sm:justify-between">
                                                            <div class="min-w-0">
                                                                <div class="text-sm font-semibold text-gray-900">{{ $label }}</div>
                                                                <div class="mt-1 text-xs text-gray-500 font-mono">{{ $key }}</div>
                                                            </div>

                                                            <div class="flex flex-wrap items-center gap-2">
                                                                <x-status-badge variant="{{ $effectiveAllowed ? 'success' : 'warning' }}">
                                                                    {{ $effectiveAllowed ? 'Allowed' : 'Denied' }}
                                                                </x-status-badge>
                                                                <select wire:model.live="editPermissions.{{ $key }}" class="select">
                                                                    <option value="default">Default</option>
                                                                    <option value="grant">Grant</option>
                                                                    <option value="revoke">Revoke</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    @empty
                                                        <div class="p-4 text-sm text-gray-600">No permissions configured.</div>
                                                    @endforelse
                                                </div>
                                            </div>
                                        </div>

                                        <div class="lg:col-span-6 flex flex-wrap justify-end gap-2">
                                            <button type="button" wire:click="cancelEdit" class="btn-outline">Cancel</button>
                                            <button type="button" wire:click="saveEdit" class="btn-primary">Save</button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-10 text-center text-sm text-gray-500">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </x-table>
        </div>

        <div class="mt-4">
            {{ $this->users->links() }}
        </div>
    </div>
</div>
