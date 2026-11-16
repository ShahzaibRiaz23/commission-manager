<x-app-layout>
    @php
        $card = 'bg-white dark:bg-[#12263A] rounded-2xl border border-[#12263A]/10 dark:border-white/10';
        $ink = 'text-[#12263A] dark:text-slate-100';
        $muted = 'text-slate-500 dark:text-slate-400';
        $field = 'w-full rounded-lg border-slate-300 dark:border-white/20 dark:bg-[#0B1622] dark:text-slate-100 text-sm focus:border-teal-600 focus:ring-teal-600';
    @endphp

    <div class="min-h-screen bg-[#EDF1F5] dark:bg-[#0B1622] py-8 transition-colors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- Header --}}
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
                <div>
                    <h1 class="font-serif text-3xl {{ $ink }} tracking-tight">Edit Project</h1>
                    <p class="text-sm {{ $muted }} mt-1">Update details for <span
                            class="font-semibold {{ $ink }}">{{ $project->name }}</span></p>
                </div>
                <div>
                    <a href="{{ route('projects.show', $project) }}"
                        class="px-4 py-2 rounded-lg border border-[#12263A]/20 dark:border-white/20 text-sm font-medium {{ $ink }} hover:bg-white dark:hover:bg-white/10 transition">
                        Back to project
                    </a>
                </div>
            </div>

            {{-- Form Card --}}
            <div class="{{ $card }} p-8">
                <form action="{{ route('projects.update', $project) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Project Name -->
                    <div>
                        <label for="name"
                            class="block text-xs font-semibold uppercase tracking-wider {{ $muted }} mb-2">Project Name
                            *</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $project->name) }}" required
                            class="{{ $field }}">
                        @error('name')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Client & Amount Grid -->
                    <div class="grid sm:grid-cols-2 gap-6">
                        <div>
                            <label for="client_id"
                                class="block text-xs font-semibold uppercase tracking-wider {{ $muted }} mb-2">Client
                                *</label>
                            <select name="client_id" id="client_id" required class="{{ $field }}">
                                <option value="">Select Client</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" @selected(old('client_id', $project->client_id) == $client->id)>{{ $client->name }}</option>
                                @endforeach
                            </select>
                            @error('client_id')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="project_amount"
                                class="block text-xs font-semibold uppercase tracking-wider {{ $muted }} mb-2">Project
                                Amount ($) *</label>
                            <input type="number" step="0.01" name="project_amount" id="project_amount"
                                value="{{ old('project_amount', $project->project_amount) }}" required
                                class="{{ $field }}">
                            @error('project_amount')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Payment Type & Status Grid -->
                    <div class="grid sm:grid-cols-2 gap-6">
                        <div>
                            <label for="payment_type"
                                class="block text-xs font-semibold uppercase tracking-wider {{ $muted }} mb-2">Payment
                                Type *</label>
                            <select name="payment_type" id="payment_type" required class="{{ $field }}">
                                <option value="one-time" @selected(old('payment_type', $project->payment_type) == 'one-time')>One-time</option>
                                <option value="recurring" @selected(old('payment_type', $project->payment_type) == 'recurring')>Recurring</option>
                            </select>
                            @error('payment_type')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="status"
                                class="block text-xs font-semibold uppercase tracking-wider {{ $muted }} mb-2">Status
                                *</label>
                            <select name="status" id="status" required class="{{ $field }}">
                                <option value="active" @selected(old('status', $project->status) == 'active')>Active
                                </option>
                                <option value="completed" @selected(old('status', $project->status) == 'completed')>
                                    Completed</option>
                                <option value="cancelled" @selected(old('status', $project->status) == 'cancelled')>
                                    Cancelled</option>
                            </select>
                            @error('status')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Assign Experts -->
                    <div>
                        <span id="vas-label"
                            class="block text-xs font-semibold uppercase tracking-wider {{ $muted }} mb-2">Assign
                            Experts</span>
                        @php
                            $assignedVas = collect(old('vas', $project->vas->pluck('id')->toArray()))->all();
                            $expertUsers = $users->filter(fn($user) => $user->hasRole('VA'));

                            // Prepare an array of expert id => name for AlpineJS mapping
                            $expertsMap = $expertUsers->mapWithKeys(function ($user) {
                                $cleanName = preg_replace('/\s*\(VA\)$/i', '', $user->name);
                                return [$user->id => $cleanName];
                            })->toArray();
                        @endphp

                        <div x-data="{ 
                open: false, 
                selectedVas: @js($assignedVas),
                expertsMap: @js($expertsMap),
                get summaryText() {
                    if (this.selectedVas.length === 0) return 'Assign Experts';
                    return this.selectedVas.map(id => this.expertsMap[id]).filter(Boolean).join(', ');
                }
            }" @click.outside="open = false" @keydown.escape.window="open = false" class="relative">

                            <button type="button" aria-labelledby="vas-label vas-selection-summary"
                                aria-controls="vas-options" :aria-expanded="open.toString()" @click="open = !open"
                                class="{{ $field }} flex items-center justify-between gap-3 px-3 py-2.5 text-left border border-slate-300 dark:border-slate-700 rounded-md shadow-sm bg-white dark:bg-[#12263A]">
                                <span id="vas-selection-summary" class="truncate" x-text="summaryText"></span>
                                <svg class="h-4 w-4 shrink-0 text-slate-500 transition-transform"
                                    :class="{ 'rotate-180': open }" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M5.22 7.22a.75.75 0 0 1 1.06 0L10 10.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 8.28a.75.75 0 0 1 0-1.06Z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>

                            <div id="vas-options" x-cloak x-show="open" x-transition style="display: none;"
                                class="absolute z-20 mt-2 max-h-64 w-full overflow-y-auto rounded-lg border border-slate-200 bg-white p-2 shadow-lg dark:border-white/10 dark:bg-[#12263A]">
                                @forelse($expertUsers as $user)
                                    @php
                                        $expertName = preg_replace('/\s*\(VA\)$/i', '', $user->name);
                                    @endphp
                                    <label for="expert-{{ $user->id }}"
                                        class="flex cursor-pointer items-center gap-2 rounded-md p-3 hover:bg-slate-50 dark:hover:bg-white/5">
                                        <input id="expert-{{ $user->id }}" type="checkbox" name="vas[]"
                                            value="{{ $user->id }}" x-model.number="selectedVas"
                                            class="rounded border-slate-300 text-teal-600 focus:ring-teal-600">
                                        <span class="text-sm {{ $ink }}">{{ $expertName }}</span>
                                    </label>
                                @empty
                                    <p class="px-3 py-2 text-sm {{ $muted }}">No experts available.</p>
                                @endforelse
                            </div>
                        </div>
                        @error('vas')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Sales Officer -->
                    @role('Super Admin')
                    <div>
                        <label for="sales_officer_id"
                            class="block text-xs font-semibold uppercase tracking-wider {{ $muted }} mb-2">Sales
                            Officer</label>
                        <select name="sales_officer_id" id="sales_officer_id" class="{{ $field }}">
                            <option value="">Select Sales Officer</option>
                            @foreach($salesOfficers as $officer)
                                <option value="{{ $officer->id }}" @selected(old('sales_officer_id', $project->sales_officer_id) == $officer->id)>
                                    {{ $officer->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endrole

                    <!-- Actions -->
                    <div
                        class="flex items-center justify-end gap-4 pt-4 border-t border-slate-100 dark:border-white/10">
                        <a href="{{ route('projects.show', $project) }}"
                            class="px-5 py-2.5 rounded-lg border border-[#12263A]/20 dark:border-white/20 text-sm font-medium {{ $ink }}">
                            Cancel
                        </a>
                        <button type="submit"
                            class="px-6 py-2.5 rounded-lg bg-teal-600 hover:bg-teal-700 text-sm font-medium text-white transition">
                            Update Project
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</x-app-layout>