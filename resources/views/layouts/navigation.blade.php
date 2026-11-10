@php
    $links = [
        ['Dashboard', 'dashboard', 'dashboard'],
        ['Clients', 'clients.index', 'clients.*'],
        ['Projects', 'projects.index', 'projects.*'],
        ['Payments Ledger', 'payments.index', 'payments.*'],
    ];
    $active = 'bg-teal-600/10 text-teal-700 dark:text-teal-300 border border-teal-600/30';
    $idle = 'text-slate-600 dark:text-slate-400 border border-transparent hover:text-[#12263A] dark:hover:text-slate-100 hover:bg-slate-100 dark:hover:bg-white/5';
@endphp

<nav x-data="{ open: false }"
     class="bg-white dark:bg-[#0B1622] border-b border-[#12263A]/10 dark:border-white/10 sticky top-0 z-50 transition-colors">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center space-x-8">
                {{-- Brand --}}
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600 rounded-lg">
                    <div class="w-9 h-9 rounded-xl bg-teal-600 flex items-center justify-center text-white">⚡</div>
                    <span class="font-extrabold text-lg tracking-tight text-[#12263A] dark:text-white">Xort<span class="text-teal-600 dark:text-teal-400">Ledger</span></span>
                </a>

                {{-- Desktop links --}}
                <div class="hidden sm:flex sm:items-center sm:space-x-1">
                    @foreach ($links as [$label, $route, $pattern])
                        <a href="{{ route($route) }}"
                           @if (request()->routeIs($pattern)) aria-current="page" @endif
                           class="px-3.5 py-2 rounded-lg text-xs font-semibold tracking-wide transition focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600 {{ request()->routeIs($pattern) ? $active : $idle }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Profile dropdown --}}
            <div class="hidden sm:flex sm:items-center">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="flex items-center space-x-3 px-3 py-1.5 rounded-lg bg-slate-100 dark:bg-white/5 border border-[#12263A]/10 dark:border-white/10 hover:bg-slate-200 dark:hover:bg-white/10 text-xs text-[#12263A] dark:text-slate-200 font-medium transition focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600">
                            <div class="w-6 h-6 rounded-md bg-teal-600/15 border border-teal-600/30 text-teal-700 dark:text-teal-300 font-bold flex items-center justify-center text-[10px]">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <span>{{ Auth::user()->name }}</span>
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            {{-- Mobile hamburger --}}
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" aria-label="Toggle menu"
                        class="p-2 rounded-lg text-slate-500 dark:text-slate-400 hover:text-[#12263A] dark:hover:text-white hover:bg-slate-100 dark:hover:bg-white/10 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile drawer --}}
    <div :class="{'block': open, 'hidden': ! open}"
         class="hidden sm:hidden bg-white dark:bg-[#0B1622] border-t border-[#12263A]/10 dark:border-white/10 px-4 pt-2 pb-4 space-y-1">
        @foreach ($links as [$label, $route, $pattern])
            <a href="{{ route($route) }}"
               class="block px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs($pattern) ? $active : $idle }}">
                {{ $label }}
            </a>
        @endforeach

        <div class="pt-3 mt-2 border-t border-[#12263A]/10 dark:border-white/10">
            <div class="px-3 text-sm font-medium text-[#12263A] dark:text-slate-200">{{ Auth::user()->name }}</div>
            <a href="{{ route('profile.edit') }}" class="block px-3 py-2 mt-1 rounded-lg text-sm text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-white/5">Profile</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="w-full text-left px-3 py-2 rounded-lg text-sm text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-white/5">Log Out</button>
            </form>
        </div>
    </div>
</nav>