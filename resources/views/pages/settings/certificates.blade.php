@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <x-page-header title="Certificate Settings" subtitle="Customize certificate design and templates." accent="settings" />

        <div class="flex gap-2">
            <a href="{{ route('settings.index') }}" class="btn-outline">← Back to Settings</a>
        </div>

        @if (session('status'))
            <div class="card-padded border border-green-200 bg-green-50/60 text-sm text-green-900">
                {{ session('status') }}
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

        <div class="rounded-3xl border border-gray-100 bg-gradient-to-br from-amber-50 to-rose-50/60 p-6 shadow-lg">
            <div class="flex items-center gap-3 mb-5">
                <div class="icon-3d grid h-12 w-12 place-items-center rounded-xl bg-gradient-to-br from-amber-500 to-rose-600 text-white shadow-lg shadow-amber-500/30">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M6 2h9l3 3v15a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z"/>
                        <path d="M14 2v4h4"/>
                        <path d="M8 13h8"/>
                        <path d="M8 17h6"/>
                    </svg>
                </div>
                <div class="text-lg font-black text-gray-900">Certificate Design</div>
            </div>

            <form method="POST" action="{{ route('settings.update-certificates') }}" enctype="multipart/form-data">
                @csrf
                <div class="space-y-6">
                    <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
                        <div>
                            <label class="text-xs font-bold uppercase tracking-wider text-gray-700">Orientation</label>
                            <select
                                name="certificate_orientation"
                                class="mt-2 w-full rounded-xl border-0 bg-white/80 px-4 py-3 text-sm font-semibold text-gray-900 ring-1 ring-white/60 backdrop-blur-sm focus:ring-2 focus:ring-amber-500"
                            >
                                @php($orientation = old('certificate_orientation', config('myacademy.certificate_orientation', 'landscape')))
                                <option value="landscape" @selected($orientation === 'landscape')>Landscape</option>
                                <option value="portrait" @selected($orientation === 'portrait')>Portrait</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-bold uppercase tracking-wider text-gray-700">Border color</label>
                            <input
                                name="certificate_border_color"
                                type="color"
                                class="mt-2 h-12 w-full rounded-xl border-0 bg-white/80 p-1 ring-1 ring-white/60 backdrop-blur-sm focus:ring-2 focus:ring-amber-500"
                                value="{{ old('certificate_border_color', config('myacademy.certificate_border_color', '#0ea5e9')) }}"
                                required
                            />
                        </div>
                        <div>
                            <label class="text-xs font-bold uppercase tracking-wider text-gray-700">Accent color</label>
                            <input
                                name="certificate_accent_color"
                                type="color"
                                class="mt-2 h-12 w-full rounded-xl border-0 bg-white/80 p-1 ring-1 ring-white/60 backdrop-blur-sm focus:ring-2 focus:ring-amber-500"
                                value="{{ old('certificate_accent_color', config('myacademy.certificate_accent_color', '#0ea5e9')) }}"
                                required
                            />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                        <div>
                            <input type="hidden" name="certificate_show_logo" value="0" />
                            <label class="flex items-center gap-3 text-sm font-semibold text-gray-700">
                                <input
                                    type="checkbox"
                                    name="certificate_show_logo"
                                    value="1"
                                    class="h-4 w-4 rounded border-gray-300 text-amber-600 focus:ring-amber-500"
                                    @checked(old('certificate_show_logo', (bool) config('myacademy.certificate_show_logo', true)))
                                />
                                Show school logo
                            </label>
                        </div>
                        <div>
                            <input type="hidden" name="certificate_show_watermark" value="0" />
                            <label class="flex items-center gap-3 text-sm font-semibold text-gray-700">
                                <input
                                    type="checkbox"
                                    name="certificate_show_watermark"
                                    value="1"
                                    class="h-4 w-4 rounded border-gray-300 text-amber-600 focus:ring-amber-500"
                                    @checked(old('certificate_show_watermark', (bool) config('myacademy.certificate_show_watermark', false)))
                                />
                                Show watermark image (if uploaded)
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="text-xs font-bold uppercase tracking-wider text-gray-700">Watermark image (optional)</label>
                        <input name="certificate_watermark_image" type="file" accept="image/*" class="mt-2 w-full rounded-xl border-0 bg-white/80 px-4 py-3 text-sm font-semibold text-gray-900 ring-1 ring-white/60 backdrop-blur-sm focus:ring-2 focus:ring-amber-500" />
                        <input type="hidden" name="certificate_watermark_remove" value="0" />
                        @if(config('myacademy.certificate_watermark_image'))
                            <div class="mt-3 flex items-center justify-between gap-3 rounded-2xl border border-white/70 bg-white/60 p-3">
                                <div class="flex items-center gap-3 min-w-0">
                                    <img
                                        src="{{ asset('uploads/'.str_replace('\\', '/', config('myacademy.certificate_watermark_image'))) }}"
                                        alt="Watermark"
                                        class="h-12 w-12 rounded-xl bg-white object-contain p-2 ring-1 ring-white/60"
                                    />
                                    <div class="min-w-0">
                                        <div class="text-xs font-bold uppercase tracking-wider text-gray-600">Current watermark</div>
                                        <div class="mt-1 truncate text-xs text-gray-600">{{ basename(config('myacademy.certificate_watermark_image')) }}</div>
                                    </div>
                                </div>
                                <label class="flex items-center gap-2 text-xs font-semibold text-gray-700">
                                    <input type="checkbox" name="certificate_watermark_remove" value="1" class="h-4 w-4 rounded border-gray-300 text-amber-600 focus:ring-amber-500" />
                                    Remove
                                </label>
                            </div>
                        @endif
                    </div>

                    <div class="rounded-2xl border border-white/60 bg-white/40 p-4">
                        <div class="text-sm font-black text-gray-900">Signatures</div>
                        <div class="mt-3 grid grid-cols-1 gap-4 lg:grid-cols-2">
                            <div class="space-y-3">
                                <div class="text-xs font-bold uppercase tracking-wider text-gray-600">Signature 1</div>
                                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                    <div>
                                        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Label</label>
                                        <input name="certificate_signature_label" class="mt-2 w-full rounded-xl border-0 bg-white/80 px-4 py-3 text-sm font-semibold text-gray-900 ring-1 ring-white/60 backdrop-blur-sm focus:ring-2 focus:ring-amber-500" value="{{ old('certificate_signature_label', config('myacademy.certificate_signature_label', 'Authorized Signature')) }}" />
                                    </div>
                                    <div>
                                        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Name (optional)</label>
                                        <input name="certificate_signature_name" class="mt-2 w-full rounded-xl border-0 bg-white/80 px-4 py-3 text-sm font-semibold text-gray-900 ring-1 ring-white/60 backdrop-blur-sm focus:ring-2 focus:ring-amber-500" value="{{ old('certificate_signature_name', config('myacademy.certificate_signature_name')) }}" />
                                    </div>
                                </div>
                                <div>
                                    <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Signature image (optional)</label>
                                    <input name="certificate_signature_image" type="file" accept="image/*" class="mt-2 w-full rounded-xl border-0 bg-white/80 px-4 py-3 text-sm font-semibold text-gray-900 ring-1 ring-white/60 backdrop-blur-sm focus:ring-2 focus:ring-amber-500" />
                                    <input type="hidden" name="certificate_signature_remove" value="0" />
                                    @if(config('myacademy.certificate_signature_image'))
                                        <div class="mt-3 flex items-center justify-between gap-3 rounded-2xl border border-white/70 bg-white/60 p-3">
                                            <div class="flex items-center gap-3 min-w-0">
                                                <img
                                                    src="{{ asset('uploads/'.str_replace('\\', '/', config('myacademy.certificate_signature_image'))) }}"
                                                    alt="Signature"
                                                    class="h-12 w-24 rounded-xl bg-white object-contain p-2 ring-1 ring-white/60"
                                                />
                                                <div class="min-w-0">
                                                    <div class="text-xs font-bold uppercase tracking-wider text-gray-600">Current signature</div>
                                                    <div class="mt-1 truncate text-xs text-gray-600">{{ basename(config('myacademy.certificate_signature_image')) }}</div>
                                                </div>
                                            </div>
                                            <label class="flex items-center gap-2 text-xs font-semibold text-gray-700">
                                                <input type="checkbox" name="certificate_signature_remove" value="1" class="h-4 w-4 rounded border-gray-300 text-amber-600 focus:ring-amber-500" />
                                                Remove
                                            </label>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="space-y-3">
                                <div class="text-xs font-bold uppercase tracking-wider text-gray-600">Signature 2 (optional)</div>
                                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                    <div>
                                        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Label</label>
                                        <input name="certificate_signature2_label" class="mt-2 w-full rounded-xl border-0 bg-white/80 px-4 py-3 text-sm font-semibold text-gray-900 ring-1 ring-white/60 backdrop-blur-sm focus:ring-2 focus:ring-amber-500" value="{{ old('certificate_signature2_label', config('myacademy.certificate_signature2_label')) }}" placeholder="e.g. Registrar" />
                                    </div>
                                    <div>
                                        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Name (optional)</label>
                                        <input name="certificate_signature2_name" class="mt-2 w-full rounded-xl border-0 bg-white/80 px-4 py-3 text-sm font-semibold text-gray-900 ring-1 ring-white/60 backdrop-blur-sm focus:ring-2 focus:ring-amber-500" value="{{ old('certificate_signature2_name', config('myacademy.certificate_signature2_name')) }}" />
                                    </div>
                                </div>
                                <div>
                                    <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Signature image (optional)</label>
                                    <input name="certificate_signature2_image" type="file" accept="image/*" class="mt-2 w-full rounded-xl border-0 bg-white/80 px-4 py-3 text-sm font-semibold text-gray-900 ring-1 ring-white/60 backdrop-blur-sm focus:ring-2 focus:ring-amber-500" />
                                    <input type="hidden" name="certificate_signature2_remove" value="0" />
                                    @if(config('myacademy.certificate_signature2_image'))
                                        <div class="mt-3 flex items-center justify-between gap-3 rounded-2xl border border-white/70 bg-white/60 p-3">
                                            <div class="flex items-center gap-3 min-w-0">
                                                <img
                                                    src="{{ asset('uploads/'.str_replace('\\', '/', config('myacademy.certificate_signature2_image'))) }}"
                                                    alt="Signature 2"
                                                    class="h-12 w-24 rounded-xl bg-white object-contain p-2 ring-1 ring-white/60"
                                                />
                                                <div class="min-w-0">
                                                    <div class="text-xs font-bold uppercase tracking-wider text-gray-600">Current signature</div>
                                                    <div class="mt-1 truncate text-xs text-gray-600">{{ basename(config('myacademy.certificate_signature2_image')) }}</div>
                                                </div>
                                            </div>
                                            <label class="flex items-center gap-2 text-xs font-semibold text-gray-700">
                                                <input type="checkbox" name="certificate_signature2_remove" value="1" class="h-4 w-4 rounded border-gray-300 text-amber-600 focus:ring-amber-500" />
                                                Remove
                                            </label>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-white/60 bg-white/40 p-4">
                        <div class="text-sm font-black text-gray-900">Default Certificate Template</div>
                        <div class="mt-1 text-sm font-semibold text-gray-600">Used as the starting template when creating new certificates.</div>

                        <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-3">
                            <div>
                                <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Default type</label>
                                <input name="certificate_default_type" class="mt-2 w-full rounded-xl border-0 bg-white/80 px-4 py-3 text-sm font-semibold text-gray-900 ring-1 ring-white/60 backdrop-blur-sm focus:ring-2 focus:ring-amber-500" value="{{ old('certificate_default_type', config('myacademy.certificate_default_type', 'General')) }}" />
                            </div>
                            <div class="lg:col-span-2">
                                <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Default title</label>
                                <input name="certificate_default_title" class="mt-2 w-full rounded-xl border-0 bg-white/80 px-4 py-3 text-sm font-semibold text-gray-900 ring-1 ring-white/60 backdrop-blur-sm focus:ring-2 focus:ring-amber-500" value="{{ old('certificate_default_title', config('myacademy.certificate_default_title', 'Certificate')) }}" />
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Default body</label>
                            <textarea name="certificate_default_body" rows="5" class="mt-2 w-full rounded-xl border-0 bg-white/80 px-4 py-3 text-sm font-semibold text-gray-900 ring-1 ring-white/60 backdrop-blur-sm focus:ring-2 focus:ring-amber-500">{{ old('certificate_default_body', config('myacademy.certificate_default_body')) }}</textarea>
                            <div class="mt-2 text-xs text-gray-600">Placeholders: <span class="font-mono">{student_name}</span>, <span class="font-mono">{admission_number}</span>, <span class="font-mono">{class}</span>, <span class="font-mono">{section}</span>, <span class="font-mono">{issue_date}</span>, <span class="font-mono">{school_name}</span></div>
                        </div>
                    </div>

                    <button type="submit" class="w-full rounded-xl bg-amber-600 px-5 py-3 text-sm font-bold text-white shadow-lg hover:bg-amber-700 transition-all">
                        Save Certificate Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
