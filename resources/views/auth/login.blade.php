<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign in - XortLedger</title>
    <script>if (localStorage.getItem('theme') === 'dark') document.documentElement.classList.add('dark');</script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased bg-[#EDF1F5] dark:bg-[#0B1622] text-[#12263A] dark:text-slate-100 transition-colors"
      x-data="{ dark: document.documentElement.classList.contains('dark') }">

    <div class="min-h-screen grid lg:grid-cols-2">

        {{-- Brand panel (large screens) --}}
        <aside class="hidden lg:flex flex-col justify-between bg-[#12263A] text-white p-12">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-teal-600 flex items-center justify-center">⚡</div>
                <span class="font-extrabold text-xl tracking-tight">Xort<span class="text-teal-400">Ledger</span></span>
            </div>

            <div class="max-w-md">
                <h2 class="font-serif text-4xl leading-tight">Know exactly where every payment goes.</h2>
                <p class="mt-4 text-slate-300">Projects, payments and commissions in one place, calculated from what has actually been received.</p>

                {{-- Example split --}}
                <div class="mt-10">
                    <p class="text-sm text-slate-300">Example: a $1,000 payment</p>
                    <div class="mt-3 flex h-4 w-full overflow-hidden rounded-full bg-white/10" role="img" aria-label="Example split: VA 10%, sales officer 5%, company 85%">
                        <div class="bg-amber-400" style="width: 10%"></div>
                        <div class="bg-sky-500" style="width: 5%"></div>
                        <div class="bg-teal-500" style="width: 85%"></div>
                    </div>
                    <dl class="mt-4 grid grid-cols-3 gap-4 text-sm">
                        <div>
                            <dt class="flex items-center gap-2 text-slate-300"><span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span>VA</dt>
                            <dd class="mt-1 font-semibold tabular-nums">$100</dd>
                        </div>
                        <div>
                            <dt class="flex items-center gap-2 text-slate-300"><span class="w-2.5 h-2.5 rounded-full bg-sky-500"></span>Sales officer</dt>
                            <dd class="mt-1 font-semibold tabular-nums">$50</dd>
                        </div>
                        <div>
                            <dt class="flex items-center gap-2 text-slate-300"><span class="w-2.5 h-2.5 rounded-full bg-teal-500"></span>Company</dt>
                            <dd class="mt-1 font-semibold tabular-nums">$850</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <p class="text-xs text-slate-400">&copy; {{ date('Y') }} XortLedger. All rights reserved.</p>
        </aside>

        {{-- Form panel --}}
        <main class="relative flex flex-col justify-center px-6 py-12 sm:px-12">

            <button type="button"
                    @click="dark = !dark; document.documentElement.classList.toggle('dark', dark); localStorage.setItem('theme', dark ? 'dark' : 'light')"
                    class="absolute top-5 right-5 px-3.5 py-2 rounded-lg border border-[#12263A]/20 dark:border-white/20 text-xs font-medium hover:bg-white dark:hover:bg-white/10 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600">
                <span x-text="dark ? 'Light theme' : 'Dark theme'"></span>
            </button>

            <div class="w-full max-w-md mx-auto">
                {{-- Brand (small screens) --}}
                <div class="lg:hidden flex items-center space-x-3 mb-10">
                    <div class="w-10 h-10 rounded-xl bg-teal-600 flex items-center justify-center text-white">⚡</div>
                    <span class="font-extrabold text-xl tracking-tight">Xort<span class="text-teal-600 dark:text-teal-400">Ledger</span></span>
                </div>

                <h1 class="font-serif text-3xl tracking-tight">Sign in</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Use your account to manage projects and payouts.</p>

                <x-auth-session-status class="mt-6" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-5">
                    @csrf

                    <div>
                        <label for="email" class="block text-sm font-medium mb-1.5">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                               placeholder="you@company.com"
                               class="w-full px-4 py-3 rounded-lg text-sm bg-white dark:bg-[#12263A] border border-slate-300 dark:border-white/15 text-[#12263A] dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:border-teal-600 focus:ring-2 focus:ring-teal-600/40 transition" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-xs text-red-600 dark:text-red-400" />
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium mb-1.5">Password</label>
                        <input id="password" type="password" name="password" required autocomplete="current-password"
                               placeholder="Your password"
                               class="w-full px-4 py-3 rounded-lg text-sm bg-white dark:bg-[#12263A] border border-slate-300 dark:border-white/15 text-[#12263A] dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:border-teal-600 focus:ring-2 focus:ring-teal-600/40 transition" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-xs text-red-600 dark:text-red-400" />
                    </div>

                    <div class="flex items-center justify-between text-sm">
                        <label for="remember_me" class="inline-flex items-center cursor-pointer">
                            <input id="remember_me" type="checkbox" name="remember"
                                   class="rounded border-slate-300 dark:border-white/20 dark:bg-[#12263A] text-teal-600 focus:ring-teal-600">
                            <span class="ms-2 text-slate-600 dark:text-slate-300">Remember me</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}"
                               class="font-medium text-teal-700 dark:text-teal-300 hover:underline underline-offset-4">
                                Forgot password?
                            </a>
                        @endif
                    </div>

                    <button type="submit"
                            class="w-full py-3 px-4 rounded-lg bg-teal-600 hover:bg-teal-700 text-white font-semibold text-sm transition focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-[#0B1622]">
                        Sign in
                    </button>
                </form>

                <p class="lg:hidden mt-10 text-center text-xs text-slate-500 dark:text-slate-400">&copy; {{ date('Y') }} XortLedger. All rights reserved.</p>
            </div>
        </main>
    </div>
</body>
</html>