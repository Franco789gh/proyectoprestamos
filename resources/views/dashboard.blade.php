@extends('layouts.app')

@section('title', 'Dashboard')
@section('header_title', 'Panel de Control Ejecutivo')

@section('content')
<div class="space-y-6">
    <!-- Top Welcome Banner & Quick Actions -->
    <div class="relative overflow-hidden bg-gradient-to-r from-slate-950 via-slate-900 to-indigo-950 rounded-3xl p-6 sm:p-8 text-white shadow-xl border border-slate-800">
        <!-- Background decorative ambient circles -->
        <div class="absolute -right-10 -top-10 w-64 h-64 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute right-40 -bottom-10 w-48 h-48 bg-indigo-500/10 rounded-full blur-2xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div class="space-y-2 max-w-xl">
                <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-300 text-xs font-semibold">
                    <i data-lucide="sparkles" class="w-3.5 h-3.5 text-blue-400"></i>
                    <span>{{ ucfirst(\Carbon\Carbon::now()->isoFormat('dddd, D [de] MMMM [de] YYYY')) }}</span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white">
                    Control Patrimonial de Equipos
                </h2>
                <p class="text-slate-300 text-xs sm:text-sm leading-relaxed">
                    Gestión inteligente de préstamos, trazabilidad de accesorios entregados y disponibilidad en tiempo real para todas las dependencias.
                </p>
            </div>

            <div class="flex flex-wrap gap-3 shrink-0">
                <a href="{{ route('loans.create') }}" 
                   class="inline-flex items-center px-5 py-3 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold text-xs sm:text-sm shadow-lg shadow-blue-600/30 transition-all hover:scale-105 active:scale-95 cursor-pointer">
                    <i data-lucide="plus-circle" class="w-4 h-4 sm:w-5 sm:h-5 mr-2"></i>
                    <span>Registrar Préstamo</span>
                </a>
                <a href="{{ route('loans.index', ['status' => 'activos']) }}" 
                   class="inline-flex items-center px-4 py-3 rounded-2xl bg-slate-900/90 hover:bg-slate-800 text-white font-semibold text-xs sm:text-sm border border-slate-700/80 transition-all hover:border-slate-500 shadow-xs">
                    <i data-lucide="arrow-down-left" class="w-4 h-4 mr-2 text-emerald-400"></i>
                    <span>Recepción Devolución</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Metric KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <!-- Disponibles -->
        <div class="group bg-white rounded-3xl p-5 shadow-xs border border-slate-200/80 hover:shadow-xl hover:border-emerald-300/60 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Equipos Disponibles</span>
                    <h3 class="text-3xl font-black text-slate-900 mt-1">{{ $availableEquipment }}</h3>
                    <div class="flex items-center space-x-1.5 mt-2">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                            {{ $totalEquipment > 0 ? round(($availableEquipment / $totalEquipment) * 100) : 0 }}%
                        </span>
                        <span class="text-[11px] text-slate-400">de {{ $totalEquipment }} bienes</span>
                    </div>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 group-hover:bg-emerald-600 group-hover:text-white text-emerald-600 flex items-center justify-center transition-colors shadow-sm">
                    <i data-lucide="check-circle-2" class="w-6 h-6"></i>
                </div>
            </div>
        </div>

        <!-- Prestados Activos -->
        <div class="group bg-white rounded-3xl p-5 shadow-xs border border-slate-200/80 hover:shadow-xl hover:border-blue-300/60 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Prestados en Uso</span>
                    <h3 class="text-3xl font-black text-blue-600 mt-1">{{ $loanedEquipment }}</h3>
                    <div class="flex items-center space-x-1.5 mt-2">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800">
                            {{ $activeLoansCount }} activos
                        </span>
                        <span class="text-[11px] text-slate-400">en dependencias</span>
                    </div>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-blue-50 group-hover:bg-blue-600 group-hover:text-white text-blue-600 flex items-center justify-center transition-colors shadow-sm">
                    <i data-lucide="arrow-right-left" class="w-6 h-6"></i>
                </div>
            </div>
        </div>

        <!-- Vencidos -->
        <div class="group bg-white rounded-3xl p-5 shadow-xs border {{ $overdueLoansCount > 0 ? 'border-rose-300 bg-rose-50/20' : 'border-slate-200/80' }} hover:shadow-xl hover:border-rose-400/70 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold {{ $overdueLoansCount > 0 ? 'text-rose-600 font-extrabold' : 'text-slate-400' }} uppercase tracking-wider block">
                        Préstamos Vencidos
                    </span>
                    <h3 class="text-3xl font-black {{ $overdueLoansCount > 0 ? 'text-rose-600' : 'text-slate-900' }} mt-1">
                        {{ $overdueLoansCount }}
                    </h3>
                    <div class="flex items-center space-x-1.5 mt-2">
                        @if($overdueLoansCount > 0)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-500 text-white animate-pulse">
                                Requiere acción
                            </span>
                        @else
                            <span class="text-[11px] text-emerald-600 font-semibold">Sin retrasos activos</span>
                        @endif
                    </div>
                </div>
                <div class="w-12 h-12 rounded-2xl {{ $overdueLoansCount > 0 ? 'bg-rose-100 text-rose-600' : 'bg-slate-100 text-slate-500' }} group-hover:bg-rose-600 group-hover:text-white flex items-center justify-center transition-colors shadow-sm">
                    <i data-lucide="alert-octagon" class="w-6 h-6"></i>
                </div>
            </div>
        </div>

        <!-- Por Devolver Hoy -->
        <div class="group bg-white rounded-3xl p-5 shadow-xs border border-slate-200/80 hover:shadow-xl hover:border-amber-300/60 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Por Devolver Hoy</span>
                    <h3 class="text-3xl font-black text-amber-600 mt-1">{{ $todayDueLoansCount }}</h3>
                    <div class="flex items-center space-x-1.5 mt-2">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                            Horario de retorno
                        </span>
                    </div>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-amber-50 group-hover:bg-amber-600 group-hover:text-white text-amber-600 flex items-center justify-center transition-colors shadow-sm">
                    <i data-lucide="clock" class="w-6 h-6"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Alerta Detallada de Préstamos Vencidos (con botón WhatsApp) -->
    @if($overdueLoans->isNotEmpty())
        <div class="bg-gradient-to-r from-rose-50 to-orange-50 border border-rose-200 rounded-3xl p-6 shadow-sm space-y-3">
            <div class="flex items-start justify-between">
                <div class="flex items-center space-x-3">
                    <div class="p-2.5 rounded-2xl bg-rose-600 text-white shadow-md shadow-rose-500/20">
                        <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-extrabold text-rose-950">
                            Atención Urgente: {{ $overdueLoans->count() }} Préstamo(s) han superado el plazo de entrega
                        </h4>
                        <p class="text-xs text-rose-700">Puede contactar al solicitante por WhatsApp o proceder a recepcionar la devolución:</p>
                    </div>
                </div>
                <a href="{{ route('loans.index', ['status' => 'vencidos']) }}" class="hidden sm:inline-flex text-xs font-bold text-rose-700 hover:text-rose-900 underline">
                    Ver todos los vencidos
                </a>
            </div>

            <div class="divide-y divide-rose-200/70 pt-2">
                @foreach($overdueLoans as $ovLoan)
                    @php
                        $cleanPhone = preg_replace('/[^0-9]/', '', $ovLoan->worker?->phone ?? '');
                        $waText = urlencode("Estimado(a) {$ovLoan->worker?->full_name}, le saludamos de Control Patrimonial. Le recordamos que su préstamo {$ovLoan->loan_code} del bien {$ovLoan->equipment?->name} se encuentra VENCIDO desde el {$ovLoan->expected_return_date->format('d/m/Y H:i')}. Por favor coordinar su devolución hoy.");
                    @endphp
                    <div class="py-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div class="space-y-0.5">
                            <div class="flex items-center space-x-2">
                                <span class="font-mono font-black text-xs text-rose-800 bg-rose-100 px-2 py-0.5 rounded-md">{{ $ovLoan->loan_code }}</span>
                                <span class="text-xs font-bold text-slate-900">{{ $ovLoan->equipment?->name }}</span>
                                <span class="text-[11px] font-mono text-slate-500">({{ $ovLoan->equipment?->code }})</span>
                            </div>
                            <p class="text-xs text-slate-700">
                                Solicitante: <strong class="text-slate-900">{{ $ovLoan->worker?->full_name }}</strong> • {{ $ovLoan->worker?->office?->name }}
                                <span class="text-rose-600 font-semibold">(Venció {{ $ovLoan->expected_return_date->diffForHumans() }})</span>
                            </p>
                        </div>
                        <div class="flex items-center space-x-2 shrink-0">
                            @if(!empty($cleanPhone))
                                <a href="https://wa.me/51{{ $cleanPhone }}?text={{ $waText }}" 
                                   target="_blank"
                                   class="inline-flex items-center px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs transition-colors"
                                   title="Avisar por WhatsApp">
                                    <i data-lucide="message-circle" class="w-3.5 h-3.5 mr-1.5"></i>
                                    <span>WhatsApp</span>
                                </a>
                            @endif
                            <a href="{{ route('loans.return', $ovLoan) }}" 
                               class="inline-flex items-center px-3.5 py-1.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-xs transition-colors">
                                <i data-lucide="check-square" class="w-3.5 h-3.5 mr-1.5"></i>
                                <span>Recepcionar</span>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Gráficos Interactivos (Chart.js) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Gráfico 1: Actividad de Préstamos (Últimos 7 Días) -->
        <div class="lg:col-span-2 bg-white rounded-3xl p-6 shadow-xs border border-slate-200/80 space-y-4">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                <div class="flex items-center space-x-2.5">
                    <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                        <i data-lucide="trending-up" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900">Actividad de Préstamos — Últimos 7 Días</h3>
                        <p class="text-[11px] text-slate-400">Frecuencia diaria de préstamos otorgados</p>
                    </div>
                </div>
                <span class="text-[11px] font-bold text-blue-600 bg-blue-50 px-2.5 py-1 rounded-full">
                    Total: {{ array_sum($chartLoansData) }} operaciones
                </span>
            </div>

            <div class="relative h-64 w-full">
                <canvas id="loansActivityChart"></canvas>
            </div>
        </div>

        <!-- Gráfico 2: Distribución del Parque de Equipos (Donut) -->
        <div class="bg-white rounded-3xl p-6 shadow-xs border border-slate-200/80 space-y-4">
            <div class="flex items-center space-x-2.5 pb-2 border-b border-slate-100">
                <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                    <i data-lucide="pie-chart" class="w-4 h-4"></i>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900">Estado del Parque</h3>
                    <p class="text-[11px] text-slate-400">Situación técnica de {{ $totalEquipment }} bienes</p>
                </div>
            </div>

            <div class="relative h-48 w-full flex items-center justify-center">
                <canvas id="equipmentDonutChart"></canvas>
            </div>

            <div class="grid grid-cols-2 gap-2 text-[11px] pt-1">
                <div class="flex items-center space-x-2 p-2 rounded-xl bg-emerald-50 text-emerald-900 font-semibold">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shrink-0"></span>
                    <span>Disponibles ({{ $availableEquipment }})</span>
                </div>
                <div class="flex items-center space-x-2 p-2 rounded-xl bg-blue-50 text-blue-900 font-semibold">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-500 shrink-0"></span>
                    <span>Prestados ({{ $loanedEquipment }})</span>
                </div>
                <div class="flex items-center space-x-2 p-2 rounded-xl bg-amber-50 text-amber-900 font-semibold">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500 shrink-0"></span>
                    <span>Mant. ({{ $maintenanceEquipment }})</span>
                </div>
                <div class="flex items-center space-x-2 p-2 rounded-xl bg-rose-50 text-rose-900 font-semibold">
                    <span class="w-2.5 h-2.5 rounded-full bg-rose-500 shrink-0"></span>
                    <span>Dañados ({{ $damagedEquipment }})</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Rankings: Top Equipos & Top Oficinas -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Top 5 Equipos Más Solicitados -->
        <div class="bg-white rounded-3xl p-6 shadow-xs border border-slate-200/80 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center space-x-2.5">
                    <div class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold">
                        <i data-lucide="award" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900">Top Equipos Más Solicitados</h3>
                        <p class="text-[11px] text-slate-400">Bienes con mayor índice de rotación</p>
                    </div>
                </div>
                <a href="{{ route('equipment.index') }}" class="text-xs font-bold text-blue-600 hover:underline">Ver catálogo</a>
            </div>

            <div class="space-y-3">
                @php $maxLoans = $topEquipment->max('loans_count') ?: 1; @endphp
                @forelse($topEquipment as $index => $tEq)
                    @php $pct = round(($tEq->loans_count / $maxLoans) * 100); @endphp
                    <div class="space-y-1">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-slate-800">
                                <span class="font-mono text-blue-600 mr-1.5">{{ $tEq->code }}</span>
                                {{ $tEq->name }}
                            </span>
                            <span class="font-mono font-bold text-slate-600">{{ $tEq->loans_count }} préstamos</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-2 rounded-full transition-all duration-500" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 italic">No hay suficientes registros aún.</p>
                @endforelse
            </div>
        </div>

        <!-- Top Oficinas Solicitantes -->
        <div class="bg-white rounded-3xl p-6 shadow-xs border border-slate-200/80 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center space-x-2.5">
                    <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                        <i data-lucide="building" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900">Demanda por Dependencia</h3>
                        <p class="text-[11px] text-slate-400">Oficinas con mayor uso de equipos</p>
                    </div>
                </div>
                <a href="{{ route('offices.index') }}" class="text-xs font-bold text-blue-600 hover:underline">Ver oficinas</a>
            </div>

            <div class="space-y-3">
                @php $maxOffLoans = $topOffices->max('loans_count') ?: 1; @endphp
                @forelse($topOffices as $tOff)
                    @php $offPct = round(($tOff->loans_count / $maxOffLoans) * 100); @endphp
                    <div class="space-y-1">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-slate-800">{{ $tOff->name }}</span>
                            <span class="font-mono font-bold text-slate-600">{{ $tOff->loans_count }} solicitudes</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                            <div class="bg-gradient-to-r from-emerald-500 to-teal-600 h-2 rounded-full transition-all duration-500" style="width: {{ $offPct }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 italic">No hay suficientes registros aún.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Tabla: Movimientos Recientes de Préstamo -->
    <div class="bg-white rounded-3xl shadow-xs border border-slate-200/80 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center space-x-2.5">
                <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center font-bold">
                    <i data-lucide="clock" class="w-4 h-4"></i>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900">Movimientos Recientes</h3>
                    <p class="text-[11px] text-slate-400">Historial en tiempo real de operaciones de entrega y recepción</p>
                </div>
            </div>
            <a href="{{ route('loans.index') }}" class="text-xs font-bold text-blue-600 hover:text-blue-800 flex items-center">
                <span>Ver todos los préstamos</span>
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
                            <th class="px-6 py-3.5">Equipo Prestado</th>
                            <th class="px-6 py-3.5">Solicitante</th>
                            <th class="px-6 py-3.5">Oficina</th>
                            <th class="px-6 py-3.5">Fecha y Hora</th>
                            <th class="px-6 py-3.5">Estado</th>
                            <th class="px-6 py-3.5 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @foreach($recentLoans as $loan)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-6 py-4 font-mono font-bold text-blue-600 whitespace-nowrap">
                                    <a href="{{ route('loans.show', $loan) }}" class="hover:underline">
                                        {{ $loan->loan_code }}
                                    </a>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-900">{{ $loan->equipment?->name ?? 'Equipo no encontrado' }}</div>
                                    <div class="text-[11px] text-slate-400 font-mono">{{ $loan->equipment?->code }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-900">{{ $loan->worker?->full_name }}</div>
                                    <div class="text-[11px] text-slate-400 font-mono">DNI: {{ $loan->worker?->dni }}</div>
                                </td>
                                <td class="px-6 py-4 text-slate-600">
                                    {{ $loan->worker?->office?->name }}
                                </td>
                                <td class="px-6 py-4 text-slate-600 whitespace-nowrap">
                                    <div>Salida: {{ $loan->loan_date->format('d/m/Y H:i') }}</div>
                                    <div class="text-[11px] text-slate-400">Retorno: {{ $loan->expected_return_date->format('d/m/Y H:i') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($loan->status === 'activo')
                                        @if($loan->isOverdue())
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500 mr-1.5 animate-pulse"></span>
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
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right space-x-1.5 whitespace-nowrap">
                                    @if($loan->status === 'activo')
                                        <a href="{{ route('loans.return', $loan) }}" 
                                           class="inline-flex items-center px-2.5 py-1 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold text-xs" 
                                           title="Recepcionar Devolución">
                                            <i data-lucide="check-circle" class="w-3.5 h-3.5 mr-1"></i>
                                            Devolver
                                        </a>
                                    @endif
                                    <a href="{{ route('loans.show', $loan) }}" class="p-1.5 text-slate-500 hover:text-slate-900 hover:bg-slate-100 rounded-lg inline-flex" title="Ver Detalle">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>
                                    <a href="{{ route('loans.pdf', $loan) }}" class="p-1.5 text-slate-500 hover:text-rose-600 hover:bg-rose-50 rounded-lg inline-flex" title="Descargar Constancia PDF">
                                        <i data-lucide="file-text" class="w-4 h-4"></i>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // 1. Chart Actividad de Préstamos
    const ctxLoans = document.getElementById('loansActivityChart');
    if (ctxLoans && window.Chart) {
        new Chart(ctxLoans, {
            type: 'bar',
            data: {
                labels: @json($chartDays),
                datasets: [{
                    label: 'Préstamos Registrados',
                    data: @json($chartLoansData),
                    backgroundColor: 'rgba(59, 130, 246, 0.85)',
                    hoverBackgroundColor: 'rgba(37, 99, 235, 1)',
                    borderRadius: 8,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleFont: { size: 12, weight: 'bold' },
                        bodyFont: { size: 12 },
                        padding: 10,
                        cornerRadius: 8,
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11, weight: '600' }, color: '#64748b' }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, font: { size: 11 }, color: '#64748b' },
                        grid: { color: '#f1f5f9' }
                    }
                }
            }
        });
    }

    // 2. Chart Donut Parque de Equipos
    const ctxDonut = document.getElementById('equipmentDonutChart');
    if (ctxDonut && window.Chart) {
        new Chart(ctxDonut, {
            type: 'doughnut',
            data: {
                labels: ['Disponibles', 'Prestados', 'Mantenimiento', 'Dañados'],
                datasets: [{
                    data: [
                        {{ $availableEquipment }},
                        {{ $loanedEquipment }},
                        {{ $maintenanceEquipment }},
                        {{ $damagedEquipment }}
                    ],
                    backgroundColor: [
                        '#10b981', // emerald
                        '#3b82f6', // blue
                        '#f59e0b', // amber
                        '#f43f5e'  // rose
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        padding: 10,
                        cornerRadius: 8,
                    }
                }
            }
        });
    }
});
</script>
@endpush
