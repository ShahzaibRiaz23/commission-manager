<x-app-layout>
    @php
        $card = 'bg-white dark:bg-[#12263A] rounded-2xl border border-[#12263A]/10 dark:border-white/10';
        $ink = 'text-[#12263A] dark:text-slate-100';
        $muted = 'text-slate-500 dark:text-slate-400';
        $th = 'px-6 py-3.5 text-left text-xs font-medium text-slate-500 dark:text-slate-400 whitespace-nowrap';
        $statusDot = ['paid' => 'bg-emerald-500', 'approved' => 'bg-sky-500'];
    @endphp

    <div class="min-h-screen bg-[#EDF1F5] dark:bg-[#0B1622] py-8 transition-colors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- Header --}}
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
                <div>
                    <h1 class="font-serif text-3xl {{ $ink }} tracking-tight">Payments ledger</h1>
                    <p class="text-sm {{ $muted }} mt-1">Every payment received and how it was divided.</p>
                </div>
                <a href="{{ route('payments.export', request()->query()) }}"
                   class="inline-flex items-center px-4 py-2 rounded-lg bg-teal-600 hover:bg-teal-700 text-sm font-medium text-white transition focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-[#0B1622]">
                    Export CSV
                </a>
            </div>

            {{-- Table --}}
            <div class="{{ $card }} overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 dark:divide-white/10">
                        <thead class="bg-slate-50 dark:bg-white/5">
                            <tr>
                                <th class="{{ $th }}">ID</th>
                                <th class="{{ $th }}">Date</th>
                                <th class="{{ $th }}">Project / client</th>
                                <th class="{{ $th }}">Amount</th>
                                <th class="{{ $th }}"><span class="inline-flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-amber-400"></span>VA commission</span></th>
                                <th class="{{ $th }}"><span class="inline-flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-sky-500"></span>Sales commission</span></th>
                                <th class="{{ $th }}"><span class="inline-flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-teal-600"></span>Company share</span></th>
                                <th class="{{ $th }}">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-white/10">
                            @forelse ($payments as $payment)
                                <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition">
                                    <td class="px-6 py-4 whitespace-nowrap text-xs {{ $muted }} tabular-nums">#{{ $payment->id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm {{ $muted }} tabular-nums">{{ $payment->payment_date->format('M d, Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold {{ $ink }}">{{ $payment->project->name ?? 'No project' }}</div>
                                        <div class="text-xs {{ $muted }}">{{ $payment->project->client->name ?? 'No client' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap font-serif text-base {{ $ink }} tabular-nums">${{ number_format($payment->payment_amount, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm {{ $ink }} tabular-nums">${{ number_format($payment->va_commission_amount, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm {{ $ink }} tabular-nums">${{ number_format($payment->sales_commission_amount, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-teal-700 dark:text-teal-300 tabular-nums">${{ number_format($payment->remaining_revenue, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center gap-2 text-sm {{ $ink }}">
                                            <span class="w-2.5 h-2.5 rounded-full {{ $statusDot[$payment->status] ?? 'bg-amber-400' }}"></span>
                                            {{ ucfirst($payment->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-14 text-center text-sm {{ $muted }}">No payments recorded yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($payments->hasPages())
                    <div class="p-6 border-t border-slate-100 dark:border-white/10">
                        {{ $payments->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>