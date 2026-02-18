<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('more-features') }}" class="inline-flex items-center px-4 py-2 bg-white/80 border border-slate-200 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50">
                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back
            </a>
            <div>
                <h2 class="text-2xl font-bold text-slate-900">Certificate Generator</h2>
                <p class="text-slate-500 text-sm mt-1">Design and issue official certificates to students</p>
            </div>
        </div>
        <button
            wire:click="bulkGenerate"
            wire:loading.attr="disabled"
            @disabled(!$classId || $this->filteredStudents->isEmpty())
            class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-yellow-500 to-amber-600 hover:from-yellow-600 hover:to-amber-700 text-white rounded-lg font-medium disabled:opacity-50"
        >
            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            <span wire:loading.remove wire:target="bulkGenerate">Bulk Generate ({{ $this->filteredStudents->count() }})</span>
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
                        <label class="text-xs font-semibold uppercase tracking-wider text-slate-600">Certificate Type</label>
                        <select wire:model.live="type" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="Achievement">Academic Merit</option>
                            <option value="Attendance">Perfect Attendance</option>
                            <option value="Excellence">Sports Excellence</option>
                            <option value="Graduation">Graduation</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-semibold uppercase tracking-wider text-slate-600">Issue Date</label>
                        <input wire:model.live="issueDate" type="date" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-semibold uppercase tracking-wider text-slate-600">Title</label>
                        <input wire:model.live="title" placeholder="Certificate of Achievement" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-semibold uppercase tracking-wider text-slate-600">Reason / Description</label>
                        <input wire:model.live="description" placeholder="e.g. Outstanding Performance in Math" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                    </div>

                    <div class="pt-4 border-t border-slate-100">
                        <label class="text-xs font-semibold uppercase tracking-wider text-slate-600 mb-2 block">Filters</label>
                        <div class="space-y-3">
                            <select wire:model.live="classId" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">All Classes</option>
                                @foreach ($this->classes as $class)
                                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                                @endforeach
                            </select>

                            <div class="relative">
                                <svg class="absolute left-3 top-2.5 h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                <input wire:model.live.debounce.250ms="search" placeholder="Search students..." class="w-full pl-9 pr-3 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Preview & Students Panel -->
        <div class="lg:col-span-2 space-y-4">
            <!-- Preview Card -->
            <div class="rounded-2xl border border-slate-200/80 bg-white/80 backdrop-blur-xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 bg-slate-50/50 border-b border-slate-100">
                    <h3 class="text-sm font-medium text-slate-500 flex items-center gap-2">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Live Design Preview
                    </h3>
                </div>
                <div class="p-8">
                    <div class="aspect-[1.414/1] w-full max-w-2xl mx-auto bg-white border-8 border-slate-100 shadow-lg rounded p-4 flex flex-col items-center justify-center text-center relative overflow-hidden">
                        <div class="absolute inset-2 border border-slate-100 pointer-events-none"></div>
                        
                        <div class="mb-4">
                            <svg class="h-10 w-10 text-slate-200 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M12 14l9-5-9-5-9 5 9 5z"/>
                                <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                            </svg>
                            <h3 class="text-lg font-bold text-slate-400 uppercase tracking-widest">{{ config('app.name') }}</h3>
                        </div>

                        <h1 class="text-4xl font-serif italic text-slate-800 mb-4">{{ $title ?: 'Certificate of Achievement' }}</h1>
                        <p class="text-sm text-slate-500 mb-4 uppercase tracking-tighter">This is to certify that</p>
                        
                        <div class="mb-6">
                            <span class="text-3xl font-serif font-bold italic text-slate-700 border-b-2 border-slate-200 px-8 py-1">
                                Student Name
                            </span>
                        </div>

                        <div class="space-y-1">
                            <p class="text-sm font-medium text-slate-600 uppercase">
                                {{ $type === 'Achievement' ? 'Outstanding Academic Merit' : ($type === 'Attendance' ? 'Perfect Attendance Record' : ($type === 'Excellence' ? 'Excellence in Athleticism' : 'Academic Excellence')) }}
                            </p>
                            <p class="text-xs text-slate-400 max-w-xs mx-auto italic">{{ $description ?: 'Outstanding performance' }}</p>
                        </div>

                        <div class="absolute bottom-8 left-8 right-8 flex justify-between items-end border-t border-slate-50 pt-2">
                            <div class="w-32 border-t border-slate-200">
                                <p class="text-[10px] text-slate-400 mt-1">Principal</p>
                            </div>
                            <div class="w-16 h-16 bg-yellow-50 rounded-full flex items-center justify-center">
                                <svg class="h-8 w-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="w-32 border-t border-slate-200">
                                <p class="text-[10px] text-slate-400 mt-1">{{ $issueDate ? \Carbon\Carbon::parse($issueDate)->format('M d, Y') : 'Date' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Student List -->
            <div class="rounded-2xl border border-slate-200/80 bg-white/80 backdrop-blur-xl shadow-lg">
                <div class="px-6 py-4 border-b border-slate-100">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-medium">Select Students ({{ $this->filteredStudents->count() }})</h3>
                        <span class="text-xs font-normal text-slate-400">Click download to generate individual certificate</span>
                    </div>
                </div>
                <div class="p-0 max-h-64 overflow-y-auto">
                    <div class="divide-y divide-slate-50">
                        @forelse ($this->filteredStudents as $student)
                            <div class="flex items-center justify-between px-6 py-3 hover:bg-slate-50/50 group transition-colors">
                                <div class="flex items-center gap-3">
                                    <div class="h-8 w-8 rounded-full bg-slate-100 flex items-center justify-center text-xs font-bold text-slate-500">
                                        {{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-slate-700">{{ $student->full_name }}</p>
                                        <p class="text-xs text-slate-400">{{ $student->admission_number ?: 'No ID' }}</p>
                                    </div>
                                </div>
                                <button
                                    wire:click="issueForStudent({{ $student->id }})"
                                    class="opacity-0 group-hover:opacity-100 transition-opacity inline-flex items-center px-3 py-1 text-xs font-medium text-blue-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg"
                                >
                                    <svg class="h-3.5 w-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
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
