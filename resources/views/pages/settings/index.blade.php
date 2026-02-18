@extends('layouts.app')

@section('content')
    @php($licenseState = app(\App\Support\LicenseManager::class)->load())
    <div class="space-y-6">
        <x-page-header title="Settings" subtitle="System configuration and offline backups." accent="settings" />

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('settings.backup') }}" style="display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; border-radius: 0.5rem; padding: 0.5rem 1rem; font-size: 0.875rem; font-weight: 600; background-color: white; color: rgb(79 70 229); border: 2px solid rgb(199 210 254);" onmouseover="this.style.backgroundColor='rgb(238 242 255)'" onmouseout="this.style.backgroundColor='white'">Backups</a>
            <a href="{{ route('settings.devices') }}" style="display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; border-radius: 0.5rem; padding: 0.5rem 1rem; font-size: 0.875rem; font-weight: 600; background-color: white; color: rgb(79 70 229); border: 2px solid rgb(199 210 254);" onmouseover="this.style.backgroundColor='rgb(238 242 255)'" onmouseout="this.style.backgroundColor='white'">Premium Devices</a>
            <a href="{{ route('settings.audit-logs') }}" style="display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; border-radius: 0.5rem; padding: 0.5rem 1rem; font-size: 0.875rem; font-weight: 600; background-color: white; color: rgb(79 70 229); border: 2px solid rgb(199 210 254);" onmouseover="this.style.backgroundColor='rgb(238 242 255)'" onmouseout="this.style.backgroundColor='white'">Audit Logs</a>
            <a href="{{ route('settings.results') }}" style="display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; border-radius: 0.5rem; padding: 0.5rem 1rem; font-size: 0.875rem; font-weight: 600; background-color: white; color: rgb(79 70 229); border: 2px solid rgb(199 210 254);" onmouseover="this.style.backgroundColor='rgb(238 242 255)'" onmouseout="this.style.backgroundColor='white'">Result Scoring</a>
            <a href="{{ route('settings.certificates') }}" style="display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; border-radius: 0.5rem; padding: 0.5rem 1rem; font-size: 0.875rem; font-weight: 600; background-color: white; color: rgb(79 70 229); border: 2px solid rgb(199 210 254);" onmouseover="this.style.backgroundColor='rgb(238 242 255)'" onmouseout="this.style.backgroundColor='white'">Certificates</a>
            <a href="{{ route('settings.templates') }}" style="display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; border-radius: 0.5rem; padding: 0.5rem 1rem; font-size: 0.875rem; font-weight: 600; background-color: white; color: rgb(79 70 229); border: 2px solid rgb(199 210 254);" onmouseover="this.style.backgroundColor='rgb(238 242 255)'" onmouseout="this.style.backgroundColor='white'">Templates</a>
        </div>

        @if (session('status'))
            <div class="card-padded border border-green-200 bg-green-50/60 text-sm text-green-900">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->has('premium') || $errors->has('license'))
            <div class="card-padded border border-rose-200 bg-rose-50/60 text-sm text-rose-900">
                <div class="font-bold">Premium</div>
                @if($errors->has('premium'))
                    <div class="mt-1">{{ $errors->first('premium') }}</div>
                @endif
                @if($errors->has('license'))
                    <div class="mt-1">{{ $errors->first('license') }}</div>
                @endif
            </div>
        @endif

        @if ($errors->any())
            <div class="card-padded border border-orange-200 bg-orange-50/60">
                <div class="text-sm font-semibold text-orange-900">Please fix the following:</div>
                <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-orange-900">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <!-- School Information Card -->
            <div class="rounded-3xl border border-gray-100 bg-gradient-to-br from-blue-50 to-indigo-50/60 p-6 shadow-lg">
                @if(config('myacademy.school_logo'))
                    <div class="mb-5 flex justify-center">
                        <img
                            src="{{ asset('uploads/'.str_replace('\\', '/', config('myacademy.school_logo'))) }}"
                            alt="School logo"
                            class="h-24 w-24 rounded-full bg-white object-contain p-3 ring-4 ring-white shadow-lg"
                        />
                    </div>
                @endif
                <div class="flex items-center gap-3 mb-5">
                    <div class="icon-3d grid h-12 w-12 place-items-center rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white shadow-lg shadow-blue-500/30">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                            <polyline points="9 22 9 12 15 12 15 22"/>
                        </svg>
                    </div>
                    <div class="text-lg font-black text-gray-900">School Information</div>
                </div>

                <form method="POST" action="{{ route('settings.update-school') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="text-xs font-bold uppercase tracking-wider text-gray-700">School Name</label>
                            <input name="school_name" class="mt-2 w-full rounded-xl border-0 bg-white/80 px-4 py-3 text-sm font-semibold text-gray-900 ring-1 ring-white/60 backdrop-blur-sm focus:ring-2 focus:ring-blue-500" value="{{ old('school_name', config('myacademy.school_name')) }}" required />
                        </div>

                        <div>
                            <label class="text-xs font-bold uppercase tracking-wider text-gray-700">Address</label>
                            <input name="school_address" class="mt-2 w-full rounded-xl border-0 bg-white/80 px-4 py-3 text-sm font-semibold text-gray-900 ring-1 ring-white/60 backdrop-blur-sm focus:ring-2 focus:ring-blue-500" value="{{ old('school_address', config('myacademy.school_address')) }}" />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs font-bold uppercase tracking-wider text-gray-700">Phone</label>
                                <input name="school_phone" class="mt-2 w-full rounded-xl border-0 bg-white/80 px-4 py-3 text-sm font-semibold text-gray-900 ring-1 ring-white/60 backdrop-blur-sm focus:ring-2 focus:ring-blue-500" value="{{ old('school_phone', config('myacademy.school_phone')) }}" />
                            </div>
                            <div>
                                <label class="text-xs font-bold uppercase tracking-wider text-gray-700">Email</label>
                                <input name="school_email" type="email" class="mt-2 w-full rounded-xl border-0 bg-white/80 px-4 py-3 text-sm font-semibold text-gray-900 ring-1 ring-white/60 backdrop-blur-sm focus:ring-2 focus:ring-blue-500" value="{{ old('school_email', config('myacademy.school_email')) }}" />
                            </div>
                        </div>

                        <div>
                            <label class="text-xs font-bold uppercase tracking-wider text-gray-700">School Logo</label>
                            <input name="school_logo" type="file" accept="image/*" class="mt-2 w-full rounded-xl border-0 bg-white/80 px-4 py-3 text-sm font-semibold text-gray-900 ring-1 ring-white/60 backdrop-blur-sm focus:ring-2 focus:ring-blue-500" />
                        </div>

                        <button type="submit" class="w-full rounded-xl bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-lg hover:bg-blue-700 transition-all">
                            Save School Information
                        </button>
                    </div>
                </form>
            </div>

            <!-- Premium License Card -->
            <div class="rounded-3xl border border-gray-100 bg-gradient-to-br from-slate-50 to-gray-50/60 p-6 shadow-lg">
                <div class="flex items-center justify-between gap-3 mb-5">
                    <div class="flex items-center gap-3">
                        <div class="icon-3d grid h-12 w-12 place-items-center rounded-xl bg-gradient-to-br from-slate-700 to-slate-900 text-white shadow-lg shadow-slate-500/20">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path d="M12 1v22" />
                                <path d="M5 5h14" />
                                <path d="M5 19h14" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-lg font-black text-gray-900">Premium License</div>
                            <div class="text-sm text-gray-600">Unlock CBT & Savings/Loan</div>
                        </div>
                    </div>
                    <div>
                        @if(($licenseState['ok'] ?? false) === true)
                            <span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-black text-emerald-800">ACTIVE</span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-rose-100 px-3 py-1 text-xs font-black text-rose-800">LOCKED</span>
                        @endif
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="rounded-2xl border border-gray-200 bg-white/80 p-4">
                        <div class="text-xs font-bold uppercase tracking-wider text-gray-700">Status</div>
                        @if(($licenseState['ok'] ?? false) === true)
                            @php($d = $licenseState['data'] ?? [])
                            <div class="mt-2 text-sm font-semibold text-gray-900">Valid until {{ \Illuminate\Support\Carbon::parse($d['expires_at'])->format('M j, Y') }}</div>
                            <div class="mt-1 text-xs text-gray-600">Device limit: {{ $d['device_limit'] }}</div>
                            <div class="mt-1 text-xs text-gray-600">Enabled: {{ implode(', ', $d['features'] ?? []) ?: '—' }}</div>
                        @else
                            <div class="mt-2 text-sm font-semibold text-gray-900">{{ $licenseState['reason'] ?? 'No license installed.' }}</div>
                            <div class="mt-1 text-xs text-gray-600">Contact support for a license key file.</div>
                        @endif
                    </div>

                    <div class="rounded-2xl border border-gray-200 bg-white/80 p-4">
                        <div class="text-xs font-bold uppercase tracking-wider text-gray-700">Upload License</div>
                        <form method="POST" action="{{ route('settings.update-license') }}" enctype="multipart/form-data" class="mt-3 space-y-3">
                            @csrf
                            <input name="license" type="file" accept=".key,.json,application/json,text/plain" class="w-full rounded-xl border-0 bg-white px-4 py-3 text-sm font-semibold text-gray-900 ring-1 ring-gray-200 focus:ring-2 focus:ring-slate-500" required />
                            <button type="submit" class="w-full rounded-xl bg-slate-800 px-5 py-3 text-sm font-bold text-white shadow-lg hover:bg-slate-900 transition-all">
                                Upload License Key
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
