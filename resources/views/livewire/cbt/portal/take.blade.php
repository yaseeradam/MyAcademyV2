@php
    $submitted = (bool) $attempt->submitted_at;
    $questions = $exam?->questions?->values() ?? collect();
    $totalQuestions = $questions->count();
    $answered = $questions->filter(function ($q) {
        $questionType = strtolower((string) ($q->type ?? 'mcq'));
        if ($questionType === 'theory') {
            return trim((string) ($this->theoryAnswers[$q->id] ?? '')) !== '';
        }

        return (int) ($this->answers[$q->id] ?? 0) > 0;
    })->count();
    $remaining = $submitted ? 0 : (int) $this->remainingSeconds();
    $mm = str_pad((string) intdiv($remaining, 60), 2, '0', STR_PAD_LEFT);
    $ss = str_pad((string) ($remaining % 60), 2, '0', STR_PAD_LEFT);
    $unlockIn = $submitted ? 0 : (int) $this->submitUnlockInSeconds();
    $unlockMm = str_pad((string) intdiv($unlockIn, 60), 2, '0', STR_PAD_LEFT);
    $unlockSs = str_pad((string) ($unlockIn % 60), 2, '0', STR_PAD_LEFT);
    $currentIndex = max(0, min((int) $this->currentIndex, max(0, $totalQuestions - 1)));
    $currentQuestion = $totalQuestions > 0 ? $questions->get($currentIndex) : null;
    $progressPercent = $totalQuestions > 0 ? round(($answered / $totalQuestions) * 100, 1) : 0;
    $hasTheory = $questions->contains(fn ($q) => $q->type === 'theory');
@endphp

<div class="cbt-take-root min-h-screen bg-gradient-to-br from-amber-50 via-white to-teal-50 text-slate-900" @if(! $submitted) wire:poll.1s="tick" @endif x-data="examKeyboard" @keydown.window="handleKeyPress($event)">
    <div class="flex flex-col">
        <div class="mx-auto w-full max-w-7xl px-6 pt-6">
            <div class="rounded-2xl bg-white/80 p-4 shadow-md ring-1 ring-slate-100 backdrop-blur">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex items-center gap-4">
                    @if ($student->passport_photo_url)
                        <img src="{{ $student->passport_photo_url }}" alt="{{ $student->full_name ?? 'Student' }}" class="h-24 w-24 rounded-full object-cover shadow-lg ring-4 ring-white" />
                    @else
                        <div class="grid h-24 w-24 place-items-center rounded-full bg-amber-100 shadow-lg ring-4 ring-white">
                            <span class="text-3xl font-bold text-amber-700">{{ mb_substr($student->first_name ?? 'S', 0, 1) }}</span>
                        </div>
                    @endif
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">CBT Session</div>
                        <h1 class="cbt-title text-2xl font-bold text-slate-900">{{ $exam->title }}</h1>
                        <div class="mt-1 text-sm text-slate-600">
                            {{ $student->full_name ?? ($student->first_name.' '.$student->last_name) }}
                            <span class="mx-1 text-slate-300">|</span>
                            <span class="font-mono text-xs text-slate-500">#{{ $student->admission_number }}</span>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <div class="rounded-xl {{ $remaining <= 60 ? 'bg-rose-100' : 'bg-emerald-100' }} px-3.5 py-2 shadow-sm">
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-600">Time Left</div>
                        <div class="mt-1 font-mono text-xl font-bold {{ $remaining <= 60 ? 'text-rose-700' : 'text-emerald-700' }}">
                            {{ $mm }}:{{ $ss }}
                        </div>
                    </div>
                    <div class="rounded-xl bg-slate-100 px-3.5 py-2 shadow-sm">
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-600">Progress</div>
                        <div class="mt-1 text-xl font-bold text-slate-800">{{ $answered }}/{{ $totalQuestions }}</div>
                        <div class="mt-2 h-1.5 w-24 overflow-hidden rounded-full bg-white">
                            <div class="h-full rounded-full bg-slate-700" style="width: {{ $progressPercent }}%"></div>
                        </div>
                    </div>
                    <div class="rounded-xl bg-amber-100 px-3.5 py-2 shadow-sm">
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-600">Question</div>
                        <div class="mt-1 text-xl font-bold text-amber-800">{{ $currentIndex + 1 }} of {{ $totalQuestions }}</div>
                    </div>
                </div>
            </div>

            @if (! $submitted && $unlockIn > 0)
                <div class="mt-3 rounded-lg bg-amber-50 px-3 py-2 text-xs font-semibold text-amber-800" style="display: none;">
                    Submit unlocks in {{ $unlockMm }}:{{ $unlockSs }}.
                </div>
            @endif
        </div>
        </div>

        <div class="mx-auto w-full max-w-7xl px-6 pb-6 pt-5">
        @if ($submitted)
            <div class="rounded-2xl bg-white p-7 shadow-lg ring-1 ring-slate-100">
                <div>
                    <div class="mb-6 grid h-20 w-20 place-items-center rounded-full bg-emerald-100">
                        <svg class="h-10 w-10 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h2 class="text-3xl font-bold text-slate-900">Exam Submitted</h2>
                    <p class="mt-2 text-base text-slate-600">Your answers have been recorded.</p>
                </div>

                <div class="mt-8 grid gap-5 sm:grid-cols-2">
                    @if ($exam->show_score)
                        <div class="rounded-2xl bg-emerald-50 p-6 text-center shadow-sm">
                            <div class="text-xs font-semibold uppercase tracking-wider text-slate-600">Your Score</div>
                            <div class="mt-3 text-5xl font-bold text-emerald-700">
                                {{ (int) $attempt->score }}
                                <span class="text-3xl text-slate-400">/{{ (int) $attempt->max_score }}</span>
                            </div>
                            @if ($hasTheory)
                                <div class="mt-2 text-xs font-semibold text-slate-500">Theory answers are not auto-graded.</div>
                            @endif
                        </div>
                    @endif
                    <div class="rounded-2xl bg-sky-50 p-6 text-center shadow-sm">
                        <div class="text-xs font-semibold uppercase tracking-wider text-slate-600">Submitted At</div>
                        <div class="mt-3 text-xl font-bold text-slate-900">{{ $attempt->submitted_at?->format('g:i A') }}</div>
                        <div class="mt-1 text-sm text-slate-600">{{ $attempt->submitted_at?->format('M j, Y') }}</div>
                    </div>
                </div>

                <div class="mt-10">
                    <a href="{{ route('cbt.student', ['code' => $examCode]) }}" class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-8 py-4 text-base font-bold text-white shadow-md hover:bg-slate-800">
                        Back to Exams
                    </a>
                </div>
            </div>
        @else
            <!-- Question Map Toggle (Mobile Only) -->
            <button @click="$dispatch('toggle-question-map')" class="mb-4 flex w-full items-center justify-between rounded-xl bg-white p-3 shadow-md ring-1 ring-slate-100 lg:hidden">
                <span class="text-sm font-bold text-slate-900">Question Map</span>
                <svg class="h-5 w-5 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <div class="grid gap-5 lg:grid-cols-[280px,1fr]">
                <div class="hidden space-y-5 lg:block" x-data="{ open: false }" @toggle-question-map.window="open = !open" :class="{ '!block fixed inset-0 z-50 bg-black/50 p-4': open }" @click.self="open = false">
                    <div class="rounded-2xl bg-white p-4 shadow-md ring-1 ring-slate-100" :class="{ 'max-w-sm mx-auto': open }" @click.stop>
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-bold text-slate-900">Question Map</h3>
                            <span class="text-xs font-semibold text-slate-500">Tap to jump</span>
                        </div>
                        <div class="mt-4 grid grid-cols-5 gap-2">
                            @foreach ($questions as $idx => $q)
                                @php
                                    $questionType = strtolower((string) ($q->type ?? 'mcq'));
                                    $isAnswered = $questionType === 'theory'
                                        ? trim((string) ($this->theoryAnswers[$q->id] ?? '')) !== ''
                                        : (int) ($this->answers[$q->id] ?? 0) > 0;
                                    $isCurrent = (int) $idx === (int) $currentIndex;
                                @endphp
                                <button
                                    type="button"
                                    wire:click="goTo({{ $idx }})"
                                    class="h-11 w-11 rounded-xl text-xs font-bold transition-all {{ $isCurrent ? 'bg-amber-500 text-white shadow-md' : ($isAnswered ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600 hover:bg-slate-200') }}"
                                >
                                    {{ $idx + 1 }}
                                </button>
                            @endforeach
                        </div>

                        <div class="mt-6 space-y-2 border-t border-slate-100 pt-4 text-xs">
                            <div class="flex items-center gap-2">
                                <div class="h-3 w-3 rounded bg-amber-500"></div>
                                <span class="text-slate-700">Current</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="h-3 w-3 rounded bg-emerald-100"></div>
                                <span class="text-slate-700">Answered</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="h-3 w-3 rounded bg-slate-100"></div>
                                <span class="text-slate-700">Unanswered</span>
                            </div>
                        </div>
                        <button @click="$dispatch('toggle-question-map')" class="mt-4 w-full rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-bold text-white lg:hidden">
                            Close
                        </button>
                    </div>
                </div>

                <div>
                    @if (! $currentQuestion)
                        <div class="rounded-2xl bg-white p-8 shadow-md ring-1 ring-slate-100">
                            <p class="text-lg text-slate-500">No questions available.</p>
                        </div>
                    @else
                        @php
                            $selected = (int) ($this->answers[$currentQuestion->id] ?? 0);
                            $currentQuestionType = strtolower((string) ($currentQuestion->type ?? 'mcq'));
                        @endphp

                        <div class="rounded-2xl bg-white p-5 shadow-md ring-1 ring-slate-100">
                            <div class="flex flex-col gap-5">
                                <div>
                                    <div class="inline-flex items-center gap-2 rounded-full bg-amber-50 px-3 py-1.5 text-xs font-bold text-amber-700">
                                        Question {{ $currentIndex + 1 }} of {{ $totalQuestions }}
                                    </div>
                                    <h3 class="mt-3 text-lg font-bold leading-relaxed text-slate-900">
                                        {{ $currentQuestion->prompt }}
                                    </h3>
                                    <div class="mt-3 inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                        {{ (int) $currentQuestion->marks }} {{ (int) $currentQuestion->marks === 1 ? 'mark' : 'marks' }}
                                    </div>
                                </div>

                                @if ($currentQuestionType === 'theory')
                                    <div class="space-y-2">
                                        <textarea
                                            wire:model.debounce.600ms="theoryAnswers.{{ $currentQuestion->id }}"
                                            rows="6"
                                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 shadow-sm focus:border-amber-400 focus:ring-amber-300"
                                            placeholder="Type your answer here..."
                                        ></textarea>
                                        <div class="text-xs text-slate-500">Your response is saved automatically.</div>
                                    </div>
                                @else
                                    <div class="space-y-2.5">
                                        @foreach ($currentQuestion->options as $opt)
                                            @php($isSelected = $selected === (int) $opt->id)
                                            <label
                                                class="group flex cursor-pointer items-start gap-3 rounded-xl border border-slate-100 p-3.5 transition-all {{ $isSelected ? 'bg-amber-500 text-white shadow-md' : 'bg-slate-50 hover:bg-white hover:shadow-sm' }}"
                                                data-option-index="{{ $loop->index }}"
                                                data-option-id="{{ $opt->id }}"
                                                data-question-id="{{ $currentQuestion->id }}"
                                            >
                                                <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg text-xs font-bold {{ $isSelected ? 'bg-white text-amber-700' : 'bg-white text-slate-700 group-hover:text-amber-700' }}">
                                                    {{ chr(65 + $loop->index) }}
                                                </div>
                                                <input
                                                    type="radio"
                                                    name="q{{ $currentQuestion->id }}"
                                                    value="{{ $opt->id }}"
                                                    @checked($isSelected)
                                                wire:click="selectOption({{ $currentQuestion->id }}, {{ $opt->id }})"
                                                class="sr-only"
                                            />
                                                <span class="min-w-0 flex-1 text-sm leading-relaxed {{ $isSelected ? 'font-semibold text-white' : 'font-medium text-slate-800' }}">
                                                    {{ $opt->label }}
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                @endif

                                <div class="mt-6 flex flex-col gap-3 border-t border-slate-100 pt-4 sm:flex-row sm:items-center sm:justify-between">
                                    <div class="text-sm font-semibold text-slate-700">
                                        <span class="text-emerald-600">{{ $answered }}</span> answered
                                        <span class="mx-2 text-slate-300">|</span>
                                        <span class="text-slate-900">{{ max(0, $totalQuestions - $answered) }}</span> remaining
                                    </div>
                                    <div class="flex flex-wrap gap-3">
                                        <button
                                            type="button"
                                            wire:click="prev"
                                            @disabled($currentIndex === 0)
                                            class="rounded-lg bg-slate-100 px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-200 disabled:cursor-not-allowed disabled:opacity-40"
                                        >
                                            Previous
                                        </button>
                                        <button
                                            type="button"
                                            wire:click="next"
                                            @disabled($currentIndex >= $totalQuestions - 1)
                                            class="rounded-lg bg-amber-500 px-4 py-2.5 text-xs font-bold text-white hover:bg-amber-600 disabled:cursor-not-allowed disabled:opacity-40"
                                        >
                                            Next
                                        </button>
                                        <button
                                            type="button"
                                            @disabled(! $this->canSubmitNow())
                                            @click="$dispatch('open-submit-modal')"
                                            class="rounded-lg bg-emerald-600 px-5 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-50"
                                        >
                                            Submit Exam
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
    </div>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap');
        .cbt-take-root {
            font-family: 'Space Grotesk', ui-sans-serif, system-ui, -apple-system, 'Segoe UI', sans-serif;
        }
        .cbt-title {
            letter-spacing: -0.01em;
        }
        [x-cloak] { display: none !important; }
    </style>

    <!-- Submit Confirmation Modal -->
    <div x-data="{ open: false }" @open-submit-modal.window="open = true" x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="open = false">
        <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl" @click.stop>
            <div class="mb-4 flex items-center gap-3">
                <div class="grid h-12 w-12 place-items-center rounded-full bg-amber-100">
                    <svg class="h-6 w-6 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900">Submit Exam?</h3>
            </div>
            <p class="text-sm text-slate-600">You cannot change answers after submitting. Are you sure you want to submit now?</p>
            <div class="mt-6 flex gap-3">
                <button @click="open = false" class="flex-1 rounded-lg bg-slate-100 px-4 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-200">
                    Cancel
                </button>
                <button @click="open = false; $wire.submitExam()" class="flex-1 rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-emerald-700">
                    Submit
                </button>
            </div>
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
</div>
