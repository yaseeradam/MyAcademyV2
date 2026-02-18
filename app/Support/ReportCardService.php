<?php

namespace App\Support;

use App\Models\AttendanceMark;
use App\Models\AttendanceSheet;
use App\Models\Score;
use App\Models\Student;
use App\Models\Subject;
use App\Models\SubjectAllocation;
use Illuminate\Support\Collection;

class ReportCardService
{
    /**
     * Build all data needed for the `pdf.report-card` view.
     *
     * @return array{
     *   student:\App\Models\Student,
     *   term:int,
     *   session:string,
     *   rows:\Illuminate\Support\Collection<int, array{subject:\App\Models\Subject,ca1:int|null,ca2:int|null,exam:int|null,total:int|null,grade:string|null}>,
     *   grandTotal:int,
     *   average:float,
     *   position:int,
     *   classAverage:float
     * }
     */
    public function build(Student $student, int $term, string $session): array
    {
        $student->load(['schoolClass', 'section']);

        $subjects = $this->subjectsForClass($student->class_id);
        $subjectIds = $subjects->pluck('id');
        $subjectCount = max(1, (int) $subjectIds->count());

        $scores = Score::query()
            ->with('subject')
            ->where('student_id', $student->id)
            ->where('class_id', $student->class_id)
            ->where('term', $term)
            ->where('session', $session)
            ->whereIn('subject_id', $subjectIds)
            ->get()
            ->keyBy('subject_id');

        $rows = $subjects->map(function (Subject $subject) use ($scores) {
            /** @var \App\Models\Score|null $score */
            $score = $scores->get($subject->id);

            return [
                'subject' => $subject,
                'ca1' => $score?->ca1 ?? null,
                'ca2' => $score?->ca2 ?? null,
                'exam' => $score?->exam ?? null,
                'total' => $score?->total ?? null,
                'grade' => $score?->grade ?? ($score ? Score::gradeForTotal((int) $score->total, max(0, (int) config('myacademy.results_ca1_max', 20)) + max(0, (int) config('myacademy.results_ca2_max', 20)) + max(0, (int) config('myacademy.results_exam_max', 60))) : null),
            ];
        });

        $grandTotal = (int) $rows->sum(fn ($r) => (int) ($r['total'] ?? 0));
        $average = round($grandTotal / $subjectCount, 2);

        [$position, $classAverage] = $this->positionAndClassAverage(
            studentId: $student->id,
            classId: $student->class_id,
            subjectIds: $subjectIds,
            term: $term,
            session: $session
        );

        [$timesOpened, $timesPresent, $timesAbsent] = $this->attendanceSummary($student, $term, $session);

        return [
            'student' => $student,
            'term' => $term,
            'session' => $session,
            'rows' => $rows,
            'grandTotal' => $grandTotal,
            'average' => $average,
            'position' => $position,
            'classAverage' => $classAverage,
            'timesOpened' => $timesOpened,
            'timesPresent' => $timesPresent,
            'timesAbsent' => $timesAbsent,
        ];
    }

    /**
     * @return array{0:int|null,1:int|null,2:int|null} times opened, present, absent.
     */
    private function attendanceSummary(Student $student, int $term, string $session): array
    {
        $sectionId = (int) ($student->section_id ?? 0);
        if ($sectionId <= 0) {
            return [null, null, null];
        }

        $sheetsQuery = AttendanceSheet::query()
            ->where('class_id', $student->class_id)
            ->where('section_id', $sectionId)
            ->where('term', $term)
            ->where('session', $session);

        $timesOpened = (int) $sheetsQuery->count();

        $counts = AttendanceMark::query()
            ->join('attendance_sheets', 'attendance_sheets.id', '=', 'attendance_marks.sheet_id')
            ->where('attendance_marks.student_id', $student->id)
            ->where('attendance_sheets.class_id', $student->class_id)
            ->where('attendance_sheets.section_id', $sectionId)
            ->where('attendance_sheets.term', $term)
            ->where('attendance_sheets.session', $session)
            ->selectRaw("SUM(CASE WHEN attendance_marks.status = 'Absent' THEN 1 ELSE 0 END) AS absent_count")
            ->selectRaw("SUM(CASE WHEN attendance_marks.status IN ('Present','Late','Excused') THEN 1 ELSE 0 END) AS present_count")
            ->first();

        $timesAbsent = (int) ($counts?->absent_count ?? 0);
        $timesPresent = (int) ($counts?->present_count ?? 0);

        if ($timesOpened === 0 && ($timesPresent + $timesAbsent) > 0) {
            $timesOpened = $timesPresent + $timesAbsent;
        }

        if ($timesOpened <= 0 && $timesPresent === 0 && $timesAbsent === 0) {
            return [null, null, null];
        }

        return [$timesOpened, $timesPresent, $timesAbsent];
    }

    private function subjectsForClass(int $classId): Collection
    {
        $ids = SubjectAllocation::query()
            ->where('class_id', $classId)
            ->pluck('subject_id')
            ->unique();

        if ($ids->isEmpty()) {
            return Subject::query()->orderBy('name')->get();
        }

        return Subject::query()->whereIn('id', $ids)->orderBy('name')->get();
    }

    /**
     * @return array{0:int,1:float} position (1-based) and class average.
     */
    private function positionAndClassAverage(
        int $studentId,
        int $classId,
        Collection $subjectIds,
        int $term,
        string $session
    ): array {
        $scores = Score::query()
            ->where('class_id', $classId)
            ->where('term', $term)
            ->where('session', $session)
            ->whereIn('subject_id', $subjectIds)
            ->get(['student_id', 'total']);

        if ($scores->isEmpty()) {
            return [1, 0.0];
        }

        $totalsByStudent = $scores
            ->groupBy('student_id')
            ->map(fn ($rows) => (int) $rows->sum('total'));

        $subjectCount = max(1, (int) $subjectIds->count());
        $classAverage = round($totalsByStudent->avg() / $subjectCount, 2);

        $sorted = $totalsByStudent->sortDesc();
        $position = 1;
        $rank = 0;
        $last = null;

        foreach ($sorted as $id => $total) {
            if ($last === null || $total !== $last) {
                $rank++;
                $last = $total;
            }

            if ((int) $id === (int) $studentId) {
                $position = $rank;
                break;
            }
        }

        return [$position, $classAverage];
    }
}
