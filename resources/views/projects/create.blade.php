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
                    <h1 class="font-serif text-3xl {{ $ink }} tracking-tight">Create New Project</h1>
                    <p class="text-sm {{ $muted }} mt-1">Set up project details, pricing structures, and team
                        assignments.</p>
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
                            placeholder="e.g. GoHighLevel CRM Integration">
                        @error('name')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Client Selection -->
                    <div>
                        <label for="client_id"
                            class="block text-xs font-semibold uppercase tracking-wider {{ $muted }} mb-2">Client
                            *</label>
                        <select name="client_id" id="client_id" required class="{{ $field }}">
                            <option value="">Select a client</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" @selected(old('client_id') == $client->id)>
                                    {{ $client->name }} @if($client->company_name) ({{ $client->company_name }}) @endif
                                </option>
                            @endforeach
                        </select>
                        @error('client_id')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Project Amount & Payment Type Grid -->
                    <div class="grid sm:grid-cols-2 gap-6">
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

                        <div>
                            <label for="payment_type"
                                class="block text-xs font-semibold uppercase tracking-wider {{ $muted }} mb-2">Payment
                                Type *</label>
                            <select name="payment_type" id="payment_type" required class="{{ $field }}">
                                <option value="one_time" @selected(old('payment_type') == 'one_time')>One-time</option>
                                <option value="recurring" @selected(old('payment_type') == 'recurring')>Recurring</option>
                            </select>
                            @error('payment_type')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Team Assignments Grid (VA & Sales Officer) -->
                    <div class="grid sm:grid-cols-2 gap-6">
                        <div>
                            <label for="va_id"
                                class="block text-xs font-semibold uppercase tracking-wider {{ $muted }} mb-2">Assign
                                VA</label>
                            <select name="va_id" id="va_id" class="{{ $field }}">
                                <option value="">None</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" @selected(old('va_id') == $user->id)>{{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('va_id')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="sales_officer_id"
                                class="block text-xs font-semibold uppercase tracking-wider {{ $muted }} mb-2">Assign
                                Sales Officer</label>
                            <select name="sales_officer_id" id="sales_officer_id" class="{{ $field }}">
                                <option value="">None</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" @selected(old('sales_officer_id') == $user->id)>
                                        {{ $user->name }}</option>
                                @endforeach
                            </select>
                            @error('sales_officer_id')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Project Status -->
                    <div>
                        <label for="status"
                            class="block text-xs font-semibold uppercase tracking-wider {{ $muted }} mb-2">Project
                            Status *</label>
                        <select name="status" id="status" required class="{{ $field }}">
                            <option value="pending" @selected(old('status') == 'pending')>Pending</option>
                            <option value="active" @selected(old('status') == 'active')>Active</option>
                            <option value="on_hold" @selected(old('status') == 'on_hold')>On Hold</option>
                            <option value="completed" @selected(old('status') == 'completed')>Completed</option>
                            <option value="cancelled" @selected(old('status') == 'cancelled')>Cancelled</option>
                        </select>
                        @error('status')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Actions -->
                    <div
                        class="flex items-center justify-end gap-4 pt-4 border-t border-slate-100 dark:border-white/10">
                        <a href="{{ route('projects.index') }}"
                            class="px-5 py-2.5 rounded-lg border border-[#12263A]/20 dark:border-white/20 text-sm font-medium {{ $ink }} hover:bg-white dark:hover:bg-white/10 transition">
                            Cancel
                        </a>
                        <button type="submit"
                            class="px-6 py-2.5 rounded-lg bg-teal-600 hover:bg-teal-700 text-sm font-medium text-white transition focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600 focus-visible:ring-offset-2">
                            Save Project
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</x-app-layout>