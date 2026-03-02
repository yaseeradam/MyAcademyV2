@extends('layouts.app')

@section('content')
    @php
        $licenseManager = app(\App\Support\LicenseManager::class);
        $hasPremium = $licenseManager->can('cbt');
        
        $certificateTemplate = old('certificate_template', config('myacademy.certificate_template', 'modern'));
        $reportCardTemplate = old('report_card_template', config('myacademy.report_card_template', 'standard'));
    @endphp

    <div class="space-y-6" x-data="{ open: false, src: null, title: '' }">
        <x-page-header title="Templates" subtitle="Choose which Certificate and Report Card template to use."
            accent="settings" />

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

        <form method="POST" action="{{ route('settings.update-templates') }}" class="space-y-6">
            @csrf

            <div class="rounded-3xl border border-gray-100 bg-gradient-to-br from-amber-50 to-rose-50/60 p-6 shadow-lg">
                <div class="mb-5 flex items-center gap-3">
                    <div
                        class="icon-3d grid h-12 w-12 place-items-center rounded-xl bg-gradient-to-br from-amber-500 to-rose-600 text-white shadow-lg shadow-amber-500/30">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="M6 2h9l3 3v15a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z" />
                            <path d="M14 2v4h4" />
                            <path d="M8 13h8" />
                            <path d="M8 17h6" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-lg font-black text-gray-900">Certificate Template</div>
                        <div class="text-sm font-semibold text-gray-600">Used for all certificate downloads.</div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    @php
                        $certificateTemplates = [
                            [
                                'key' => 'modern',
                                'title' => 'Modern',
                                'desc' => 'Geometric background with seal and signatures.',
                                'preview' => route('settings.templates.preview', ['type' => 'certificate', 'template' => 'modern']),
                                'free' => true,
                            ],
                            [
                                'key' => 'classic',
                                'title' => 'Classic',
                                'desc' => 'Simple formal layout with clean border.',
                                'preview' => route('settings.templates.preview', ['type' => 'certificate', 'template' => 'classic']),
                                'free' => false,
                            ],
                            [
                                'key' => 'elegant',
                                'title' => 'Elegant',
                                'desc' => 'Gold & navy formal with decorative corner flourishes.',
                                'preview' => route('settings.templates.preview', ['type' => 'certificate', 'template' => 'elegant']),
                                'free' => false,
                            ],
                            [
                                'key' => 'vibrant',
                                'title' => 'Vibrant',
                                'desc' => 'Colorful wave gradients with sparkles and medal badge.',
                                'preview' => route('settings.templates.preview', ['type' => 'certificate', 'template' => 'vibrant']),
                                'free' => false,
                            ],
                            [
                                'key' => 'minimal',
                                'title' => 'Minimal',
                                'desc' => 'Clean contemporary design with generous whitespace.',
                                'preview' => route('settings.templates.preview', ['type' => 'certificate', 'template' => 'minimal']),
                                'free' => false,
                            ],
                            [
                                'key' => 'royal',
                                'title' => 'Royal',
                                'desc' => 'Rich purple banner with gold ribbon and ornate borders.',
                                'preview' => route('settings.templates.preview', ['type' => 'certificate', 'template' => 'royal']),
                                'free' => false,
                            ],
                        ];
                    @endphp

                    @foreach ($certificateTemplates as $t)
                        @php
                            $isLocked = !$t['free'] && !$hasPremium;
                        @endphp
                        <label
                            class="group cursor-pointer rounded-3xl border bg-white/70 p-5 shadow-sm ring-1 ring-white/50 backdrop-blur transition hover:shadow-md {{ $certificateTemplate === $t['key'] ? 'border-amber-300 ring-2 ring-amber-500' : 'border-gray-100' }} {{ $isLocked ? 'opacity-60' : '' }}">
                            <input type="radio" name="certificate_template" value="{{ $t['key'] }}" class="sr-only"
                                @checked($certificateTemplate === $t['key']) @disabled($isLocked) />
                            <div class="flex items-start justify-between gap-4">
                                <div class="min-w-0">
                                    <div class="text-sm font-black text-gray-900 flex items-center gap-2">
                                        {{ $t['title'] }}
                                        @if($isLocked)
                                            <svg class="h-4 w-4 text-red-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                            </svg>
                                        @endif
                                    </div>
                                    <div class="mt-1 text-sm font-semibold text-gray-600">{{ $t['desc'] }}</div>
                                    @if($isLocked)
                                        <div class="mt-2 text-xs font-bold text-red-600">🔒 Premium License Required</div>
                                    @endif
                                </div>
                                <span
                                    class="inline-flex items-center rounded-full {{ $t['free'] ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800' }} px-3 py-1 text-xs font-black group-hover:{{ $t['free'] ? 'bg-green-200' : 'bg-amber-200' }}">
                                    {{ $t['free'] ? 'FREE' : 'PRO' }}
                                </span>
                            </div>

                            <div class="mt-4 flex items-center justify-between">
                                <div class="text-xs font-semibold text-gray-500">{{ $isLocked ? 'Locked' : 'Click card to select' }}</div>
                                <button type="button" class="btn-outline"
                                    @click.stop="open = true; src = @js($t['preview']); title = @js('Certificate · ' . $t['title'])">
                                    Preview
                                </button>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="rounded-3xl border border-gray-100 bg-gradient-to-br from-emerald-50 to-sky-50/60 p-6 shadow-lg">
                <div class="mb-5 flex items-center gap-3">
                    <div
                        class="icon-3d grid h-12 w-12 place-items-center rounded-xl bg-gradient-to-br from-emerald-500 to-sky-600 text-white shadow-lg shadow-emerald-500/30">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                            <polyline points="14 2 14 8 20 8" />
                            <line x1="16" y1="13" x2="8" y2="13" />
                            <line x1="16" y1="17" x2="8" y2="17" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-lg font-black text-gray-900">Report Card Template</div>
                        <div class="text-sm font-semibold text-gray-600">Used for single and bulk report card PDFs.</div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    @php
                        $reportTemplates = [
                            [
                                'key' => 'standard',
                                'title' => 'Standard',
                                'desc' => 'Warm amber brand with gradient stats, color-coded grades, and double border frame.',
                                'preview' => route('settings.templates.preview', ['type' => 'report-card', 'template' => 'standard']),
                                'free' => true,
                            ],
                            [
                                'key' => 'compact',
                                'title' => 'Compact',
                                'desc' => 'Clean, minimal layout focused on scores and summary.',
                                'preview' => route('settings.templates.preview', ['type' => 'report-card', 'template' => 'compact']),
                                'free' => false,
                            ],
                            [
                                'key' => 'elegant',
                                'title' => 'Elegant',
                                'desc' => 'Formal navy and gold theme with ornate borders and refined typography.',
                                'preview' => route('settings.templates.preview', ['type' => 'report-card', 'template' => 'elegant']),
                                'free' => false,
                            ],
                            [
                                'key' => 'modern',
                                'title' => 'Modern',
                                'desc' => 'Bold dark mode design with cyan accents and card-based layout.',
                                'preview' => route('settings.templates.preview', ['type' => 'report-card', 'template' => 'modern']),
                                'free' => false,
                            ],
                            [
                                'key' => 'classic',
                                'title' => 'Classic',
                                'desc' => 'Traditional black and white formal layout with maximum readability.',
                                'preview' => route('settings.templates.preview', ['type' => 'report-card', 'template' => 'classic']),
                                'free' => false,
                            ],
                        ];
                    @endphp


                    @foreach ($reportTemplates as $t)
                        @php
                            $isLocked = !$t['free'] && !$hasPremium;
                        @endphp
                        <label
                            class="group cursor-pointer rounded-3xl border bg-white/70 p-5 shadow-sm ring-1 ring-white/50 backdrop-blur transition hover:shadow-md {{ $reportCardTemplate === $t['key'] ? 'border-emerald-300 ring-2 ring-emerald-600' : 'border-gray-100' }} {{ $isLocked ? 'opacity-60' : '' }}">
                            <input type="radio" name="report_card_template" value="{{ $t['key'] }}" class="sr-only"
                                @checked($reportCardTemplate === $t['key']) @disabled($isLocked) />
                            <div class="flex items-start justify-between gap-4">
                                <div class="min-w-0">
                                    <div class="text-sm font-black text-gray-900 flex items-center gap-2">
                                        {{ $t['title'] }}
                                        @if($isLocked)
                                            <svg class="h-4 w-4 text-red-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                            </svg>
                                        @endif
                                    </div>
                                    <div class="mt-1 text-sm font-semibold text-gray-600">{{ $t['desc'] }}</div>
                                    @if($isLocked)
                                        <div class="mt-2 text-xs font-bold text-red-600">🔒 Premium License Required</div>
                                    @endif
                                </div>
                                <span
                                    class="inline-flex items-center rounded-full {{ $t['free'] ? 'bg-green-100 text-green-800' : 'bg-emerald-100 text-emerald-800' }} px-3 py-1 text-xs font-black group-hover:{{ $t['free'] ? 'bg-green-200' : 'bg-emerald-200' }}">
                                    {{ $t['free'] ? 'FREE' : 'PRO' }}
                                </span>
                            </div>

                            <div class="mt-4 flex items-center justify-between">
                                <div class="text-xs font-semibold text-gray-500">{{ $isLocked ? 'Locked' : 'Click card to select' }}</div>
                                <button type="button" class="btn-outline"
                                    @click.stop="open = true; src = @js($t['preview']); title = @js('Report Card · ' . $t['title'])">
                                    Preview
                                </button>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            <button type="submit"
                class="w-full rounded-xl bg-slate-900 px-5 py-3 text-sm font-bold text-white shadow-lg hover:bg-slate-800 transition-all">
                Save Template Selection
            </button>
        </form>

        <!-- Preview Modal -->
        <div x-show="open" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 p-4"
            @click.self="open = false">
            <div class="w-full max-w-5xl overflow-hidden rounded-3xl bg-white shadow-2xl ring-1 ring-black/5">
                <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                    <div class="text-sm font-black text-slate-900" x-text="title"></div>
                    <button type="button"
                        class="rounded-xl bg-slate-100 px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-200"
                        @click="open = false">
                        Close
                    </button>
                </div>
                <div class="h-[80vh] bg-slate-50">
                    <iframe :src="src" class="h-full w-full" title="Template Preview"></iframe>
                </div>
            </div>
        </div>
    </div>
@endsection