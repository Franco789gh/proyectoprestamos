@extends('layouts.app')

@section('title', 'Reportes y Exportación')
@section('header_title', 'Reportes y Estadísticas de Préstamos')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Módulo de Reportes</h2>
            <p class="text-xs text-slate-500">Filtrado avanzado y exportación oficial en PDF y formato Excel (CSV)</p>
        </div>

        <div class="flex items-center space-x-2">
            <a href="{{ route('reports.pdf', request()->all()) }}" 
               class="inline-flex items-center px-4 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-semibold text-xs shadow-sm transition-colors cursor-pointer">
                <i data-lucide="file-text" class="w-4 h-4 mr-2"></i>
                <span>Descargar PDF</span>
            </a>
            <a href="{{ route('reports.csv', request()->all()) }}" 
               class="inline-flex items-center px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs shadow-sm transition-colors cursor-pointer">
                <i data-lucide="file-spreadsheet" class="w-4 h-4 mr-2"></i>
                <span>Exportar Excel (CSV)</span>
            </a>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="bg-white rounded-2xl p-6 shadow-xs border border-slate-200/80 space-y-4">
        <form action="{{ route('reports.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div>
                <label for="date_from" class="block text-[11px] font-semibold text-slate-600 uppercase tracking-wider mb-1">Desde Fecha</label>
                <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" 
                       class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs focus:bg-white">
            </div>

            <div>
                <label for="date_to" class="block text-[11px] font-semibold text-slate-600 uppercase tracking-wider mb-1">Hasta Fecha</label>
                <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" 
                       class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs focus:bg-white">
            </div>

            <div>
                <label for="office_id" class="block text-[11px] font-semibold text-slate-600 uppercase tracking-wider mb-1">Oficina</label>
                <select name="office_id" id="office_id" class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs focus:bg-white">
                    <option value="">-- Todas --</option>
                    @foreach($offices as $off)
                        <option value="{{ $off->id }}" {{ request('office_id') == $off->id ? 'selected' : '' }}>{{ $off->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="equipment_id" class="block text-[11px] font-semibold text-slate-600 uppercase tracking-wider mb-1">Equipo</label>
                <select name="equipment_id" id="equipment_id" class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs focus:bg-white">
                    <option value="">-- Todos --</option>
                    @foreach($equipments as $eq)
                        <option value="{{ $eq->id }}" {{ request('equipment_id') == $eq->id ? 'selected' : '' }}>{{ $eq->code }} - {{ $eq->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="status" class="block text-[11px] font-semibold text-slate-600 uppercase tracking-wider mb-1">Estado</label>
                <select name="status" id="status" class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs focus:bg-white">
                    <option value="">-- Todos --</option>
                    <option value="activo" {{ request('status') === 'activo' ? 'selected' : '' }}>Prestado / Activo</option>
                    <option value="vencido" {{ request('status') === 'vencido' ? 'selected' : '' }}>Vencido</option>
                    <option value="devuelto" {{ request('status') === 'devuelto' ? 'selected' : '' }}>Devuelto Conforme</option>
                    <option value="devuelto_con_incidencia" {{ request('status') === 'devuelto_con_incidencia' ? 'selected' : '' }}>Con Incidencia</option>
                </select>
            </div>

            <div class="lg:col-span-5 flex items-center justify-end space-x-2 pt-2 border-t border-slate-100">
                <button type="submit" class="px-5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs">
                    Aplicar Filtros
                </button>
                @if(request()->hasAny(['date_from', 'date_to', 'office_id', 'equipment_id', 'status']))
                    <a href="{{ route('reports.index') }}" class="px-4 py-2 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-600 text-xs">
                        Restablecer
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Summary Metrics -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="p-4 rounded-xl bg-white border border-slate-200/80 shadow-xs">
            <span class="text-xs font-semibold text-slate-500">Total Registros</span>
            <p class="text-xl font-bold text-slate-900 mt-1">{{ $summary['total'] }}</p>
        </div>
        <div class="p-4 rounded-xl bg-white border border-slate-200/80 shadow-xs">
            <span class="text-xs font-semibold text-emerald-600">Devueltos Conformes</span>
            <p class="text-xl font-bold text-emerald-700 mt-1">{{ $summary['devueltos'] }}</p>
        </div>
        <div class="p-4 rounded-xl bg-white border border-slate-200/80 shadow-xs">
            <span class="text-xs font-semibold text-amber-600">Con Incidencias</span>
            <p class="text-xl font-bold text-amber-700 mt-1">{{ $summary['incidencias'] }}</p>
        </div>
        <div class="p-4 rounded-xl bg-white border border-slate-200/80 shadow-xs">
            <span class="text-xs font-semibold text-blue-600">En Curso / Activos</span>
            <p class="text-xl font-bold text-blue-700 mt-1">{{ $summary['activos'] }}</p>
        </div>
    </div>

    <!-- Results Table -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 overflow-hidden">
        @if($loans->isEmpty())
            <div class="p-12 text-center text-xs text-slate-400">
                No se encontraron resultados con los filtros aplicados.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-3.5">Código</th>
                            <th class="px-6 py-3.5">Solicitante</th>
                            <th class="px-6 py-3.5">Oficina</th>
                            <th class="px-6 py-3.5">Equipo</th>
                            <th class="px-6 py-3.5">Fecha Préstamo</th>
                            <th class="px-6 py-3.5">Fecha Devolución</th>
                            <th class="px-6 py-3.5">Estado</th>
                            <th class="px-6 py-3.5">Encargado Entrega</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @foreach($loans as $loan)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 font-mono font-bold text-blue-600">
                                    <a href="{{ route('loans.show', $loan) }}" class="hover:underline">
                                        {{ $loan->loan_code }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 font-medium text-slate-900">
                                    {{ $loan->worker?->full_name }}
                                </td>
                                <td class="px-6 py-4 text-slate-600">
                                    {{ $loan->worker?->office?->name }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-slate-900">{{ $loan->equipment?->name }}</div>
                                    <div class="text-[11px] text-slate-400 font-mono">{{ $loan->equipment?->code }}</div>
                                </td>
                                <td class="px-6 py-4 text-slate-600 whitespace-nowrap">
                                    {{ $loan->loan_date->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ $loan->actual_return_date ? $loan->actual_return_date->format('d/m/Y H:i') : ($loan->expected_return_date->format('d/m/Y H:i') . ' (Est.)') }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($loan->status === 'activo')
                                        @if($loan->isOverdue())
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">Vencido</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800">Prestado</span>
                                        @endif
                                    @elseif($loan->status === 'devuelto')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Devuelto</span>
                                    @elseif($loan->status === 'devuelto_con_incidencia')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">Con Incidencia</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-slate-600">
                                    {{ $loan->loanUser?->name }}
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
