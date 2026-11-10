<x-app-layout>
    @php
        $card = 'bg-white dark:bg-[#12263A] rounded-2xl border border-[#12263A]/10 dark:border-white/10';
        $ink = 'text-[#12263A] dark:text-slate-100';
        $muted = 'text-slate-500 dark:text-slate-400';
        $th = 'px-6 py-3.5 text-left text-xs font-medium text-slate-500 dark:text-slate-400';

        // One color per project status
        $statusDot = [
            'pending' => 'bg-amber-400',
            'active' => 'bg-emerald-500',
            'on_hold' => 'bg-slate-400',
            'completed' => 'bg-sky-500',
            'cancelled' => 'bg-red-500',
        ];
    @endphp

    <div class="min-h-screen bg-[#EDF1F5] dark:bg-[#0B1622] py-8 transition-colors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- Header --}}
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
                <div>
                    <h1 class="font-serif text-3xl {{ $ink }} tracking-tight">Projects</h1>
                    <p class="text-sm {{ $muted }} mt-1">Track budgets, sales officers and where each project stands.</p>
                </div>
                <a href="{{ route('projects.create') }}"
                   class="inline-flex items-center px-4 py-2 rounded-lg bg-teal-600 hover:bg-teal-700 text-sm font-medium text-white transition focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-[#0B1622]">
                    Create project
                </a>
            </div>

            {{-- Table --}}
            <div class="{{ $card }} overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 dark:divide-white/10">
                        <thead class="bg-slate-50 dark:bg-white/5">
                            <tr>
                                <th class="{{ $th }}">Project</th>
                                <th class="{{ $th }}">Client</th>
                                <th class="{{ $th }}">Amount</th>
                                <th class="{{ $th }}">Sales officer</th>
                                <th class="{{ $th }}">Status</th>
                                <th class="{{ $th }} text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-white/10">
                            @forelse ($projects as $project)
                                <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="{{ route('projects.show', $project->id) }}"
                                           class="text-sm font-semibold text-teal-700 dark:text-teal-300 hover:underline underline-offset-4">{{ $project->name }}</a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm {{ $muted }}">{{ $project->client->name ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-serif text-base {{ $ink }} tabular-nums">${{ number_format($project->project_amount, 2) }}</div>
                                        <div class="text-xs {{ $muted }}">{{ ucfirst(str_replace('_', ' ', $project->payment_type)) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm {{ $muted }}">{{ $project->salesOfficer->name ?? 'Not assigned' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center gap-2 text-sm {{ $ink }}">
                                            <span class="w-2.5 h-2.5 rounded-full {{ $statusDot[$project->status] ?? 'bg-slate-400' }}"></span>
                                            {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-4">
                                        <a href="{{ route('projects.show', $project->id) }}" class="text-teal-700 dark:text-teal-300 hover:underline underline-offset-4">View</a>
                                        <a href="{{ route('projects.edit', $project->id) }}" class="text-teal-700 dark:text-teal-300 hover:underline underline-offset-4">Edit</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-14 text-center">
                                        <p class="text-sm {{ $muted }}">No projects yet.</p>
                                        <a href="{{ route('projects.create') }}" class="inline-block mt-3 text-sm font-medium text-teal-700 dark:text-teal-300 underline underline-offset-4">Create your first project</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($projects->hasPages())
                    <div class="p-6 border-t border-slate-100 dark:border-white/10">
                        {{ $projects->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>