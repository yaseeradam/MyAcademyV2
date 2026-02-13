<div class="space-y-6">
    <x-page-header
        :title="$student ? 'Edit Student' : 'Add Student'"
        subtitle="Admissions and student bio-data."
        accent="students"
    >
        <x-slot:actions>
            <a href="{{ route('students.index') }}" class="btn-ghost">Back</a>
        </x-slot:actions>
    </x-page-header>

    <form wire:submit="save" class="space-y-6">
        <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
            <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm lg:col-span-2">
                <div class="text-sm font-semibold text-gray-900">Bio</div>

                <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <div class="flex items-center justify-between">
                            <label class="text-sm font-semibold text-gray-700">Admission Number</label>
                            @if(!$student)
                                <label class="flex items-center gap-2 text-xs text-gray-600">
                                    <input type="checkbox" wire:model.live="auto_admission" class="rounded">
                                    Auto-generate
                                </label>
                            @endif
                        </div>
                        <input
                            wire:model.live="admission_number"
                            type="text"
                            @if($auto_admission && !$student) readonly @endif
                            class="mt-2 w-full rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm text-gray-700 shadow-sm focus:border-brand-500 focus:ring-brand-500 {{ $auto_admission && !$student ? 'bg-gray-50' : '' }}"
                        />
                        @error('admission_number')
                            <div class="mt-2 text-sm text-orange-700">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-gray-700">Gender</label>
                        <select
                            wire:model.live="gender"
                            class="mt-2 w-full rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm text-gray-700 shadow-sm focus:border-brand-500 focus:ring-brand-500"
                        >
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                        @error('gender')
                            <div class="mt-2 text-sm text-orange-700">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-gray-700">First Name</label>
                        <input
                            wire:model.live="first_name"
                            type="text"
                            class="mt-2 w-full rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm text-gray-700 shadow-sm focus:border-brand-500 focus:ring-brand-500"
                        />
                        @error('first_name')
                            <div class="mt-2 text-sm text-orange-700">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-gray-700">Last Name</label>
                        <input
                            wire:model.live="last_name"
                            type="text"
                            class="mt-2 w-full rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm text-gray-700 shadow-sm focus:border-brand-500 focus:ring-brand-500"
                        />
                        @error('last_name')
                            <div class="mt-2 text-sm text-orange-700">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-gray-700">Class</label>
                        <select
                            wire:model.live="class_id"
                            class="mt-2 w-full rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm text-gray-700 shadow-sm focus:border-brand-500 focus:ring-brand-500"
                        >
                            <option value="">Select class</option>
                            @foreach ($this->classes as $class)
                                <option value="{{ $class->id }}">{{ $class->name }}</option>
                            @endforeach
                        </select>
                        @error('class_id')
                            <div class="mt-2 text-sm text-orange-700">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-gray-700">Section</label>
                        <select
                            wire:model.live="section_id"
                            @disabled(! $class_id)
                            class="mt-2 w-full rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm text-gray-700 shadow-sm focus:border-brand-500 focus:ring-brand-500 disabled:bg-gray-50 disabled:text-gray-400"
                        >
                            <option value="">Select section</option>
                            @foreach ($this->sections as $section)
                                <option value="{{ $section->id }}">{{ $section->name }}</option>
                            @endforeach
                        </select>
                        @error('section_id')
                            <div class="mt-2 text-sm text-orange-700">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-gray-700">Date of Birth</label>
                        <input
                            wire:model.live="dob"
                            type="date"
                            class="mt-2 w-full rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm text-gray-700 shadow-sm focus:border-brand-500 focus:ring-brand-500"
                        />
                        @error('dob')
                            <div class="mt-2 text-sm text-orange-700">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-gray-700">Blood Group</label>
                        <input
                            wire:model.live="blood_group"
                            type="text"
                            placeholder="e.g. O+"
                            class="mt-2 w-full rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm text-gray-700 shadow-sm focus:border-brand-500 focus:ring-brand-500"
                        />
                        @error('blood_group')
                            <div class="mt-2 text-sm text-orange-700">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                    <div class="text-sm font-semibold text-gray-900">Passport Photo</div>

                    <div class="mt-5">
                        @if ($passport)
                            <div class="flex items-center gap-3">
                                <img
                                    src="{{ $passport->temporaryUrl() }}"
                                    alt="Passport preview"
                                    class="h-20 w-20 rounded-xl object-cover ring-1 ring-inset ring-gray-200"
                                />
                                <div class="text-xs text-gray-500">Preview (not saved yet)</div>
                            </div>
                        @elseif ($student?->passport_photo_url)
                            <div class="flex items-center gap-3">
                                <img
                                    src="{{ $student->passport_photo_url }}"
                                    alt="{{ $student->full_name }}"
                                    class="h-20 w-20 rounded-xl object-cover ring-1 ring-inset ring-gray-200"
                                />
                                <div class="text-xs text-gray-500">Current photo</div>
                            </div>
                        @endif

                        <input
                            type="file"
                            wire:model="passport"
                            accept="image/*"
                            class="mt-3 block w-full text-sm text-gray-700 file:mr-4 file:rounded-lg file:border-0 file:bg-gray-100 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-gray-700 hover:file:bg-gray-200"
                        />
                        @error('passport')
                            <div class="mt-2 text-sm text-orange-700">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                    <div class="text-sm font-semibold text-gray-900">Guardian</div>

                    <div class="mt-5 space-y-4">
                        <div>
                            <label class="text-sm font-semibold text-gray-700">Name</label>
                            <input
                                wire:model.live="guardian_name"
                                type="text"
                                class="mt-2 w-full rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm text-gray-700 shadow-sm focus:border-brand-500 focus:ring-brand-500"
                            />
                            @error('guardian_name')
                                <div class="mt-2 text-sm text-orange-700">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-gray-700">Phone</label>
                            <input
                                wire:model.live="guardian_phone"
                                type="text"
                                class="mt-2 w-full rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm text-gray-700 shadow-sm focus:border-brand-500 focus:ring-brand-500"
                            />
                            @error('guardian_phone')
                                <div class="mt-2 text-sm text-orange-700">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-gray-700">Address</label>
                            <textarea
                                wire:model.live="guardian_address"
                                rows="3"
                                class="mt-2 w-full rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm text-gray-700 shadow-sm focus:border-brand-500 focus:ring-brand-500"
                            ></textarea>
                            @error('guardian_address')
                                <div class="mt-2 text-sm text-orange-700">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                    <div class="text-sm font-semibold text-gray-900">Status</div>

                    <div class="mt-5 space-y-4">
                        <select
                            wire:model.live="status"
                            class="w-full rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm text-gray-700 shadow-sm focus:border-brand-500 focus:ring-brand-500"
                        >
                            <option value="Active">Active</option>
                            <option value="Graduated">Graduated</option>
                            <option value="Expelled">Expelled</option>
                        </select>
                        @error('status')
                            <div class="text-sm text-orange-700">{{ $message }}</div>
                        @enderror

                        <button
                            type="submit"
                            class="inline-flex w-full items-center justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-600"
                        >
                            Save Student
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
