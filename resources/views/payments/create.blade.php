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
                    <h1 class="font-serif text-3xl {{ $ink }} tracking-tight">Record Payment</h1>
                    <p class="text-sm {{ $muted }} mt-1">Record a payment for project: <span
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
                <form action="{{ route('payments.store', $project) }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Payment Amount & Type Grid -->
                    <div class="grid sm:grid-cols-2 gap-6">
                        <div>
                            <label for="payment_amount"
                                class="block text-xs font-semibold uppercase tracking-wider {{ $muted }} mb-2">Payment
                                Amount ($) *</label>
                            <input type="number" step="0.01" name="payment_amount" id="payment_amount"
                                value="{{ old('payment_amount', $project->project_amount) }}" required
                                class="{{ $field }}" placeholder="0.00">
                            @error('payment_amount')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

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
                    </div>

                    <!-- Payment Date & Status Grid -->
                    <div class="grid sm:grid-cols-2 gap-6">
                        <div>
                            <label for="payment_date"
                                class="block text-xs font-semibold uppercase tracking-wider {{ $muted }} mb-2">Payment
                                Date
                                *</label>
                            <input type="date" name="payment_date" id="payment_date"
                                value="{{ old('payment_date', date('Y-m-d')) }}" required class="{{ $field }}">
                            @error('payment_date')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="status"
                                class="block text-xs font-semibold uppercase tracking-wider {{ $muted }} mb-2">Status
                                *</label>
                            <select name="status" id="status" required class="{{ $field }}">
                                <option value="pending" @selected(old('status') == 'pending')>Pending</option>
                                <option value="approved" @selected(old('status') == 'approved')>Approved</option>
                                <option value="paid" @selected(old('status', 'paid') == 'paid')>Paid</option>
                            </select>
                            @error('status')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Actions -->
                    <div
                        class="flex items-center justify-end gap-4 pt-4 border-t border-slate-100 dark:border-white/10">
                        <a href="{{ route('projects.show', $project) }}"
                            class="px-5 py-2.5 rounded-lg border border-[#12263A]/20 dark:border-white/20 text-sm font-medium {{ $ink }} hover:bg-white dark:hover:bg-white/10 transition">
                            Cancel
                        </a>
                        <button type="submit"
                            class="px-6 py-2.5 rounded-lg bg-teal-600 hover:bg-teal-700 text-sm font-medium text-white transition focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600 focus-visible:ring-offset-2">
                            Save Payment
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</x-app-layout>