<x-layouts.guest>
    <div class="w-full max-w-md">
        <div class="rounded-2xl border border-gray-100 bg-white p-8 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="grid h-12 w-12 place-items-center rounded-xl bg-blue-50 text-blue-600">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 3 1 9l11 6 9-4.91V17a2 2 0 0 1-1.1 1.79l-7.4 3.7a2 2 0 0 1-1.8 0l-7.4-3.7A2 2 0 0 1 2 17V9" />
                        <path d="M12 21V9" />
                    </svg>
                </div>
                <div>
                    <div class="text-lg font-bold tracking-tight text-gray-900">
                        {{ config('myacademy.school_name', config('app.name', 'MyAcademy')) }}
                    </div>
                    <div class="text-sm text-gray-500">Offline School Management</div>
                </div>
            </div>

            <div class="mt-8">
                <div class="text-xl font-semibold tracking-tight text-gray-900">Sign in</div>
                <div class="mt-1 text-sm text-gray-600">Use your local account to continue.</div>
            </div>

            <form class="mt-6 space-y-5" method="POST" action="{{ route('login.store') }}">
                @csrf

                <div>
                    <label class="text-sm font-semibold text-gray-700" for="email">Email</label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email') }}"
                        autocomplete="username"
                        required
                        class="mt-2 input"
                    />
                    @error('email')
                        <div class="mt-2 text-sm text-orange-700">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label class="text-sm font-semibold text-gray-700" for="password">Password</label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        autocomplete="current-password"
                        required
                        class="mt-2 input"
                    />
                    @error('password')
                        <div class="mt-2 text-sm text-orange-700">{{ $message }}</div>
                    @enderror
                </div>

                <label class="flex items-center gap-2 text-sm text-gray-600">
                    <input
                        type="checkbox"
                        name="remember"
                        class="h-4 w-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500"
                    />
                    Remember me
                </label>

                <button
                    type="submit"
                    class="btn-primary w-full py-2.5"
                >
                    Sign in
                </button>
            </form>

            <div class="mt-6 rounded-xl border border-gray-100 bg-gray-50 p-4 text-xs text-gray-600">
                <div class="font-semibold text-gray-800">Default demo accounts</div>
                <div class="mt-1">Admin: <span class="font-mono">admin@myacademy.local</span> / <span class="font-mono">password</span></div>
                <div>Bursar: <span class="font-mono">bursar@myacademy.local</span> / <span class="font-mono">password</span></div>
                <div>Teacher: <span class="font-mono">teacher@myacademy.local</span> / <span class="font-mono">password</span></div>
            </div>
        </div>
    </div>
</x-layouts.guest>
