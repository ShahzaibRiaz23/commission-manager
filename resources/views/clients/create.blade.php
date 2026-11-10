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
                    <h1 class="font-serif text-3xl {{ $ink }} tracking-tight">Add New Client</h1>
                    <p class="text-sm {{ $muted }} mt-1">Register a new client into the system for project tracking.</p>
                </div>
                <div>
                    <a href="{{ route('clients.index') }}"
                        class="px-4 py-2 rounded-lg border border-[#12263A]/20 dark:border-white/20 text-sm font-medium {{ $ink }} hover:bg-white dark:hover:bg-white/10 transition">
                        Back to clients
                    </a>
                </div>
            </div>

            {{-- Form Card --}}
            <div class="{{ $card }} p-8">
                <form action="{{ route('clients.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Client Full Name -->
                    <div>
                        <label for="name"
                            class="block text-xs font-semibold uppercase tracking-wider {{ $muted }} mb-2">Client Full
                            Name *</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required class="{{ $field }}"
                            placeholder="John Doe">
                        @error('name')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Company Name -->
                    <div>
                        <label for="company_name"
                            class="block text-xs font-semibold uppercase tracking-wider {{ $muted }} mb-2">Company
                            Name</label>
                        <input type="text" name="company_name" id="company_name" value="{{ old('company_name') }}"
                            class="{{ $field }}" placeholder="Acme Corp">
                        @error('company_name')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email Address -->
                    <div>
                        <label for="email"
                            class="block text-xs font-semibold uppercase tracking-wider {{ $muted }} mb-2">Email
                            Address</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" class="{{ $field }}"
                            placeholder="john@example.com">
                        @error('email')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone Number -->
                    <div>
                        <label for="phone"
                            class="block text-xs font-semibold uppercase tracking-wider {{ $muted }} mb-2">Phone
                            Number</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone') }}" class="{{ $field }}"
                            placeholder="+1 (555) 019-2834">
                        @error('phone')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Actions -->
                    <div
                        class="flex items-center justify-end gap-4 pt-4 border-t border-slate-100 dark:border-white/10">
                        <a href="{{ route('clients.index') }}"
                            class="px-5 py-2.5 rounded-lg border border-[#12263A]/20 dark:border-white/20 text-sm font-medium {{ $ink }} hover:bg-white dark:hover:bg-white/10 transition">
                            Cancel
                        </a>
                        <button type="submit"
                            class="px-6 py-2.5 rounded-lg bg-teal-600 hover:bg-teal-700 text-sm font-medium text-white transition focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600 focus-visible:ring-offset-2">
                            Save Client
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</x-app-layout>