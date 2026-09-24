@extends('layouts.app')

@section('title', 'Detalle de Préstamo ' . $loan->loan_code)
@section('header_title', 'Préstamo ' . $loan->loan_code)

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Header Banner -->
    <div class="bg-white rounded-2xl p-6 shadow-xs border border-slate-200/80 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="space-y-1">
            <div class="flex items-center space-x-3">
                <span class="font-mono text-xl sm:text-2xl font-black text-blue-600 tracking-tight">{{ $loan->loan_code }}</span>
                @if($loan->status === 'activo')
                    @if($loan->isOverdue())
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800">
                            <span class="w-2 h-2 rounded-full bg-rose-600 mr-1.5 animate-ping"></span>
                            Vencido
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
                            <span class="w-2 h-2 rounded-full bg-blue-600 mr-1.5"></span>
                            Prestado / En Curso
                        </span>
                    @endif
                @elseif($loan->status === 'devuelto')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                        <span class="w-2 h-2 rounded-full bg-emerald-600 mr-1.5"></span>
                        Devuelto Conforme
                    </span>
                @elseif($loan->status === 'devuelto_con_incidencia')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                        <span class="w-2 h-2 rounded-full bg-amber-600 mr-1.5"></span>
                        Devuelto con Incidencia
                    </span>
                @endif
            </div>
            <p class="text-xs text-slate-500">
                Registrado el {{ $loan->created_at->format('d/m/Y \a \l\a\s H:i') }} por <span class="font-semibold text-slate-700">{{ $loan->loanUser?->name }}</span>
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            @if($loan->status === 'activo')
                <a href="{{ route('loans.return', $loan) }}" 
                   class="inline-flex items-center px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm transition-colors cursor-pointer">
                    <i data-lucide="check-square" class="w-4 h-4 mr-1.5"></i>
                    <span>Recepcionar Devolución</span>
                </a>
            @endif

            <a href="{{ route('loans.stream-pdf', $loan) }}" target="_blank" 
               class="inline-flex items-center px-3.5 py-2.5 rounded-xl border border-slate-300 hover:bg-slate-50 text-slate-700 font-semibold text-xs transition-colors">
                <i data-lucide="printer" class="w-4 h-4 mr-1.5"></i>
                <span>Imprimir / Ver</span>
            </a>

            <a href="{{ route('loans.pdf', $loan) }}" 
               class="inline-flex items-center px-3.5 py-2.5 rounded-xl border border-slate-300 hover:bg-slate-50 text-slate-700 font-semibold text-xs transition-colors">
                <i data-lucide="download" class="w-4 h-4 mr-1.5"></i>
                <span>Descargar PDF</span>
            </a>

            <a href="{{ route('loans.index') }}" 
               class="inline-flex items-center px-3.5 py-2.5 rounded-xl border border-slate-200 text-slate-500 hover:text-slate-800 text-xs">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i>
                <span>Volver</span>
            </a>
        </div>
    </div>

    <!-- Grid Details -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Card 1: Solicitante -->
        <div class="bg-white rounded-2xl p-6 shadow-xs border border-slate-200/80 space-y-4">
            <div class="flex items-center space-x-2 pb-3 border-b border-slate-100">
                <i data-lucide="user" class="w-5 h-5 text-blue-600"></i>
                <h3 class="font-bold text-sm text-slate-900">Datos del Solicitante</h3>
            </div>

            <div class="space-y-2 text-xs">
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-500 font-medium">Trabajador:</span>
                    <span class="font-bold text-slate-900 text-right">{{ $loan->worker?->full_name }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-500 font-medium">DNI:</span>
                    <span class="font-mono font-bold text-slate-800">{{ $loan->worker?->dni }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-500 font-medium">Oficina:</span>
                    <span class="font-semibold text-blue-700 text-right">{{ $loan->worker?->office?->name }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-500 font-medium">Cargo:</span>
                    <span class="text-slate-800 text-right">{{ $loan->worker?->position ?? '-' }}</span>
                </div>
                <div class="flex justify-between py-1">
                    <span class="text-slate-500 font-medium">Teléfono / Email:</span>
                    <span class="text-slate-800 text-right">{{ $loan->worker?->phone ?? '-' }} / {{ $loan->worker?->email ?? '-' }}</span>
                </div>
            </div>
        </div>

        <!-- Card 2: Equipo -->
        <div class="bg-white rounded-2xl p-6 shadow-xs border border-slate-200/80 space-y-4">
            <div class="flex items-center space-x-2 pb-3 border-b border-slate-100">
                <i data-lucide="monitor" class="w-5 h-5 text-blue-600"></i>
                <h3 class="font-bold text-sm text-slate-900">Datos del Bien Prestado</h3>
            </div>

            <div class="space-y-2 text-xs">
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-500 font-medium">Código Patrimonial:</span>
                    <span class="font-mono font-bold text-blue-700">{{ $loan->equipment?->code }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-500 font-medium">Denominación:</span>
                    <span class="font-bold text-slate-900 text-right">{{ $loan->equipment?->name }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-500 font-medium">Marca y Modelo:</span>
                    <span class="text-slate-800 text-right">{{ $loan->equipment?->brand }} {{ $loan->equipment?->model }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-500 font-medium">Número de Serie:</span>
                    <span class="font-mono text-slate-800 text-right">{{ $loan->equipment?->serial_number ?? 'S/N' }}</span>
                </div>
                <div class="flex justify-between py-1">
                    <span class="text-slate-500 font-medium">Estado Físico Actual:</span>
                    <span class="font-semibold text-slate-800 capitalize">{{ $loan->equipment?->physical_condition }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Accesorios Entregados vs Devueltos -->
    <div class="bg-white rounded-2xl p-6 shadow-xs border border-slate-200/80 space-y-4">
        <div class="flex items-center space-x-2 pb-3 border-b border-slate-100">
            <i data-lucide="package-check" class="w-5 h-5 text-blue-600"></i>
            <h3 class="font-bold text-sm text-slate-900">Checklist de Accesorios</h3>
        </div>

        @if($loan->loanAccessories->isEmpty())
            <p class="text-xs text-slate-500 italic">No se registraron accesorios específicos para este préstamo.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-100">
                        <tr>
                            <th class="px-4 py-2.5">Accesorio</th>
                            <th class="px-4 py-2.5">Entregado en Salida</th>
                            <th class="px-4 py-2.5">Devuelto en Recepción</th>
                            <th class="px-4 py-2.5">Observaciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($loan->loanAccessories as $acc)
                            <tr>
                                <td class="px-4 py-3 font-medium text-slate-800">{{ $acc->accessory?->name }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700">
                                        <i data-lucide="check" class="w-3 h-3 mr-1"></i> Sí entregado
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    @if($loan->status === 'activo')
                                        <span class="text-slate-400 font-medium">Pendiente de devolución</span>
                                    @else
                                        @if($acc->was_returned)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-800">
                                                <i data-lucide="check" class="w-3 h-3 mr-1"></i> Devuelto
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-rose-100 text-rose-800 font-bold">
                                                <i data-lucide="x" class="w-3 h-3 mr-1"></i> NO DEVUELTO (FALTANTE)
                                            </span>
                                        @endif
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-slate-600">{{ $acc->observation ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Período, Motivo e Historial de Operadores -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Tiempos y Motivo -->
        <div class="bg-white rounded-2xl p-6 shadow-xs border border-slate-200/80 space-y-4">
            <div class="flex items-center space-x-2 pb-3 border-b border-slate-100">
                <i data-lucide="clock-3" class="w-5 h-5 text-blue-600"></i>
                <h3 class="font-bold text-sm text-slate-900">Período y Finalidad</h3>
            </div>

            <div class="space-y-2 text-xs">
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-500 font-medium">Fecha y Hora de Salida:</span>
                    <span class="font-semibold text-slate-900">{{ $loan->loan_date->format('d/m/Y H:i') }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-500 font-medium">Devolución Estimada:</span>
                    <span class="font-semibold {{ $loan->isOverdue() ? 'text-rose-600 font-bold' : 'text-slate-900' }}">
                        {{ $loan->expected_return_date->format('d/m/Y H:i') }}
                    </span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-500 font-medium">Devolución Efectiva:</span>
                    <span class="font-semibold text-slate-900">
                        {{ $loan->actual_return_date ? $loan->actual_return_date->format('d/m/Y H:i') : 'Pendiente' }}
                    </span>
                </div>
                <div class="py-1">
                    <span class="text-slate-500 font-medium block mb-1">Motivo / Destino:</span>
                    <p class="p-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 font-medium">
                        {{ $loan->purpose }}
                    </p>
                </div>
                @if($loan->loan_notes)
                    <div class="py-1">
                        <span class="text-slate-500 font-medium block mb-1">Observaciones de Salida:</span>
                        <p class="p-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-700">
                            {{ $loan->loan_notes }}
                        </p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Operadores & Devolución -->
        <div class="bg-white rounded-2xl p-6 shadow-xs border border-slate-200/80 space-y-4">
            <div class="flex items-center space-x-2 pb-3 border-b border-slate-100">
                <i data-lucide="shield-check" class="w-5 h-5 text-blue-600"></i>
                <h3 class="font-bold text-sm text-slate-900">Auditoría y Responsables</h3>
            </div>

            <div class="space-y-2 text-xs">
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-500 font-medium">Encargado que Entregó:</span>
                    <span class="font-bold text-slate-900">{{ $loan->loanUser?->name ?? 'Sistema' }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-500 font-medium">Encargado que Recepcionó:</span>
                    <span class="font-bold text-slate-900">{{ $loan->returnUser?->name ?? 'Pendiente' }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-500 font-medium">Condición en Devolución:</span>
                    <span class="font-semibold text-slate-800 capitalize">{{ $loan->equipment_return_condition ?? '-' }}</span>
                </div>
                @if($loan->return_notes)
                    <div class="py-1">
                        <span class="text-slate-500 font-medium block mb-1">Observaciones en Devolución:</span>
                        <p class="p-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-700">
                            {{ $loan->return_notes }}
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Registro de Incidencias si existen -->
    @if($loan->incidents->isNotEmpty())
        <div class="bg-amber-50 rounded-2xl p-6 shadow-xs border border-amber-200 space-y-4">
            <div class="flex items-center space-x-2 pb-3 border-b border-amber-200/60">
                <i data-lucide="alert-triangle" class="w-5 h-5 text-amber-700"></i>
                <h3 class="font-bold text-sm text-amber-950">Bitácora de Incidencias Registradas</h3>
            </div>

            <div class="space-y-3">
                @foreach($loan->incidents as $inc)
                    <div class="p-4 rounded-xl bg-white border border-amber-200 space-y-2 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-amber-900 uppercase tracking-wider text-[11px]">
                                {{ str_replace('_', ' ', $inc->incident_type) }}
                            </span>
                            <span class="px-2 py-0.5 rounded-full font-bold text-[10px] bg-amber-100 text-amber-800">
                                {{ ucfirst($inc->status) }}
                            </span>
                        </div>
                        <p class="text-slate-800 whitespace-pre-line">{{ $inc->description }}</p>
                        @if($inc->resolution_notes)
                            <div class="pt-2 border-t border-slate-100 text-slate-600">
                                <span class="font-semibold">Resolución:</span> {{ $inc->resolution_notes }}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
