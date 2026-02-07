<?php

namespace App\Livewire\Billing;

use App\Models\FeeStructure;
use App\Models\Student;
use App\Models\Transaction;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Billing')]
class Index extends Component
{
    public string $tab = 'transactions';

    public ?int $studentId = null;
    public string $type = 'Income';
    public string $category = 'Tuition';
    public ?int $term = null;
    public ?string $session = null;
    public string $paymentMethod = 'Cash';
    public string $amountPaid = '';
    public string $date = '';

    public ?string $filterType = null;
    public string $filterCategory = '';
    public ?int $filterStudentId = null;
    public ?string $filterSession = null;
    public ?int $filterTerm = null;
    public ?string $filterFrom = null;
    public ?string $filterTo = null;
    public bool $includeVoided = false;

    public ?int $voidingTransactionId = null;
    public string $voidReason = '';

    public function mount(): void
    {
        $this->tab = request('tab', 'transactions');
        $this->date = now()->toDateString();
        $this->session = $this->session ?? $this->defaultSession();
        $this->term = $this->term ?? 1;

        $this->filterFrom = now()->subDays(30)->toDateString();
        $this->filterTo = now()->toDateString();
    }

    #[Computed]
    public function students()
    {
        return Student::query()
            ->with(['schoolClass', 'section'])
            ->orderBy('last_name')
            ->get();
    }

    #[Computed]
    public function transactions()
    {
        $query = Transaction::query()
            ->with('student')
            ->orderByDesc('date')
            ->orderByDesc('id');

        if (! $this->includeVoided) {
            $query->where('is_void', false);
        }

        if ($this->filterType) {
            $query->where('type', $this->filterType);
        }

        if ($this->filterCategory) {
            $q = trim($this->filterCategory);
            $query->where('category', 'like', "%{$q}%");
        }

        if ($this->filterStudentId) {
            $query->where('student_id', $this->filterStudentId);
        }

        if ($this->filterSession) {
            $query->where('session', $this->filterSession);
        }

        if ($this->filterTerm) {
            $query->where('term', $this->filterTerm);
        }

        if ($this->filterFrom) {
            $query->whereDate('date', '>=', $this->filterFrom);
        }

        if ($this->filterTo) {
            $query->whereDate('date', '<=', $this->filterTo);
        }

        return $query->limit(100)->get();
    }

    #[Computed]
    public function debtors()
    {
        $category = $this->category ?: 'Tuition';

        $feesByClass = FeeStructure::query()
            ->where('category', $category)
            ->whereNull('term')
            ->whereNull('session')
            ->pluck('amount_due', 'class_id');

        $paidByStudent = Transaction::query()
            ->where('type', 'Income')
            ->where('category', $category)
            ->where('is_void', false)
            ->selectRaw('student_id, COALESCE(SUM(amount_paid),0) as paid')
            ->groupBy('student_id')
            ->pluck('paid', 'student_id');

        return Student::query()
            ->with(['schoolClass', 'section'])
            ->where('status', 'Active')
            ->get()
            ->map(function (Student $student) use ($feesByClass, $paidByStudent) {
                $due = (float) ($feesByClass[$student->class_id] ?? 0);
                $paid = (float) ($paidByStudent[$student->id] ?? 0);
                $balance = max(0, $due - $paid);

                return [
                    'student' => $student,
                    'due' => $due,
                    'paid' => $paid,
                    'balance' => $balance,
                ];
            })
            ->filter(fn (array $row) => $row['balance'] > 0)
            ->sortByDesc('balance')
            ->values();
    }

    public function saveTransaction(): void
    {
        $data = $this->validate([
            'studentId' => [
                Rule::requiredIf(fn () => $this->type === 'Income'),
                'nullable',
                'integer',
                Rule::exists('students', 'id'),
            ],
            'type' => ['required', Rule::in(['Income', 'Expense'])],
            'category' => ['required', 'string', 'max:255'],
            'term' => ['nullable', 'integer', 'between:1,3'],
            'session' => ['nullable', 'string', 'max:9'],
            'paymentMethod' => ['nullable', Rule::in(['Cash', 'Transfer', 'POS'])],
            'amountPaid' => ['required', 'numeric', 'min:0'],
            'date' => ['required', 'date'],
        ]);

        Transaction::query()->create([
            'student_id' => $data['studentId'],
            'type' => $data['type'],
            'category' => $data['category'],
            'term' => $data['type'] === 'Income' ? $data['term'] : null,
            'session' => $data['type'] === 'Income' ? $data['session'] : null,
            'payment_method' => $data['type'] === 'Income' ? $data['paymentMethod'] : null,
            'amount_paid' => $data['amountPaid'],
            'date' => Carbon::parse($data['date'])->toDateString(),
        ]);

        $this->reset(['studentId', 'amountPaid']);
        $this->amountPaid = '';

        $this->dispatch('alert', message: 'Transaction saved.', type: 'success');
    }

    public function startVoid(int $transactionId): void
    {
        $this->voidingTransactionId = $transactionId;
        $this->voidReason = '';
    }

    public function cancelVoid(): void
    {
        $this->voidingTransactionId = null;
        $this->voidReason = '';
    }

    public function confirmVoid(int $transactionId): void
    {
        $data = $this->validate([
            'voidReason' => ['nullable', 'string', 'max:255'],
        ]);

        $transaction = Transaction::query()->findOrFail($transactionId);
        if ($transaction->is_void) {
            return;
        }

        $transaction->update([
            'is_void' => true,
            'void_reason' => $data['voidReason'] ?: null,
            'voided_at' => now(),
            'voided_by' => auth()->id(),
        ]);

        $this->cancelVoid();
        $this->dispatch('alert', message: 'Transaction voided.', type: 'warning');
    }

    private function defaultSession(): string
    {
        $year = (int) now()->format('Y');
        $next = $year + 1;

        return "{$year}/{$next}";
    }

    public function render()
    {
        return view('livewire.billing.index');
    }
}
