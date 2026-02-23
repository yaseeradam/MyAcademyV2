@extends('layouts.app')

@section('content')
    @php($licenseState = app(\App\Support\LicenseManager::class)->load())
    <div class="space-y-6">
        <x-page-header title="Settings" subtitle="System configuration and offline backups." accent="settings" />

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('settings.backup') }}" style="display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; border-radius: 0.75rem; padding: 0.625rem 1.25rem; font-size: 0.875rem; font-weight: 700; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.3);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 10px 15px -3px rgba(16, 185, 129, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px -1px rgba(16, 185, 129, 0.3)'">
                <svg style="width: 1.125rem; height: 1.125rem;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                    <polyline points="3.27 6.96 12 12.01 20.73 6.96"/>
                    <line x1="12" y1="22.08" x2="12" y2="12"/>
                </svg>
                Backups
            </a>
            <a href="{{ route('settings.devices') }}" style="display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; border-radius: 0.75rem; padding: 0.625rem 1.25rem; font-size: 0.875rem; font-weight: 700; background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); color: white; box-shadow: 0 4px 6px -1px rgba(139, 92, 246, 0.3);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 10px 15px -3px rgba(139, 92, 246, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px -1px rgba(139, 92, 246, 0.3)'">
                <svg style="width: 1.125rem; height: 1.125rem;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/>
                    <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                </svg>
                Premium Devices
            </a>
            <a href="{{ route('settings.audit-logs') }}" style="display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; border-radius: 0.75rem; padding: 0.625rem 1.25rem; font-size: 0.875rem; font-weight: 700; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; box-shadow: 0 4px 6px -1px rgba(245, 158, 11, 0.3);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 10px 15px -3px rgba(245, 158, 11, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px -1px rgba(245, 158, 11, 0.3)'">
                <svg style="width: 1.125rem; height: 1.125rem;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="16" y1="13" x2="8" y2="13"/>
                    <line x1="16" y1="17" x2="8" y2="17"/>
                    <line x1="10" y1="9" x2="8" y2="9"/>
                </svg>
                Audit Logs
            </a>
            <a href="{{ route('settings.results') }}" style="display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; border-radius: 0.75rem; padding: 0.625rem 1.25rem; font-size: 0.875rem; font-weight: 700; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white; box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 10px 15px -3px rgba(59, 130, 246, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px -1px rgba(59, 130, 246, 0.3)'">
                <svg style="width: 1.125rem; height: 1.125rem;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                </svg>
                Result Scoring
            </a>
            <a href="{{ route('settings.certificates') }}" style="display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; border-radius: 0.75rem; padding: 0.625rem 1.25rem; font-size: 0.875rem; font-weight: 700; background: linear-gradient(135deg, #ec4899 0%, #db2777 100%); color: white; box-shadow: 0 4px 6px -1px rgba(236, 72, 153, 0.3);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 10px 15px -3px rgba(236, 72, 153, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px -1px rgba(236, 72, 153, 0.3)'">
                <svg style="width: 1.125rem; height: 1.125rem;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <circle cx="12" cy="8" r="7"/>
                    <polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/>
                </svg>
                Certificates
            </a>
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
            <div class="rounded-3xl border-2 border-blue-200 bg-gradient-to-br from-blue-500 via-indigo-500 to-purple-600 p-6 shadow-2xl">
                @if(config('myacademy.school_logo'))
                    <div class="mb-5 flex justify-center">
                        <img
                            src="{{ asset('uploads/'.str_replace('\\', '/', config('myacademy.school_logo'))) }}"
                            alt="School logo"
                            class="h-24 w-24 rounded-full bg-white object-contain p-3 ring-4 ring-white shadow-2xl"
                        />
                    </div>
                @endif
                <div class="flex items-center gap-3 mb-5">
                    <div class="grid h-12 w-12 place-items-center rounded-xl bg-white/20 backdrop-blur-sm text-white shadow-lg">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                            <polyline points="9 22 9 12 15 12 15 22"/>
                        </svg>
                    </div>
                    <div class="text-lg font-black text-white">School Information</div>
                </div>

                <form method="POST" action="{{ route('settings.update-school') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="text-xs font-bold uppercase tracking-wider text-white/90">School Name</label>
                            <input name="school_name" class="mt-2 w-full rounded-xl border-0 bg-white/95 px-4 py-3 text-sm font-semibold text-gray-900 shadow-lg focus:ring-2 focus:ring-white" value="{{ old('school_name', config('myacademy.school_name')) }}" required />
                        </div>

                        <div>
                            <label class="text-xs font-bold uppercase tracking-wider text-white/90">Address</label>
                            <input name="school_address" class="mt-2 w-full rounded-xl border-0 bg-white/95 px-4 py-3 text-sm font-semibold text-gray-900 shadow-lg focus:ring-2 focus:ring-white" value="{{ old('school_address', config('myacademy.school_address')) }}" />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs font-bold uppercase tracking-wider text-white/90">Phone</label>
                                <input name="school_phone" class="mt-2 w-full rounded-xl border-0 bg-white/95 px-4 py-3 text-sm font-semibold text-gray-900 shadow-lg focus:ring-2 focus:ring-white" value="{{ old('school_phone', config('myacademy.school_phone')) }}" />
                            </div>
                            <div>
                                <label class="text-xs font-bold uppercase tracking-wider text-white/90">Email</label>
                                <input name="school_email" type="email" class="mt-2 w-full rounded-xl border-0 bg-white/95 px-4 py-3 text-sm font-semibold text-gray-900 shadow-lg focus:ring-2 focus:ring-white" value="{{ old('school_email', config('myacademy.school_email')) }}" />
                            </div>
                        </div>

                        <div>
                            <label class="text-xs font-bold uppercase tracking-wider text-white/90">School Logo</label>
                            <input name="school_logo" type="file" accept="image/*" class="mt-2 w-full rounded-xl border-0 bg-white/95 px-4 py-3 text-sm font-semibold text-gray-900 shadow-lg focus:ring-2 focus:ring-white" />
                        </div>

                        <button type="submit" class="w-full rounded-xl bg-white px-5 py-3 text-sm font-bold text-indigo-600 shadow-xl hover:bg-white/90 transition-all flex items-center justify-center gap-2">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                                <polyline points="17 21 17 13 7 13 7 21"/>
                                <polyline points="7 3 7 8 15 8"/>
                            </svg>
                            Save School Information
                        </button>
                    </div>
                </form>
            </div>

            <!-- Premium License Card -->
            <div class="rounded-3xl border-2 border-purple-200 bg-gradient-to-br from-purple-500 via-violet-600 to-indigo-600 p-6 shadow-2xl">
                <div class="flex items-center justify-between gap-3 mb-5">
                    <div class="flex items-center gap-3">
                        <div class="grid h-12 w-12 place-items-center rounded-xl bg-white/20 backdrop-blur-sm text-white shadow-lg">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path d="M12 1v22" />
                                <path d="M5 5h14" />
                                <path d="M5 19h14" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-lg font-black text-white">Premium License</div>
                            <div class="text-sm text-white/90 font-semibold">Unlock CBT & Savings/Loan</div>
                        </div>
                    </div>
                    <div>
                        @if(($licenseState['ok'] ?? false) === true)
                            <span class="inline-flex items-center rounded-full bg-emerald-500 px-3 py-1 text-xs font-black text-white shadow-lg gap-1">
                                <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>
                                ACTIVE
                            </span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-red-600 px-3 py-1 text-xs font-black text-white shadow-lg gap-1">
                                <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                </svg>
                                LOCKED
                            </span>
                        @endif
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="rounded-2xl border-2 border-white/30 bg-white/95 backdrop-blur-sm p-4 shadow-xl">
                        <div class="text-xs font-bold uppercase tracking-wider text-gray-700">Status</div>
                        @if(($licenseState['ok'] ?? false) === true)
                            @php($d = $licenseState['data'] ?? [])
                            <div class="mt-2 text-sm font-semibold text-gray-900 flex items-center gap-2">
                                <svg class="h-4 w-4 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>
                                Valid until {{ \Illuminate\Support\Carbon::parse($d['expires_at'])->format('M j, Y') }}
                            </div>
                            <div class="mt-1 text-xs text-gray-600 flex items-center gap-2">
                                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <rect x="5" y="2" width="14" height="20" rx="2" ry="2"/>
                                    <line x1="12" y1="18" x2="12.01" y2="18"/>
                                </svg>
                                Device limit: {{ $d['device_limit'] }}
                            </div>
                            <div class="mt-1 text-xs text-gray-600 flex items-center gap-2">
                                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <circle cx="12" cy="12" r="10"/>
                                    <path d="M12 6v6l4 2"/>
                                </svg>
                                Enabled: {{ implode(', ', $d['features'] ?? []) ?: '—' }}
                            </div>
                        @else
                            <div class="mt-2 text-sm font-semibold text-gray-900">{{ $licenseState['reason'] ?? 'No license installed.' }}</div>
                            <div class="mt-1 text-xs text-gray-600">Contact support for a license key file.</div>
                        @endif
                    </div>

                    <div class="rounded-2xl border-2 border-white/30 bg-white/95 backdrop-blur-sm p-4 shadow-xl">
                        <div class="text-xs font-bold uppercase tracking-wider text-gray-700">Upload License</div>
                        <form method="POST" action="{{ route('settings.update-license') }}" enctype="multipart/form-data" class="mt-3 space-y-3">
                            @csrf
                            <input name="license" type="file" accept=".key,.json,application/json,text/plain" class="w-full rounded-xl border-0 bg-white px-4 py-3 text-sm font-semibold text-gray-900 shadow-lg focus:ring-2 focus:ring-amber-500" required />
                            <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-slate-700 to-slate-900 px-5 py-3 text-sm font-bold text-white shadow-xl hover:from-slate-800 hover:to-black transition-all flex items-center justify-center gap-2">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"/>
                                </svg>
                                Upload License Key
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
