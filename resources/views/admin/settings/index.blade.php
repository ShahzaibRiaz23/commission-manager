<x-app-layout>
    @php
        $card = 'bg-white dark:bg-[#12263A] rounded-2xl border border-[#12263A]/10 dark:border-white/10';
        $ink = 'text-[#12263A] dark:text-slate-100';
        $muted = 'text-slate-500 dark:text-slate-400';
        $field = 'w-full rounded-lg border-slate-300 bg-white text-[#12263A] dark:border-white/20 dark:bg-[#0B1622] dark:text-slate-100 text-sm placeholder:text-slate-400 dark:placeholder:text-slate-500 transition-colors focus:border-teal-600 focus:ring-teal-600';
    @endphp

    <div class="min-h-screen bg-[#EDF1F5] dark:bg-[#0B1622] py-8 transition-colors">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- Header --}}
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="font-serif text-3xl {{ $ink }} tracking-tight">Default Commission Settings</h1>
                    <p class="text-sm {{ $muted }} mt-1">Configure default percentages for One-time and Recurring projects.</p>
                </div>
                <a href="{{ route('dashboard') }}"
                    class="px-4 py-2 rounded-lg border border-[#12263A]/20 dark:border-white/20 text-sm font-medium {{ $ink }} hover:bg-white dark:hover:bg-white/10 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-[#0B1622]">
                    Back to Dashboard
                </a>
            </div>

            @if(session('success'))
                <div class="p-4 rounded-xl bg-teal-600/10 border border-teal-600/30 text-teal-700 dark:text-teal-300 text-sm font-medium">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Form Card --}}
            <div class="{{ $card }} p-8 transition-colors">
                <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid sm:grid-cols-2 gap-6">
                        <!-- One-Time Defaults -->
                        <div class="space-y-4 p-4 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-[#0B1622]/50">
                            <h3 class="font-semibold text-base {{ $ink }}">One-Time Project Defaults</h3>

                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider {{ $muted }} mb-2">
                                    Experts Commission (%)
                                </label>
                                <input type="number" step="0.01" name="default_one_time_va"
                                    value="{{ old('default_one_time_va', $settings['default_one_time_va'] ?? 10) }}"
                                    required class="{{ $field }}">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider {{ $muted }} mb-2">
                                    Sales Commission (%)
                                </label>
                                <input type="number" step="0.01" name="default_one_time_sales"
                                    value="{{ old('default_one_time_sales', $settings['default_one_time_sales'] ?? 5) }}"
                                    required class="{{ $field }}">
                            </div>
                        </div>

                        <!-- Recurring Defaults -->
                        <div class="space-y-4 p-4 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-[#0B1622]/50">
                            <h3 class="font-semibold text-base {{ $ink }}">Recurring Project Defaults</h3>

                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider {{ $muted }} mb-2">
                                    Experts Commission (%)
                                </label>
                                <input type="number" step="0.01" name="default_recurring_va"
                                    value="{{ old('default_recurring_va', $settings['default_recurring_va'] ?? 10) }}"
                                    required class="{{ $field }}">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider {{ $muted }} mb-2">
                                    Sales Commission (%)
                                </label>
                                <input type="number" step="0.01" name="default_recurring_sales"
                                    value="{{ old('default_recurring_sales', $settings['default_recurring_sales'] ?? 5) }}"
                                    required class="{{ $field }}">
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end gap-4 pt-4 border-t border-slate-100 dark:border-white/10">
                        <button type="submit"
                            class="px-6 py-2.5 rounded-lg bg-teal-600 hover:bg-teal-700 text-sm font-medium text-white transition focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-[#0B1622]">
                            Save Settings
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</x-app-layout>