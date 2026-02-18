@extends('layouts.app')

@section('content')
    @php
        $certificateTemplate = old('certificate_template', config('myacademy.certificate_template', 'modern'));
        $reportCardTemplate = old('report_card_template', config('myacademy.report_card_template', 'standard'));
    @endphp

    <div class="space-y-6" x-data="{ open: false, src: null, title: '' }">
        <x-page-header title="Templates" subtitle="Choose which Certificate and Report Card template to use." accent="settings" />

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
                    <div class="icon-3d grid h-12 w-12 place-items-center rounded-xl bg-gradient-to-br from-amber-500 to-rose-600 text-white shadow-lg shadow-amber-500/30">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="M6 2h9l3 3v15a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z"/>
                            <path d="M14 2v4h4"/>
                            <path d="M8 13h8"/>
                            <path d="M8 17h6"/>
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
                            ],
                            [
                                'key' => 'classic',
                                'title' => 'Classic',
                                'desc' => 'Simple formal layout with clean border.',
                                'preview' => route('settings.templates.preview', ['type' => 'certificate', 'template' => 'classic']),
                            ],
                        ];
                    @endphp

                    @foreach ($certificateTemplates as $t)
                        <label
                            class="group cursor-pointer rounded-3xl border bg-white/70 p-5 shadow-sm ring-1 ring-white/50 backdrop-blur transition hover:shadow-md {{ $certificateTemplate === $t['key'] ? 'border-amber-300 ring-2 ring-amber-500' : 'border-gray-100' }}"
                        >
                            <input type="radio" name="certificate_template" value="{{ $t['key'] }}" class="sr-only" @checked($certificateTemplate === $t['key']) />
                            <div class="flex items-start justify-between gap-4">
                                <div class="min-w-0">
                                    <div class="text-sm font-black text-gray-900">{{ $t['title'] }}</div>
                                    <div class="mt-1 text-sm font-semibold text-gray-600">{{ $t['desc'] }}</div>
                                </div>
                                <span class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-xs font-black text-amber-800 group-hover:bg-amber-200">
                                    {{ strtoupper($t['key']) }}
                                </span>
                            </div>

                            <div class="mt-4 flex items-center justify-between">
                                <div class="text-xs font-semibold text-gray-500">Click card to select</div>
                                <button
                                    type="button"
                                    class="btn-outline"
                                    @click.stop="open = true; src = @js($t['preview']); title = @js('Certificate · '.$t['title'])"
                                >
                                    Preview
                                </button>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="rounded-3xl border border-gray-100 bg-gradient-to-br from-emerald-50 to-sky-50/60 p-6 shadow-lg">
                <div class="mb-5 flex items-center gap-3">
                    <div class="icon-3d grid h-12 w-12 place-items-center rounded-xl bg-gradient-to-br from-emerald-500 to-sky-600 text-white shadow-lg shadow-emerald-500/30">
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
                                'desc' => 'Full report card layout with detailed sections.',
                                'preview' => route('settings.templates.preview', ['type' => 'report-card', 'template' => 'standard']),
                            ],
                            [
                                'key' => 'compact',
                                'title' => 'Compact',
                                'desc' => 'Clean, minimal layout focused on scores and summary.',
                                'preview' => route('settings.templates.preview', ['type' => 'report-card', 'template' => 'compact']),
                            ],
                        ];
                    @endphp

                    @foreach ($reportTemplates as $t)
                        <label
                            class="group cursor-pointer rounded-3xl border bg-white/70 p-5 shadow-sm ring-1 ring-white/50 backdrop-blur transition hover:shadow-md {{ $reportCardTemplate === $t['key'] ? 'border-emerald-300 ring-2 ring-emerald-600' : 'border-gray-100' }}"
                        >
                            <input type="radio" name="report_card_template" value="{{ $t['key'] }}" class="sr-only" @checked($reportCardTemplate === $t['key']) />
                            <div class="flex items-start justify-between gap-4">
                                <div class="min-w-0">
                                    <div class="text-sm font-black text-gray-900">{{ $t['title'] }}</div>
                                    <div class="mt-1 text-sm font-semibold text-gray-600">{{ $t['desc'] }}</div>
                                </div>
                                <span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-black text-emerald-800 group-hover:bg-emerald-200">
                                    {{ strtoupper($t['key']) }}
                                </span>
                            </div>

                            <div class="mt-4 flex items-center justify-between">
                                <div class="text-xs font-semibold text-gray-500">Click card to select</div>
                                <button
                                    type="button"
                                    class="btn-outline"
                                    @click.stop="open = true; src = @js($t['preview']); title = @js('Report Card · '.$t['title'])"
                                >
                                    Preview
                                </button>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            <button type="submit" class="w-full rounded-xl bg-slate-900 px-5 py-3 text-sm font-bold text-white shadow-lg hover:bg-slate-800 transition-all">
                Save Template Selection
            </button>
        </form>

        <!-- Preview Modal -->
        <div x-show="open" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 p-4" @click.self="open = false">
            <div class="w-full max-w-5xl overflow-hidden rounded-3xl bg-white shadow-2xl ring-1 ring-black/5">
                <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                    <div class="text-sm font-black text-slate-900" x-text="title"></div>
                    <button type="button" class="rounded-xl bg-slate-100 px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-200" @click="open = false">
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

