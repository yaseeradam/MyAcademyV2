@php
    $submitted = (bool) $attempt->submitted_at;
    $questions = $exam?->questions?->values() ?? collect();
    $totalQuestions = $questions->count();
    $answered = count(array_filter($this->answers, fn ($v) => (int) $v > 0));
    $remaining = $submitted ? 0 : (int) $this->remainingSeconds();
    $mm = str_pad((string) intdiv($remaining, 60), 2, '0', STR_PAD_LEFT);
    $ss = str_pad((string) ($remaining % 60), 2, '0', STR_PAD_LEFT);
    $unlockIn = $submitted ? 0 : (int) $this->submitUnlockInSeconds();
    $unlockMm = str_pad((string) intdiv($unlockIn, 60), 2, '0', STR_PAD_LEFT);
    $unlockSs = str_pad((string) ($unlockIn % 60), 2, '0', STR_PAD_LEFT);
    $currentIndex = max(0, min((int) $this->currentIndex, max(0, $totalQuestions - 1)));
    $currentQuestion = $totalQuestions > 0 ? $questions->get($currentIndex) : null;
@endphp

<div class="min-h-screen bg-slate-50" @if(! $submitted) wire:poll.10s="tick" @endif x-data="examKeyboard" @keydown.window="handleKeyPress($event)">
    <div class="border-b-4 border-blue-600 bg-white px-6 py-6 shadow-sm">
        <div class="mx-auto max-w-7xl">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div class="flex items-start gap-4">
                    @if ($student->passport_photo_url)
                        <img src="{{ $student->passport_photo_url }}" alt="{{ $student->full_name ?? 'Student' }}" class="h-20 w-20 rounded-lg object-cover ring-2 ring-slate-200" />
                    @else
                        <div class="grid h-20 w-20 place-items-center rounded-lg bg-blue-100 ring-2 ring-blue-200">
                            <span class="text-3xl font-black text-blue-700">{{ mb_substr($student->first_name ?? 'S', 0, 1) }}</span>
                        </div>
                    @endif
                    <div>
                        <h1 class="text-xl font-black text-slate-900">{{ $exam->title }}</h1>
                        <p class="mt-0.5 text-sm font-semibold text-slate-700">
                            {{ $student->full_name ?? ($student->first_name.' '.$student->last_name) }}
                            <span class="font-mono text-xs text-slate-500">({{ $student->admission_number }})</span>
                        </p>
                        <p class="mt-1 text-xs text-slate-600">
                            {{ $exam->schoolClass?->name ?? '-' }} &middot; {{ $exam->subject?->name ?? '-' }} &middot; {{ (int) $exam->duration_minutes }} minutes
                        </p>
                    </div>
                </div>

                @if (! $submitted)
                    <div class="flex flex-wrap items-center gap-3">
                        <div class="rounded-lg border-2 {{ $remaining <= 60 ? 'border-red-300 bg-red-50' : 'border-blue-200 bg-blue-50' }} px-5 py-3">
                            <div class="text-[10px] font-bold uppercase tracking-wider text-slate-600">Time Remaining</div>
                            <div class="mt-0.5 font-mono text-2xl font-black {{ $remaining <= 60 ? 'text-red-600' : 'text-blue-600' }}">
                                {{ $mm }}:{{ $ss }}
                            </div>
                        </div>
                        <div class="rounded-lg border-2 border-emerald-200 bg-emerald-50 px-5 py-3">
                            <div class="text-[10px] font-bold uppercase tracking-wider text-slate-600">Answered</div>
                            <div class="mt-0.5 text-2xl font-black text-emerald-600">{{ $answered }}/{{ $totalQuestions }}</div>
                        </div>
                    </div>
                @else
                    <div class="rounded-lg border-2 border-emerald-300 bg-emerald-50 px-5 py-3">
                        <div class="text-[10px] font-bold uppercase tracking-wider text-emerald-700">Status</div>
                        <div class="mt-0.5 text-xl font-black text-emerald-700">✓ Submitted</div>
                    </div>
                @endif
            </div>

            @if (! $submitted && $unlockIn > 0)
                <div class="mt-4 rounded-lg border-2 border-amber-300 bg-amber-50 px-4 py-2.5">
                    <p class="text-xs font-semibold text-amber-900">
                        ⏳ Submit button unlocks in {{ $unlockMm }}:{{ $unlockSs }} (after half exam time)
                    </p>
                </div>
            @endif

            @if ($this->lastSavedAt && ! $submitted)
                <div class="mt-3 text-xs font-medium text-slate-500">
                    ✓ Auto-saved at {{ $this->lastSavedAt }}
                </div>
            @endif
        </div>
    </div>

    <div class="mx-auto max-w-7xl px-6 py-8">
        @if ($submitted)
            <div class="rounded-2xl bg-white p-8 shadow-lg ring-1 ring-slate-200">
                <h2 class="text-xl font-black text-slate-900">🎉 Exam Submitted</h2>
                <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="rounded-xl border-2 border-emerald-200 bg-emerald-50 p-6">
                        <div class="text-xs font-bold uppercase tracking-wider text-emerald-700">Score</div>
                        <div class="mt-2 text-3xl font-black text-emerald-900">{{ (int) $attempt->score }} / {{ (int) $attempt->max_score }}</div>
                    </div>
                    <div class="rounded-xl border-2 border-purple-200 bg-purple-50 p-6">
                        <div class="text-xs font-bold uppercase tracking-wider text-purple-700">Submitted</div>
                        <div class="mt-2 text-sm font-semibold text-purple-900">{{ $attempt->submitted_at?->format('M j, Y g:i A') }}</div>
                    </div>
                </div>

                <div class="mt-6">
                    <a href="{{ route('cbt.student') }}" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-6 py-3 text-sm font-bold text-white shadow-lg hover:bg-blue-700">
                        ← Back to Exams
                    </a>
                </div>
            </div>
        @else
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-4">
                <div class="rounded-2xl bg-white p-6 shadow-lg ring-1 ring-slate-200 lg:col-span-1">
                    <h3 class="text-sm font-black text-slate-900">Question Navigator</h3>
                    <p class="mt-1 text-xs text-slate-600">Tap to jump to question</p>

                    <div class="mt-6 grid grid-cols-5 gap-2">
                        @foreach ($questions as $idx => $q)
                            @php
                                $isAnswered = (int) ($this->answers[$q->id] ?? 0) > 0;
                                $isCurrent = (int) $idx === (int) $currentIndex;
                                $cls = $isCurrent
                                    ? 'bg-blue-600 text-white shadow-md scale-105'
                                    : ($isAnswered ? 'bg-emerald-100 text-emerald-800 border-2 border-emerald-300' : 'bg-slate-100 text-slate-700 border-2 border-slate-200 hover:bg-slate-200');
                            @endphp
                            <button type="button" wire:click="goTo({{ $idx }})" class="h-12 w-12 rounded-lg text-xs font-black transition-all {{ $cls }}">
                                {{ $idx + 1 }}
                            </button>
                        @endforeach
                    </div>

                    <div class="mt-6 space-y-2 text-xs">
                        <div class="flex items-center gap-2">
                            <div class="h-4 w-4 rounded bg-blue-600"></div>
                            <span class="font-medium text-slate-700">Current</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="h-4 w-4 rounded border-2 border-emerald-300 bg-emerald-100"></div>
                            <span class="font-medium text-slate-700">Answered</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="h-4 w-4 rounded border-2 border-slate-200 bg-slate-100"></div>
                            <span class="font-medium text-slate-700">Unanswered</span>
                        </div>
                    </div>
                </div>

                <div class="space-y-6 lg:col-span-3">
                    @if (! $currentQuestion)
                        <div class="rounded-2xl bg-white p-8 text-center shadow-lg ring-1 ring-slate-200">
                            <p class="text-sm text-slate-600">No questions in this exam yet.</p>
                        </div>
                    @else
                        @php
                            $selected = (int) ($this->answers[$currentQuestion->id] ?? 0);
                        @endphp
                        <div class="rounded-2xl bg-white p-8 shadow-lg ring-1 ring-slate-200">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                <div class="min-w-0">
                                    <div class="inline-flex items-center gap-2 rounded-lg border-2 border-blue-200 bg-blue-50 px-3 py-1 text-xs font-bold text-blue-800">
                                        Question {{ $currentIndex + 1 }} of {{ $totalQuestions }}
                                    </div>
                                    <h3 class="mt-3 text-lg font-bold leading-relaxed text-slate-900">
                                        {{ $currentQuestion->prompt }}
                                    </h3>
                                    <p class="mt-2 text-xs font-semibold text-slate-600">Worth {{ (int) $currentQuestion->marks }} mark(s)</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button type="button" wire:click="prev" @disabled($currentIndex === 0) class="rounded-lg border-2 border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50">
                                        ← Prev
                                    </button>
                                    <button type="button" wire:click="next" @disabled($currentIndex >= $totalQuestions - 1) class="rounded-lg border-2 border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50">
                                        Next →
                                    </button>
                                </div>
                            </div>

                            <div class="mt-6 space-y-3">
                                @foreach ($currentQuestion->options as $opt)
                                    @php
                                        $isSelected = $selected === (int) $opt->id;
                                        $optionCls = $isSelected
                                            ? 'border-blue-500 bg-blue-50 border-2'
                                            : 'border-slate-200 bg-slate-50 border-2 hover:bg-slate-100';
                                    @endphp
                                    <label class="flex cursor-pointer items-start gap-4 rounded-xl px-5 py-4 transition-all {{ $optionCls }}" data-option-index="{{ $loop->index }}" data-option-id="{{ $opt->id }}" data-question-id="{{ $currentQuestion->id }}">
                                        <input
                                            type="radio"
                                            name="q{{ $currentQuestion->id }}"
                                            value="{{ $opt->id }}"
                                            @checked($isSelected)
                                            wire:click="selectOption({{ $currentQuestion->id }}, {{ $opt->id }})"
                                            class="mt-1 h-5 w-5 text-blue-600"
                                        />
                                        <span class="min-w-0 text-sm leading-relaxed {{ $isSelected ? 'font-semibold text-slate-900' : 'text-slate-800' }}">
                                            <span class="font-black {{ $isSelected ? 'text-blue-600' : 'text-slate-600' }}">{{ chr(65 + $loop->index) }}.</span>
                                            {{ $opt->label }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="rounded-2xl bg-white p-6 shadow-lg ring-1 ring-slate-200">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                <div class="text-sm font-medium text-slate-700">
                                    <span class="font-bold text-emerald-600">{{ $answered }}</span> answered
                                    <span class="mx-2 text-slate-400">•</span>
                                    <span class="font-bold text-slate-900">{{ max(0, $totalQuestions - $answered) }}</span> remaining
                                </div>
                                <button
                                    type="button"
                                    wire:click="submitExam"
                                    @disabled(! $this->canSubmitNow())
                                    onclick="if (!confirm('Submit this exam now? You cannot change answers after submitting.')) { event.stopImmediatePropagation(); }"
                                    class="rounded-xl bg-emerald-600 px-8 py-3 text-sm font-bold text-white shadow-lg hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-60"
                                >
                                    Submit Exam
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('examKeyboard', () => ({
            handleKeyPress(event) {
                if (event.target.tagName === 'INPUT' || event.target.tagName === 'TEXTAREA') return;
                
                const key = event.key.toLowerCase();
                
                if (key === 'arrowright' || key === 'arrowdown') {
                    event.preventDefault();
                    const nextBtn = document.querySelector('button[wire\\:click="next"]');
                    if (nextBtn && !nextBtn.disabled) nextBtn.click();
                } else if (key === 'arrowleft' || key === 'arrowup') {
                    event.preventDefault();
                    const prevBtn = document.querySelector('button[wire\\:click="prev"]');
                    if (prevBtn && !prevBtn.disabled) prevBtn.click();
                } else if (['a', 'b', 'c', 'd'].includes(key)) {
                    event.preventDefault();
                    const index = key.charCodeAt(0) - 97;
                    const option = document.querySelector(`label[data-option-index="${index}"]`);
                    if (option) {
                        const radio = option.querySelector('input[type="radio"]');
                        if (radio) radio.click();
                    }
                }
            }
        }));
    });
</script>
