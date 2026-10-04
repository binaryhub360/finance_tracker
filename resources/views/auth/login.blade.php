<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign In - Finance Tracker Pro</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css'])
</head>
<body class="h-full text-slate-800 antialiased selection:bg-emerald-500 selection:text-white flex items-center justify-center relative overflow-hidden p-4">
    {{-- Ambient Glowing Background Orbs --}}
    <div class="fixed inset-0 pointer-events-none overflow-hidden">
        <div class="absolute -top-32 -left-32 w-96 h-96 bg-emerald-400/20 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute top-1/2 -right-32 w-96 h-96 bg-teal-400/20 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-32 left-1/3 w-96 h-96 bg-indigo-400/15 rounded-full blur-3xl"></div>
    </div>

    {{-- Login Card Container --}}
    <div class="w-full max-w-md relative z-10">
        {{-- Brand Header --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-tr from-emerald-600 via-emerald-500 to-teal-400 text-white shadow-xl shadow-emerald-500/30 mb-4 transform hover:scale-105 transition-transform duration-300">
                <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">Finance Tracker</h1>
            <p class="mt-1.5 text-xs sm:text-sm text-slate-500">Sign in to manage your wealth & accounts</p>
        </div>

        {{-- Card --}}
        <div class="bg-white/90 backdrop-blur-2xl border border-slate-200/80 shadow-[0_20px_60px_-15px_rgba(15,23,42,0.08)] rounded-3xl p-7 sm:p-9 transition-all">
            @if($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200/80 text-rose-700 text-xs font-semibold flex items-center gap-2.5">
                    <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                    </svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Email Address</label>
                    <div class="relative">
                        <input id="email" name="email" type="email" required autofocus
                            value="{{ old('email') }}"
                            class="block w-full px-4 py-3 rounded-xl bg-slate-50/80 border border-slate-200 text-slate-900 text-sm font-medium placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-emerald-500/15 focus:border-emerald-500 transition shadow-xs"
                            placeholder="name@company.com">
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Password</label>
                    </div>
                    <div class="relative">
                        <input id="password" name="password" type="password" required
                            class="block w-full px-4 py-3 rounded-xl bg-slate-50/80 border border-slate-200 text-slate-900 text-sm font-medium placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-emerald-500/15 focus:border-emerald-500 transition shadow-xs"
                            placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;">
                    </div>
                </div>

                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input id="remember" name="remember" type="checkbox"
                            class="w-4 h-4 rounded-md border-slate-300 text-emerald-600 focus:ring-emerald-500 focus:ring-offset-0 transition">
                        <span class="text-xs font-medium text-slate-600">Keep me signed in</span>
                    </label>
                </div>

                <button type="submit"
                    class="w-full flex items-center justify-center gap-2 py-3.5 px-4 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-emerald-600 via-emerald-500 to-teal-600 hover:from-emerald-700 hover:to-teal-700 shadow-lg shadow-emerald-500/25 hover:shadow-xl hover:shadow-emerald-500/35 focus:outline-none focus:ring-4 focus:ring-emerald-500/20 transition-all duration-200 hover:-translate-y-0.5 active:translate-y-0">
                    <span>Sign In</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
                </button>
            </form>
        </div>

        <p class="mt-8 text-center text-xs text-slate-400">
            Protected with end-to-end encryption &bull; Finance Tracker Pro
        </p>
    </div>
</body>
</html>
