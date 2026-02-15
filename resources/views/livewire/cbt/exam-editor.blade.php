@php
    $status = (string) ($exam->status ?? 'draft');
    $variant = match ($status) {
        'approved' => 'success',
        'submitted' => 'info',
        'assigned' => 'info',
        'rejected' => 'warning',
        default => 'neutral',
    };
    $canEdit = (bool) $this->canEdit;
    $tab = $this->tab ?? 'details';
    $hasTheory = $exam->questions->contains('type', 'theory');
@endphp

<div class="space-y-6" x-data="{ tab: '{{ $tab }}' }">
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-violet-600 to-purple-600 p-6 shadow-xl">
        <div class="relative">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-white">{{ $exam->title }}</h1>
                    <div class="mt-2 flex items-center gap-2">
                        <x-status-badge variant="{{ $variant }}">{{ ucfirst($status) }}</x-status-badge>
                        @if ($status === 'approved' && $exam->access_code)
                            <span class="rounded-lg bg-white/20 px-3 py-1 text-xs font-black text-white backdrop-blur-sm">
                                Code: {{ $exam->access_code }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('cbt.index') }}" class="rounded-lg bg-white/20 px-4 py-2 text-sm font-semibold text-white backdrop-blur-sm hover:bg-white/30">Back</a>
                    
                    @if ($canEdit)
                        <button wire:click="saveDetails" class="rounded-lg bg-white px-4 py-2 text-sm font-semibold text-violet-600 hover:bg-pink-50">Save</button>
                    @endif
                    
                    @if ($me?->role === 'teacher' && $canEdit)
                        <button wire:click="submitToAdmin" class="rounded-lg bg-emerald-500 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-600">Submit</button>
                    @endif
                    
                    @if ($me?->role === 'admin' && $status === 'submitted')
                        <button wire:click="approve" class="rounded-lg bg-emerald-500 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-600">Approve</button>
                        <button wire:click="startReject" class="rounded-lg bg-orange-500 px-4 py-2 text-sm font-semibold text-white hover:bg-orange-600">Reject</button>
                    @endif
                    
                    @if ($me?->role === 'admin' && $status === 'approved')
                        <button wire:click="togglePublish" class="rounded-lg bg-white px-4 py-2 text-sm font-semibold text-violet-600 hover:bg-pink-50">{{ $exam->published_at ? 'Pause' : 'Go Live' }}</button>
                    @endif
                </div>
            </div>
            
            <div class="mt-6 flex gap-2 border-t border-white/20 pt-4">
                <button @click="tab = 'details'" :class="tab === 'details' ? 'bg-white text-violet-600' : 'bg-white/10 text-white hover:bg-white/20'" class="rounded-lg px-4 py-2 text-sm font-semibold transition">Details</button>
                <button @click="tab = 'questions'" :class="tab === 'questions' ? 'bg-white text-violet-600' : 'bg-white/10 text-white hover:bg-white/20'" class="rounded-lg px-4 py-2 text-sm font-semibold transition">Questions</button>
                @if ($status === 'approved')
                    <button @click="tab = 'monitor'" :class="tab === 'monitor' ? 'bg-white text-violet-600' : 'bg-white/10 text-white hover:bg-white/20'" class="rounded-lg px-4 py-2 text-sm font-semibold transition">Monitor</button>
                @endif
                <button @click="tab = 'actions'" :class="tab === 'actions' ? 'bg-white text-violet-600' : 'bg-white/10 text-white hover:bg-white/20'" class="rounded-lg px-4 py-2 text-sm font-semibold transition">Actions</button>
            </div>
        </div>
    </div>

    @if ($status === 'rejected' && $exam->note)
        <div class="rounded-lg border border-orange-200 bg-orange-50 p-4 text-sm text-orange-900">
            <div class="font-semibold">Admin note:</div>
            <div class="mt-1">{{ $exam->note }}</div>
        </div>
    @endif

    @if ($status === 'assigned' && $exam->request_note)
        <div class="rounded-lg border border-indigo-200 bg-indigo-50 p-4 text-sm text-indigo-900">
            <div class="font-semibold">Admin request:</div>
            <div class="mt-1">{{ $exam->request_note }}</div>
        </div>
    @endif

    <div x-show="tab === 'details'" class="rounded-2xl bg-white p-6 shadow-lg">
        @if ($canEdit)
            <div class="grid gap-4 lg:grid-cols-2">
                <div class="lg:col-span-2">
                    <label class="text-xs font-semibold uppercase text-gray-500">Title</label>
                    <input wire:model="title" class="mt-1 input w-full" />
                    @error('title') <div class="mt-1 text-xs text-orange-700">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase text-gray-500">Class</label>
                    <select wire:model.live="classId" class="mt-1 select w-full">
                        <option value="">Select</option>
                        @foreach ($this->classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                    @error('classId') <div class="mt-1 text-xs text-orange-700">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase text-gray-500">Subject</label>
                    <select wire:model.live="subjectId" @disabled(! $classId) class="mt-1 select w-full">
                        <option value="">Select</option>
                        @foreach ($this->subjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                        @endforeach
                    </select>
                    @error('subjectId') <div class="mt-1 text-xs text-orange-700">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase text-gray-500">Duration (min)</label>
                    <input wire:model="durationMinutes" type="number" min="1" class="mt-1 input w-full" />
                    @error('durationMinutes') <div class="mt-1 text-xs text-orange-700">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase text-gray-500">Term</label>
                    <select wire:model.live="term" class="mt-1 select w-full">
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                    </select>
                </div>
                <div class="lg:col-span-2">
                    <label class="text-xs font-semibold uppercase text-gray-500">Session</label>
                    <input wire:model="session" class="mt-1 input w-full" placeholder="2025/2026" />
                    @error('session') <div class="mt-1 text-xs text-orange-700">{{ $message }}</div> @enderror
                </div>
            </div>

            @if ($me?->role === 'admin')
                <div class="mt-4 grid gap-4 lg:grid-cols-2">
                    <div>
                        <label class="text-xs font-semibold uppercase text-gray-500">Start Time</label>
                        <input wire:model="startsAt" type="datetime-local" class="mt-1 input w-full" />
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase text-gray-500">End Time</label>
                        <input wire:model="endsAt" type="datetime-local" class="mt-1 input w-full" />
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase text-gray-500">PIN</label>
                        <input wire:model="pin" class="mt-1 input w-full font-mono" placeholder="1234" />
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase text-gray-500">Grace Minutes</label>
                        <input wire:model="graceMinutes" type="number" min="0" class="mt-1 input w-full" />
                    </div>
                    <div class="lg:col-span-2">
                        <label class="text-xs font-semibold uppercase text-gray-500">Allowed CIDRs</label>
                        <textarea wire:model="allowedCidrs" rows="2" class="mt-1 w-full rounded-lg border px-3 py-2 text-sm" placeholder="192.168.99.0/24"></textarea>
                    </div>
                    <div class="lg:col-span-2">
                        <label class="flex items-center gap-2">
                            <input type="checkbox" wire:model="showScore" class="rounded" />
                            <span class="text-xs font-semibold uppercase text-gray-500">Show Score After Submit</span>
                        </label>
                    </div>
                </div>
            @endif

            <div class="mt-4">
                <label class="text-xs font-semibold uppercase text-gray-500">Description</label>
                <textarea wire:model="description" rows="2" class="mt-1 w-full rounded-lg border px-3 py-2 text-sm"></textarea>
            </div>
        @else
            <div class="grid gap-3 text-sm">
                <div><span class="font-semibold">Class:</span> {{ $exam->schoolClass?->name ?? '-' }}</div>
                <div><span class="font-semibold">Subject:</span> {{ $exam->subject?->name ?? '-' }}</div>
                <div><span class="font-semibold">Duration:</span> {{ (int) $exam->duration_minutes }} min</div>
                <div><span class="font-semibold">Term:</span> {{ $exam->term }} | <span class="font-semibold">Session:</span> {{ $exam->session }}</div>
            </div>
        @endif
    </div>

    @if ($me?->role === 'admin' && $showRejectForm)
        <div class="rounded-lg border border-orange-200 bg-orange-50 p-4">
            <div class="flex items-center justify-between">
                <div class="text-sm font-semibold">Reject Exam</div>
                <button wire:click="cancelReject" class="btn-outline">Cancel</button>
            </div>
            <textarea wire:model="reviewNote" rows="2" class="mt-3 w-full rounded-lg border px-3 py-2 text-sm"></textarea>
            <button wire:click="confirmReject" class="mt-2 btn-primary">Confirm</button>
        </div>
    @endif

    <div x-show="tab === 'questions'" class="rounded-2xl bg-white p-6 shadow-lg">
        @if ($canEdit)
            <div class="rounded-lg border-2 border-dashed border-violet-200 bg-violet-50 p-4">
                <div class="text-sm font-semibold text-gray-900">{{ $editingQuestionId ? 'Edit' : 'Add' }} Question</div>
                <textarea wire:model="questionPrompt" rows="2" class="mt-2 w-full rounded-lg border px-3 py-2 text-sm" placeholder="Question..."></textarea>
                @error('questionPrompt') <div class="mt-1 text-xs text-orange-700">{{ $message }}</div> @enderror
                
                <div class="mt-3 grid gap-3 lg:grid-cols-[200px,1fr]">
                    <div>
                        <label class="text-xs font-semibold uppercase text-gray-500">Type</label>
                        <select wire:model="questionType" class="mt-1 select w-full">
                            <option value="mcq">MCQ</option>
                            <option value="theory">Theory</option>
                        </select>
                    </div>

                    @if ($questionType === 'mcq')
                        <div class="grid gap-2 lg:grid-cols-4">
                            @for ($i = 0; $i < 4; $i++)
                                <div>
                                    <div class="flex items-center gap-2">
                                        <input type="radio" wire:model="correctIndex" value="{{ $i }}" id="opt{{ $i }}" />
                                        <label for="opt{{ $i }}" class="text-xs font-semibold text-gray-500">{{ chr(65 + $i) }}</label>
                                    </div>
                                    <input wire:model="optionLabels.{{ $i }}" class="mt-1 input w-full text-sm" placeholder="Option {{ chr(65 + $i) }}" />
                                </div>
                            @endfor
                        </div>
                    @else
                        <div class="rounded-lg border border-dashed border-violet-200 bg-white px-3 py-2 text-xs text-gray-500">
                            Theory questions accept written answers from students and are not auto-graded.
                        </div>
                    @endif
                </div>
                
                <div class="mt-3 flex items-center gap-2">
                    <input wire:model="questionMarks" type="number" min="1" class="input w-20 text-sm" placeholder="Marks" />
                    <button wire:click="saveQuestion" class="btn-primary">{{ $editingQuestionId ? 'Update' : 'Add' }}</button>
                    @if ($editingQuestionId)
                        <button wire:click="startNewQuestion" class="btn-outline">Cancel</button>
                    @endif
                </div>
            </div>
        @endif

        <div class="mt-4 space-y-2">
            @forelse ($exam->questions as $q)
                <div class="rounded-lg border bg-gray-50 p-3">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="text-sm font-semibold text-gray-900">Q{{ $loop->iteration }}. {{ $q->prompt }}</div>
                            <div class="mt-2">
                                @if ($q->type === 'theory')
                                    <span class="rounded bg-slate-200 px-2 py-1 text-xs font-semibold text-slate-700">Theory</span>
                                @else
                                    <div class="grid gap-1 text-xs lg:grid-cols-4">
                                        @foreach ($q->options as $opt)
                                            <div class="rounded bg-white px-2 py-1 {{ $opt->is_correct ? 'ring-2 ring-emerald-500' : '' }}">
                                                <span class="font-semibold">{{ chr(65 + $loop->index) }}.</span> {{ $opt->label }}
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            <div class="mt-1 text-xs text-gray-500">Marks: {{ (int) $q->marks }}</div>
                        </div>
                        @if ($canEdit)
                            <div class="flex gap-1">
                                <button wire:click="editQuestion({{ $q->id }})" class="rounded bg-gray-200 px-2 py-1 text-xs font-semibold hover:bg-gray-300">Edit</button>
                                <button wire:click="deleteQuestion({{ $q->id }})" class="rounded bg-rose-600 px-2 py-1 text-xs font-semibold text-white hover:bg-rose-700">Del</button>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="rounded-lg border-2 border-dashed bg-gray-50 p-6 text-center text-sm text-gray-600">No questions yet</div>
            @endforelse
        </div>
    </div>

    <div x-show="tab === 'monitor'" class="space-y-4">
        @if ($status === 'approved')
            @php
                $roster = collect();
                $submittedCount = 0;
                $inProgressCount = 0;
                $notStartedCount = 0;
                $terminatedCount = 0;

                if ($me?->role === 'admin') {
                    $roster = $this->roster;
                    $submittedCount = (int) $roster->where('state', 'submitted')->count();
                    $inProgressCount = (int) $roster->where('state', 'in_progress')->count();
                    $notStartedCount = (int) $roster->where('state', 'not_started')->count();
                    $terminatedCount = (int) $roster->where('state', 'terminated')->count();
                }
            @endphp
            
            <div class="grid gap-4 lg:grid-cols-4">
                <div class="rounded-lg bg-emerald-50 p-4 ring-1 ring-emerald-200">
                    <div class="text-2xl font-bold text-emerald-900">{{ $submittedCount }}</div>
                    <div class="text-xs font-semibold text-emerald-700">Submitted</div>
                </div>
                <div class="rounded-lg bg-blue-50 p-4 ring-1 ring-blue-200">
                    <div class="text-2xl font-bold text-blue-900">{{ $inProgressCount }}</div>
                    <div class="text-xs font-semibold text-blue-700">In Progress</div>
                </div>
                <div class="rounded-lg bg-orange-50 p-4 ring-1 ring-orange-200">
                    <div class="text-2xl font-bold text-orange-900">{{ $terminatedCount }}</div>
                    <div class="text-xs font-semibold text-orange-700">Terminated</div>
                </div>
                <div class="rounded-lg bg-gray-50 p-4 ring-1 ring-gray-200">
                    <div class="text-2xl font-bold text-gray-900">{{ $notStartedCount }}</div>
                    <div class="text-xs font-semibold text-gray-700">Not Started</div>
                </div>
            </div>

            <div class="rounded-2xl bg-white p-6 shadow-lg">
                @if ($me?->role === 'admin')
                    <div class="space-y-2">
                        @forelse ($roster as $row)
                            @php
                                $student = $row['student'];
                                $attempt = $row['attempt'];
                                $state = (string) $row['state'];
                                $answered = (int) ($row['answered'] ?? 0);
                                $totalQuestions = (int) ($exam?->questions?->count() ?? 0);
                            @endphp
                            <div class="flex items-center justify-between rounded-lg border bg-gray-50 p-3">
                                <div class="flex items-center gap-3">
                                    @if ($student->passport_photo_url)
                                        <img src="{{ $student->passport_photo_url }}" class="h-10 w-10 rounded-lg object-cover" />
                                    @else
                                        <div class="grid h-10 w-10 place-items-center rounded-lg bg-gray-200 text-sm font-bold">{{ mb_substr($student->first_name ?? 'S', 0, 1) }}</div>
                                    @endif
                                    <div>
                                        <div class="text-sm font-semibold">{{ $student->full_name ?? trim($student->first_name.' '.$student->last_name) }}</div>
                                        <div class="text-xs text-gray-500">{{ $student->admission_number }}</div>
                                    </div>
                                </div>
                            <div class="flex items-center gap-3">
                                @if ($state === 'submitted')
                                    <span class="rounded bg-emerald-100 px-2 py-1 text-xs font-semibold text-emerald-800">Submitted</span>
                                @elseif ($state === 'in_progress')
                                    <span class="rounded bg-blue-100 px-2 py-1 text-xs font-semibold text-blue-800">{{ $answered }}/{{ $totalQuestions }}</span>
                                @elseif ($state === 'terminated')
                                    <span class="rounded bg-orange-100 px-2 py-1 text-xs font-semibold text-orange-800">Terminated</span>
                                @else
                                    <span class="rounded bg-gray-100 px-2 py-1 text-xs font-semibold text-gray-800">Not Started</span>
                                @endif

                                    @if ($hasTheory && $attempt && ($attempt->submitted_at || $attempt->terminated_at))
                                        <button wire:click="startReview({{ $attempt->id }})" class="rounded bg-violet-100 px-2 py-1 text-xs font-semibold text-violet-700 hover:bg-violet-200">
                                            Review Theory
                                        </button>
                                    @endif
                                    
                                    @if ($attempt)
                                        <div class="relative" x-data="{ open: false }">
                                            <button @click="open = !open" class="rounded bg-gray-200 px-3 py-1 text-xs font-semibold hover:bg-gray-300">•••</button>
                                            <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 z-10 mt-1 w-40 rounded-lg bg-white shadow-xl ring-1 ring-black/5">
                                                <button wire:click="startIpOverride({{ $attempt->id }})" @click="open = false" class="block w-full px-3 py-2 text-left text-xs font-semibold hover:bg-gray-50">Change IP</button>
                                                @if ($state === 'in_progress')
                                                    <button wire:click="forceSubmitAttempt({{ $attempt->id }})" @click="open = false" class="block w-full px-3 py-2 text-left text-xs font-semibold text-blue-700 hover:bg-blue-50">Force Submit</button>
                                                    <button wire:click="terminateAttempt({{ $attempt->id }})" @click="open = false" class="block w-full px-3 py-2 text-left text-xs font-semibold text-orange-700 hover:bg-orange-50">Terminate</button>
                                                @endif
                                                <button wire:click="resetAttempt({{ $attempt->id }})" @click="open = false" class="block w-full px-3 py-2 text-left text-xs font-semibold text-rose-700 hover:bg-rose-50">Reset</button>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="rounded-lg border-2 border-dashed bg-gray-50 p-6 text-center text-sm text-gray-600">No students</div>
                        @endforelse
                    </div>

                    @if ($editingAttemptIpId)
                        <div class="mt-4 rounded-lg border border-violet-200 bg-violet-50 p-4">
                            <div class="text-sm font-semibold">Allow IP Override</div>
                            <div class="mt-2 flex gap-2">
                                <input wire:model="allowedIp" class="input flex-1 font-mono text-sm" placeholder="192.168.1.50" />
                                <button wire:click="saveIpOverride" class="btn-primary">Save</button>
                                <button wire:click="cancelIpOverride" class="btn-outline">Cancel</button>
                            </div>
                        </div>
                    @endif
                @else
                    <div class="space-y-2">
                        @forelse ($exam->attempts as $a)
                            <div class="flex items-center justify-between rounded-lg border bg-gray-50 p-3">
                                <div>
                                    <div class="text-sm font-semibold">{{ $a->student?->full_name ?? 'Student' }}</div>
                                    <div class="text-xs text-gray-500">{{ $a->student?->admission_number }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-semibold">{{ (int) $a->score }}/{{ (int) $a->max_score }}</div>
                                    <div class="text-xs text-gray-500">{{ number_format((float) $a->percent, 1) }}%</div>
                                    @if ($hasTheory && ($a->submitted_at || $a->terminated_at))
                                        <button wire:click="startReview({{ $a->id }})" class="mt-2 rounded bg-violet-100 px-2 py-1 text-xs font-semibold text-violet-700 hover:bg-violet-200">
                                            Review Theory
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="rounded-lg border-2 border-dashed bg-gray-50 p-6 text-center text-sm text-gray-600">No attempts</div>
                        @endforelse
                    </div>
                @endif
            </div>
        @endif
    </div>

    @if ($hasTheory && $this->reviewAttempt)
        @php
            $reviewAttempt = $this->reviewAttempt;
            $theoryQuestions = $exam->questions->where('type', 'theory');
        @endphp
        <div class="rounded-2xl bg-white p-6 shadow-lg">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <div class="text-sm font-semibold text-gray-900">Theory Review</div>
                    <div class="text-xs text-gray-500">
                        {{ $reviewAttempt->student?->full_name ?? 'Student' }} • {{ $reviewAttempt->student?->admission_number }}
                    </div>
                </div>
                <button wire:click="cancelReview" class="btn-outline">Close</button>
            </div>

            <div class="mt-4 space-y-4">
                @foreach ($theoryQuestions as $question)
                    @php
                        $answer = $reviewAttempt->answers->firstWhere('question_id', $question->id);
                        $response = trim((string) ($answer?->text_answer ?? ''));
                    @endphp
                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                        <div class="text-sm font-semibold text-gray-900">{{ $question->prompt }}</div>
                        <div class="mt-2 rounded-lg bg-white p-3 text-sm text-gray-700">
                            {{ $response !== '' ? $response : 'No answer submitted.' }}
                        </div>
                        <div class="mt-3 flex items-center gap-3">
                            <label class="text-xs font-semibold uppercase text-gray-500">Marks ({{ (int) $question->marks }})</label>
                            <input
                                type="number"
                                min="0"
                                max="{{ (int) $question->marks }}"
                                wire:model.defer="theoryMarks.{{ $question->id }}"
                                class="input w-24 text-sm"
                            />
                            @error("theoryMarks.{$question->id}") <div class="text-xs text-rose-600">{{ $message }}</div> @enderror
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-4 flex flex-wrap items-center justify-end gap-2">
                <button wire:click="saveTheoryMarks" class="btn-primary">Save Marks</button>
            </div>
        </div>
    @endif

    <div x-show="tab === 'actions'" class="rounded-2xl bg-white p-6 shadow-lg">
        <div class="space-y-2">
            <a href="{{ route('cbt.exams.pdf', $exam) }}" target="_blank" class="block rounded-lg border bg-gray-50 p-3 text-sm font-semibold hover:bg-gray-100">📄 Download PDF</a>
            @if ($status === 'approved' && $exam->access_code)
                <a href="{{ route('cbt.student', ['code' => $exam->access_code]) }}" target="_blank" class="block rounded-lg border bg-gray-50 p-3 text-sm font-semibold hover:bg-gray-100">🎓 Student Portal</a>
            @endif
            @if ($me?->role === 'admin')
                <a href="{{ route('cbt.exams.export', $exam) }}" class="block rounded-lg border bg-gray-50 p-3 text-sm font-semibold hover:bg-gray-100">📊 Export CSV</a>
                @if ($status === 'approved')
                    <button wire:click="transferToResults" onclick="return confirm('Transfer CBT scores to academic results?')" class="block w-full rounded-lg border bg-emerald-50 p-3 text-left text-sm font-semibold text-emerald-700 hover:bg-emerald-100">✅ Transfer to Results</button>
                    <button wire:click="endAllExams" onclick="return confirm('End this exam?')" class="block w-full rounded-lg border bg-orange-50 p-3 text-left text-sm font-semibold text-orange-700 hover:bg-orange-100">⏹️ End This Exam</button>
                @endif
                <button wire:click="deleteExam" onclick="return confirm('Delete this exam?')" class="block w-full rounded-lg border bg-red-50 p-3 text-left text-sm font-semibold text-red-700 hover:bg-red-100">🗑️ Delete Exam</button>
            @endif
        </div>
    </div>

</div>
