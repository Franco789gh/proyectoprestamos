@extends('layouts.app')

@section('title', 'Préstamos y Devoluciones')
@section('header_title', 'Gestión de Préstamos y Devoluciones')

@section('content')
<div class="space-y-6">
    <!-- Header & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Control de Préstamos</h2>
            <p class="text-xs text-slate-500">Historial completo, préstamos en curso y recepción de devoluciones</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('loans.create') }}" 
               class="inline-flex items-center px-4 py-2.5 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-xs font-bold shadow-md shadow-blue-500/20 transition-all cursor-pointer">
                <i data-lucide="plus-circle" class="w-4 h-4 mr-2"></i>
                <span>Nuevo Préstamo</span>
            </a>
        </div>
    </div>

    <!-- Status Tabs -->
    <div class="flex items-center space-x-2 border-b border-slate-200 overflow-x-auto pb-px">
        <a href="{{ route('loans.index', ['status' => 'activos', 'search' => request('search')]) }}" 
           class="flex items-center space-x-2 px-4 py-3 text-xs font-bold border-b-2 whitespace-nowrap transition-all {{ $statusTab === 'activos' ? 'border-blue-600 text-blue-600 bg-blue-50/50 rounded-t-xl' : 'border-transparent text-slate-500 hover:text-slate-800 hover:border-slate-300' }}">
            <i data-lucide="clock" class="w-4 h-4"></i>
            <span>En Curso / Activos</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-100 text-blue-800">{{ $counts['activos'] }}</span>
        </a>

        <a href="{{ route('loans.index', ['status' => 'vencidos', 'search' => request('search')]) }}" 
           class="flex items-center space-x-2 px-4 py-3 text-xs font-bold border-b-2 whitespace-nowrap transition-all {{ $statusTab === 'vencidos' ? 'border-rose-600 text-rose-600 bg-rose-50/50 rounded-t-xl' : 'border-transparent text-slate-500 hover:text-slate-800 hover:border-slate-300' }}">
            <i data-lucide="alert-octagon" class="w-4 h-4"></i>
            <span>Vencidos</span>
            @if($counts['vencidos'] > 0)
                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-500 text-white animate-pulse">{{ $counts['vencidos'] }}</span>
            @else
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">0</span>
            @endif
        </a>

        <a href="{{ route('loans.index', ['status' => 'devueltos', 'search' => request('search')]) }}" 
           class="flex items-center space-x-2 px-4 py-3 text-xs font-bold border-b-2 whitespace-nowrap transition-all {{ $statusTab === 'devueltos' ? 'border-emerald-600 text-emerald-600 bg-emerald-50/50 rounded-t-xl' : 'border-transparent text-slate-500 hover:text-slate-800 hover:border-slate-300' }}">
            <i data-lucide="check-circle" class="w-4 h-4"></i>
            <span>Devueltos Conformes</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800">{{ $counts['devueltos'] }}</span>
        </a>

        <a href="{{ route('loans.index', ['status' => 'incidencias', 'search' => request('search')]) }}" 
           class="flex items-center space-x-2 px-4 py-3 text-xs font-bold border-b-2 whitespace-nowrap transition-all {{ $statusTab === 'incidencias' ? 'border-amber-600 text-amber-600 bg-amber-50/50 rounded-t-xl' : 'border-transparent text-slate-500 hover:text-slate-800 hover:border-slate-300' }}">
            <i data-lucide="alert-triangle" class="w-4 h-4"></i>
            <span>Con Incidencias</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-800">{{ $counts['incidencias'] }}</span>
        </a>

        <a href="{{ route('loans.index', ['status' => 'todos', 'search' => request('search')]) }}" 
           class="flex items-center space-x-2 px-4 py-3 text-xs font-bold border-b-2 whitespace-nowrap transition-all {{ $statusTab === 'todos' ? 'border-slate-900 text-slate-900 bg-slate-100/70 rounded-t-xl' : 'border-transparent text-slate-500 hover:text-slate-800 hover:border-slate-300' }}">
            <i data-lucide="layers" class="w-4 h-4"></i>
            <span>Todos</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-200 text-slate-700">{{ $counts['todos'] }}</span>
        </a>
    </div>

    <!-- Search Form -->
    <div class="bg-white rounded-3xl p-4 shadow-xs border border-slate-200/80">
        <form action="{{ route('loans.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
            <input type="hidden" name="status" value="{{ $statusTab }}">
            <div class="flex-1 relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </div>
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Buscar por código PRE-XXXX, DNI, trabajador, oficina o equipo..." 
                       class="w-full pl-10 pr-4 py-2.5 rounded-2xl bg-slate-50 border border-slate-200 text-xs focus:bg-white focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600">
            </div>
            <button type="submit" class="px-5 py-2.5 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition-colors cursor-pointer">
                Filtrar
            </button>
            @if(request('search'))
                <a href="{{ route('loans.index', ['status' => $statusTab]) }}" class="px-4 py-2.5 rounded-2xl border border-slate-200 hover:bg-slate-50 text-slate-600 font-semibold text-xs flex items-center justify-center">
                    Limpiar
                </a>
            @endif
        </form>
    </div>

    <!-- Loans Table -->
    <div class="bg-white rounded-3xl shadow-xs border border-slate-200/80 overflow-hidden">
        @if($loans->isEmpty())
            <div class="p-12 text-center space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                    <i data-lucide="file-search" class="w-6 h-6"></i>
                </div>
                <h4 class="text-sm font-semibold text-slate-700">No se encontraron préstamos</h4>
                <p class="text-xs text-slate-500">No hay registros con los filtros seleccionados.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50/75 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-3.5">Código</th>
                            <th class="px-6 py-3.5">Solicitante</th>
                            <th class="px-6 py-3.5">Oficina</th>
                            <th class="px-6 py-3.5">Equipo Prestado</th>
                            <th class="px-6 py-3.5">Fecha Salida</th>
                            <th class="px-6 py-3.5">Devolución Estimada</th>
                            <th class="px-6 py-3.5">Estado</th>
                            <th class="px-6 py-3.5 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @foreach($loans as $loan)
                            @php
                                $cleanPhone = preg_replace('/[^0-9]/', '', $loan->worker?->phone ?? '');
                                $statusText = $loan->isOverdue() ? 'VENCIDO' : 'programado';
                                $waText = urlencode("Estimado(a) {$loan->worker?->full_name}, le recordamos sobre su préstamo {$loan->loan_code} ({$loan->equipment?->name}), pactado para devolver el {$loan->expected_return_date->format('d/m/Y H:i')}.");
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-6 py-4 font-mono font-black text-blue-600 whitespace-nowrap">
                                    <a href="{{ route('loans.show', $loan) }}" class="hover:underline">
                                        {{ $loan->loan_code }}
                                    </a>
                                    @if(($loan->extensions_count ?? 0) > 0)
                                        <span class="block text-[9px] font-sans font-bold text-purple-700 bg-purple-50 px-1.5 py-0.2 rounded mt-0.5 inline-block">
                                            +{{ $loan->extensions_count }} prórroga
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-900">{{ $loan->worker?->full_name }}</div>
                                    <div class="text-[11px] text-slate-500 font-mono">DNI: {{ $loan->worker?->dni }}</div>
                                </td>
                                <td class="px-6 py-4 text-slate-600">
                                    {{ $loan->worker?->office?->name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-900">{{ $loan->equipment?->name }}</div>
                                    <div class="text-[11px] text-slate-500 font-mono">{{ $loan->equipment?->code }}</div>
                                </td>
                                <td class="px-6 py-4 text-slate-600 whitespace-nowrap">
                                    {{ $loan->loan_date->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="{{ $loan->isOverdue() ? 'text-rose-600 font-bold' : 'text-slate-600 font-medium' }}">
                                        {{ $loan->expected_return_date->format('d/m/Y H:i') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($loan->status === 'activo')
                                        @if($loan->isOverdue())
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500 mr-1.5 animate-ping"></span>
                                                Vencido
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800">
                                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500 mr-1.5"></span>
                                                Prestado
                                            </span>
                                        @endif
                                    @elseif($loan->status === 'devuelto')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                            Devuelto
                                        </span>
                                    @elseif($loan->status === 'devuelto_con_incidencia')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5"></span>
                                            Incidencia
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-800">
                                            {{ ucfirst($loan->status) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right space-x-1 whitespace-nowrap">
                                    @if(!empty($cleanPhone) && $loan->status === 'activo')
                                        <a href="https://wa.me/51{{ $cleanPhone }}?text={{ $waText }}" 
                                           target="_blank" 
                                           class="p-1.5 text-emerald-600 hover:text-emerald-800 hover:bg-emerald-50 rounded-xl inline-flex transition-colors" 
                                           title="Avisar por WhatsApp">
                                            <i data-lucide="message-circle" class="w-4 h-4"></i>
                                        </a>
                                    @endif

                                    @if($loan->status === 'activo')
                                        <a href="{{ route('loans.return', $loan) }}" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-xl inline-flex font-bold" title="Recepcionar Devolución">
                                            <i data-lucide="check-square" class="w-4 h-4"></i>
                                        </a>
                                    @endif

                                    <a href="{{ route('loans.show', $loan) }}" class="p-1.5 text-slate-500 hover:text-slate-900 hover:bg-slate-100 rounded-xl inline-flex" title="Ver Detalle">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>

                                    <a href="{{ route('loans.stream-pdf', $loan) }}" target="_blank" class="p-1.5 text-slate-500 hover:text-rose-600 hover:bg-rose-50 rounded-xl inline-flex" title="Constancia PDF">
                                        <i data-lucide="printer" class="w-4 h-4"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($loans->hasPages())
                <div class="px-6 py-4 border-t border-slate-100">
                    {{ $loans->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
