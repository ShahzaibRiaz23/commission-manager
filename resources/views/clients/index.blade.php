<x-app-layout>
    @php
        $card = 'bg-white dark:bg-[#12263A] rounded-2xl border border-[#12263A]/10 dark:border-white/10';
        $ink = 'text-[#12263A] dark:text-slate-100';
        $muted = 'text-slate-500 dark:text-slate-400';
        $th = 'px-6 py-3.5 text-left text-xs font-medium text-slate-500 dark:text-slate-400';
    @endphp

    <div class="min-h-screen bg-[#EDF1F5] dark:bg-[#0B1622] py-8 transition-colors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- Header --}}
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
                <div>
                    <h1 class="font-serif text-3xl {{ $ink }} tracking-tight">Clients</h1>
                    <p class="text-sm {{ $muted }} mt-1">Everyone you work with, active or prospective.</p>
                </div>
                <a href="{{ route('clients.create') }}"
                   class="inline-flex items-center px-4 py-2 rounded-lg bg-teal-600 hover:bg-teal-700 text-sm font-medium text-white transition focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-[#0B1622]">
                    Add client
                </a>
            </div>

            {{-- Table --}}
            <div class="{{ $card }} overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 dark:divide-white/10">
                        <thead class="bg-slate-50 dark:bg-white/5">
                            <tr>
                                <th class="{{ $th }}">Name</th>
                                <th class="{{ $th }}">Company</th>
                                <th class="{{ $th }}">Email</th>
                                <th class="{{ $th }}">Phone</th>
                                <th class="{{ $th }} text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-white/10">
                            @forelse ($clients as $client)
                                <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-teal-600/15 text-teal-700 dark:text-teal-300 flex items-center justify-center text-xs font-bold">
                                                {{ strtoupper(substr($client->name, 0, 1)) }}
                                            </div>
                                            <span class="text-sm font-semibold {{ $ink }}">{{ $client->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm {{ $muted }}">{{ $client->company_name ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm {{ $muted }}">{{ $client->email ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm {{ $muted }} tabular-nums">{{ $client->phone ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-4">
                                        <a href="{{ route('clients.edit', $client->id) }}"
                                           class="text-teal-700 dark:text-teal-300 hover:underline underline-offset-4">Edit</a>
                                        <form action="{{ route('clients.destroy', $client->id) }}" method="POST" class="inline-block"
                                              onsubmit="return confirm('Are you sure you want to delete this client?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 dark:text-red-400 hover:underline underline-offset-4">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-14 text-center">
                                        <p class="text-sm {{ $muted }}">No clients yet.</p>
                                        <a href="{{ route('clients.create') }}" class="inline-block mt-3 text-sm font-medium text-teal-700 dark:text-teal-300 underline underline-offset-4">Add your first client</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($clients->hasPages())
                    <div class="p-6 border-t border-slate-100 dark:border-white/10">
                        {{ $clients->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>