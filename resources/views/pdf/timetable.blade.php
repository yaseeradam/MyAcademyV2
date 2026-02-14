<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Timetable - {{ $class->name }}</title>
    <style>
        @page { size: A4 landscape; margin: 15mm; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 9px; color: #1e293b; }
        .header { text-align: center; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px solid #0ea5e9; }
        .school-name { font-size: 16px; font-weight: bold; color: #0c4a6e; margin-bottom: 3px; }
        .doc-title { font-size: 13px; font-weight: bold; color: #0369a1; margin-top: 5px; }
        .class-info { font-size: 10px; color: #475569; margin-top: 3px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #0ea5e9; color: white; padding: 6px 4px; text-align: center; font-size: 9px; font-weight: bold; border: 1px solid #0284c7; }
        th.time-col { width: 12%; }
        th.day-col { width: 17.6%; }
        td { padding: 5px 3px; border: 1px solid #cbd5e1; font-size: 8px; vertical-align: top; }
        td.time-cell { background: #f1f5f9; font-weight: bold; text-align: center; color: #475569; }
        .entry { background: #e0f2fe; padding: 4px; border-radius: 3px; margin-bottom: 2px; }
        .entry:last-child { margin-bottom: 0; }
        .subject { font-weight: bold; color: #0369a1; font-size: 9px; margin-bottom: 2px; }
        .teacher { color: #475569; font-size: 8px; }
        .room { color: #64748b; font-size: 7px; margin-top: 1px; }
        .empty { text-align: center; color: #cbd5e1; }
        .footer { margin-top: 15px; padding-top: 10px; border-top: 1px solid #cbd5e1; text-align: center; font-size: 8px; color: #64748b; }
    </style>
</head>
<body>
    <div class="header">
        <div class="school-name">{{ $schoolName }}</div>
        <div class="doc-title">Weekly Timetable</div>
        <div class="class-info">{{ $class->name }}@if($section) - {{ $section->name }}@endif</div>
    </div>

    @php
        $days = [1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday'];
        $timeSlots = [];
        foreach ($grouped as $g) {
            foreach ($g['rows'] as $row) {
                $key = substr($row->starts_at, 0, 5) . '-' . substr($row->ends_at, 0, 5);
                $timeSlots[$key] = substr($row->starts_at, 0, 5) . ' - ' . substr($row->ends_at, 0, 5);
            }
        }
        ksort($timeSlots);
        
        $schedule = [];
        foreach ($grouped as $g) {
            foreach ($g['rows'] as $row) {
                $key = substr($row->starts_at, 0, 5) . '-' . substr($row->ends_at, 0, 5);
                $schedule[$key][$g['day']] = $row;
            }
        }
    @endphp

    <table>
        <thead>
            <tr>
                <th class="time-col">Time</th>
                @foreach($days as $dayNum => $dayName)
                    <th class="day-col">{{ $dayName }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($timeSlots as $slotKey => $slotLabel)
                <tr>
                    <td class="time-cell">{{ $slotLabel }}</td>
                    @foreach($days as $dayNum => $dayName)
                        <td>
                            @if(isset($schedule[$slotKey][$dayNum]))
                                @php($entry = $schedule[$slotKey][$dayNum])
                                <div class="entry">
                                    <div class="subject">{{ $entry->subject?->name ?? '-' }}</div>
                                    <div class="teacher">{{ $entry->teacher?->name ?? '-' }}</div>
                                    @if($entry->room)
                                        <div class="room">Room: {{ $entry->room }}</div>
                                    @endif
                                </div>
                            @else
                                <div class="empty">-</div>
                            @endif
                        </td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 30px; color: #94a3b8;">
                        No timetable entries found
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Generated on {{ now()->format('F j, Y \a\t g:i A') }}
    </div>
</body>
</html>
