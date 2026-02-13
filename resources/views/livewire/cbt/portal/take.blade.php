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

<div class="space-y-6" @if(! $submitted) wire:poll.10s="tick" @endif>
    <div class="card-padded">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
            <div class="min-w-0">
                <div class="text-sm font-semibold text-gray-900">{{ $exam->title }}</div>
                <div class="mt-1 text-xs text-gray-600">
                    {{ $exam->schoolClass?->name ?? '-' }}
                    &middot;
                    {{ $exam->subject?->name ?? '-' }}
                    &middot;
                    Duration: {{ (int) $exam->duration_minutes }} mins
                </div>
                <div class="mt-2 text-xs text-gray-600">
                    Student: <span class="font-semibold text-gray-900">{{ $student->full_name ?? ($student->first_name.' '.$student->last_name) }}</span>
                    <span class="font-mono text-gray-500">({{ $student->admission_number }})</span>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <x-status-badge variant="{{ $submitted ? 'success' : 'info' }}">{{ $submitted ? 'Submitted' : 'In Progress' }}</x-status-badge>
                <x-status-badge variant="neutral">{{ $answered }}/{{ $totalQuestions }} answered</x-status-badge>
                @if (! $submitted)
                    <x-status-badge variant="{{ $remaining <= 60 ? 'warning' : 'neutral' }}">
                        Time: {{ $mm }}:{{ $ss }}
                    </x-status-badge>
                    @if ($unlockIn > 0)
                        <x-status-badge variant="warning">
                            Submit in: {{ $unlockMm }}:{{ $unlockSs }}
                        </x-status-badge>
                    @endif
                    @if ($this->lastSavedAt)
                        <x-status-badge variant="neutral">Saved: {{ $this->lastSavedAt }}</x-status-badge>
                    @endif
                @endif
            </div>
        </div>

        <div class="mt-4 flex items-center gap-3">
            @if ($student->passport_photo_url)
                <img
                    src="{{ $student->passport_photo_url }}"
                    alt="{{ $student->full_name ?? 'Student' }}"
                    class="h-12 w-12 rounded-2xl object-cover ring-1 ring-inset ring-slate-200"
                />
            @else
                <div class="grid h-12 w-12 place-items-center rounded-2xl bg-slate-100 text-slate-700 ring-1 ring-inset ring-slate-200">
                    <span class="text-sm font-black">{{ mb_substr($student->first_name ?? 'S', 0, 1) }}</span>
                </div>
            @endif

            <div class="text-xs text-slate-600">
                @if (! $submitted && $unlockIn > 0)
                    Submission is locked until half of the exam time is used.
                @else
                    &nbsp;
                @endif
            </div>
        </div>
    </div>

    @if ($submitted)
        <div class="card-padded">
            <div class="text-sm font-semibold text-gray-900">Result</div>
            <div class="mt-2 grid grid-cols-1 gap-3 sm:grid-cols-3">
                <div class="rounded-2xl bg-gray-50 p-4 ring-1 ring-inset ring-gray-100">
                    <div class="text-xs font-semibold uppercase tracking-wider text-gray-500">Score</div>
                    <div class="mt-1 text-lg font-black text-gray-900">{{ (int) $attempt->score }} / {{ (int) $attempt->max_score }}</div>
                </div>
                <div class="rounded-2xl bg-gray-50 p-4 ring-1 ring-inset ring-gray-100">
                    <div class="text-xs font-semibold uppercase tracking-wider text-gray-500">Percent</div>
                    <div class="mt-1 text-lg font-black text-gray-900">{{ number_format((float) $attempt->percent, 2) }}%</div>
                </div>
                <div class="rounded-2xl bg-gray-50 p-4 ring-1 ring-inset ring-gray-100">
                    <div class="text-xs font-semibold uppercase tracking-wider text-gray-500">Submitted</div>
                    <div class="mt-1 text-sm font-semibold text-gray-900">{{ $attempt->submitted_at?->format('M j, Y g:i A') }}</div>
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('cbt.student') }}" class="btn-outline">Back</a>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 gap-4 lg:grid-cols-4">
            <div class="card-padded lg:col-span-1">
                <div class="text-sm font-semibold text-gray-900">Questions</div>
                <div class="mt-1 text-xs text-gray-600">Tap a number to jump.</div>

                <div class="mt-4 grid grid-cols-6 gap-2 sm:grid-cols-10 lg:grid-cols-6">
                    @foreach ($questions as $idx => $q)
                        @php
                            $isAnswered = (int) ($this->answers[$q->id] ?? 0) > 0;
                            $isCurrent = (int) $idx === (int) $currentIndex;
                            $cls = $isCurrent
                                ? 'bg-slate-900 text-white'
                                : ($isAnswered ? 'bg-emerald-50 text-emerald-800 ring-1 ring-inset ring-emerald-200' : 'bg-gray-50 text-gray-700 ring-1 ring-inset ring-gray-200');
                        @endphp
                        <button type="button" wire:click="goTo({{ $idx }})" class="h-10 w-10 rounded-xl text-xs font-black {{ $cls }}">
                            {{ $idx + 1 }}
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="space-y-4 lg:col-span-3">
                @if (! $currentQuestion)
                    <div class="card-padded text-sm text-gray-600">No questions in this exam yet.</div>
                @else
                    @php($selected = (int) ($this->answers[$currentQuestion->id] ?? 0))
                    <div class="card-padded">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                            <div class="min-w-0">
                                <div class="text-sm font-semibold text-gray-900">
                                    Q{{ $currentIndex + 1 }}. {{ $currentQuestion->prompt }}
                                </div>
                                <div class="mt-1 text-xs text-gray-600">Marks: {{ (int) $currentQuestion->marks }}</div>
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" wire:click="prev" @disabled($currentIndex === 0) class="btn-outline disabled:opacity-50 disabled:cursor-not-allowed">Prev</button>
                                <button type="button" wire:click="next" @disabled($currentIndex >= $totalQuestions - 1) class="btn-outline disabled:opacity-50 disabled:cursor-not-allowed">Next</button>
                            </div>
                        </div>

                        <div class="mt-4 grid grid-cols-1 gap-2 sm:grid-cols-2">
                            @foreach ($currentQuestion->options as $opt)
                                <label class="flex cursor-pointer items-start gap-3 rounded-2xl bg-gray-50 px-4 py-3 text-sm text-gray-800 ring-1 ring-inset ring-gray-100 hover:bg-gray-100">
                                    <input
                                        type="radio"
                                        name="q{{ $currentQuestion->id }}"
                                        value="{{ $opt->id }}"
                                        @checked($selected === (int) $opt->id)
                                        wire:click="selectOption({{ $currentQuestion->id }}, {{ $opt->id }})"
                                        class="mt-0.5"
                                    />
                                    <span class="min-w-0">
                                        <span class="font-semibold">{{ chr(65 + $loop->index) }}.</span>
                                        {{ $opt->label }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="card-padded">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div class="text-sm text-gray-600">
                                Answered: <span class="font-semibold text-gray-900">{{ $answered }}</span> / {{ $totalQuestions }}
                                &middot;
                                Remaining: <span class="font-semibold text-gray-900">{{ max(0, $totalQuestions - $answered) }}</span>
                            </div>
                            <button
                                type="button"
                                wire:click="submitExam"
                                @disabled(! $this->canSubmitNow())
                                onclick="if (!confirm('Submit this exam now? You may not be able to change answers after submitting.')) { event.stopImmediatePropagation(); }"
                                class="btn-primary disabled:opacity-60 disabled:cursor-not-allowed"
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
