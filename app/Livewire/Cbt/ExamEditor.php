<?php

namespace App\Livewire\Cbt;

use App\Models\CbtExam;
use App\Models\CbtAnswer;
use App\Models\CbtOption;
use App\Models\CbtQuestion;
use App\Models\CbtAttempt;
use App\Models\InAppNotification;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
use App\Models\SubjectAllocation;
use App\Models\User;
use App\Support\Audit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('CBT Exam')]
class ExamEditor extends Component
{
    public int $examId;

    public string $title = '';
    public string $description = '';
    public ?int $classId = null;
    public ?int $subjectId = null;
    public int $durationMinutes = 30;
    public int $term = 1;
    public string $session = '';

    public ?int $editingQuestionId = null;
    public string $questionPrompt = '';
    public int $questionMarks = 1;

    /** @var array<int,string> */
    public array $optionLabels = ['', '', '', ''];

    public int $correctIndex = 0;

    public bool $showRejectForm = false;
    public string $reviewNote = '';

    public ?int $editingAttemptIpId = null;
    public string $allowedIp = '';

    public string $startsAt = '';
    public string $endsAt = '';
    public string $pin = '';
    public int $graceMinutes = 0;
    public string $allowedCidrs = '';

    public function mount(CbtExam $exam): void
    {
        $user = auth()->user();
        abort_unless($user && in_array($user->role, ['admin', 'teacher'], true), 403);

        if ($user->role === 'teacher') {
            $canAccess = (int) $exam->created_by === (int) $user->id
                || (int) ($exam->assigned_teacher_id ?? 0) === (int) $user->id;
            abort_unless($canAccess, 403);
        }

        $this->examId = (int) $exam->id;

        $this->fillFromExam($exam);
    }

    private function fillFromExam(CbtExam $exam): void
    {
        $this->title = (string) $exam->title;
        $this->description = (string) ($exam->description ?? '');
        $this->classId = (int) $exam->class_id;
        $this->subjectId = (int) $exam->subject_id;
        $this->durationMinutes = (int) ($exam->duration_minutes ?? 30);
        $this->term = (int) ($exam->term ?? 1);
        $this->session = (string) ($exam->session ?? '');

        $this->startsAt = $exam->starts_at ? $exam->starts_at->format('Y-m-d\TH:i') : '';
        $this->endsAt = $exam->ends_at ? $exam->ends_at->format('Y-m-d\TH:i') : '';

        $this->pin = (string) ($exam->pin ?? '');
        $this->graceMinutes = (int) ($exam->grace_minutes ?? 0);
        $this->allowedCidrs = (string) ($exam->allowed_cidrs ?? '');
    }

    #[Computed]
    public function exam(): CbtExam
    {
        return CbtExam::query()
            ->with([
                'schoolClass:id,name',
                'subject:id,name',
                'creator:id,name',
                'assignedTeacher:id,name',
                'requester:id,name',
                'questions.options',
                'attempts' => fn ($q) => $q
                    ->with(['student:id,admission_number,first_name,last_name'])
                    ->orderByDesc('submitted_at')
                    ->orderByDesc('id'),
            ])
            ->findOrFail($this->examId);
    }

    #[Computed]
    public function canEdit(): bool
    {
        $user = auth()->user();
        if (! $user) {
            return false;
        }

        $exam = $this->exam;

        // Prevent editing if exam has any attempts (students have started)
        if ($exam->attempts()->exists()) {
            return false;
        }

        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role !== 'teacher') {
            return false;
        }

        $isAssignee = (int) $exam->created_by === (int) $user->id
            || (int) ($exam->assigned_teacher_id ?? 0) === (int) $user->id;

        return $isAssignee && in_array($exam->status, ['draft', 'assigned', 'rejected'], true);
    }

    #[Computed]
    public function classes()
    {
        $user = auth()->user();
        abort_unless($user, 403);

        if ($user->role === 'admin') {
            return SchoolClass::query()->orderBy('level')->get();
        }

        return SchoolClass::query()
            ->whereIn('id', SubjectAllocation::query()->where('teacher_id', $user->id)->pluck('class_id'))
            ->orderBy('level')
            ->get();
    }

    #[Computed]
    public function subjects()
    {
        if (! $this->classId) {
            return collect();
        }

        $user = auth()->user();
        abort_unless($user, 403);

        if ($user->role === 'admin') {
            $ids = SubjectAllocation::query()->where('class_id', $this->classId)->pluck('subject_id')->unique();

            return $ids->isNotEmpty()
                ? Subject::query()->whereIn('id', $ids)->orderBy('name')->get()
                : Subject::query()->orderBy('name')->get();
        }

        $ids = SubjectAllocation::query()
            ->where('class_id', $this->classId)
            ->where('teacher_id', $user->id)
            ->pluck('subject_id')
            ->unique();

        return Subject::query()->whereIn('id', $ids)->orderBy('name')->get();
    }

    public function updatedClassId(): void
    {
        if (! $this->canEdit) {
            return;
        }

        $this->subjectId = null;
        $this->resetValidation();
    }

    public function saveDetails(): void
    {
        $user = auth()->user();
        abort_unless($user, 403);

        if ($user->role === 'teacher') {
            abort_unless($this->canEdit, 403);
        }

        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'classId' => ['required', 'integer', 'exists:classes,id'],
            'subjectId' => ['required', 'integer', 'exists:subjects,id'],
            'session' => ['nullable', 'string', 'max:9'],
            'term' => ['required', 'integer', 'min:1', 'max:3'],
            'durationMinutes' => ['required', 'integer', 'min:1', 'max:300'],
        ];

        if ($user->role === 'admin') {
            $rules['startsAt'] = ['nullable', 'string', 'max:20'];
            $rules['endsAt'] = ['nullable', 'string', 'max:20'];
            $rules['pin'] = ['nullable', 'string', 'max:20'];
            $rules['graceMinutes'] = ['required', 'integer', 'min:0', 'max:120'];
            $rules['allowedCidrs'] = ['nullable', 'string', 'max:2000'];
        }

        $data = $this->validate($rules);

        if ($user->role === 'teacher') {
            $allocated = SubjectAllocation::query()
                ->where('teacher_id', $user->id)
                ->where('class_id', $data['classId'])
                ->where('subject_id', $data['subjectId'])
                ->exists();

            if (! $allocated) {
                $this->addError('subjectId', 'You are not allocated to this subject for this class.');
                return;
            }
        }

        $exam = $this->exam;

        $attrs = [
            'title' => trim($data['title']),
            'description' => trim((string) ($data['description'] ?? '')) !== '' ? trim((string) $data['description']) : null,
            'class_id' => (int) $data['classId'],
            'subject_id' => (int) $data['subjectId'],
            'term' => (int) $data['term'],
            'session' => trim((string) ($data['session'] ?? '')) !== '' ? trim((string) $data['session']) : null,
            'duration_minutes' => (int) $data['durationMinutes'],
        ];

        if ($user->role === 'admin') {
            $startsAt = trim((string) ($data['startsAt'] ?? ''));
            $endsAt = trim((string) ($data['endsAt'] ?? ''));

            $starts = $startsAt !== '' ? Carbon::createFromFormat('Y-m-d\TH:i', $startsAt) : null;
            $ends = $endsAt !== '' ? Carbon::createFromFormat('Y-m-d\TH:i', $endsAt) : null;

            if ($starts && $ends && $ends->lessThanOrEqualTo($starts)) {
                throw ValidationException::withMessages([
                    'endsAt' => 'End time must be after start time.',
                ]);
            }

            $attrs['starts_at'] = $starts;
            $attrs['ends_at'] = $ends;

            $pin = trim((string) ($data['pin'] ?? ''));
            $attrs['pin'] = $pin !== '' ? $pin : null;
            $attrs['grace_minutes'] = (int) ($data['graceMinutes'] ?? 0);

            $allowed = trim((string) ($data['allowedCidrs'] ?? ''));
            $attrs['allowed_cidrs'] = $allowed !== '' ? $allowed : null;
        }

        $exam->forceFill($attrs)->save();

        Audit::log('cbt.exam_updated', $exam);

        unset($this->exam);
        $this->dispatch('alert', message: 'Exam details saved.', type: 'success');
    }

    public function editQuestion(int $id): void
    {
        abort_unless($this->canEdit, 403);

        $q = CbtQuestion::query()
            ->where('exam_id', $this->examId)
            ->with(['options' => fn ($q) => $q->orderBy('position')])
            ->findOrFail($id);

        $this->editingQuestionId = $q->id;
        $this->questionPrompt = (string) $q->prompt;
        $this->questionMarks = (int) $q->marks;

        $labels = [];
        $correctIndex = 0;
        foreach ($q->options as $idx => $opt) {
            $labels[$idx] = (string) $opt->label;
            if ($opt->is_correct) {
                $correctIndex = (int) $idx;
            }
        }

        $this->optionLabels = array_pad($labels, 4, '');
        $this->correctIndex = $correctIndex;

        $this->resetValidation();
    }

    public function startNewQuestion(): void
    {
        abort_unless($this->canEdit, 403);

        $this->editingQuestionId = null;
        $this->questionPrompt = '';
        $this->questionMarks = 1;
        $this->optionLabels = ['', '', '', ''];
        $this->correctIndex = 0;
        $this->resetValidation();
    }

    public function saveQuestion(): void
    {
        abort_unless($this->canEdit, 403);

        $data = $this->validate([
            'questionPrompt' => ['required', 'string', 'max:5000'],
            'questionMarks' => ['required', 'integer', 'min:1', 'max:100'],
            'optionLabels' => ['required', 'array', 'size:4'],
            'optionLabels.*' => ['required', 'string', 'max:1000'],
            'correctIndex' => ['required', 'integer', 'min:0', 'max:3'],
        ]);

        $prompt = trim($data['questionPrompt']);
        $labels = array_map(fn ($v) => trim((string) $v), (array) $data['optionLabels']);

        foreach ($labels as $idx => $label) {
            if ($label === '') {
                throw ValidationException::withMessages(["optionLabels.{$idx}" => 'Option is required.']);
            }
        }

        DB::transaction(function () use ($prompt, $labels, $data) {
            if ($this->editingQuestionId) {
                $question = CbtQuestion::query()
                    ->where('exam_id', $this->examId)
                    ->findOrFail($this->editingQuestionId);
            } else {
                $nextPos = (int) CbtQuestion::query()->where('exam_id', $this->examId)->max('position') + 1;
                $question = CbtQuestion::query()->create([
                    'exam_id' => $this->examId,
                    'type' => 'mcq',
                    'prompt' => $prompt,
                    'marks' => (int) $data['questionMarks'],
                    'position' => max(1, $nextPos),
                ]);
            }

            $question->forceFill([
                'prompt' => $prompt,
                'marks' => (int) $data['questionMarks'],
            ])->save();

            $existing = CbtOption::query()
                ->where('question_id', $question->id)
                ->orderBy('position')
                ->get();

            for ($i = 0; $i < 4; $i++) {
                $opt = $existing->get($i);
                $attrs = [
                    'label' => $labels[$i],
                    'is_correct' => $i === (int) $data['correctIndex'],
                    'position' => $i + 1,
                ];

                if ($opt) {
                    $opt->forceFill($attrs)->save();
                } else {
                    CbtOption::query()->create(array_merge($attrs, [
                        'question_id' => $question->id,
                    ]));
                }
            }

            if ($existing->count() > 4) {
                $ids = $existing->slice(4)->pluck('id')->all();
                CbtOption::query()->whereIn('id', $ids)->delete();
            }
        });

        Audit::log($this->editingQuestionId ? 'cbt.question_updated' : 'cbt.question_created', $this->exam);

        $this->startNewQuestion();
        unset($this->exam);
        $this->dispatch('alert', message: 'Question saved.', type: 'success');
    }

    public function deleteQuestion(int $id): void
    {
        abort_unless($this->canEdit, 403);

        $q = CbtQuestion::query()
            ->where('exam_id', $this->examId)
            ->findOrFail($id);

        $q->delete();

        Audit::log('cbt.question_deleted', $this->exam, ['question_id' => $id]);

        if ($this->editingQuestionId === $id) {
            $this->startNewQuestion();
        }

        unset($this->exam);
        $this->dispatch('alert', message: 'Question deleted.', type: 'success');
    }

    public function resetAttempt(int $attemptId): void
    {
        $user = auth()->user();
        abort_unless($user?->role === 'admin', 403);

        $attempt = CbtAttempt::query()
            ->where('exam_id', $this->examId)
            ->with(['student:id,admission_number,first_name,last_name'])
            ->findOrFail($attemptId);

        DB::transaction(function () use ($attempt) {
            $attempt->answers()->delete();
            $attempt->delete();
        });

        Audit::log('cbt.attempt_reset', $this->exam, [
            'attempt_id' => $attemptId,
            'student_id' => $attempt->student_id,
        ]);

        unset($this->exam);
        $this->dispatch('alert', message: 'Attempt reset. Student can retake.', type: 'success');
    }

    #[Computed]
    public function roster()
    {
        $user = auth()->user();
        if (! $user || $user->role !== 'admin') {
            return collect();
        }

        $exam = $this->exam;
        $totalQuestions = (int) $exam->questions->count();

        $students = Student::query()
            ->where('class_id', $exam->class_id)
            ->where('status', 'Active')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get(['id', 'admission_number', 'first_name', 'last_name', 'status']);

        $attempts = CbtAttempt::query()
            ->where('exam_id', $exam->id)
            ->get()
            ->keyBy('student_id');

        $attemptIds = $attempts->pluck('id')->filter()->values();
        $answeredCounts = $attemptIds->isNotEmpty()
            ? CbtAnswer::query()
                ->selectRaw('attempt_id, count(*) as answered')
                ->whereIn('attempt_id', $attemptIds)
                ->whereNotNull('option_id')
                ->groupBy('attempt_id')
                ->pluck('answered', 'attempt_id')
            : collect();

        return $students->map(function (Student $student) use ($attempts, $answeredCounts, $totalQuestions) {
            /** @var ?CbtAttempt $attempt */
            $attempt = $attempts->get($student->id);

            $state = 'not_started';
            if ($attempt) {
                if ($attempt->terminated_at) {
                    $state = 'terminated';
                } elseif ($attempt->submitted_at) {
                    $state = 'submitted';
                } elseif ($attempt->started_at) {
                    $state = 'in_progress';
                }
            }

            $answered = $attempt ? (int) ($answeredCounts[$attempt->id] ?? 0) : 0;

            return [
                'student' => $student,
                'attempt' => $attempt,
                'state' => $state,
                'answered' => $answered,
                'remaining' => $attempt ? max(0, $totalQuestions - $answered) : $totalQuestions,
            ];
        });
    }

    public function startIpOverride(int $attemptId): void
    {
        $user = auth()->user();
        abort_unless($user?->role === 'admin', 403);

        $attempt = CbtAttempt::query()
            ->where('exam_id', $this->examId)
            ->findOrFail($attemptId);

        $this->editingAttemptIpId = (int) $attempt->id;
        $this->allowedIp = (string) ($attempt->allowed_ip ?? '');
        $this->resetValidation();
    }

    public function cancelIpOverride(): void
    {
        $user = auth()->user();
        abort_unless($user?->role === 'admin', 403);

        $this->editingAttemptIpId = null;
        $this->allowedIp = '';
        $this->resetValidation();
    }

    public function saveIpOverride(): void
    {
        $user = auth()->user();
        abort_unless($user?->role === 'admin', 403);
        abort_unless($this->editingAttemptIpId, 422);

        $data = $this->validate([
            'allowedIp' => ['nullable', 'string', 'max:45'],
        ]);

        $attempt = CbtAttempt::query()
            ->where('exam_id', $this->examId)
            ->findOrFail((int) $this->editingAttemptIpId);

        $value = trim((string) ($data['allowedIp'] ?? ''));

        $attempt->forceFill([
            'allowed_ip' => $value !== '' ? $value : null,
        ])->save();

        Audit::log('cbt.attempt_allowed_ip_updated', $this->exam, [
            'attempt_id' => $attempt->id,
            'allowed_ip' => $attempt->allowed_ip,
        ]);

        $this->cancelIpOverride();
        unset($this->exam);
        $this->dispatch('alert', message: 'Allowed IP updated.', type: 'success');
    }

    public function clearIpLock(int $attemptId): void
    {
        $user = auth()->user();
        abort_unless($user?->role === 'admin', 403);

        $attempt = CbtAttempt::query()
            ->where('exam_id', $this->examId)
            ->findOrFail($attemptId);

        $attempt->forceFill([
            'ip_address' => null,
            'allowed_ip' => null,
        ])->save();

        Audit::log('cbt.attempt_ip_cleared', $this->exam, [
            'attempt_id' => $attempt->id,
        ]);

        unset($this->exam);
        $this->dispatch('alert', message: 'IP lock cleared.', type: 'success');
    }

    public function terminateAttempt(int $attemptId): void
    {
        $user = auth()->user();
        abort_unless($user?->role === 'admin', 403);

        $attempt = CbtAttempt::query()
            ->where('exam_id', $this->examId)
            ->with(['exam.questions.options', 'answers'])
            ->findOrFail($attemptId);

        if ($attempt->submitted_at || $attempt->terminated_at) {
            $this->dispatch('alert', message: 'Attempt already ended.', type: 'warning');
            return;
        }

        DB::transaction(function () use ($attempt, $user) {
            $attempt->refresh();
            if ($attempt->submitted_at || $attempt->terminated_at) {
                return;
            }

            $attempt->loadMissing(['exam.questions.options', 'answers']);

            $answers = $attempt->answers->keyBy('question_id');

            $maxScore = 0;
            $score = 0;

            foreach ($attempt->exam->questions as $question) {
                $maxScore += (int) ($question->marks ?? 0);

                $correctOptionId = (int) ($question->options->firstWhere('is_correct', true)?->id ?? 0);
                $selectedOptionId = (int) ($answers->get($question->id)?->option_id ?? 0);
                if ($selectedOptionId > 0 && ! $question->options->contains('id', $selectedOptionId)) {
                    $selectedOptionId = 0;
                }

                $isCorrect = $selectedOptionId > 0 && $selectedOptionId === $correctOptionId;
                if ($isCorrect) {
                    $score += (int) ($question->marks ?? 0);
                }

                CbtAnswer::query()->updateOrCreate(
                    [
                        'attempt_id' => $attempt->id,
                        'question_id' => $question->id,
                    ],
                    [
                        'option_id' => $selectedOptionId > 0 ? $selectedOptionId : null,
                        'is_correct' => $isCorrect,
                    ]
                );
            }

            $percent = $maxScore > 0 ? round(($score / $maxScore) * 100, 2) : 0;

            $attempt->forceFill([
                'score' => $score,
                'max_score' => $maxScore,
                'percent' => $percent,
                'submitted_at' => now(),
                'terminated_at' => now(),
                'terminated_by' => $user->id,
            ])->save();
        });

        Audit::log('cbt.attempt_terminated', $this->exam, [
            'attempt_id' => $attemptId,
        ]);

        unset($this->exam);
        $this->dispatch('alert', message: 'Attempt terminated.', type: 'success');
    }

    public function forceSubmitAttempt(int $attemptId): void
    {
        $user = auth()->user();
        abort_unless($user?->role === 'admin', 403);

        $attempt = CbtAttempt::query()
            ->where('exam_id', $this->examId)
            ->with(['exam.questions.options', 'answers'])
            ->findOrFail($attemptId);

        if ($attempt->submitted_at || $attempt->terminated_at) {
            $this->dispatch('alert', message: 'Attempt already ended.', type: 'warning');
            return;
        }

        DB::transaction(function () use ($attempt) {
            $attempt->refresh();
            if ($attempt->submitted_at || $attempt->terminated_at) {
                return;
            }

            $attempt->loadMissing(['exam.questions.options', 'answers']);

            $answers = $attempt->answers->keyBy('question_id');

            $maxScore = 0;
            $score = 0;

            foreach ($attempt->exam->questions as $question) {
                $maxScore += (int) ($question->marks ?? 0);

                $correctOptionId = (int) ($question->options->firstWhere('is_correct', true)?->id ?? 0);
                $selectedOptionId = (int) ($answers->get($question->id)?->option_id ?? 0);
                if ($selectedOptionId > 0 && ! $question->options->contains('id', $selectedOptionId)) {
                    $selectedOptionId = 0;
                }

                $isCorrect = $selectedOptionId > 0 && $selectedOptionId === $correctOptionId;
                if ($isCorrect) {
                    $score += (int) ($question->marks ?? 0);
                }

                CbtAnswer::query()->updateOrCreate(
                    [
                        'attempt_id' => $attempt->id,
                        'question_id' => $question->id,
                    ],
                    [
                        'option_id' => $selectedOptionId > 0 ? $selectedOptionId : null,
                        'is_correct' => $isCorrect,
                    ]
                );
            }

            $percent = $maxScore > 0 ? round(($score / $maxScore) * 100, 2) : 0;

            $attempt->forceFill([
                'score' => $score,
                'max_score' => $maxScore,
                'percent' => $percent,
                'submitted_at' => now(),
            ])->save();
        });

        Audit::log('cbt.attempt_force_submitted', $this->exam, [
            'attempt_id' => $attemptId,
        ]);

        unset($this->exam);
        $this->dispatch('alert', message: 'Attempt submitted.', type: 'success');
    }

    public function duplicateExam()
    {
        $user = auth()->user();
        abort_unless($user && in_array($user->role, ['admin', 'teacher'], true), 403);

        $source = $this->exam;
        if ($user->role === 'teacher') {
            $canAccess = (int) $source->created_by === (int) $user->id
                || (int) ($source->assigned_teacher_id ?? 0) === (int) $user->id;
            abort_unless($canAccess, 403);
        }

        $newExam = DB::transaction(function () use ($user, $source) {
            $copy = CbtExam::query()->create([
                'title' => $source->title.' (Copy)',
                'description' => $source->description,
                'class_id' => $source->class_id,
                'subject_id' => $source->subject_id,
                'term' => $source->term,
                'session' => $source->session,
                'duration_minutes' => $source->duration_minutes,
                'status' => 'draft',
                'created_by' => $user->id,
                'assigned_teacher_id' => $user->role === 'teacher' ? $user->id : $source->assigned_teacher_id,
            ]);

            $source->loadMissing(['questions.options']);

            foreach ($source->questions as $q) {
                $newQ = CbtQuestion::query()->create([
                    'exam_id' => $copy->id,
                    'type' => $q->type,
                    'prompt' => $q->prompt,
                    'marks' => $q->marks,
                    'position' => $q->position,
                ]);

                foreach ($q->options as $opt) {
                    CbtOption::query()->create([
                        'question_id' => $newQ->id,
                        'label' => $opt->label,
                        'is_correct' => (bool) $opt->is_correct,
                        'position' => $opt->position,
                    ]);
                }
            }

            return $copy;
        });

        Audit::log('cbt.exam_duplicated', $newExam, [
            'source_exam_id' => $source->id,
        ]);

        return redirect()->route('cbt.exams.edit', $newExam);
    }

    public function submitToAdmin(): void
    {
        $user = auth()->user();
        abort_unless($user?->role === 'teacher', 403);
        abort_unless($this->canEdit, 403);

        $exam = $this->exam;
        $questions = $exam->questions;

        if ($questions->isEmpty()) {
            $this->dispatch('alert', message: 'Add at least one question before submitting.', type: 'warning');
            return;
        }

        foreach ($questions as $q) {
            $options = $q->options;
            if ($options->count() < 2) {
                $this->dispatch('alert', message: 'Each question must have options.', type: 'warning');
                return;
            }

            $correctCount = $options->where('is_correct', true)->count();
            if ($correctCount !== 1) {
                $this->dispatch('alert', message: 'Each question must have exactly one correct option.', type: 'warning');
                return;
            }
        }

        $exam->forceFill([
            'status' => 'submitted',
            'submitted_at' => now(),
            'reviewed_by' => null,
            'reviewed_at' => null,
            'note' => null,
        ])->save();

        Audit::log('cbt.exam_submitted', $exam);

        $adminIds = User::query()
            ->where('role', 'admin')
            ->where('is_active', true)
            ->pluck('id');

        foreach ($adminIds as $adminId) {
            InAppNotification::query()->create([
                'user_id' => (int) $adminId,
                'title' => 'CBT exam submitted',
                'body' => "{$user->name} submitted an exam for approval: {$exam->title}.",
                'link' => route('cbt.exams.edit', $exam),
            ]);
        }

        $this->showRejectForm = false;
        $this->reviewNote = '';
        unset($this->exam);
        $this->dispatch('alert', message: 'Submitted to admin.', type: 'success');
    }

    public function togglePublish(): void
    {
        $user = auth()->user();
        abort_unless($user?->role === 'admin', 403);

        $exam = $this->exam;
        abort_unless($exam->status === 'approved', 403);

        $exam->published_at = $exam->published_at ? null : now();
        $exam->save();

        Audit::log('cbt.exam_publish_toggled', $exam, [
            'published' => (bool) $exam->published_at,
        ]);

        unset($this->exam);
        $this->dispatch('alert', message: $exam->published_at ? 'Exam is now live.' : 'Exam paused.', type: 'success');
    }

    public function approve(): void
    {
        $user = auth()->user();
        abort_unless($user?->role === 'admin', 403);

        $exam = $this->exam;
        if ($exam->status !== 'submitted') {
            $this->dispatch('alert', message: 'Only submitted exams can be approved.', type: 'warning');
            return;
        }

        $code = $exam->access_code ?: $this->generateAccessCode();

        $exam->forceFill([
            'status' => 'approved',
            'access_code' => $code,
            'reviewed_by' => $user->id,
            'reviewed_at' => now(),
            'note' => null,
            'published_at' => now(),
        ])->save();

        Audit::log('cbt.exam_approved', $exam, ['reviewed_by' => $user->id]);

        $notifyUserId = (int) ($exam->assigned_teacher_id ?: $exam->created_by);
        InAppNotification::query()->create([
            'user_id' => $notifyUserId,
            'title' => 'CBT exam approved',
            'body' => "Your CBT exam was approved: {$exam->title}. Code: {$code}",
            'link' => route('cbt.exams.edit', $exam),
        ]);

        $this->showRejectForm = false;
        $this->reviewNote = '';
        unset($this->exam);
        $this->dispatch('alert', message: 'Exam approved.', type: 'success');
    }

    public function startReject(): void
    {
        $user = auth()->user();
        abort_unless($user?->role === 'admin', 403);

        $exam = $this->exam;
        if ($exam->status !== 'submitted') {
            $this->dispatch('alert', message: 'Only submitted exams can be rejected.', type: 'warning');
            return;
        }

        $this->showRejectForm = true;
        $this->reviewNote = '';
        $this->resetValidation();
    }

    public function cancelReject(): void
    {
        $user = auth()->user();
        abort_unless($user?->role === 'admin', 403);

        $this->showRejectForm = false;
        $this->reviewNote = '';
        $this->resetValidation();
    }

    public function confirmReject(): void
    {
        $user = auth()->user();
        abort_unless($user?->role === 'admin', 403);

        $exam = $this->exam;
        if ($exam->status !== 'submitted') {
            $this->dispatch('alert', message: 'This exam has already been reviewed.', type: 'warning');
            $this->cancelReject();
            return;
        }

        $this->validate([
            'reviewNote' => ['required', 'string', 'min:3', 'max:2000'],
        ]);

        $note = trim($this->reviewNote);

        $exam->forceFill([
            'status' => 'rejected',
            'reviewed_by' => $user->id,
            'reviewed_at' => now(),
            'note' => $note,
            'published_at' => null,
        ])->save();

        Audit::log('cbt.exam_rejected', $exam, ['reviewed_by' => $user->id, 'note' => $note]);

        $notifyUserId = (int) ($exam->assigned_teacher_id ?: $exam->created_by);
        InAppNotification::query()->create([
            'user_id' => $notifyUserId,
            'title' => 'CBT exam rejected',
            'body' => "Your CBT exam was rejected: {$note}",
            'link' => route('cbt.exams.edit', $exam),
        ]);

        $this->showRejectForm = false;
        $this->reviewNote = '';
        unset($this->exam);
        $this->dispatch('alert', message: 'Exam rejected.', type: 'success');
    }

    private function generateAccessCode(): string
    {
        for ($i = 0; $i < 20; $i++) {
            $code = 'CBT-'.strtoupper(bin2hex(random_bytes(3)));

            $exists = CbtExam::query()->where('access_code', $code)->exists();
            if (! $exists) {
                return $code;
            }
        }

        return 'CBT-'.strtoupper(Str::random(8));
    }

    public function render()
    {
        $user = auth()->user();
        abort_unless($user && in_array($user->role, ['admin', 'teacher'], true), 403);

        $exam = $this->exam;

        if ($user->role === 'teacher') {
            $canAccess = (int) $exam->created_by === (int) $user->id
                || (int) ($exam->assigned_teacher_id ?? 0) === (int) $user->id;
            abort_unless($canAccess, 403);
        }

        return view('livewire.cbt.exam-editor', [
            'me' => $user,
            'exam' => $exam,
        ]);
    }
}
