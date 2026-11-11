<x-app-layout>
    @php
        $card = 'bg-white dark:bg-[#12263A] rounded-2xl border border-[#12263A]/10 dark:border-white/10';
        $ink = 'text-[#12263A] dark:text-slate-100';
        $muted = 'text-slate-500 dark:text-slate-400';
    @endphp

    <div class="min-h-screen bg-[#EDF1F5] dark:bg-[#0B1622] py-8 transition-colors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- Header --}}
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
                <div>
                    <h1 class="font-serif text-3xl {{ $ink }} tracking-tight">{{ $project->name }}</h1>
                    <p class="text-sm {{ $muted }} mt-1">Project Details & Payment History</p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('projects.edit', $project) }}"
                        class="px-4 py-2 rounded-lg bg-teal-600 hover:bg-teal-700 text-white text-sm font-medium transition shadow-sm">Edit
                        Project</a>

                    <form action="{{ route('projects.destroy', $project) }}" method="POST" class="inline"
                        onsubmit="return confirm('Are you sure you want to delete this project?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="px-4 py-2 rounded-lg border border-red-500/30 text-red-600 hover:bg-red-50 dark:hover:bg-red-500/10 text-sm font-medium transition">
                            Delete Project
                        </button>
                    </form>

                    <a href="{{ route('projects.index') }}"
                        class="px-4 py-2 rounded-lg border border-[#12263A]/20 dark:border-white/20 text-sm font-medium {{ $ink }} hover:bg-white dark:hover:bg-white/10 transition">Back
                        to projects</a>
                </div>
            </div>

            {{-- Details Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="{{ $card }} p-6 space-y-4">
                    <h3 class="font-semibold text-base {{ $ink }}">Project Info</h3>
                    <div class="space-y-2 text-sm">
                        <p><span class="{{ $muted }}">Client:</span> <span
                                class="{{ $ink }}">{{ optional($project->client)->name ?? 'N/A' }}</span></p>
                        <p><span class="{{ $muted }}">Amount:</span> <span
                                class="{{ $ink }} font-semibold">${{ number_format($project->project_amount, 2) }}</span>
                        </p>
                        <p><span class="{{ $muted }}">Payment Type:</span> <span
                                class="{{ $ink }} capitalize">{{ $project->payment_type }}</span></p>
                        <p><span class="{{ $muted }}">Status:</span> <span
                                class="capitalize px-2 py-0.5 rounded text-xs bg-teal-100 text-teal-800 dark:bg-teal-900/30 dark:text-teal-300">{{ $project->status }}</span>
                        </p>
                        <p><span class="{{ $muted }}">Sales Officer:</span> <span
                                class="{{ $ink }}">{{ optional($project->salesOfficer)->name ?? 'None' }}</span></p>
                    </div>
                </div>

                <div class="{{ $card }} p-6 md:col-span-2 space-y-4">
                    <h3 class="font-semibold text-base {{ $ink }}">Assigned VAs</h3>
                    @if($project->vas->count() > 0)
                        <div class="divide-y divide-slate-200 dark:divide-white/10">
                            @foreach($project->vas as $va)
                                <div class="py-2 flex items-center justify-between text-sm">
                                    <span class="{{ $ink }}">{{ $va->name }}</span>
                                    <span class="{{ $muted }}">Custom Commission:
                                        {{ $va->pivot->custom_commission_percentage ?? $project->va_commission_percentage }}%</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm {{ $muted }}">No VAs assigned to this project.</p>
                    @endif
                </div>
            </div>

            {{-- Payments Section --}}
            <div class="{{ $card }} p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold text-base {{ $ink }}">Payments History</h3>
                    <a href="{{ route('payments.create', $project) }}"
                        class="px-4 py-2 rounded-lg bg-teal-600 hover:bg-teal-700 text-white text-xs font-medium transition shadow-sm">
                        + Record Payment
                    </a>
                </div>
                @if($project->payments->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="border-b border-slate-200 dark:border-white/10 {{ $muted }}">
                                <tr>
                                    <th class="pb-3">Date</th>
                                    <th class="pb-3">Amount</th>
                                    <th class="pb-3">VA Commission</th>
                                    <th class="pb-3">Sales Commission</th>
                                    <th class="pb-3">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                                @foreach($project->payments as $payment)
                                    <tr>
                                        <td class="py-3 {{ $ink }}">{{ $payment->payment_date->format('M d, Y') }}</td>
                                        <td class="py-3 {{ $ink }} font-semibold">
                                            ${{ number_format($payment->payment_amount, 2) }}</td>
                                        <td class="py-3 {{ $ink }}">${{ number_format($payment->va_commission_amount, 2) }}</td>
                                        <td class="py-3 {{ $ink }}">${{ number_format($payment->sales_commission_amount, 2) }}
                                        </td>
                                        <td class="py-3 capitalize {{ $ink }}">{{ $payment->status }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-sm {{ $muted }}">No payments recorded for this project yet.</p>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>