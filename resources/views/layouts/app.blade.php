<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Inicio') — {{ config('app.name', 'Sistema de Préstamos') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="h-full font-sans antialiased text-slate-800" x-data="{ sidebarOpen: false }">
    <div class="min-h-full flex">
        <!-- Sidebar Backdrop on Mobile -->
        <div x-show="sidebarOpen" 
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="sidebarOpen = false" 
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-40 lg:hidden" style="display: none;"></div>

        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
               class="fixed inset-y-0 left-0 z-50 w-72 bg-slate-900 text-white flex flex-col transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 border-r border-slate-800 shadow-xl">
            <!-- Brand -->
            <div class="h-16 flex items-center justify-between px-6 bg-slate-950/80 border-b border-slate-800/80">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center text-white shadow-lg shadow-blue-500/30">
                        <i data-lucide="boxes" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <div class="font-bold text-sm leading-tight text-white">SISTEMA DE</div>
                        <div class="text-xs font-semibold text-blue-400 tracking-wider">PRÉSTAMOS</div>
                    </div>
                </a>
                <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white p-1">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-4 py-5 space-y-1.5 overflow-y-auto">
                <div class="px-3 pb-2 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Principal</div>

                <a href="{{ route('dashboard') }}" 
                   class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/20' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                    <i data-lucide="layout-dashboard" class="w-4 h-4 mr-3 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-slate-400' }}"></i>
                    <span>Dashboard / Inicio</span>
                </a>

                <a href="{{ route('loans.index') }}" 
                   class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('loans.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/20' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                    <div class="flex items-center">
                        <i data-lucide="arrow-left-right" class="w-4 h-4 mr-3 {{ request()->routeIs('loans.*') ? 'text-white' : 'text-slate-400' }}"></i>
                        <span>Préstamos y Devolución</span>
                    </div>
                    @php
                        $overdueBadgeCount = \App\Models\Loan::overdue()->count();
                    @endphp
                    @if($overdueBadgeCount > 0)
                        <span class="px-2 py-0.5 text-[11px] font-bold rounded-full bg-rose-500 text-white animate-pulse">
                            {{ $overdueBadgeCount }}
                        </span>
                    @endif
                </a>

                <div class="pt-4 px-3 pb-2 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Inventario y Personal</div>

                <a href="{{ route('equipment.index') }}" 
                   class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('equipment.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/20' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                    <i data-lucide="monitor" class="w-4 h-4 mr-3 {{ request()->routeIs('equipment.*') ? 'text-white' : 'text-slate-400' }}"></i>
                    <span>Equipos y Accesorios</span>
                </a>

                <a href="{{ route('workers.index') }}" 
                   class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('workers.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/20' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                    <i data-lucide="users" class="w-4 h-4 mr-3 {{ request()->routeIs('workers.*') ? 'text-white' : 'text-slate-400' }}"></i>
                    <span>Trabajadores</span>
                </a>

                <a href="{{ route('offices.index') }}" 
                   class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('offices.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/20' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                    <i data-lucide="building-2" class="w-4 h-4 mr-3 {{ request()->routeIs('offices.*') ? 'text-white' : 'text-slate-400' }}"></i>
                    <span>Oficinas</span>
                </a>

                <a href="{{ route('reports.index') }}" 
                   class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('reports.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/20' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                    <i data-lucide="file-spreadsheet" class="w-4 h-4 mr-3 {{ request()->routeIs('reports.*') ? 'text-white' : 'text-slate-400' }}"></i>
                    <span>Reportes y Exportación</span>
                </a>

                @if(auth()->user() && auth()->user()->isAdmin())
                    <div class="pt-4 px-3 pb-2 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Administración</div>

                    <a href="{{ route('users.index') }}" 
                       class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('users.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/20' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <i data-lucide="shield-check" class="w-4 h-4 mr-3 {{ request()->routeIs('users.*') ? 'text-white' : 'text-slate-400' }}"></i>
                        <span>Usuarios y Roles</span>
                    </a>

                    <a href="{{ route('audit.index') }}" 
                       class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('audit.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/20' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <i data-lucide="history" class="w-4 h-4 mr-3 {{ request()->routeIs('audit.*') ? 'text-white' : 'text-slate-400' }}"></i>
                        <span>Auditoría del Sistema</span>
                    </a>
                @endif
            </nav>

            <!-- User Quick Info & Logout in Sidebar Footer -->
            <div class="p-4 border-t border-slate-800/80 bg-slate-950/40">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3 min-w-0">
                        <div class="w-9 h-9 rounded-full bg-blue-500/20 border border-blue-500/30 flex items-center justify-center text-blue-400 font-bold text-sm shrink-0">
                            {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-semibold text-white truncate">{{ auth()->user()->name ?? 'Usuario' }}</p>
                            <p class="text-[11px] text-slate-400 capitalize">{{ auth()->user()->role ?? 'encargado' }}</p>
                        </div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST" class="shrink-0">
                        @csrf
                        <button type="submit" title="Cerrar Sesión" class="p-2 text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 rounded-lg transition-colors">
                            <i data-lucide="log-out" class="w-4 h-4"></i>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Top Navbar -->
            <header class="h-16 bg-white border-b border-slate-200/80 flex items-center justify-between px-4 sm:px-6 lg:px-8 shrink-0 z-10">
                <div class="flex items-center space-x-3">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 text-slate-600 hover:text-slate-900 rounded-lg hover:bg-slate-100">
                        <i data-lucide="menu" class="w-5 h-5"></i>
                    </button>
                    <h1 class="text-lg font-bold text-slate-800">
                        @yield('header_title', 'Sistema de Préstamos')
                    </h1>
                </div>

                <div class="flex items-center space-x-3">
                    <!-- Botón Rápido Registrar Préstamo -->
                    <a href="{{ route('loans.create') }}" 
                       class="inline-flex items-center px-3.5 py-2 text-xs font-semibold rounded-xl bg-blue-600 text-white hover:bg-blue-700 shadow-sm shadow-blue-500/20 transition-colors">
                        <i data-lucide="plus-circle" class="w-4 h-4 mr-1.5"></i>
                        <span>Nuevo Préstamo</span>
                    </a>

                    <!-- Perfil dropdown -->
                    <a href="{{ route('profile') }}" 
                       class="p-2 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-xl transition-colors"
                       title="Mi Perfil">
                        <i data-lucide="user-cog" class="w-5 h-5"></i>
                    </a>
                </div>
            </header>

            <!-- Main Body -->
            <main class="flex-1 overflow-y-auto bg-slate-50/50 p-4 sm:p-6 lg:p-8">
                <div class="max-w-7xl mx-auto space-y-6">
                    <!-- Flash Messages -->
                    @if(session('success'))
                        <div class="flex items-center justify-between p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 shadow-xs" role="alert">
                            <div class="flex items-center space-x-3">
                                <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-600 shrink-0"></i>
                                <span class="text-sm font-medium">{{ session('success') }}</span>
                            </div>
                            <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                                <i data-lucide="x" class="w-4 h-4"></i>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="flex items-center justify-between p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 shadow-xs" role="alert">
                            <div class="flex items-center space-x-3">
                                <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600 shrink-0"></i>
                                <span class="text-sm font-medium">{{ session('error') }}</span>
                            </div>
                            <button type="button" @click="$el.parentElement.remove()" class="text-rose-500 hover:text-rose-700">
                                <i data-lucide="x" class="w-4 h-4"></i>
                            </button>
                        </div>
                    @endif

                    @if(session('warning'))
                        <div class="flex items-center justify-between p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 shadow-xs" role="alert">
                            <div class="flex items-center space-x-3">
                                <i data-lucide="alert-triangle" class="w-5 h-5 text-amber-600 shrink-0"></i>
                                <span class="text-sm font-medium">{{ session('warning') }}</span>
                            </div>
                            <button type="button" @click="$el.parentElement.remove()" class="text-amber-500 hover:text-amber-700">
                                <i data-lucide="x" class="w-4 h-4"></i>
                            </button>
                        </div>
                    @endif

                    @if(session('info'))
                        <div class="flex items-center justify-between p-4 rounded-xl bg-blue-50 border border-blue-200 text-blue-800 shadow-xs" role="alert">
                            <div class="flex items-center space-x-3">
                                <i data-lucide="info" class="w-5 h-5 text-blue-600 shrink-0"></i>
                                <span class="text-sm font-medium">{{ session('info') }}</span>
                            </div>
                            <button type="button" @click="$el.parentElement.remove()" class="text-blue-500 hover:text-blue-700">
                                <i data-lucide="x" class="w-4 h-4"></i>
                            </button>
                        </div>
                    @endif

                    <!-- View Content -->
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.lucide) {
                window.lucide.createIcons();
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
