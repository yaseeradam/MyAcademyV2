<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('more-features') }}"
                class="inline-flex items-center px-4 py-2 bg-white/80 border border-slate-200 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50">
                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back
            </a>
            <div>
                <h2 class="text-2xl font-bold text-slate-900">Certificate Generator</h2>
                <p class="text-slate-500 text-sm mt-1">Design and issue official certificates to students</p>
            </div>
        </div>
        <button wire:click="bulkGenerate" wire:loading.attr="disabled" @disabled(!$classId || $this->filteredStudents->isEmpty())
            class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-yellow-500 to-amber-600 hover:from-yellow-600 hover:to-amber-700 text-white rounded-lg font-medium disabled:opacity-50">
            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
            <span wire:loading.remove wire:target="bulkGenerate">Bulk Generate
                ({{ $this->filteredStudents->count() }})</span>
            <span wire:loading wire:target="bulkGenerate">Generating...</span>
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Settings Panel -->
        <div class="lg:col-span-1">
            <div class="rounded-2xl border border-slate-200/80 bg-white/80 backdrop-blur-xl shadow-lg">
                <div class="p-6 border-b border-slate-100">
                    <h3 class="text-lg font-bold text-slate-800">Certificate Settings</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="space-y-2">
                        <label class="text-xs font-semibold uppercase tracking-wider text-slate-600">Certificate
                            Type</label>
                        <select wire:model.live="type"
                            class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="Achievement">Academic Merit</option>
                            <option value="Attendance">Perfect Attendance</option>
                            <option value="Excellence">Sports Excellence</option>
                            <option value="Graduation">Graduation</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-semibold uppercase tracking-wider text-slate-600">Issue Date</label>
                        <input wire:model.live="issueDate" type="date"
                            class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-semibold uppercase tracking-wider text-slate-600">Title</label>
                        <input wire:model.live="title" placeholder="Certificate of Achievement"
                            class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-semibold uppercase tracking-wider text-slate-600">Reason /
                            Description</label>
                        <input wire:model.live="description" placeholder="e.g. Outstanding Performance in Math"
                            class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                    </div>

                    <div class="pt-4 border-t border-slate-100">
                        <label
                            class="text-xs font-semibold uppercase tracking-wider text-slate-600 mb-2 block">Filters</label>
                        <div class="space-y-3">
                            <select wire:model.live="classId"
                                class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">All Classes</option>
                                @foreach ($this->classes as $class)
                                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                                @endforeach
                            </select>

                            <div class="relative">
                                <svg class="absolute left-3 top-2.5 h-4 w-4 text-slate-400" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                <input wire:model.live.debounce.250ms="search" placeholder="Search students..."
                                    class="w-full pl-9 pr-3 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Preview & Students Panel -->
        <div class="lg:col-span-2 space-y-4">
            <!-- Preview Card with Template Navigation -->
            <div class="rounded-2xl border border-slate-200/80 bg-white/80 backdrop-blur-xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 bg-slate-50/50 border-b border-slate-100">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-medium text-slate-500 flex items-center gap-2">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Live Design Preview
                        </h3>
                        <div class="flex items-center gap-3">
                            <span
                                class="text-xs font-bold text-slate-600 bg-slate-100 px-3 py-1 rounded-full">{{ $this->templateLabel }}</span>
                            <span class="text-xs text-slate-400">{{ $previewIndex + 1 }} /
                                {{ $this->templateCount }}</span>
                        </div>
                    </div>
                </div>
                <div class="p-8 relative">
                    <!-- Left Arrow -->
                    <button wire:click="prevTemplate"
                        class="absolute left-2 top-1/2 -translate-y-1/2 z-10 w-10 h-10 flex items-center justify-center rounded-full bg-white/90 shadow-lg border border-slate-200 hover:bg-slate-50 hover:shadow-xl transition-all group"
                        title="Previous template">
                        <svg class="h-5 w-5 text-slate-400 group-hover:text-slate-700 transition-colors" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <!-- Right Arrow -->
                    <button wire:click="nextTemplate"
                        class="absolute right-2 top-1/2 -translate-y-1/2 z-10 w-10 h-10 flex items-center justify-center rounded-full bg-white/90 shadow-lg border border-slate-200 hover:bg-slate-50 hover:shadow-xl transition-all group"
                        title="Next template">
                        <svg class="h-5 w-5 text-slate-400 group-hover:text-slate-700 transition-colors" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>

                    {{-- Modern --}}
                    @if($template === 'modern')
                        <div
                            class="aspect-[1.414/1] w-full max-w-2xl mx-auto bg-white border-8 border-slate-100 shadow-lg rounded p-4 flex flex-col items-center justify-center text-center relative overflow-hidden transition-all duration-300">
                            <div class="absolute inset-2 border border-slate-100 pointer-events-none"></div>
                            <div class="mb-4">
                                <svg class="h-10 w-10 text-slate-200 mx-auto mb-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path d="M12 14l9-5-9-5-9 5 9 5z" />
                                    <path
                                        d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                                </svg>
                                <h3 class="text-lg font-bold text-slate-400 uppercase tracking-widest">
                                    {{ config('app.name') }}</h3>
                            </div>
                            <h1 class="text-4xl font-serif italic text-slate-800 mb-4">
                                {{ $title ?: 'Certificate of Achievement' }}</h1>
                            <p class="text-sm text-slate-500 mb-4 uppercase tracking-tighter">This is to certify that</p>
                            <div class="mb-6"><span
                                    class="text-3xl font-serif font-bold italic text-slate-700 border-b-2 border-slate-200 px-8 py-1">Student
                                    Name</span></div>
                            <div class="space-y-1">
                                <p class="text-sm font-medium text-slate-600 uppercase">
                                    {{ $type === 'Achievement' ? 'Outstanding Academic Merit' : ($type === 'Attendance' ? 'Perfect Attendance Record' : ($type === 'Excellence' ? 'Excellence in Athleticism' : 'Academic Excellence')) }}
                                </p>
                                <p class="text-xs text-slate-400 max-w-xs mx-auto italic">
                                    {{ $description ?: 'Outstanding performance' }}</p>
                            </div>
                            <div
                                class="absolute bottom-8 left-8 right-8 flex justify-between items-end border-t border-slate-50 pt-2">
                                <div class="w-32 border-t border-slate-200">
                                    <p class="text-[10px] text-slate-400 mt-1">Principal</p>
                                </div>
                                <div class="w-16 h-16 bg-yellow-50 rounded-full flex items-center justify-center"><svg
                                        class="h-8 w-8 text-yellow-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg></div>
                                <div class="w-32 border-t border-slate-200">
                                    <p class="text-[10px] text-slate-400 mt-1">
                                        {{ $issueDate ? \Carbon\Carbon::parse($issueDate)->format('M d, Y') : 'Date' }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Classic --}}
                    @if($template === 'classic')
                        <div
                            class="aspect-[1.414/1] w-full max-w-2xl mx-auto bg-white shadow-lg rounded p-4 flex flex-col items-center justify-center text-center relative overflow-hidden border-4 border-blue-500 transition-all duration-300">
                            <div class="absolute inset-2 border-2 border-blue-400 pointer-events-none"></div>
                            <h3 class="text-base font-bold text-slate-900 uppercase tracking-widest mb-1">
                                {{ config('app.name') }}</h3>
                            <div class="w-3/4 border-t-2 border-blue-500 my-3"></div>
                            <h1 class="text-3xl font-bold text-slate-900 mb-1">{{ $title ?: 'Certificate' }}</h1>
                            <p class="text-xs font-bold uppercase tracking-widest text-slate-600 mb-3">{{ $type }}</p>
                            <p class="text-xs uppercase tracking-wider text-slate-500 mb-2">Presented to</p>
                            <span class="text-2xl font-bold text-blue-600 mb-2">Student Name</span>
                            <p class="text-xs text-slate-500 italic max-w-xs">
                                {{ $description ?: 'Outstanding performance' }}</p>
                            <div class="absolute bottom-6 left-8 right-8 flex justify-between">
                                <div class="w-28 border-t border-slate-300">
                                    <p class="text-[9px] text-slate-500 mt-1 font-bold uppercase">Signature</p>
                                </div>
                                <div class="w-28 border-t border-slate-300">
                                    <p class="text-[9px] text-slate-500 mt-1 font-bold uppercase">
                                        {{ $issueDate ? \Carbon\Carbon::parse($issueDate)->format('M d, Y') : 'Date' }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Elegant --}}
                    @if($template === 'elegant')
                        <div class="aspect-[1.414/1] w-full max-w-2xl mx-auto shadow-lg rounded p-4 flex flex-col items-center justify-center text-center relative overflow-hidden transition-all duration-300"
                            style="background: linear-gradient(135deg, #fffef5, #fef9e7); border: 6px solid #d4af37;">
                            <div class="absolute inset-3 border-2 border-blue-900 pointer-events-none"></div>
                            <div class="absolute top-5 left-5 w-12 h-12 border-t-4 border-l-4 border-yellow-600"></div>
                            <div class="absolute top-5 right-5 w-12 h-12 border-t-4 border-r-4 border-yellow-600"></div>
                            <div class="absolute bottom-5 left-5 w-12 h-12 border-b-4 border-l-4 border-yellow-600"></div>
                            <div class="absolute bottom-5 right-5 w-12 h-12 border-b-4 border-r-4 border-yellow-600"></div>
                            <h3 class="text-sm font-bold text-blue-900 uppercase tracking-[0.3em]">{{ config('app.name') }}
                            </h3>
                            <div class="w-48 border-t border-yellow-600 my-3 relative">
                                <span
                                    class="absolute -top-2 left-1/2 -translate-x-1/2 bg-yellow-50 px-2 text-yellow-600 text-[10px]">◆</span>
                            </div>
                            <h1 class="text-3xl font-serif text-blue-900 mb-1">{{ $title ?: 'Certificate of Achievement' }}
                            </h1>
                            <p class="text-xs font-bold uppercase tracking-[0.2em] text-yellow-600 mb-3">{{ $type }}</p>
                            <p class="text-xs italic text-slate-500 mb-2">This Certificate is Proudly Presented To</p>
                            <span
                                class="text-2xl font-serif italic text-blue-900 border-b-2 border-yellow-600 px-6 py-1">Student
                                Name</span>
                            <p class="text-xs text-slate-500 italic mt-3 max-w-xs">
                                {{ $description ?: 'Outstanding performance' }}</p>
                            <div class="absolute bottom-8 left-10 right-10 flex justify-between items-end">
                                <div class="w-28 border-t border-blue-900">
                                    <p class="text-[9px] text-blue-900 mt-1 font-bold uppercase tracking-wider">Signature
                                    </p>
                                </div>
                                <div
                                    class="w-14 h-14 rounded-full border-2 border-yellow-600 bg-yellow-50 flex items-center justify-center text-[8px] font-bold text-blue-900 uppercase">
                                    SEAL</div>
                                <div class="w-28 border-t border-blue-900">
                                    <p class="text-[9px] text-blue-900 mt-1 font-bold uppercase tracking-wider">
                                        {{ $issueDate ? \Carbon\Carbon::parse($issueDate)->format('M d, Y') : 'Date' }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Vibrant --}}
                    @if($template === 'vibrant')
                        <div
                            class="aspect-[1.414/1] w-full max-w-2xl mx-auto bg-amber-50 shadow-lg rounded-2xl p-4 flex flex-col items-center justify-center text-center relative overflow-hidden transition-all duration-300">
                            <div class="absolute top-0 left-0 right-0 h-28 rounded-t-2xl"
                                style="background: linear-gradient(135deg, #6c5ce7, #a855f7, #ec4899, #f59e0b);"></div>
                            <div class="absolute bottom-0 left-0 right-0 h-20 rounded-b-2xl"
                                style="background: linear-gradient(135deg, #f59e0b, #06b6d4, #6c5ce7);"></div>
                            <div class="absolute top-32 left-16 w-2 h-2 bg-yellow-400 rounded-full opacity-60"></div>
                            <div class="absolute top-36 right-24 w-1.5 h-1.5 bg-purple-400 rounded-full opacity-50"></div>
                            <div class="relative z-10">
                                <h3 class="text-sm font-extrabold text-white uppercase tracking-wider drop-shadow mb-6">
                                    {{ config('app.name') }}</h3>
                            </div>
                            <div class="relative z-10 mt-2">
                                <h1 class="text-2xl font-extrabold text-slate-900 mb-0">Certificate</h1>
                                <span class="text-lg italic text-purple-500 font-semibold">of</span>
                                <p class="text-xl font-extrabold text-purple-600 uppercase tracking-wider">
                                    {{ $type ?: 'Achievement' }}</p>
                            </div>
                            <p class="relative z-10 text-xs uppercase tracking-wider text-slate-500 mt-3 font-bold">This
                                Certificate is Presented To</p>
                            <span class="relative z-10 text-2xl font-serif font-bold italic text-slate-900 mt-1">Student
                                Name</span>
                            <div class="relative z-10 w-52 h-0.5 mx-auto mt-1"
                                style="background: linear-gradient(90deg, transparent, #a855f7, transparent);"></div>
                            <p class="relative z-10 text-xs text-slate-500 italic mt-2 max-w-xs">
                                {{ $description ?: 'Outstanding performance' }}</p>
                            <div class="relative z-10 mt-3 w-14 h-14 rounded-full flex items-center justify-center text-[7px] font-extrabold text-white uppercase mx-auto"
                                style="background: linear-gradient(135deg, #fbbf24, #f59e0b); border: 2px solid #d97706;">
                                EXCELLENCE<br>AWARD</div>
                            <div class="absolute bottom-24 left-10 right-10 flex justify-between z-10">
                                <div class="w-24 border-t border-slate-300">
                                    <p class="text-[9px] text-slate-500 mt-1 font-bold">Principal</p>
                                </div>
                                <div class="w-24 border-t border-slate-300">
                                    <p class="text-[9px] text-slate-500 mt-1 font-bold">
                                        {{ $issueDate ? \Carbon\Carbon::parse($issueDate)->format('M d, Y') : 'Dated' }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Minimal --}}
                    @if($template === 'minimal')
                        <div class="aspect-[1.414/1] w-full max-w-2xl mx-auto bg-white shadow-lg rounded p-6 flex flex-col items-center justify-center text-center relative overflow-hidden transition-all duration-300"
                            style="border: 1px solid #e2e8f0;">
                            <div class="absolute top-0 left-6 right-6 h-1 bg-sky-500"></div>
                            <div class="absolute inset-5 border border-slate-100 pointer-events-none"></div>
                            <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.4em]">
                                {{ config('app.name') }}</h3>
                            <div class="w-14 border-t border-sky-500 my-5"></div>
                            <h1 class="text-4xl font-light text-slate-900 tracking-tight">{{ $title ?: 'Certificate' }}</h1>
                            <p class="text-[10px] font-bold uppercase tracking-[0.3em] text-sky-500 mt-2">{{ $type }}</p>
                            <p class="text-[10px] uppercase tracking-[0.2em] text-slate-400 mt-6 font-semibold">Presented to
                            </p>
                            <span class="text-2xl font-bold text-slate-900 mt-2 tracking-wide">Student Name</span>
                            <div class="w-44 border-t border-slate-200 mt-2"></div>
                            <p class="text-xs text-slate-400 mt-4 max-w-xs">{{ $description ?: 'Outstanding performance' }}
                            </p>
                            <div class="absolute bottom-8 left-12 right-12 flex justify-between">
                                <div class="w-28 border-t border-slate-200">
                                    <p class="text-[8px] text-slate-400 mt-1 font-bold uppercase tracking-widest">Signature
                                    </p>
                                </div>
                                <div class="w-28 border-t border-slate-200">
                                    <p class="text-[8px] text-slate-400 mt-1 font-bold uppercase tracking-widest">
                                        {{ $issueDate ? \Carbon\Carbon::parse($issueDate)->format('M d, Y') : 'Date' }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Royal --}}
                    @if($template === 'royal')
                        <div
                            class="aspect-[1.414/1] w-full max-w-2xl mx-auto bg-purple-50 shadow-lg rounded p-0 flex relative overflow-hidden transition-all duration-300">
                            <div class="w-1/4 h-full"
                                style="background: linear-gradient(180deg, #581c87, #7c3aed, #a855f7, #d946ef);"></div>
                            <div class="absolute top-6 left-[28%] right-6 bottom-6 border-2 border-yellow-600"></div>
                            <div class="absolute top-8 left-[29%] right-8 bottom-8 border border-purple-400"></div>
                            <div class="absolute top-8 left-4 w-[22%] text-center">
                                <p class="text-[10px] font-bold text-white/90 uppercase tracking-wider mt-4 leading-snug">
                                    {{ config('app.name') }}</p>
                            </div>
                            <div class="flex-1 flex flex-col items-center justify-center text-center px-6 relative z-10">
                                <h1 class="text-3xl font-bold text-slate-900 uppercase tracking-widest">Certificate</h1>
                                <span class="text-base italic text-purple-500 font-medium">of</span>
                                <p class="text-lg font-bold text-purple-700 uppercase tracking-[0.2em]">
                                    {{ $type ?: 'Achievement' }}</p>
                                <p class="text-xs text-slate-500 mt-4 font-semibold uppercase tracking-wider">This is
                                    Proudly Presented To</p>
                                <span class="text-xl font-serif font-bold italic text-slate-900 mt-2">Student Name</span>
                                <div class="w-44 border-t-2 border-yellow-600 mt-1 relative">
                                    <span
                                        class="absolute -top-1.5 left-1/2 -translate-x-1/2 bg-purple-50 px-1 text-yellow-600 text-[8px]">◇</span>
                                </div>
                                <p class="text-xs text-slate-500 italic mt-2 max-w-[16rem]">
                                    {{ $description ?: 'Outstanding performance' }}</p>
                                <div class="mt-3 w-12 h-12 rounded-full flex items-center justify-center text-[7px] font-extrabold uppercase mx-auto border-2 border-amber-700"
                                    style="background: linear-gradient(135deg, #fbbf24, #f59e0b); color: #7c2d12;">
                                    EXCEL-<br>LENCE</div>
                            </div>
                            <div class="absolute bottom-6 left-[30%] right-10 flex justify-between">
                                <div class="w-24 border-t border-purple-300">
                                    <p class="text-[8px] text-purple-700 mt-1 font-bold uppercase">Signature</p>
                                </div>
                                <div class="w-24 border-t border-purple-300">
                                    <p class="text-[8px] text-purple-700 mt-1 font-bold uppercase">
                                        {{ $issueDate ? \Carbon\Carbon::parse($issueDate)->format('M d, Y') : 'Date' }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Template Dots Navigation -->
                    <div class="flex items-center justify-center gap-2 mt-4">
                        @foreach($availableTemplates as $i => $tpl)
                            <button wire:click="setTemplate({{ $i }})"
                                class="w-2.5 h-2.5 rounded-full transition-all {{ $previewIndex === $i ? 'bg-amber-500 scale-125' : 'bg-slate-300 hover:bg-slate-400' }}"
                                title="{{ $tpl['label'] }}"></button>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Student List -->
            <div class="rounded-2xl border border-slate-200/80 bg-white/80 backdrop-blur-xl shadow-lg">
                <div class="px-6 py-4 border-b border-slate-100">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-medium">Select Students ({{ $this->filteredStudents->count() }})</h3>
                        <span class="text-xs font-normal text-slate-400">Click download to generate individual
                            certificate</span>
                    </div>
                </div>
                <div class="p-0 max-h-64 overflow-y-auto">
                    <div class="divide-y divide-slate-50">
                        @forelse ($this->filteredStudents as $student)
                            <div
                                class="flex items-center justify-between px-6 py-3 hover:bg-slate-50/50 group transition-colors">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="h-8 w-8 rounded-full bg-slate-100 flex items-center justify-center text-xs font-bold text-slate-500">
                                        {{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-slate-700">{{ $student->full_name }}</p>
                                        <p class="text-xs text-slate-400">{{ $student->admission_number ?: 'No ID' }}</p>
                                    </div>
                                </div>
                                <button wire:click="issueForStudent({{ $student->id }})"
                                    class="opacity-0 group-hover:opacity-100 transition-opacity inline-flex items-center px-3 py-1 text-xs font-medium text-blue-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg">
                                    <svg class="h-3.5 w-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                    Download
                                </button>
                            </div>
                        @empty
                            <div class="p-8 text-center text-slate-400 text-sm">No students match your criteria</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@once
    @push('scripts')
        <script>
            document.addEventListener('livewire:init', () => {
                Livewire.on('open-url', (event) => {
                    const data = event[0] || event;
                    const url = data?.url || data;
                    if (url) {
                        window.open(url, '_blank');
                    }
                });

                Livewire.on('alert', (event) => {
                    const data = event[0] || event;
                    if (data?.message) {
                        alert(data.message);
                    }
                });
            });
        </script>
    @endpush
@endonce