<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Timetable - {{ $class->name }}</title>
    <style>
        @page { 
            size: A4 landscape; 
            margin: 10mm; 
        }
        
        * { 
            margin: 0; 
            padding: 0; 
            box-sizing: border-box; 
        }
        
        body { 
            font-family: 'DejaVu Sans', sans-serif; 
            font-size: 9px; 
            color: #1e293b;
        }
        
        .header {
            background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 100%);
            padding: 15px 20px;
            border-radius: 6px;
            margin-bottom: 15px;
            color: white;
        }
        
        .school-name { 
            font-size: 18px; 
            font-weight: bold; 
            margin-bottom: 4px;
        }
        
        .doc-title { 
            font-size: 13px; 
            font-weight: 600;
            opacity: 0.95;
        }
        
        .class-info { 
            font-size: 10px; 
            margin-top: 6px;
            opacity: 0.9;
        }
        
        table { 
            width: 100%; 
            border-collapse: collapse;
            background: white;
        }
        
        thead {
            background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 100%);
        }
        
        th { 
            color: white; 
            padding: 8px 6px; 
            text-align: center; 
            font-size: 9px; 
            font-weight: bold;
            border: 1px solid rgba(255,255,255,0.2);
        }
        
        th.time-col { 
            width: 10%;
        }
        
        th.day-col { 
            width: 18%; 
        }
        
        tbody tr:nth-child(even) {
            background: #f8fafc;
        }
        
        tbody tr:nth-child(odd) {
            background: #ffffff;
        }
        
        td { 
            padding: 6px; 
            border: 1px solid #e2e8f0; 
            font-size: 8px; 
            vertical-align: top;
            min-height: 45px;
        }
        
        td.time-cell { 
            background: #f1f5f9;
            font-weight: bold; 
            text-align: center; 
            color: #475569;
            font-size: 8.5px;
        }
        
        .entry { 
            background: linear-gradient(135deg, #e0f2fe 0%, #dbeafe 100%);
            padding: 5px 6px; 
            border-radius: 4px; 
            margin-bottom: 3px;
            border-left: 2px solid #0ea5e9;
        }
        
        .entry:last-child { 
            margin-bottom: 0; 
        }
        
        .subject { 
            font-weight: bold; 
            color: #0369a1; 
            font-size: 9px; 
            margin-bottom: 2px;
        }
        
        .teacher { 
            color: #64748b; 
            font-size: 7.5px;
            margin-bottom: 1px;
        }
        
        .room { 
            color: #0284c7; 
            font-size: 7.5px; 
            font-weight: 600;
        }
        
        .empty { 
            text-align: center; 
            color: #cbd5e1; 
            font-size: 9px;
            padding: 12px 0;
        }
        
        .footer { 
            margin-top: 12px; 
            padding: 8px 15px; 
            background: #f8fafc;
            border-radius: 4px;
            font-size: 7px;
            color: #64748b;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="school-name">{{ $schoolName }}</div>
        <div class="doc-title">Weekly Timetable</div>
        <div class="class-info">{{ $class->name }}@if($section) - {{ $section->name }}@endif | Academic Year {{ now()->format('Y') }}</div>
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
                                    <div class="subject">{{ $entry->subject?->name ?? 'N/A' }}</div>
                                    <div class="teacher">{{ $entry->teacher?->name ?? 'No Teacher' }}</div>
                                    @if($entry->room)
                                        <div class="room">Room: {{ $entry->room }}</div>
                                    @endif
                                </div>
                            @else
                                <div class="empty">—</div>
                            @endif
                        </td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 30px; color: #94a3b8;">No timetable entries found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Generated on {{ now()->format('F j, Y \a\t g:i A') }} | {{ $schoolName }}
    </div>
</body>
</html>
