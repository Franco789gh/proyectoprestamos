@extends('layouts.app')

@section('title', 'Dashboard')
@section('header_title', 'Panel de Control')

@section('content')
<div class="space-y-6">
    <!-- Top Welcome Banner & Quick Action -->
    <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-blue-900 rounded-2xl p-6 sm:p-8 text-white shadow-xl flex flex-col md:flex-row md:items-center md:justify-between gap-6 border border-slate-700/50">
        <div class="space-y-2">
            <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-300 text-xs font-semibold">
                <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                <span>{{ \Carbon\Carbon::now()->isoFormat('LLLL') }}</span>
            </div>
            <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight">
                Gestión de Préstamos y Devoluciones
            </h2>
            <p class="text-slate-300 text-sm max-w-xl">
                Registro rápido por DNI, control estricto de accesorios y disponibilidad de inventario en tiempo real.
            </p>
        </div>

        <div class="flex flex-wrap gap-3 shrink-0">
            <a href="{{ route('loans.create') }}" 
               class="inline-flex items-center px-5 py-3 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-sm shadow-lg shadow-blue-600/30 transition-all hover:scale-105 active:scale-95 cursor-pointer">
                <i data-lucide="plus-circle" class="w-5 h-5 mr-2"></i>
                <span>Registrar Préstamo</span>
            </a>
            <a href="{{ route('loans.index', ['status' => 'activos']) }}" 
               class="inline-flex items-center px-4 py-3 rounded-xl bg-slate-800/80 hover:bg-slate-700 text-white font-semibold text-sm border border-slate-600/60 transition-colors">
                <i data-lucide="arrow-down-left" class="w-4 h-4 mr-2 text-emerald-400"></i>
                <span>Devolver Equipo</span>
            </a>
        </div>
    </div>

    <!-- Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <!-- Disponibles -->
        <div class="bg-white rounded-2xl p-5 shadow-xs border border-slate-200/80 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Disponibles</p>
                    <h3 class="text-2xl sm:text-3xl font-extrabold text-emerald-600 mt-1">{{ $availableEquipment }}</h3>
                    <p class="text-[11px] text-slate-400 mt-1">de {{ $totalEquipment }} equipos en total</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <i data-lucide="check-circle" class="w-6 h-6"></i>
                </div>
            </div>
        </div>

        <!-- Prestados -->
        <div class="bg-white rounded-2xl p-5 shadow-xs border border-slate-200/80 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Prestados Activos</p>
                    <h3 class="text-2xl sm:text-3xl font-extrabold text-blue-600 mt-1">{{ $loanedEquipment }}</h3>
                    <p class="text-[11px] text-slate-400 mt-1">en uso por personal</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <i data-lucide="arrow-right-left" class="w-6 h-6"></i>
                </div>
            </div>
        </div>

        <!-- Vencidos -->
        <div class="bg-white rounded-2xl p-5 shadow-xs border {{ $overdueLoansCount > 0 ? 'border-rose-300 bg-rose-50/30 ring-2 ring-rose-500/10' : 'border-slate-200/80' }} hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold {{ $overdueLoansCount > 0 ? 'text-rose-600' : 'text-slate-500' }} uppercase tracking-wider">Vencidos</p>
                    <h3 class="text-2xl sm:text-3xl font-extrabold {{ $overdueLoansCount > 0 ? 'text-rose-600' : 'text-slate-700' }} mt-1">{{ $overdueLoansCount }}</h3>
                    <p class="text-[11px] {{ $overdueLoansCount > 0 ? 'text-rose-500 font-medium' : 'text-slate-400' }} mt-1">
                        {{ $overdueLoansCount > 0 ? 'Requieren devolución urgente' : 'Sin retrasos pendientes' }}
                    </p>
                </div>
                <div class="w-12 h-12 rounded-2xl {{ $overdueLoansCount > 0 ? 'bg-rose-100 text-rose-600' : 'bg-slate-100 text-slate-500' }} flex items-center justify-center">
                    <i data-lucide="alert-octagon" class="w-6 h-6"></i>
                </div>
            </div>
        </div>

        <!-- Devoluciones Hoy -->
        <div class="bg-white rounded-2xl p-5 shadow-xs border border-slate-200/80 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Por Devolver Hoy</p>
                    <h3 class="text-2xl sm:text-3xl font-extrabold text-amber-600 mt-1">{{ $todayDueLoansCount }}</h3>
                    <p class="text-[11px] text-slate-400 mt-1">programados para hoy</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <i data-lucide="clock" class="w-6 h-6"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Alerta de Préstamos Vencidos (si existen) -->
    @if($overdueLoans->isNotEmpty())
        <div class="bg-rose-50 border border-rose-200 rounded-2xl p-5 shadow-xs">
            <div class="flex items-start space-x-3">
                <div class="p-2 rounded-xl bg-rose-100 text-rose-600 shrink-0">
                    <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                </div>
                <div class="flex-1">
                    <h4 class="text-sm font-bold text-rose-900">Atención: Hay {{ $overdueLoans->count() }} préstamo(s) con fecha de devolución vencida</h4>
                    <p class="text-xs text-rose-700 mt-0.5">Los siguientes equipos no han sido devueltos dentro del horario establecido:</p>
                    
                    <div class="mt-3 divide-y divide-rose-200/60">
                        @foreach($overdueLoans as $ovLoan)
                            <div class="py-2.5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                <div class="space-y-0.5">
                                    <div class="flex items-center space-x-2">
                                        <span class="font-mono font-bold text-xs text-rose-900">{{ $ovLoan->loan_code }}</span>
                                        <span class="text-xs font-semibold text-slate-800">— {{ $ovLoan->equipment?->name }} ({{ $ovLoan->equipment?->code }})</span>
                                    </div>
                                    <p class="text-xs text-slate-600">
                                        Solicitante: <span class="font-medium text-slate-900">{{ $ovLoan->worker?->full_name }}</span> ({{ $ovLoan->worker?->office?->name }})
                                        • Debió entregar: <span class="font-semibold text-rose-700">{{ $ovLoan->expected_return_date->format('d/m/Y H:i') }}</span>
                                    </p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('loans.return', $ovLoan) }}" 
                                       class="px-3 py-1.5 rounded-lg bg-rose-600 hover:bg-rose-700 text-white font-medium text-xs shadow-xs transition-colors">
                                        Recepcionar Devolución
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Tabla: Préstamos Recientes / Movimientos -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900">Movimientos Recientes de Préstamo</h3>
                <p class="text-xs text-slate-500">Últimos registros y estado de operaciones</p>
            </div>
            <a href="{{ route('loans.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-800 flex items-center">
                <span>Ver todos</span>
                <i data-lucide="chevron-right" class="w-4 h-4 ml-1"></i>
            </a>
        </div>

        @if($recentLoans->isEmpty())
            <div class="p-12 text-center space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                    <i data-lucide="inbox" class="w-6 h-6"></i>
                </div>
                <h4 class="text-sm font-semibold text-slate-700">No hay préstamos registrados aún</h4>
                <p class="text-xs text-slate-500 max-w-sm mx-auto">Comience registrando el primer préstamo para reemplazar el cuaderno tradicional.</p>
                <a href="{{ route('loans.create') }}" class="inline-flex items-center px-4 py-2 rounded-xl bg-blue-600 text-white text-xs font-semibold hover:bg-blue-700">
                    <i data-lucide="plus" class="w-4 h-4 mr-1.5"></i>
                    Registrar Primer Préstamo
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50/75 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-3.5">Código</th>
                            <th class="px-6 py-3.5">Equipo</th>
                            <th class="px-6 py-3.5">Solicitante</th>
                            <th class="px-6 py-3.5">Oficina</th>
                            <th class="px-6 py-3.5">Fecha Salida</th>
                            <th class="px-6 py-3.5">Devolución Estimada</th>
                            <th class="px-6 py-3.5">Estado</th>
                            <th class="px-6 py-3.5 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @foreach($recentLoans as $loan)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-6 py-4 font-mono font-bold text-blue-600">
                                    <a href="{{ route('loans.show', $loan) }}" class="hover:underline">
                                        {{ $loan->loan_code }}
                                    </a>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-slate-900">{{ $loan->equipment?->name }}</div>
                                    <div class="text-[11px] text-slate-500 font-mono">{{ $loan->equipment?->code }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-slate-900">{{ $loan->worker?->full_name }}</div>
                                    <div class="text-[11px] text-slate-500">DNI: {{ $loan->worker?->dni }}</div>
                                </td>
                                <td class="px-6 py-4 text-slate-600">
                                    {{ $loan->worker?->office?->name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-slate-600 whitespace-nowrap">
                                    {{ $loan->loan_date->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="{{ $loan->isOverdue() ? 'text-rose-600 font-bold' : 'text-slate-600' }}">
                                        {{ $loan->expected_return_date->format('d/m/Y H:i') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($loan->status === 'activo')
                                        @if($loan->isOverdue())
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-rose-100 text-rose-800">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500 mr-1.5 animate-ping"></span>
                                                Vencido
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-blue-100 text-blue-800">
                                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500 mr-1.5"></span>
                                                Prestado
                                            </span>
                                        @endif
                                    @elseif($loan->status === 'devuelto')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                            Devuelto
                                        </span>
                                    @elseif($loan->status === 'devuelto_con_incidencia')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-amber-100 text-amber-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5"></span>
                                            Con Incidencia
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-800">
                                            {{ ucfirst($loan->status) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right space-x-1 whitespace-nowrap">
                                    <a href="{{ route('loans.show', $loan) }}" class="p-1.5 text-slate-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg inline-flex" title="Ver Detalle">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>
                                    @if($loan->status === 'activo')
                                        <a href="{{ route('loans.return', $loan) }}" class="p-1.5 text-emerald-600 hover:bg-emerald-50 rounded-lg inline-flex" title="Devolver">
                                            <i data-lucide="check-square" class="w-4 h-4"></i>
                                        </a>
                                    @endif
                                    <a href="{{ route('loans.stream-pdf', $loan) }}" target="_blank" class="p-1.5 text-slate-500 hover:text-slate-900 hover:bg-slate-100 rounded-lg inline-flex" title="Ver Constancia PDF">
                                        <i data-lucide="printer" class="w-4 h-4"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
