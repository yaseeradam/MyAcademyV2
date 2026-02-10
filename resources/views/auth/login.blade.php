<x-layouts.guest>
    <div class="min-h-screen flex items-center justify-center relative overflow-hidden p-4 sm:p-6 lg:p-8">
        <!-- Background Image with Overlay -->
        <div class="absolute inset-0 z-0">
            <img 
                src="{{ asset('images/bg.png') }}" 
                alt="School background" 
                class="w-full h-full object-cover"
            />
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-900/60 via-purple-900/50 to-slate-900/60"></div>
        </div>

        <div class="w-full max-w-md relative z-10">
            <!-- Login Card -->
            <div class="relative">
                <!-- Card -->
                <div class="relative rounded-3xl bg-white/10 backdrop-blur-2xl shadow-2xl border border-white/20 overflow-hidden">
                    <!-- Header -->
                    <div class="p-8 text-center border-b border-white/10">
                        <div class="mx-auto grid h-16 w-16 place-items-center rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 shadow-lg mb-4">
                            <svg class="h-8 w-8 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path d="M12 3 1 9l11 6 9-4.91V17a2 2 0 0 1-1.1 1.79l-7.4 3.7a2 2 0 0 1-1.8 0l-7.4-3.7A2 2 0 0 1 2 17V9" />
                                <path d="M12 21V9" />
                            </svg>
                        </div>
                        <h1 class="text-2xl font-black text-white mb-1">
                            {{ config('myacademy.school_name', config('app.name', 'MyAcademy')) }}
                        </h1>
                        <p class="text-sm text-white/70">School Management System</p>
                    </div>

                    <!-- Form -->
                    <div class="p-8">
                        <form class="space-y-5" method="POST" action="{{ route('login.store') }}">
                            @csrf

                            <div>
                                <label class="text-sm font-semibold text-white/90 mb-2 block" for="email">Email Address</label>
                                <input
                                    id="email"
                                    name="email"
                                    type="email"
                                    value="{{ old('email') }}"
                                    autocomplete="username"
                                    required
                                    class="w-full rounded-xl border-0 bg-white/10 backdrop-blur-sm px-4 py-3 text-sm text-white placeholder:text-white/50 ring-1 ring-white/20 focus:ring-2 focus:ring-indigo-400 transition"
                                    placeholder="admin@myacademy.local"
                                />
                                @error('email')
                                    <div class="mt-2 text-xs text-red-300">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-white/90 mb-2 block" for="password">Password</label>
                                <input
                                    id="password"
                                    name="password"
                                    type="password"
                                    autocomplete="current-password"
                                    required
                                    class="w-full rounded-xl border-0 bg-white/10 backdrop-blur-sm px-4 py-3 text-sm text-white placeholder:text-white/50 ring-1 ring-white/20 focus:ring-2 focus:ring-indigo-400 transition"
                                    placeholder="••••••••"
                                />
                                @error('password')
                                    <div class="mt-2 text-xs text-red-300">{{ $message }}</div>
                                @enderror
                            </div>

                            <label class="flex items-center gap-2 text-sm text-white/80 cursor-pointer">
                                <input
                                    type="checkbox"
                                    name="remember"
                                    class="h-4 w-4 rounded border-white/30 bg-white/10 text-indigo-500 focus:ring-indigo-400"
                                />
                                Remember me for 30 days
                            </label>

                            <button
                                type="submit"
                                class="w-full rounded-xl bg-gradient-to-r from-indigo-500 to-purple-600 px-4 py-3.5 text-sm font-bold text-white shadow-lg hover:shadow-xl hover:from-indigo-600 hover:to-purple-700 transition-all duration-200"
                            >
                                Sign in to Dashboard
                            </button>
                        </form>

                        <!-- Demo Accounts -->
                        <div class="mt-6 rounded-xl bg-white/5 backdrop-blur-sm p-4 ring-1 ring-white/10">
                            <div class="text-xs font-bold text-white/70 uppercase tracking-wider mb-3">Demo Accounts</div>
                            <div class="space-y-2 text-xs text-white/80">
                                <div class="flex justify-between">
                                    <span>Admin:</span>
                                    <span class="font-mono">admin@myacademy.local</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Teacher:</span>
                                    <span class="font-mono">teacher@myacademy.local</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Bursar:</span>
                                    <span class="font-mono">bursar@myacademy.local</span>
                                </div>
                                <div class="mt-3 pt-3 border-t border-white/10 text-center">
                                    <span class="text-white/70">Password:</span>
                                    <span class="font-mono text-indigo-300 ml-2">password</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="mt-6 text-center">
                <p class="text-sm font-semibold text-white/90 drop-shadow-lg">
                    Offline Edition • LAN Network Only
                </p>
            </div>
        </div>
    </div>
</x-layouts.guest>
