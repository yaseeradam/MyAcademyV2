<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Report Card - {{ $student->admission_number }}</title>
        <style>
            body {
                font-family: DejaVu Sans, Arial, sans-serif;
                font-size: 12px;
                color: #111827;
            }
            .header {
                border-bottom: 1px solid #e5e7eb;
                padding-bottom: 12px;
                margin-bottom: 16px;
            }
            .title {
                font-size: 18px;
                font-weight: 700;
            }
            .muted {
                color: #6b7280;
            }
            table {
                width: 100%;
                border-collapse: collapse;
            }
            th,
            td {
                padding: 8px;
                border-bottom: 1px solid #e5e7eb;
            }
            th {
                background: #f9fafb;
                color: #6b7280;
                text-transform: uppercase;
                letter-spacing: 0.06em;
                font-size: 10px;
                text-align: left;
            }
            .summary {
                margin-top: 14px;
            }
            .summary td {
                border: none;
                padding: 4px 0;
            }
            .summary td:first-child {
                width: 35%;
                color: #6b7280;
            }
        </style>
    </head>
    <body>
        <div class="header">
            <div class="title">{{ config('app.name', 'MyAcademy') }} - Report Card</div>
            <div class="muted">Session {{ $session }} • Term {{ $term }}</div>
        </div>

        <table class="summary">
            <tr>
                <td>Student</td>
                <td><strong>{{ $student->full_name }}</strong> ({{ $student->admission_number }})</td>
            </tr>
            <tr>
                <td>Class / Section</td>
                <td>{{ $student->schoolClass?->name }} / {{ $student->section?->name }}</td>
            </tr>
            <tr>
                <td>Total</td>
                <td><strong>{{ $grandTotal }}</strong></td>
            </tr>
            <tr>
                <td>Average</td>
                <td><strong>{{ number_format($average, 2) }}</strong></td>
            </tr>
            <tr>
                <td>Position</td>
                <td><strong>{{ $position }}</strong></td>
            </tr>
            <tr>
                <td>Class Average</td>
                <td><strong>{{ number_format($classAverage, 2) }}</strong></td>
            </tr>
        </table>

        <div style="height: 12px;"></div>

        <table>
            <thead>
                <tr>
                    <th style="width: 36%;">Subject</th>
                    <th style="width: 12%; text-align: right;">CA1</th>
                    <th style="width: 12%; text-align: right;">CA2</th>
                    <th style="width: 12%; text-align: right;">Exam</th>
                    <th style="width: 12%; text-align: right;">Total</th>
                    <th style="width: 16%; text-align: right;">Grade</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rows as $row)
                    <tr>
                        <td>{{ $row['subject']->name }}</td>
                        <td style="text-align: right;">{{ $row['ca1'] ?? '—' }}</td>
                        <td style="text-align: right;">{{ $row['ca2'] ?? '—' }}</td>
                        <td style="text-align: right;">{{ $row['exam'] ?? '—' }}</td>
                        <td style="text-align: right;"><strong>{{ $row['total'] ?? '—' }}</strong></td>
                        <td style="text-align: right;">{{ $row['grade'] ?? '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top: 18px; font-size: 11px;" class="muted">
            Generated offline on {{ now()->format('M j, Y g:i A') }}.
        </div>
    </body>
</html>

