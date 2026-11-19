<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Inicio') — {{ config('app.name', 'Sistema de Préstamos') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased text-slate-800 selection:bg-blue-600 selection:text-white" 
      x-data="globalApp()"
      @keydown.window.ctrl.k.prevent="openSearchModal()"
      @keydown.window.cmd.k.prevent="openSearchModal()">
    
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
             class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs z-40 lg:hidden" style="display: none;"></div>

        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
               class="fixed inset-y-0 left-0 z-50 w-72 bg-slate-950 text-white flex flex-col transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 border-r border-slate-800/80 shadow-2xl">
            <!-- Brand -->
            <div class="h-16 flex items-center justify-between px-6 bg-slate-950 border-b border-slate-800/80">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 group">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-500 flex items-center justify-center text-white shadow-lg shadow-blue-500/30 group-hover:scale-105 transition-transform">
                        <i data-lucide="boxes" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <div class="font-bold text-sm leading-tight text-white tracking-wide">CONTROL PATRIMONIAL</div>
                        <div class="text-[11px] font-semibold text-blue-400 tracking-wider">PRÉSTAMOS V1.5</div>
                    </div>
                </a>
                <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white p-1">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-4 py-5 space-y-1.5 overflow-y-auto">
                <div class="px-3 pb-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Principal</div>

                <a href="{{ route('dashboard') }}" 
                   class="flex items-center px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-900 hover:text-white' }}">
                    <i data-lucide="layout-dashboard" class="w-4 h-4 mr-3 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-slate-400' }}"></i>
                    <span>Dashboard / Inicio</span>
                </a>

                <a href="{{ route('loans.index') }}" 
                   class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('loans.*') ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-900 hover:text-white' }}">
                    <div class="flex items-center">
                        <i data-lucide="arrow-left-right" class="w-4 h-4 mr-3 {{ request()->routeIs('loans.*') ? 'text-white' : 'text-slate-400' }}"></i>
                        <span>Préstamos y Devolución</span>
                    </div>
                    @php
                        $overdueBadgeCount = \App\Models\Loan::overdue()->count();
                    @endphp
                    @if($overdueBadgeCount > 0)
                        <span class="px-2 py-0.5 text-[10px] font-extrabold rounded-full bg-rose-500 text-white animate-pulse">
                            {{ $overdueBadgeCount }}
                        </span>
                    @endif
                </a>

                <div class="pt-5 px-3 pb-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Inventario y Personal</div>

                <a href="{{ route('equipment.index') }}" 
                   class="flex items-center px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('equipment.*') ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-900 hover:text-white' }}">
                    <i data-lucide="monitor" class="w-4 h-4 mr-3 {{ request()->routeIs('equipment.*') ? 'text-white' : 'text-slate-400' }}"></i>
                    <span>Equipos y Herramientas</span>
                </a>

                <a href="{{ route('workers.index') }}" 
                   class="flex items-center px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('workers.*') ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-900 hover:text-white' }}">
                    <i data-lucide="users" class="w-4 h-4 mr-3 {{ request()->routeIs('workers.*') ? 'text-white' : 'text-slate-400' }}"></i>
                    <span>Trabajadores</span>
                </a>

                <a href="{{ route('offices.index') }}" 
                   class="flex items-center px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('offices.*') ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-900 hover:text-white' }}">
                    <i data-lucide="building-2" class="w-4 h-4 mr-3 {{ request()->routeIs('offices.*') ? 'text-white' : 'text-slate-400' }}"></i>
                    <span>Oficinas</span>
                </a>

                <a href="{{ route('reports.index') }}" 
                   class="flex items-center px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('reports.*') ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-900 hover:text-white' }}">
                    <i data-lucide="file-spreadsheet" class="w-4 h-4 mr-3 {{ request()->routeIs('reports.*') ? 'text-white' : 'text-slate-400' }}"></i>
                    <span>Reportes y Estadísticas</span>
                </a>

                @if(auth()->user() && auth()->user()->isAdmin())
                    <div class="pt-5 px-3 pb-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Administración</div>

                    <a href="{{ route('users.index') }}" 
                       class="flex items-center px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('users.*') ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-900 hover:text-white' }}">
                        <i data-lucide="shield-check" class="w-4 h-4 mr-3 {{ request()->routeIs('users.*') ? 'text-white' : 'text-slate-400' }}"></i>
                        <span>Usuarios y Roles</span>
                    </a>

                    <a href="{{ route('audit.index') }}" 
                       class="flex items-center px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('audit.*') ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-900 hover:text-white' }}">
                        <i data-lucide="history" class="w-4 h-4 mr-3 {{ request()->routeIs('audit.*') ? 'text-white' : 'text-slate-400' }}"></i>
                        <span>Auditoría de Seguridad</span>
                    </a>
                @endif
            </nav>

            <!-- User Quick Info in Sidebar Footer -->
            <div class="p-4 border-t border-slate-800/80 bg-slate-950/60">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3 min-w-0">
                        <div class="w-9 h-9 rounded-xl bg-blue-500/20 border border-blue-500/30 flex items-center justify-center text-blue-400 font-bold text-xs shrink-0 shadow-xs">
                            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-bold text-white truncate leading-tight">{{ auth()->user()->name ?? 'Usuario' }}</p>
                            <p class="text-[10px] text-blue-300/80 uppercase font-semibold tracking-wider">{{ auth()->user()->role ?? 'encargado' }}</p>
                        </div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST" class="shrink-0">
                        @csrf
                        <button type="submit" title="Cerrar Sesión" class="p-2 text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 rounded-xl transition-colors cursor-pointer">
                            <i data-lucide="log-out" class="w-4 h-4"></i>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden bg-slate-50/70">
            <!-- Top Navbar -->
            <header class="h-16 bg-white/95 backdrop-blur-md border-b border-slate-200/80 flex items-center justify-between px-4 sm:px-6 lg:px-8 shrink-0 z-20 sticky top-0 shadow-xs">
                <div class="flex items-center space-x-3 min-w-0">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 text-slate-600 hover:text-slate-900 rounded-xl hover:bg-slate-100">
                        <i data-lucide="menu" class="w-5 h-5"></i>
                    </button>
                    <h1 class="text-base sm:text-lg font-extrabold text-slate-900 truncate">
                        @yield('header_title', 'Sistema de Préstamos')
                    </h1>
                </div>

                <!-- Central/Right Controls -->
                <div class="flex items-center space-x-2 sm:space-x-3">
                    <!-- Quick Global Search Button (Ctrl+K) -->
                    <button @click="openSearchModal()" 
                            class="inline-flex items-center space-x-2 px-3 py-1.5 rounded-xl bg-slate-100/80 hover:bg-slate-200/80 border border-slate-200/70 text-slate-500 text-xs font-medium transition-all cursor-pointer">
                        <i data-lucide="search" class="w-3.5 h-3.5 text-slate-400"></i>
                        <span class="hidden md:inline">Buscar préstamo, equipo o DNI...</span>
                        <span class="md:hidden">Buscar</span>
                        <kbd class="hidden md:inline-block px-1.5 py-0.5 text-[10px] font-mono font-semibold bg-white border border-slate-300 rounded shadow-2xs text-slate-500">Ctrl K</kbd>
                    </button>

                    <!-- Notifications Bell Dropdown -->
                    @php
                        $pendingAlerts = \App\Models\Loan::with(['worker', 'equipment'])->overdue()->take(4)->get();
                        $totalAlertsCount = \App\Models\Loan::overdue()->count();
                    @endphp
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" 
                                class="relative p-2 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors cursor-pointer"
                                title="Notificaciones">
                            <i data-lucide="bell" class="w-5 h-5"></i>
                            @if($totalAlertsCount > 0)
                                <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-rose-500 animate-ping"></span>
                                <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-rose-600"></span>
                            @endif
                        </button>

                        <div x-show="open" 
                             @click.outside="open = false" 
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-2xl shadow-2xl border border-slate-200/90 z-50 overflow-hidden" 
                             style="display: none;">
                            <div class="p-4 bg-slate-900 text-white flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <i data-lucide="alert-circle" class="w-4 h-4 text-rose-400"></i>
                                    <span class="text-xs font-bold uppercase tracking-wider">Alertas y Retrasos</span>
                                </div>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/30 text-rose-300 border border-rose-500/40">
                                    {{ $totalAlertsCount }} vencidos
                                </span>
                            </div>

                            <div class="divide-y divide-slate-100 max-h-72 overflow-y-auto">
                                @forelse($pendingAlerts as $alertLoan)
                                    <div class="p-3.5 hover:bg-slate-50 transition-colors space-y-1">
                                        <div class="flex items-center justify-between">
                                            <span class="font-mono font-bold text-xs text-rose-600">{{ $alertLoan->loan_code }}</span>
                                            <span class="text-[10px] text-slate-400">{{ $alertLoan->expected_return_date->diffForHumans() }}</span>
                                        </div>
                                        <p class="text-xs font-semibold text-slate-800">{{ $alertLoan->equipment?->name }}</p>
                                        <p class="text-[11px] text-slate-500">{{ $alertLoan->worker?->full_name }}</p>
                                        <div class="pt-1 flex items-center justify-end space-x-2">
                                            <a href="{{ route('loans.return', $alertLoan) }}" class="text-[11px] font-bold text-emerald-600 hover:text-emerald-700">
                                                Recepcionar
                                            </a>
                                            <span class="text-slate-300">•</span>
                                            <a href="{{ route('loans.show', $alertLoan) }}" class="text-[11px] font-bold text-blue-600 hover:text-blue-700">
                                                Ver Detalle
                                            </a>
                                        </div>
                                    </div>
                                @empty
                                    <div class="p-6 text-center text-xs text-slate-400 space-y-1">
                                        <i data-lucide="check-check" class="w-8 h-8 text-emerald-500 mx-auto"></i>
                                        <p class="font-semibold text-slate-700 mt-2">¡Todo al día!</p>
                                        <p class="text-[11px]">No hay préstamos vencidos en este momento.</p>
                                    </div>
                                @endforelse
                            </div>

                            @if($totalAlertsCount > 4)
                                <div class="p-2.5 bg-slate-50 border-t border-slate-100 text-center">
                                    <a href="{{ route('loans.index', ['status' => 'vencidos']) }}" class="text-xs font-bold text-blue-600 hover:underline">
                                        Ver todos los {{ $totalAlertsCount }} préstamos vencidos
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Botón Rápido Registrar Préstamo -->
                    <a href="{{ route('loans.create') }}" 
                       class="inline-flex items-center px-3.5 py-2 text-xs font-bold rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white hover:from-blue-700 hover:to-indigo-700 shadow-sm shadow-blue-500/20 transition-all hover:scale-102 active:scale-98 cursor-pointer">
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
            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
                <div class="max-w-7xl mx-auto space-y-6">
                    <!-- Flash Messages -->
                    @if(session('success'))
                        <div class="flex items-center justify-between p-4 rounded-2xl bg-emerald-50 border border-emerald-200/90 text-emerald-800 shadow-xs animate-in fade-in duration-200" role="alert">
                            <div class="flex items-center space-x-3">
                                <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-600 shrink-0"></i>
                                <span class="text-xs sm:text-sm font-semibold">{{ session('success') }}</span>
                            </div>
                            <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 p-1">
                                <i data-lucide="x" class="w-4 h-4"></i>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="flex items-center justify-between p-4 rounded-2xl bg-rose-50 border border-rose-200/90 text-rose-800 shadow-xs animate-in fade-in duration-200" role="alert">
                            <div class="flex items-center space-x-3">
                                <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600 shrink-0"></i>
                                <span class="text-xs sm:text-sm font-semibold">{{ session('error') }}</span>
                            </div>
                            <button type="button" @click="$el.parentElement.remove()" class="text-rose-500 hover:text-rose-700 p-1">
                                <i data-lucide="x" class="w-4 h-4"></i>
                            </button>
                        </div>
                    @endif

                    @if(session('warning'))
                        <div class="flex items-center justify-between p-4 rounded-2xl bg-amber-50 border border-amber-200/90 text-amber-800 shadow-xs animate-in fade-in duration-200" role="alert">
                            <div class="flex items-center space-x-3">
                                <i data-lucide="alert-triangle" class="w-5 h-5 text-amber-600 shrink-0"></i>
                                <span class="text-xs sm:text-sm font-semibold">{{ session('warning') }}</span>
                            </div>
                            <button type="button" @click="$el.parentElement.remove()" class="text-amber-500 hover:text-amber-700 p-1">
                                <i data-lucide="x" class="w-4 h-4"></i>
                            </button>
                        </div>
                    @endif

                    @if(session('info'))
                        <div class="flex items-center justify-between p-4 rounded-2xl bg-blue-50 border border-blue-200/90 text-blue-800 shadow-xs animate-in fade-in duration-200" role="alert">
                            <div class="flex items-center space-x-3">
                                <i data-lucide="info" class="w-5 h-5 text-blue-600 shrink-0"></i>
                                <span class="text-xs sm:text-sm font-semibold">{{ session('info') }}</span>
                            </div>
                            <button type="button" @click="$el.parentElement.remove()" class="text-blue-500 hover:text-blue-700 p-1">
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

    <!-- Modal de Búsqueda Rápida Global (Ctrl+K) -->
    <div x-show="searchModalOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/70 backdrop-blur-xs p-4 sm:p-6 md:p-20 flex justify-center items-start"
         style="display: none;">
        
        <div class="w-full max-w-2xl bg-white rounded-3xl shadow-2xl border border-slate-200 overflow-hidden" 
             @click.outside="searchModalOpen = false">
            <div class="relative border-b border-slate-200">
                <i data-lucide="search" class="w-5 h-5 absolute left-4 top-3.5 text-slate-400"></i>
                <input type="text" 
                       x-ref="searchInput"
                       x-model="searchQuery"
                       @input.debounce.300ms="performQuickSearch()"
                       placeholder="Buscar por código de préstamo (PRE-...), equipo o DNI..."
                       class="w-full pl-12 pr-12 py-3.5 text-sm font-medium text-slate-800 placeholder-slate-400 focus:outline-none bg-transparent">
                <button @click="searchModalOpen = false" class="absolute right-4 top-3.5 text-slate-400 hover:text-slate-600">
                    <kbd class="px-1.5 py-0.5 text-xs bg-slate-100 border border-slate-300 rounded font-mono">ESC</kbd>
                </button>
            </div>

            <!-- Resultados -->
            <div class="max-h-96 overflow-y-auto p-4 space-y-4">
                <template x-if="isSearching">
                    <div class="py-6 text-center text-xs text-slate-400">
                        <i data-lucide="loader-2" class="w-5 h-5 animate-spin mx-auto text-blue-600"></i>
                        <span class="mt-2 block">Buscando en la base de datos...</span>
                    </div>
                </template>

                <template x-if="!isSearching && searchQuery.length >= 2 && (!searchResults.loans.length && !searchResults.equipment.length && !searchResults.workers.length)">
                    <div class="py-6 text-center text-xs text-slate-400">
                        No se encontraron registros para "<span class="font-bold text-slate-600" x-text="searchQuery"></span>"
                    </div>
                </template>

                <!-- Préstamos encontrados -->
                <template x-if="searchResults.loans && searchResults.loans.length > 0">
                    <div class="space-y-1.5">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Préstamos</span>
                        <template x-for="item in searchResults.loans" :key="item.id">
                            <a :href="item.url" class="flex items-center justify-between p-2.5 rounded-xl hover:bg-slate-100 transition-colors">
                                <div class="flex items-center space-x-3">
                                    <span class="font-mono font-bold text-xs text-blue-600" x-text="item.code"></span>
                                    <div>
                                        <p class="text-xs font-bold text-slate-800" x-text="item.equipment"></p>
                                        <p class="text-[11px] text-slate-500" x-text="item.worker"></p>
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold capitalize bg-slate-200 text-slate-700" x-text="item.status"></span>
                            </a>
                        </template>
                    </div>
                </template>

                <!-- Equipos encontrados -->
                <template x-if="searchResults.equipment && searchResults.equipment.length > 0">
                    <div class="space-y-1.5 pt-2 border-t border-slate-100">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Equipos</span>
                        <template x-for="eq in searchResults.equipment" :key="eq.id">
                            <a :href="eq.url" class="flex items-center justify-between p-2.5 rounded-xl hover:bg-slate-100 transition-colors">
                                <div class="flex items-center space-x-3">
                                    <span class="font-mono font-bold text-xs text-slate-900" x-text="eq.code"></span>
                                    <span class="text-xs font-semibold text-slate-800" x-text="eq.name"></span>
                                </div>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold capitalize" 
                                      :class="eq.status === 'disponible' ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800'"
                                      x-text="eq.status"></span>
                            </a>
                        </template>
                    </div>
                </template>

                <!-- Trabajadores encontrados -->
                <template x-if="searchResults.workers && searchResults.workers.length > 0">
                    <div class="space-y-1.5 pt-2 border-t border-slate-100">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Trabajadores</span>
                        <template x-for="w in searchResults.workers" :key="w.id">
                            <a :href="w.url" class="flex items-center justify-between p-2.5 rounded-xl hover:bg-slate-100 transition-colors">
                                <div class="flex items-center space-x-3">
                                    <span class="font-mono font-bold text-xs text-slate-600" x-text="w.dni"></span>
                                    <div>
                                        <p class="text-xs font-bold text-slate-800" x-text="w.name"></p>
                                        <p class="text-[11px] text-slate-500" x-text="w.office"></p>
                                    </div>
                                </div>
                                <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400"></i>
                            </a>
                        </template>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <script>
        function globalApp() {
            return {
                sidebarOpen: false,
                searchModalOpen: false,
                searchQuery: '',
                searchResults: { loans: [], equipment: [], workers: [] },
                isSearching: false,

                openSearchModal() {
                    this.searchModalOpen = true;
                    this.$nextTick(() => {
                        this.$refs.searchInput.focus();
                    });
                },

                async performQuickSearch() {
                    if (this.searchQuery.length < 2) {
                        this.searchResults = { loans: [], equipment: [], workers: [] };
                        return;
                    }
                    this.isSearching = true;
                    try {
                        const res = await fetch(`{{ route('quick-search') }}?q=${encodeURIComponent(this.searchQuery)}`);
                        const data = await res.json();
                        this.searchResults = data;
                    } catch (e) {
                        console.error('Error in search:', e);
                    } finally {
                        this.isSearching = false;
                        this.$nextTick(() => {
                            if (window.renderLucideIcons) window.renderLucideIcons();
                        });
                    }
                }
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            if (window.renderLucideIcons) {
                window.renderLucideIcons();
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
