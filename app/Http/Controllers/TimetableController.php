<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\TimetableEntry;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class TimetableController extends Controller
{
    public function downloadPdf(Request $request)
    {
        $classId = (int) $request->query('class_id');
        $sectionId = $request->query('section_id') ? (int) $request->query('section_id') : null;

        $class = SchoolClass::query()->findOrFail($classId);
        $section = $sectionId ? Section::query()->findOrFail($sectionId) : null;

        $entries = TimetableEntry::query()
            ->with(['subject:id,name', 'teacher:id,name'])
            ->where('class_id', $classId)
            ->when($sectionId, fn($q) => $q->where('section_id', $sectionId))
            ->orderBy('day_of_week')
            ->orderBy('starts_at')
            ->get();

        $days = [
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
        ];

        $grouped = $entries->groupBy('day_of_week')->map(function ($rows, $day) use ($days) {
            return [
                'day' => (int) $day,
                'label' => $days[$day] ?? 'Day',
                'rows' => $rows,
            ];
        })->sortBy('day')->values();

        $pdf = Pdf::loadView('pdf.timetable', [
            'class' => $class,
            'section' => $section,
            'grouped' => $grouped,
            'schoolName' => config('myacademy.school_name', 'School'),
        ]);

        $filename = 'Timetable_' . str_replace(' ', '_', $class->name);
        if ($section) {
            $filename .= '_' . str_replace(' ', '_', $section->name);
        }
        $filename .= '.pdf';

        return $pdf->download($filename);
    }
}
