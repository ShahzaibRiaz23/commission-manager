<x-app-layout>
    @php
        // Safe defaults so the page never breaks if the controller omits something
        $totalProjects = $totalProjects ?? 0;
        $activeProjects = $activeProjects ?? 0;
        $projectRevenue = (float) ($totalProjectRevenue ?? 0);
        $recurringRevenue = (float) ($totalRecurringRevenue ?? 0);
        $vaTotal = (float) ($totalVaCommissions ?? 0);
        $salesTotal = (float) ($totalSalesCommissions ?? 0);
        $remaining = (float) ($totalRemainingRevenue ?? 0);
        $pending = (float) ($pendingCommissions ?? 0);
        $recurringComm = (float) ($recurringCommission ?? 0);
        $earnedComm = (float) ($totalEarnedCommission ?? ($vaTotal + $salesTotal));
        $monthly = $monthly ?? [];
        $recentPayments = $recentPayments ?? collect();

        $gross = $projectRevenue + $recurringRevenue;
        $p = fn ($v) => $gross > 0 ? round(($v / $gross) * 100, 1) : 0;
        $money = fn ($v) => '$' . number_format((float) $v, 2);

        $card = 'bg-white dark:bg-[#12263A] rounded-2xl border border-[#12263A]/10 dark:border-white/10';
        $ink = 'text-[#12263A] dark:text-slate-100';
        $muted = 'text-slate-500 dark:text-slate-400';
        $field = 'w-full rounded-lg border-slate-300 dark:border-white/20 dark:bg-[#0B1622] dark:text-slate-100 text-sm focus:border-teal-600 focus:ring-teal-600';

        $filters = [
            ['project_id', 'Project', ($projects ?? collect())->pluck('name', 'id')],
            ['client_id', 'Client', ($clients ?? collect())->pluck('name', 'id')],
            ['va_id', 'VA / team member', ($vas ?? collect())->pluck('name', 'id')],
            ['sales_officer_id', 'Sales officer', ($salesOfficers ?? collect())->pluck('name', 'id')],
            ['status', 'Project status', collect(['pending' => 'Pending', 'active' => 'Active', 'on_hold' => 'On hold', 'completed' => 'Completed', 'cancelled' => 'Cancelled'])],
            ['payment_type', 'Payment type', collect(['one_time' => 'One-time', 'recurring' => 'Recurring'])],
        ];

        $breakdowns = [
            ['VA-wise commission', $vaBreakdown ?? collect(), 'bg-amber-400'],
            ['Sales officer-wise commission', $salesBreakdown ?? collect(), 'bg-sky-500'],
            ['Project-wise commission', $projectBreakdown ?? collect(), 'bg-teal-600'],
        ];
    @endphp

    <div class="min-h-screen bg-[#EDF1F5] dark:bg-[#0B1622] py-8 transition-colors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- Header with Create Staff Button --}}
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
                <div>
                    <h1 class="font-serif text-3xl {{ $ink }} tracking-tight">Where the money goes</h1>
                    <p class="text-sm {{ $muted }} mt-1">Every dollar collected, split between VAs, sales officers and the company.</p>
                </div>
                <div class="flex flex-wrap items-center gap-2" x-data="{ dark: document.documentElement.classList.contains('dark') }">
                    {{-- Create VA / Sales Officer Button --}}
                    <a href="{{ route('admin.users.create') }}"
                       class="px-4 py-2 rounded-lg bg-teal-600 text-sm font-medium text-white hover:bg-teal-700 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600 focus-visible:ring-offset-2">
                        + Create Staff Account
                    </a>
                    <button type="button"
                            @click="dark = !dark; document.documentElement.classList.toggle('dark', dark); localStorage.setItem('theme', dark ? 'dark' : 'light')"
                            class="px-4 py-2 rounded-lg border border-[#12263A]/20 dark:border-white/20 text-sm font-medium {{ $ink }} hover:bg-white dark:hover:bg-white/10 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600">
                        <span x-text="dark ? 'Light theme' : 'Dark theme'"></span>
                    </button>
                    <a href="{{ route('payments.export', request()->query()) }}"
                       class="px-4 py-2 rounded-lg border border-[#12263A]/20 dark:border-white/20 text-sm font-medium {{ $ink }} hover:bg-white dark:hover:bg-white/10 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600">
                        Export payments
                    </a>
                    <a href="{{ route('payments.index') }}"
                       class="px-4 py-2 rounded-lg bg-[#12263A] dark:bg-teal-700 text-sm font-medium text-white hover:opacity-90 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600 focus-visible:ring-offset-2">
                        Open ledger
                    </a>
                </div>
            </div>

            {{-- Filters --}}
            <form method="GET" action="{{ route('dashboard') }}" class="{{ $card }} p-5">
                <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <label class="block">
                        <span class="text-xs {{ $muted }}">From date</span>
                        <input type="date" name="from" value="{{ request('from') }}" class="{{ $field }} mt-1">
                    </label>
                    <label class="block">
                        <span class="text-xs {{ $muted }}">To date</span>
                        <input type="date" name="to" value="{{ request('to') }}" class="{{ $field }} mt-1">
                    </label>
                    @foreach ($filters as [$name, $label, $options])
                        <label class="block">
                            <span class="text-xs {{ $muted }}">{{ $label }}</span>
                            <select name="{{ $name }}" class="{{ $field }} mt-1">
                                <option value="">All</option>
                                @foreach ($options as $value => $text)
                                    <option value="{{ $value }}" @selected((string) request($name) === (string) $value)>{{ $text }}</option>
                                @endforeach
                            </select>
                        </label>
                    @endforeach
                </div>
                <div class="mt-4 flex items-center gap-3">
                    <button class="px-4 py-2 rounded-lg bg-teal-600 hover:bg-teal-700 text-sm font-medium text-white transition focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600 focus-visible:ring-offset-2">Apply filters</button>
                    <a href="{{ route('dashboard') }}" class="text-sm {{ $muted }} underline underline-offset-4 hover:text-teal-600">Clear all</a>
                </div>
            </form>

            {{-- Row 1: projects and revenue --}}
            <section class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach ([
                    ['Total projects', number_format($totalProjects), 'All projects, any status'],
                    ['Active projects', number_format($activeProjects), 'Currently in progress'],
                    ['Project revenue', $money($projectRevenue), 'One-time payments received'],
                    ['Recurring revenue', $money($recurringRevenue), 'Recurring payments received'],
                ] as [$label, $value, $hint])
                    <div class="{{ $card }} p-6">
                        <p class="text-sm {{ $muted }}">{{ $label }}</p>
                        <p class="font-serif text-3xl {{ $ink }} mt-1 tabular-nums">{{ $value }}</p>
                        <p class="text-xs {{ $muted }} mt-2">{{ $hint }}</p>
                    </div>
                @endforeach
            </section>

            {{-- Row 2: the split --}}
            <section class="grid lg:grid-cols-3 gap-4">
                <div class="lg:col-span-2 {{ $card }} p-8">
                    <p class="text-sm {{ $muted }}">Total collected: <span class="font-semibold {{ $ink }} tabular-nums">{{ $money($gross) }}</span></p>

                    <div class="mt-5 flex h-5 w-full overflow-hidden rounded-full bg-slate-100 dark:bg-white/10"
                         role="img" aria-label="VA {{ $p($vaTotal) }}%, sales {{ $p($salesTotal) }}%, company {{ $p($remaining) }}%">
                        <div class="bg-amber-400" style="width: {{ $p($vaTotal) }}%"></div>
                        <div class="bg-sky-500" style="width: {{ $p($salesTotal) }}%"></div>
                        <div class="bg-teal-600" style="width: {{ $p($remaining) }}%"></div>
                    </div>

                    <dl class="mt-6 grid sm:grid-cols-3 gap-6">
                        @foreach ([
                            ['Paid to VAs', $vaTotal, 'bg-amber-400', $ink],
                            ['Paid to sales officers', $salesTotal, 'bg-sky-500', $ink],
                            ['Kept by the company', $remaining, 'bg-teal-600', 'text-teal-700 dark:text-teal-300'],
                        ] as [$label, $amount, $dotColor, $textColor])
                            <div>
                                <dt class="flex items-center gap-2 text-sm {{ $muted }}">
                                    <span class="w-2.5 h-2.5 rounded-full {{ $dotColor }}"></span> {{ $label }}
                                </dt>
                                <dd class="mt-1 text-xl font-semibold {{ $textColor }} tabular-nums">{{ $money($amount) }}</dd>
                                <dd class="text-xs {{ $muted }}">{{ $p($amount) }}% of collected</dd>
                            </div>
                        @endforeach
                    </dl>
                </div>

                <div class="bg-[#12263A] dark:bg-teal-700 rounded-2xl p-8 text-white flex flex-col">
                    <p class="text-sm text-slate-300 dark:text-teal-100">Pending commissions</p>
                    <p class="font-serif text-4xl mt-1 tabular-nums">{{ $money($pending) }}</p>
                    <p class="text-sm text-slate-300 dark:text-teal-100 mt-2">Commission on recorded payments that has not been paid out yet.</p>
                    <dl class="mt-6 space-y-3 text-sm flex-1">
                        <div class="flex justify-between"><dt class="text-slate-300 dark:text-teal-100">Recurring commission</dt><dd class="tabular-nums">{{ $money($recurringComm) }}</dd></div>
                        <div class="flex justify-between"><dt class="text-slate-300 dark:text-teal-100">Total earned</dt><dd class="tabular-nums">{{ $money($earnedComm) }}</dd></div>
                    </dl>
                    <a href="{{ route('payments.index', ['status' => 'pending']) }}" class="mt-6 text-sm font-medium text-teal-300 dark:text-white underline underline-offset-4">Review pending payments</a>
                </div>
            </section>

            {{-- Monthly chart --}}
            <section class="{{ $card }} p-6">
                <div class="flex items-baseline justify-between mb-4">
                    <h2 class="font-serif text-xl {{ $ink }}">Monthly revenue and commission</h2>
                    <p class="text-xs {{ $muted }}">Based on recorded payments</p>
                </div>
                @if (count($monthly))
                    <div class="h-72"><canvas id="monthlyChart"></canvas></div>
                @else
                    <p class="text-sm {{ $muted }} py-10 text-center">No payments in this period yet.</p>
                @endif
            </section>

            {{-- Breakdowns --}}
            <section class="grid lg:grid-cols-3 gap-4">
                @foreach ($breakdowns as [$title, $rows, $bar])
                    @php $max = max(1, (float) collect($rows)->max(fn ($r) => (float) data_get($r, 'commission', 0))); @endphp
                    <div class="{{ $card }} p-6">
                        <h3 class="font-serif text-lg {{ $ink }}">{{ $title }}</h3>
                        <ul class="mt-4 space-y-4">
                            @forelse ($rows as $row)
                                <li>
                                    <div class="flex justify-between text-sm">
                                        <span class="{{ $ink }} truncate pr-3">{{ data_get($row, 'name') }}</span>
                                        <span class="font-semibold {{ $ink }} tabular-nums">{{ $money(data_get($row, 'commission', 0)) }}</span>
                                    </div>
                                    <div class="mt-1.5 h-1.5 rounded-full bg-slate-100 dark:bg-white/10">
                                        <div class="h-1.5 rounded-full {{ $bar }}" style="width: {{ round(((float) data_get($row, 'commission', 0) / $max) * 100) }}%"></div>
                                    </div>
                                </li>
                            @empty
                                <li class="text-sm {{ $muted }}">Nothing to show for these filters.</li>
                            @endforelse
                        </ul>
                    </div>
                @endforeach
            </section>

            {{-- Latest payments --}}
            <section>
                <div class="flex items-baseline justify-between mb-4">
                    <h2 class="font-serif text-2xl {{ $ink }}">Latest payments</h2>
                    <p class="text-sm {{ $muted }}">Each bar shows how that payment was divided.</p>
                </div>

                <div class="{{ $card }} divide-y divide-slate-100 dark:divide-white/10">
                    @forelse ($recentPayments as $payment)
                        @php
                            $amt = (float) $payment->payment_amount;
                            $va = (float) $payment->va_commission_amount;
                            $sales = (float) $payment->sales_commission_amount;
                            $company = max($amt - $va - $sales, 0);
                            $w = fn ($v) => $amt > 0 ? round(($v / $amt) * 100, 1) : 0;
                            $dot = $payment->status == 'paid' ? 'bg-emerald-500' : ($payment->status == 'approved' ? 'bg-sky-500' : 'bg-amber-400');
                            $ref = data_get($payment, 'transaction_reference');
                        @endphp
                        <div class="grid grid-cols-2 md:grid-cols-12 gap-x-4 gap-y-3 items-center px-6 py-5">
                            <div class="md:col-span-2 text-sm {{ $muted }} tabular-nums">{{ $payment->payment_date->format('M d, Y') }}</div>
                            <div class="md:col-span-3 col-span-2">
                                <div class="text-sm font-semibold {{ $ink }}">{{ $payment->project->name }}</div>
                                <div class="text-xs {{ $muted }}">{{ $payment->project->client->name ?? 'No client' }}</div>
                            </div>
                            <div class="md:col-span-2 text-lg font-serif {{ $ink }} tabular-nums">{{ $money($amt) }}</div>
                            <div class="md:col-span-3 col-span-2">
                                <div class="flex h-2 w-full overflow-hidden rounded-full bg-slate-100 dark:bg-white/10">
                                    <div class="bg-amber-400" style="width: {{ $w($va) }}%"></div>
                                    <div class="bg-sky-500" style="width: {{ $w($sales) }}%"></div>
                                    <div class="bg-teal-600" style="width: {{ $w($company) }}%"></div>
                                </div>
                                <div class="mt-1.5 text-xs {{ $muted }} tabular-nums">VA {{ $money($va) }} &middot; Sales {{ $money($sales) }}</div>
                            </div>
                            <div class="md:col-span-2 md:text-right">
                                <div class="flex md:justify-end items-center gap-2 text-sm {{ $ink }}">
                                    <span class="w-2.5 h-2.5 rounded-full {{ $dot }}"></span>{{ ucfirst($payment->status) }}
                                </div>
                                @if ($ref)<div class="text-xs {{ $muted }} mt-0.5">Ref {{ $ref }}</div>@endif
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-14 text-center">
                            <p class="text-sm {{ $muted }}">No payments recorded yet.</p>
                            <a href="{{ route('payments.index') }}" class="inline-block mt-3 text-sm font-medium text-teal-700 dark:text-teal-300 underline underline-offset-4">Record the first payment</a>
                        </div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>

    {{-- Apply saved theme, then draw the chart --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <script>
        (function () {
            if (localStorage.getItem('theme') === 'dark') document.documentElement.classList.add('dark');
            const el = document.getElementById('monthlyChart');
            if (!el || typeof Chart === 'undefined') return;
            const data = @json($monthly);
            const dark = document.documentElement.classList.contains('dark');
            const tick = dark ? '#94a3b8' : '#64748b';
            const grid = dark ? 'rgba(255,255,255,0.08)' : 'rgba(18,38,58,0.08)';
            new Chart(el, {
                type: 'bar',
                data: {
                    labels: data.map(d => d.month),
                    datasets: [
                        { label: 'Revenue', data: data.map(d => d.revenue), backgroundColor: '#0f766e', borderRadius: 6 },
                        { label: 'Commission', data: data.map(d => d.commission), backgroundColor: '#fbbf24', borderRadius: 6 }
                    ]
                },
                options: {
                    maintainAspectRatio: false,
                    plugins: { legend: { labels: { color: tick } } },
                    scales: {
                        x: { ticks: { color: tick }, grid: { display: false } },
                        y: { ticks: { color: tick, callback: v => '$' + v.toLocaleString() }, grid: { color: grid } }
                    }
                }
            });
        })();
    </script>
</x-app-layout>