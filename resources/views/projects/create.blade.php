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
                    <h1 class="font-serif text-3xl {{ $ink }} tracking-tight">Create Project</h1>
                    <p class="text-sm {{ $muted }} mt-1">Add a new project and assign team members.</p>
                </div>
                <div>
                    <a href="{{ route('projects.index') }}"
                        class="px-4 py-2 rounded-lg border border-[#12263A]/20 dark:border-white/20 text-sm font-medium {{ $ink }} hover:bg-white dark:hover:bg-white/10 transition">
                        Back to projects
                    </a>
                </div>
            </div>

            {{-- Form Card --}}
            <div class="{{ $card }} p-8">
                <form action="{{ route('projects.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Project Name -->
                    <div>
                        <label for="name"
                            class="block text-xs font-semibold uppercase tracking-wider {{ $muted }} mb-2">Project Name
                            *</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required class="{{ $field }}"
                            placeholder="Enter project name">
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
                                    <option value="{{ $client->id }}" @selected(old('client_id') == $client->id)>
                                        {{ $client->name }}</option>
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
                                value="{{ old('project_amount') }}" required class="{{ $field }}" placeholder="0.00">
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
                                <option value="one-time" @selected(old('payment_type') == 'one-time')>One-time</option>
                                <option value="recurring" @selected(old('payment_type') == 'recurring')>Recurring</option>
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
                                <option value="active" @selected(old('status', 'active') == 'active')>Active</option>
                                <option value="pending" @selected(old('status') == 'pending')>Pending</option>
                                <option value="completed" @selected(old('status') == 'completed')>Completed</option>
                            </select>
                            @error('status')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Commission Percentages -->
                    <div class="grid sm:grid-cols-2 gap-6">
                        <div>
                            <label for="va_commission_percentage"
                                class="block text-xs font-semibold uppercase tracking-wider {{ $muted }} mb-2">VA
                                Commission (%)</label>
                            <input type="number" step="0.01" name="va_commission_percentage"
                                id="va_commission_percentage" value="{{ old('va_commission_percentage', '10.00') }}"
                                class="{{ $field }}">
                            @error('va_commission_percentage')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="sales_commission_percentage"
                                class="block text-xs font-semibold uppercase tracking-wider {{ $muted }} mb-2">Sales
                                Commission (%)</label>
                            <input type="number" step="0.01" name="sales_commission_percentage"
                                id="sales_commission_percentage"
                                value="{{ old('sales_commission_percentage', '10.00') }}" class="{{ $field }}">
                            @error('sales_commission_percentage')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Assign VAs -->
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider {{ $muted }} mb-2">Assign
                            Virtual Assistants (VAs)</label>
                        <div class="grid sm:grid-cols-3 gap-3">
                            @foreach($users as $user)
                                <label
                                    class="flex items-center space-x-2 p-3 rounded-lg border border-slate-200 dark:border-white/10 hover:bg-slate-50 dark:hover:bg-white/5 cursor-pointer">
                                    <input type="checkbox" name="vas[]" value="{{ $user->id }}" @checked(in_array($user->id, old('vas', []))) class="rounded border-slate-300 text-teal-600 focus:ring-teal-600">
                                    <span class="text-sm {{ $ink }}">{{ $user->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Sales Officer -->
                    <div>
                        <label for="sales_officer_id"
                            class="block text-xs font-semibold uppercase tracking-wider {{ $muted }} mb-2">Sales
                            Officer</label>
                        <select name="sales_officer_id" id="sales_officer_id" class="{{ $field }}">
                            <option value="">Select Sales Officer</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" @selected(old('sales_officer_id') == $user->id)>
                                    {{ $user->name }}</option>
                            @endforeach
                        </select>
                        @error('sales_officer_id')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Actions -->
                    <div
                        class="flex items-center justify-end gap-4 pt-4 border-t border-slate-100 dark:border-white/10">
                        <a href="{{ route('projects.index') }}"
                            class="px-5 py-2.5 rounded-lg border border-[#12263A]/20 dark:border-white/20 text-sm font-medium {{ $ink }}">
                            Cancel
                        </a>
                        <button type="submit"
                            class="px-6 py-2.5 rounded-lg bg-teal-600 hover:bg-teal-700 text-sm font-medium text-white transition">
                            Create Project
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</x-app-layout>