<div class="space-y-6">
    <x-page-header title="Billing" subtitle="Record fees and expenses (offline)." accent="billing">
        <x-slot:actions>
            <a href="{{ route('accounts') }}" class="btn-outline">Accounts</a>
        </x-slot:actions>
    </x-page-header>

    <div class="card-padded">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="min-w-0">
                <div class="text-sm font-semibold text-slate-900">New Transaction</div>
                <div class="mt-1 text-sm text-slate-600">Save income (fees) or expenses.</div>
            </div>

            <form wire:submit="saveTransaction" class="grid w-full gap-3 sm:grid-cols-6 lg:max-w-4xl">
                <select
                    wire:model.live="type"
                    class="sm:col-span-1 select"
                >
                    <option value="Income">Income</option>
                    <option value="Expense">Expense</option>
                </select>

                <select
                    wire:model.live="studentId"
                    @disabled($type !== 'Income')
                    class="sm:col-span-2 select"
                >
                    <option value="">Student (Income only)</option>
                    @foreach ($this->students as $student)
                        <option value="{{ $student->id }}">
                            {{ $student->full_name }} ({{ $student->admission_number }})
                        </option>
                    @endforeach
                </select>

                <input
                    wire:model.live="category"
                    type="text"
                    placeholder="Category (e.g. Tuition)"
                    class="sm:col-span-1 input-compact"
                />

                <input
                    wire:model.live="amountPaid"
                    type="number"
                    step="0.01"
                    placeholder="Amount"
                    class="sm:col-span-1 input-compact"
                />

                <button
                    type="submit"
                    class="sm:col-span-1 btn-primary"
                >
                    Save
                </button>
            </form>
        </div>

        <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-6">
            <div class="sm:col-span-2">
                <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Payment Method</label>
                <select
                    wire:model.live="paymentMethod"
                    @disabled($type !== 'Income')
                    class="mt-2 select"
                >
                    <option value="Cash">Cash</option>
                    <option value="Transfer">Transfer</option>
                    <option value="POS">POS</option>
                </select>
            </div>

            <div class="sm:col-span-2">
                <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Session</label>
                <input
                    wire:model.live="session"
                    @disabled($type !== 'Income')
                    type="text"
                    placeholder="2025/2026"
                    class="mt-2 input-compact"
                />
            </div>

            <div class="sm:col-span-1">
                <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Term</label>
                <select
                    wire:model.live="term"
                    @disabled($type !== 'Income')
                    class="mt-2 select"
                >
                    <option value="">-</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                </select>
            </div>

            <div class="sm:col-span-1">
                <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Date</label>
                <input
                    wire:model.live="date"
                    type="date"
                    class="mt-2 w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700 shadow-sm focus:border-brand-500 focus:ring-brand-500"
                />
            </div>
        </div>

        <div class="mt-6 flex gap-6 border-b border-gray-100">
            <button
                type="button"
                wire:click="$set('tab', 'transactions')"
                class="{{ $tab === 'transactions' ? 'border-b-2 border-brand-500 text-brand-700' : 'border-b-2 border-transparent text-gray-600 hover:text-gray-900' }} -mb-px pb-3 text-sm font-semibold"
            >
                Transactions
            </button>
            <button
                type="button"
                wire:click="$set('tab', 'debtors')"
                class="{{ $tab === 'debtors' ? 'border-b-2 border-brand-500 text-brand-700' : 'border-b-2 border-transparent text-gray-600 hover:text-gray-900' }} -mb-px pb-3 text-sm font-semibold"
            >
                Debtors
            </button>
        </div>
    </div>

    @if ($tab === 'transactions')
        <div class="card-padded">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="text-sm font-semibold text-gray-900">Filters</div>
                    <div class="mt-1 text-sm text-gray-600">Narrow the transaction list (up to 100 rows).</div>
                </div>
                <div class="flex flex-wrap items-center gap-3 text-xs text-gray-500">
                    <a
                        href="{{ route('billing.export.transactions', [
                            'type' => $filterType,
                            'category' => $filterCategory,
                            'student_id' => $filterStudentId,
                            'session' => $filterSession,
                            'term' => $filterTerm,
                            'from' => $filterFrom,
                            'to' => $filterTo,
                            'include_voided' => $includeVoided ? 1 : 0,
                        ]) }}"
                        class="underline"
                    >
                        Export CSV
                    </a>
                    <a href="{{ route('billing.index') }}" class="underline">Reset</a>
                </div>
            </div>

            <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-6">
                <div class="lg:col-span-2">
                    <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Category</label>
                    <input wire:model.live.debounce.300ms="filterCategory" type="text" placeholder="e.g. Tuition" class="mt-2 input-compact" />
                </div>

                <div class="lg:col-span-2">
                    <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Student</label>
                    <select wire:model.live="filterStudentId" class="mt-2 select">
                        <option value="">All</option>
                        @foreach ($this->students as $student)
                            <option value="{{ $student->id }}">{{ $student->full_name }} ({{ $student->admission_number }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="lg:col-span-1">
                    <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Type</label>
                    <select wire:model.live="filterType" class="mt-2 select">
                        <option value="">All</option>
                        <option value="Income">Income</option>
                        <option value="Expense">Expense</option>
                    </select>
                </div>

                <div class="lg:col-span-1">
                    <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Voided</label>
                    <select wire:model.live="includeVoided" class="mt-2 select">
                        <option value="0">Hide</option>
                        <option value="1">Show</option>
                    </select>
                </div>

                <div class="lg:col-span-2">
                    <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Session</label>
                    <input wire:model.live="filterSession" type="text" placeholder="2025/2026" class="mt-2 input-compact" />
                </div>

                <div class="lg:col-span-1">
                    <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Term</label>
                    <select wire:model.live="filterTerm" class="mt-2 select">
                        <option value="">All</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                    </select>
                </div>

                <div class="lg:col-span-1">
                    <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">From</label>
                    <input wire:model.live="filterFrom" type="date" class="mt-2 w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700 shadow-sm focus:border-brand-500 focus:ring-brand-500" />
                </div>

                <div class="lg:col-span-1">
                    <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">To</label>
                    <input wire:model.live="filterTo" type="date" class="mt-2 w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700 shadow-sm focus:border-brand-500 focus:ring-brand-500" />
                </div>
            </div>
        </div>
    @endif

    @if ($tab === 'debtors')
        <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
            <div class="text-sm font-semibold text-gray-800">Debtors List ({{ $category }})</div>
            <div class="mt-2 text-sm text-gray-600">Uses the configured fee structure per class and sums recorded payments.</div>
        </div>

        <x-table>
            <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wider text-gray-500">
                <tr>
                    <th class="px-5 py-3">Student</th>
                    <th class="px-5 py-3">Class</th>
                    <th class="px-5 py-3 text-right">Due</th>
                    <th class="px-5 py-3 text-right">Paid</th>
                    <th class="px-5 py-3 text-right">Balance</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($this->debtors as $row)
                    <tr class="bg-white hover:bg-gray-50">
                        <td class="px-5 py-4">
                            <div class="text-sm font-semibold text-gray-900">{{ $row['student']->full_name }}</div>
                            <div class="mt-1 text-xs text-gray-500">{{ $row['student']->admission_number }}</div>
                        </td>
                        <td class="px-5 py-4 text-sm text-gray-700">
                            {{ $row['student']->schoolClass?->name }} / {{ $row['student']->section?->name }}
                        </td>
                        <td class="px-5 py-4 text-right text-sm font-semibold text-gray-900">{{ config('myacademy.currency_symbol') }}{{ number_format($row['due'], 2) }}</td>
                        <td class="px-5 py-4 text-right text-sm font-semibold text-gray-900">{{ config('myacademy.currency_symbol') }}{{ number_format($row['paid'], 2) }}</td>
                        <td class="px-5 py-4 text-right">
                            <x-status-badge variant="warning">{{ config('myacademy.currency_symbol') }}{{ number_format($row['balance'], 2) }}</x-status-badge>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-10 text-center text-sm text-gray-500">No debtors found.</td>
                    </tr>
                @endforelse
            </tbody>
        </x-table>
    @else
        <x-table>
            <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wider text-gray-500">
                <tr>
                    <th class="px-5 py-3">Date</th>
                    <th class="px-5 py-3">Student</th>
                    <th class="px-5 py-3">Type</th>
                    <th class="px-5 py-3">Category</th>
                    <th class="px-5 py-3 text-right">Amount</th>
                    <th class="px-5 py-3">Receipt</th>
                    <th class="px-5 py-3 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($this->transactions as $t)
                    <tr class="bg-white hover:bg-gray-50">
                        <td class="px-5 py-4 text-sm text-gray-700">{{ $t->date?->format('M j, Y') }}</td>
                        <td class="px-5 py-4">
                            <div class="text-sm font-semibold text-gray-900">{{ $t->student?->full_name ?: '-' }}</div>
                            <div class="mt-1 text-xs text-gray-500">{{ $t->student?->admission_number ?: '' }}</div>
                        </td>
                        <td class="px-5 py-4">
                            @if ($t->is_void)
                                <x-status-badge variant="warning">Voided</x-status-badge>
                            @elseif ($t->type === 'Income')
                                <x-status-badge variant="success">Income</x-status-badge>
                            @else
                                <x-status-badge variant="warning">Expense</x-status-badge>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-sm text-gray-700">
                            <div class="font-medium text-gray-900">{{ $t->category }}</div>
                            <div class="mt-1 text-xs text-gray-500">
                                @if ($t->session)
                                    {{ $t->session }}
                                @endif
                                @if ($t->term)
                                    &middot; Term {{ $t->term }}
                                @endif
                            </div>
                        </td>
                        <td class="px-5 py-4 text-right text-sm font-semibold text-gray-900">{{ config('myacademy.currency_symbol') }}{{ number_format((float) $t->amount_paid, 2) }}</td>
                        <td class="px-5 py-4 text-sm font-medium text-gray-700">
                            <div class="flex items-center gap-2">
                                <span>{{ $t->receipt_number ?: '-' }}</span>
                                @if ($t->is_void)
                                    <x-status-badge variant="warning">VOID</x-status-badge>
                                @endif
                            </div>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <div class="flex flex-wrap justify-end gap-2">
                                @if ($t->receipt_number)
                                    <a
                                        href="{{ route('billing.receipt', $t) }}"
                                        class="{{ $t->is_void ? 'opacity-60' : '' }} inline-flex items-center justify-center rounded-lg bg-white px-3 py-2 text-sm font-semibold text-brand-700 ring-1 ring-inset ring-brand-100 hover:bg-brand-50"
                                    >
                                        Receipt
                                    </a>
                                @endif

                                @if (! $t->is_void)
                                    <button type="button" wire:click="startVoid({{ $t->id }})" class="btn-ghost">
                                        Void
                                    </button>
                                @else
                                    <span class="text-sm text-gray-400">-</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @if ($voidingTransactionId === $t->id)
                        <tr class="bg-slate-50">
                            <td colspan="7" class="px-5 py-4">
                                <div class="grid grid-cols-1 gap-3 sm:grid-cols-6">
                                    <div class="sm:col-span-4">
                                        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Void reason (optional)</label>
                                        <input wire:model.live="voidReason" type="text" class="mt-2 input-compact" placeholder="e.g. Duplicate entry" />
                                    </div>
                                    <div class="sm:col-span-2 flex items-end justify-end gap-2">
                                        <button type="button" wire:click="cancelVoid" class="btn-outline">Cancel</button>
                                        <button type="button" wire:click="confirmVoid({{ $t->id }})" class="btn-primary">Confirm Void</button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-10 text-center text-sm text-gray-500">No transactions yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </x-table>
    @endif
</div>
