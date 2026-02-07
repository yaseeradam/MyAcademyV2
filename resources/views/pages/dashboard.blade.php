@php
    use App\Models\SchoolClass;
    use App\Models\Score;
    use App\Models\Student;
    use App\Models\Transaction;
    use App\Models\User;
    use Illuminate\Support\Facades\DB;

    $todayLabel = now()->format('l, F j, Y');
    $user = auth()->user();
    $schoolName = config('myacademy.school_name', config('app.name', 'MyAcademy'));

    $studentsPresent = 240;
    $teachersPresent = 58;
    $classesRunning = 18;

    $studentsTotal = Student::query()->count();
    $teachersTotal = User::query()->where('role', 'teacher')->count();
    $classesTotal = SchoolClass::query()->count();

    $feesCollectedToday = (float) Transaction::query()
        ->where('type', 'Income')
        ->where('is_void', false)
        ->whereDate('date', today())
        ->sum('amount_paid');

    $feesCollectedThisWeek = (float) Transaction::query()
        ->where('type', 'Income')
        ->where('is_void', false)
        ->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()])
        ->sum('amount_paid');

    $estimatedTuitionDueAllTime = (float) DB::table('students')
        ->leftJoin('fee_structures', function ($join) {
            $join->on('fee_structures.class_id', '=', 'students.class_id')
                ->where('fee_structures.category', '=', 'Tuition')
                ->whereNull('fee_structures.term')
                ->whereNull('fee_structures.session');
        })
        ->sum('fee_structures.amount_due');

    $incomeAllTime = (float) Transaction::query()
        ->where('type', 'Income')
        ->where('is_void', false)
        ->sum('amount_paid');

    $outstandingPaymentsEstimate = max(0.0, $estimatedTuitionDueAllTime - $incomeAllTime);

    $dueByStudent = DB::table('students')
        ->leftJoin('fee_structures', function ($join) {
            $join->on('fee_structures.class_id', '=', 'students.class_id')
                ->where('fee_structures.category', '=', 'Tuition')
                ->whereNull('fee_structures.term')
                ->whereNull('fee_structures.session');
        })
        ->select('students.id', DB::raw('COALESCE(SUM(fee_structures.amount_due), 0) as due'))
        ->groupBy('students.id');

    $paidByStudent = DB::table('transactions')
        ->where('type', 'Income')
        ->where('is_void', false)
        ->whereNotNull('student_id')
        ->select('student_id', DB::raw('SUM(amount_paid) as paid'))
        ->groupBy('student_id');

    $overdueInvoices = DB::query()
        ->fromSub($dueByStudent, 'd')
        ->leftJoinSub($paidByStudent, 'p', 'p.student_id', '=', 'd.id')
        ->whereRaw('d.due > COALESCE(p.paid, 0)')
        ->count();

    $latestScores = Score::query()
        ->with(['student', 'subject', 'schoolClass'])
        ->latest('updated_at')
        ->limit(6)
        ->get();
@endphp

@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <section class="space-y-4">
            <div class="card overflow-hidden">
                <div class="relative h-72 w-full sm:h-80">
                    <img
                        src="{{ asset('images/kid-pencil.svg') }}"
                        alt="School banner"
                        class="absolute inset-0 h-full w-full object-cover"
                    />
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900/65 via-slate-900/25 to-transparent"></div>

                    <div class="absolute inset-0 flex flex-col justify-end p-6">
                        <div class="max-w-xl">
                            <div class="text-base font-semibold text-white sm:text-lg">{{ $schoolName }}</div>
                            <div class="mt-1 text-sm text-white/80">
                                {{ config('myacademy.current_term', 'Term 1') }} · {{ config('myacademy.current_week', 'Week 1') }}
                            </div>
                            <div class="mt-3 text-sm text-white/80">
                                Manage classes, subjects, allocations, and academic setup.
                            </div>
                        </div>

                        <div class="mt-5 flex flex-col gap-2 sm:flex-row sm:items-center">
                            <a href="{{ route('attendance') }}" class="btn-primary w-full justify-center sm:w-auto">
                                Take Attendance
                            </a>
                            <a href="{{ route('institute') }}" class="btn-primary w-full justify-center sm:w-auto">
                                Open Institute
                            </a>
                            <a href="{{ route('students.index') }}" class="btn-outline w-full justify-center sm:w-auto">
                                View Students
                            </a>
                            <a href="{{ route('teachers') }}" class="btn-outline w-full justify-center sm:w-auto">
                                View Teachers
                            </a>
                            <a href="{{ route('results.entry') }}" class="btn-outline w-full justify-center sm:w-auto">
                                Enter Results
                            </a>
                            <a href="{{ route('billing.index') }}" class="btn-outline w-full justify-center sm:w-auto">
                                Record Payment
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </section>

        <section class="space-y-3">
            <div class="flex items-center justify-between">
                <div class="text-sm font-semibold text-slate-900">Today at a Glance</div>
                <div class="text-xs text-slate-500">Signed in as {{ $user?->name ?? 'Admin' }}</div>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="card-padded">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">Students Present</div>
                            <div class="mt-2 text-3xl font-semibold tracking-tight text-slate-900">{{ number_format($studentsPresent) }}</div>
                            <div class="mt-1 text-sm text-slate-600">Total enrolled: {{ number_format((int) $studentsTotal) }}</div>
                        </div>
                        <div class="grid h-12 w-12 place-items-center rounded-2xl bg-brand-50 text-brand-700 ring-1 ring-inset ring-brand-100">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                <circle cx="9" cy="7" r="4" />
                                <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="card-padded">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">Teachers Present</div>
                            <div class="mt-2 text-3xl font-semibold tracking-tight text-slate-900">{{ number_format($teachersPresent) }}</div>
                            <div class="mt-1 text-sm text-slate-600">Total teachers: {{ number_format((int) $teachersTotal) }}</div>
                        </div>
                        <div class="grid h-12 w-12 place-items-center rounded-2xl bg-purple-50 text-purple-700 ring-1 ring-inset ring-purple-100">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M2 7l10-5 10 5-10 5L2 7z" />
                                <path d="M12 12v10" />
                                <path d="M22 7v10l-10 5" />
                                <path d="M2 7v10l10 5" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="card-padded">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">Classes Running</div>
                            <div class="mt-2 text-3xl font-semibold tracking-tight text-slate-900">{{ number_format($classesRunning) }}</div>
                            <div class="mt-1 text-sm text-slate-600">Total classes: {{ number_format((int) $classesTotal) }}</div>
                        </div>
                        <div class="grid h-12 w-12 place-items-center rounded-2xl bg-indigo-50 text-indigo-700 ring-1 ring-inset ring-indigo-100">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 4h16v12H4z" />
                                <path d="M2 20h20" />
                                <path d="M8 8h8" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-inset ring-orange-200">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">Pending Actions</div>
                            <div class="mt-2 text-3xl font-semibold tracking-tight text-slate-900">{{ number_format((int) $overdueInvoices) }}</div>
                            <div class="mt-1 text-sm text-slate-600">Overdue invoices</div>
                        </div>
                        <div class="grid h-12 w-12 place-items-center rounded-2xl bg-orange-50 text-orange-700 ring-1 ring-inset ring-orange-100">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 8v4l3 3" />
                                <circle cx="12" cy="12" r="10" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="space-y-3">
            <div class="text-sm font-semibold text-slate-900">Academics</div>

            <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
                <div class="card-padded lg:col-span-2">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <div class="text-sm font-semibold text-slate-900">Attendance</div>
                            <div class="mt-1 text-sm text-slate-600">7-day present vs absent trend.</div>
                        </div>
                        <x-status-badge variant="info">This week</x-status-badge>
                    </div>
                    <div class="mt-4">
                        <canvas id="attendanceTrendChart" height="220"></canvas>
                    </div>
                </div>

                <div class="card-padded">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <div class="text-sm font-semibold text-slate-900">Exam Status</div>
                            <div class="mt-1 text-sm text-slate-600">Performance snapshot.</div>
                        </div>
                        <x-status-badge variant="success">Ongoing</x-status-badge>
                    </div>
                    <div class="mt-4">
                        <canvas id="examPerformanceChart" height="220"></canvas>
                    </div>
                </div>

                <div class="card-padded lg:col-span-3">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <div class="text-sm font-semibold text-slate-900">Latest Score Entry</div>
                            <div class="mt-1 text-sm text-slate-600">Most recent updates across classes.</div>
                        </div>
                        <a href="{{ route('results.entry') }}" class="btn-outline">Open Entry</a>
                    </div>

                    <div class="mt-4">
                        <x-table>
                            <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wider text-slate-500">
                                <tr>
                                    <th class="px-5 py-3">Student</th>
                                    <th class="px-5 py-3">Class</th>
                                    <th class="px-5 py-3">Subject</th>
                                    <th class="px-5 py-3 text-right">Total</th>
                                    <th class="px-5 py-3 text-right">Updated</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse ($latestScores as $row)
                                    <tr class="bg-white hover:bg-slate-50">
                                        <td class="px-5 py-4">
                                            <div class="text-sm font-semibold text-slate-900">{{ $row->student?->full_name ?? '—' }}</div>
                                            <div class="mt-1 text-xs text-slate-500">{{ $row->student?->admission_number ?? '' }}</div>
                                        </td>
                                        <td class="px-5 py-4 text-sm text-slate-700">{{ $row->schoolClass?->name ?? '—' }}</td>
                                        <td class="px-5 py-4 text-sm text-slate-700">
                                            <div class="font-medium text-slate-900">{{ $row->subject?->name ?? '—' }}</div>
                                            <div class="mt-1 text-xs text-slate-500">{{ $row->subject?->code ?? '' }}</div>
                                        </td>
                                        <td class="px-5 py-4 text-right text-sm font-semibold text-slate-900">{{ $row->total }}</td>
                                        <td class="px-5 py-4 text-right text-sm text-slate-600">{{ $row->updated_at?->diffForHumans() }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-5 py-10 text-center text-sm text-slate-500">
                                            No score records yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </x-table>
                    </div>
                </div>
            </div>
        </section>

        <section class="space-y-3">
            <div class="text-sm font-semibold text-slate-900">Finance</div>

            <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
                <div class="card-padded">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">Fees Collected</div>
                            <div class="mt-2 text-3xl font-semibold tracking-tight text-slate-900">{{ config('myacademy.currency_symbol') }}{{ number_format($feesCollectedToday, 2) }}</div>
                            <div class="mt-1 text-sm text-slate-600">This week: {{ config('myacademy.currency_symbol') }}{{ number_format($feesCollectedThisWeek, 2) }}</div>
                        </div>
                        <div class="grid h-12 w-12 place-items-center rounded-2xl bg-green-50 text-green-700 ring-1 ring-inset ring-green-100">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 1v22" />
                                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7H14a3.5 3.5 0 0 1 0 7H6" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="card-padded">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">Outstanding Payments</div>
                            <div class="mt-2 text-3xl font-semibold tracking-tight text-slate-900">{{ config('myacademy.currency_symbol') }}{{ number_format($outstandingPaymentsEstimate, 2) }}</div>
                            <div class="mt-1 text-sm text-slate-600">Estimated from tuition fee structures</div>
                        </div>
                        <div class="grid h-12 w-12 place-items-center rounded-2xl bg-brand-50 text-brand-700 ring-1 ring-inset ring-brand-100">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 8v4l3 3" />
                                <circle cx="12" cy="12" r="10" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-inset ring-orange-200">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">Overdue Invoices</div>
                            <div class="mt-2 text-3xl font-semibold tracking-tight text-slate-900">{{ number_format((int) $overdueInvoices) }}</div>
                            <div class="mt-1 text-sm text-slate-600">Students with balance due</div>
                        </div>
                        <div class="grid h-12 w-12 place-items-center rounded-2xl bg-orange-50 text-orange-700 ring-1 ring-inset ring-orange-100">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 8v4l3 3" />
                                <circle cx="12" cy="12" r="10" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    @vite('resources/js/pages/dashboard.js')
@endpush
