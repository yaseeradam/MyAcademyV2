<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>{{ $transaction->receipt_number }}</title>
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
            td,
            th {
                padding: 8px 0;
                vertical-align: top;
            }
            .kv td:first-child {
                width: 35%;
                color: #6b7280;
            }
            .amount {
                font-size: 16px;
                font-weight: 700;
            }
            .badge {
                display: inline-block;
                padding: 4px 10px;
                border-radius: 999px;
                background: #dcfce7;
                color: #15803d;
                font-weight: 700;
                font-size: 11px;
            }
            .badge-warning {
                background: #ffedd5;
                color: #9a3412;
            }
            .void-watermark {
                position: fixed;
                top: 40%;
                left: 18%;
                font-size: 90px;
                font-weight: 800;
                color: #b91c1c;
                opacity: 0.12;
                transform: rotate(-20deg);
                letter-spacing: 4px;
            }
            .footer {
                border-top: 1px solid #e5e7eb;
                margin-top: 18px;
                padding-top: 12px;
                color: #6b7280;
                font-size: 11px;
            }
        </style>
    </head>
    <body>
        @if ($transaction->is_void)
            <div class="void-watermark">VOID</div>
        @endif

        <div class="header">
            <div class="title">{{ config('myacademy.school_name', config('app.name', 'MyAcademy')) }}</div>
            <div class="muted">Official Receipt</div>
        </div>

        <table class="kv">
            <tr>
                <td>Receipt Number</td>
                <td>
                    <strong>{{ $transaction->receipt_number }}</strong>
                    <span class="badge {{ $transaction->is_void ? 'badge-warning' : '' }}">
                        {{ $transaction->is_void ? 'VOID' : 'PAID' }}
                    </span>
                </td>
            </tr>
            <tr>
                <td>Date</td>
                <td>{{ $transaction->date?->format('M j, Y') }}</td>
            </tr>
            @if ($transaction->is_void)
                <tr>
                    <td>Voided At</td>
                    <td>{{ $transaction->voided_at?->format('M j, Y g:i A') ?: '-' }}</td>
                </tr>
                <tr>
                    <td>Reason</td>
                    <td>{{ $transaction->void_reason ?: '-' }}</td>
                </tr>
            @endif
            <tr>
                <td>Student</td>
                <td>
                    <strong>{{ $transaction->student?->full_name ?: '-' }}</strong>
                    @if ($transaction->student?->admission_number)
                        <span class="muted">({{ $transaction->student->admission_number }})</span>
                    @endif
                </td>
            </tr>
            <tr>
                <td>Category</td>
                <td>{{ $transaction->category }}</td>
            </tr>
            <tr>
                <td>Session / Term</td>
                <td>
                    {{ $transaction->session ?: '—' }}
                    @if ($transaction->term)
                        / Term {{ $transaction->term }}
                    @endif
                </td>
            </tr>
            <tr>
                <td>Payment Method</td>
                <td>{{ $transaction->payment_method ?: '—' }}</td>
            </tr>
            <tr>
                <td>Amount Paid</td>
                <td class="amount">{{ config('myacademy.currency_symbol') }}{{ number_format((float) $transaction->amount_paid, 2) }}</td>
            </tr>
        </table>

        <div class="footer">
            Generated offline on {{ now()->format('M j, Y g:i A') }}.
        </div>
    </body>
</html>
