<div class="space-y-6">
    <div class="card-padded">
        <div class="text-sm font-semibold text-slate-900">Start CBT Exam</div>
        <div class="mt-1 text-sm text-slate-600">Enter the exam code and your admission number to begin.</div>

        <form wire:submit="start" class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-6">
            <div class="sm:col-span-2">
                <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Exam Code</label>
                <input wire:model="examCode" class="mt-2 input w-full font-mono uppercase" placeholder="CBT-ABC123" />
                @error('examCode') <div class="mt-1 text-xs font-semibold text-rose-700">{{ $message }}</div> @enderror
            </div>

            <div class="sm:col-span-2">
                <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Admission Number</label>
                <input wire:model="admissionNumber" class="mt-2 input w-full" placeholder="e.g. ADM/001" />
                @error('admissionNumber') <div class="mt-1 text-xs font-semibold text-rose-700">{{ $message }}</div> @enderror
            </div>

            <div class="sm:col-span-2">
                <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Surname (optional)</label>
                <input wire:model="surname" class="mt-2 input w-full" placeholder="Surname" />
                @error('surname') <div class="mt-1 text-xs font-semibold text-rose-700">{{ $message }}</div> @enderror
            </div>

            <div class="sm:col-span-2">
                <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Exam PIN (if required)</label>
                <input wire:model="pin" class="mt-2 input w-full font-mono" placeholder="PIN" />
                @error('pin') <div class="mt-1 text-xs font-semibold text-rose-700">{{ $message }}</div> @enderror
            </div>

            <div class="sm:col-span-6 flex flex-wrap items-center gap-2 pt-1">
                <button type="submit" class="btn-primary">Start Exam</button>
                <div class="text-xs text-gray-600">
                    Tip: If the surname is enabled by your school, it helps confirm identity.
                </div>
            </div>
        </form>
    </div>
</div>
