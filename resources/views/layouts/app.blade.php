<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Finance Tracker' }} - Finance Tracker</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="h-full text-slate-800 antialiased selection:bg-emerald-500 selection:text-white">
    {{-- Ambient Background Glow Effects --}}
    <div class="fixed inset-0 pointer-events-none overflow-hidden z-0">
        <div class="absolute -top-40 -right-40 w-96 h-96 bg-emerald-400/10 rounded-full blur-3xl"></div>
        <div class="absolute top-1/3 -left-40 w-96 h-96 bg-teal-400/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-40 right-1/4 w-96 h-96 bg-indigo-400/8 rounded-full blur-3xl"></div>
    </div>

    <div class="relative min-h-full z-10">
        {{-- Mobile menu overlay --}}
        <div id="mobile-menu-overlay" class="fixed inset-0 z-40 bg-slate-900/40 backdrop-blur-xs hidden lg:hidden transition-opacity" onclick="toggleMobileMenu()"></div>

        {{-- Sidebar --}}
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-72 bg-white/95 backdrop-blur-xl border-r border-slate-200/80 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out flex flex-col shadow-xl lg:shadow-none">
            {{-- Brand Logo --}}
            <div class="flex items-center h-20 px-6 border-b border-slate-100">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3.5 group">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-600 via-emerald-500 to-teal-400 flex items-center justify-center text-white shadow-lg shadow-emerald-500/25 transition-transform duration-300 group-hover:scale-105">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-base font-bold tracking-tight text-slate-900">FinanceTracker</span>
                            <span class="px-1.5 py-0.5 text-[10px] font-bold tracking-wide uppercase bg-emerald-50 text-emerald-700 border border-emerald-200/60 rounded-md">Pro</span>
                        </div>
                        <p class="text-xs text-slate-400">Smart Wealth Manager</p>
                    </div>
                </a>
            </div>

            {{-- Nav items --}}
            <nav class="flex-1 px-4 py-5 space-y-6 overflow-y-auto">
                <div>
                    <p class="px-3 mb-2 text-[10px] font-bold tracking-wider uppercase text-slate-400">Main Menu</p>
                    <div class="space-y-1">
                        <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')" icon="home">
                            Dashboard
                        </x-nav-link>
                        <x-nav-link href="{{ route('transactions.index') }}" :active="request()->routeIs('transactions.*')" icon="list">
                            Transactions
                        </x-nav-link>
                    </div>
                </div>

                <div>
                    <p class="px-3 mb-2 text-[10px] font-bold tracking-wider uppercase text-slate-400">Finance</p>
                    <div class="space-y-1">
                        <x-nav-link href="{{ route('accounts.index') }}" :active="request()->routeIs('accounts.*')" icon="wallet">
                            Accounts
                        </x-nav-link>
                        <x-nav-link href="{{ route('income-categories.index') }}" :active="request()->routeIs('income-categories.*')" icon="arrow-down">
                            Income Categories
                        </x-nav-link>
                        <x-nav-link href="{{ route('expense-categories.index') }}" :active="request()->routeIs('expense-categories.*')" icon="arrow-up">
                            Expense Categories
                        </x-nav-link>
                    </div>
                </div>

                <div>
                    <p class="px-3 mb-2 text-[10px] font-bold tracking-wider uppercase text-slate-400">Billing & Clients</p>
                    <div class="space-y-1">
                        <x-nav-link href="{{ route('invoices.index') }}" :active="request()->routeIs('invoices.*')" icon="document-text">
                            Invoices
                        </x-nav-link>
                        <x-nav-link href="{{ route('clients.index') }}" :active="request()->routeIs('clients.*')" icon="users">
                            Clients
                        </x-nav-link>
                    </div>
                </div>

                <div>
                    <p class="px-3 mb-2 text-[10px] font-bold tracking-wider uppercase text-slate-400">Insights & Settings</p>
                    <div class="space-y-1">
                        <x-nav-link href="{{ route('reports.index') }}" :active="request()->routeIs('reports.*')" icon="chart">
                            Reports & Analytics
                        </x-nav-link>
                        <x-nav-link href="{{ route('settings.edit') }}" :active="request()->routeIs('settings.*')" icon="cog">
                            Settings
                        </x-nav-link>
                    </div>
                </div>
            </nav>

            {{-- User card at bottom --}}
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                <div class="flex items-center justify-between p-2 rounded-xl bg-white border border-slate-200/70 shadow-xs">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="relative w-9 h-9 rounded-xl bg-gradient-to-tr from-emerald-500 to-teal-500 flex items-center justify-center text-white font-bold text-xs shadow-sm">
                            {{ substr(auth()->user()->name ?? 'U', 0, 2) }}
                            <span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 bg-emerald-500 rounded-full border-2 border-white animate-beacon"></span>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-slate-900 truncate">{{ auth()->user()->name }}</p>
                            <p class="text-[11px] text-slate-400 truncate">{{ auth()->user()->email }}</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Log out">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        {{-- Main Content Area --}}
        <div class="lg:pl-72 flex flex-col min-h-screen">
            {{-- Top Sticky Navigation Header --}}
            <header class="sticky top-0 z-30 flex items-center justify-between h-20 px-4 sm:px-6 lg:px-8 bg-white/80 backdrop-blur-xl border-b border-slate-200/80 transition-shadow">
                <div class="flex items-center gap-3">
                    <button type="button" class="lg:hidden p-2 text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-xl transition" onclick="toggleMobileMenu()">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>
                    <div>
                        <h1 class="text-xl sm:text-2xl font-extrabold tracking-tight text-slate-900">@yield('page-title', 'Dashboard')</h1>
                        <p class="text-xs text-slate-400 hidden sm:block">Welcome back, {{ auth()->user()->name }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    @yield('page-actions')
                </div>
            </header>

            {{-- Flash messages --}}
            @if(session('success'))
                <x-alert type="success" :message="session('success')" />
            @endif
            @if(session('error'))
                <x-alert type="error" :message="session('error')" />
            @endif

            {{-- Main view slot --}}
            <main class="flex-1 px-4 py-8 sm:px-6 lg:px-8 max-w-7xl w-full mx-auto">
                {{ $slot }}
            </main>

            {{-- Subtle modern footer --}}
            <footer class="px-6 py-4 border-t border-slate-200/60 text-center text-xs text-slate-400">
                <span>Finance Tracker &copy; {{ date('Y') }} &bull; Ultra Modern Edition</span>
            </footer>
        </div>
    </div>

    <script>
        function toggleMobileMenu() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobile-menu-overlay');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }
    </script>
    @stack('scripts')
</body>
</html>
