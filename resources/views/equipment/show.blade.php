@extends('layouts.app')

@section('title', 'Hoja de Vida ' . $equipment->code)
@section('header_title', 'Hoja de Vida — ' . $equipment->code)

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Header Card -->
    <div class="bg-white rounded-2xl p-6 shadow-xs border border-slate-200/80 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="space-y-1">
            <div class="flex items-center space-x-3">
                <span class="font-mono text-2xl font-black text-blue-600">{{ $equipment->code }}</span>
                @if($equipment->status === 'disponible')
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">Disponible</span>
                @elseif($equipment->status === 'prestado')
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800">Prestado</span>
                @elseif($equipment->status === 'mantenimiento')
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">Mantenimiento</span>
                @elseif($equipment->status === 'danado')
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800">Dañado</span>
                @else
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-slate-200 text-slate-700">De Baja</span>
                @endif
            </div>
            <h2 class="text-xl font-bold text-slate-900">{{ $equipment->name }}</h2>
            <p class="text-xs text-slate-500">{{ $equipment->brand }} {{ $equipment->model }} • Categoría: {{ $equipment->category }}</p>
        </div>

        <div class="flex items-center space-x-2">
            @if($equipment->status === 'disponible')
                <a href="{{ route('loans.create', ['equipment_id' => $equipment->id]) }}" 
                   class="inline-flex items-center px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-sm transition-colors">
                    <i data-lucide="plus-circle" class="w-4 h-4 mr-1.5"></i>
                    <span>Prestar Este Equipo</span>
                </a>
            @endif
            <a href="{{ route('equipment.edit', $equipment) }}" class="inline-flex items-center px-3.5 py-2.5 rounded-xl border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-semibold">
                <i data-lucide="pencil" class="w-4 h-4 mr-1.5"></i>
                <span>Editar</span>
            </a>
            <a href="{{ route('equipment.index') }}" class="inline-flex items-center px-3.5 py-2.5 rounded-xl border border-slate-200 text-slate-500 hover:text-slate-800 text-xs">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i>
                <span>Volver</span>
            </a>
        </div>
    </div>

    <!-- Specifications & Accessories -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Specs -->
        <div class="bg-white rounded-2xl p-6 shadow-xs border border-slate-200/80 space-y-4">
            <h3 class="font-bold text-sm text-slate-900 pb-3 border-b border-slate-100 flex items-center space-x-2">
                <i data-lucide="info" class="w-4 h-4 text-blue-600"></i>
                <span>Especificaciones Técnicas</span>
            </h3>

            <div class="space-y-2 text-xs">
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-500">Número de Serie:</span>
                    <span class="font-mono font-bold text-slate-900">{{ $equipment->serial_number ?? 'S/N' }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-500">Condición Física:</span>
                    <span class="capitalize font-bold text-slate-800">{{ $equipment->physical_condition }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-500">Veces Prestado:</span>
                    <span class="font-bold text-blue-600">{{ $equipment->loans->count() }} operaciones</span>
                </div>
                @if($equipment->notes)
                    <div class="py-1">
                        <span class="text-slate-500 block mb-1">Notas:</span>
                        <p class="p-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-700">{{ $equipment->notes }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Accessories -->
        <div class="bg-white rounded-2xl p-6 shadow-xs border border-slate-200/80 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="font-bold text-sm text-slate-900 flex items-center space-x-2">
                    <i data-lucide="package" class="w-4 h-4 text-blue-600"></i>
                    <span>Accesorios Asociados ({{ $equipment->accessories->count() }})</span>
                </h3>
            </div>

            <!-- Form para agregar accesorio rápido -->
            <form action="{{ route('equipment.accessories.store', $equipment) }}" method="POST" class="flex space-x-2">
                @csrf
                <input type="text" name="name" required placeholder="Nuevo accesorio (ej: Cable HDMI)..." 
                       class="flex-1 px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-200 text-xs focus:bg-white">
                <button type="submit" class="px-3 py-1.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-semibold">
                    Agregar
                </button>
            </form>

            @if($equipment->accessories->isEmpty())
                <p class="text-xs text-slate-400 italic">No hay accesorios registrados para este equipo.</p>
            @else
                <div class="divide-y divide-slate-100 max-h-56 overflow-y-auto">
                    @foreach($equipment->accessories as $acc)
                        <div class="py-2 flex items-center justify-between text-xs">
                            <span class="font-medium {{ $acc->is_active ? 'text-slate-900' : 'text-slate-400 line-through' }}">{{ $acc->name }}</span>
                            <div class="flex items-center space-x-1">
                                <form action="{{ route('accessories.toggle', $acc) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="p-1 text-xs text-slate-400 hover:text-slate-700" title="{{ $acc->is_active ? 'Desactivar' : 'Activar' }}">
                                        <i data-lucide="{{ $acc->is_active ? 'eye' : 'eye-off' }}" class="w-3.5 h-3.5"></i>
                                    </button>
                                </form>
                                <form action="{{ route('accessories.destroy', $acc) }}" method="POST" onsubmit="return confirm('¿Eliminar accesorio?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1 text-xs text-rose-400 hover:text-rose-600" title="Eliminar">
                                        <i data-lucide="trash" class="w-3.5 h-3.5"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Hoja de Vida / Historial Completo del Equipo -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-900">Hoja de Vida y Trazabilidad de Préstamos</h3>
            <p class="text-xs text-slate-500">Historial cronológico de todos los usuarios e incidencias asociadas a este bien</p>
        </div>

        @if($equipment->loans->isEmpty())
            <div class="p-8 text-center text-xs text-slate-400">
                Este equipo aún no ha registrado ningún préstamo en el sistema.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-3.5">Código</th>
                            <th class="px-6 py-3.5">Solicitante</th>
                            <th class="px-6 py-3.5">Oficina</th>
                            <th class="px-6 py-3.5">Fecha Salida</th>
                            <th class="px-6 py-3.5">Fecha Devolución</th>
                            <th class="px-6 py-3.5">Estado Final</th>
                            <th class="px-6 py-3.5 text-right">Detalle</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @foreach($equipment->loans as $hLoan)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 font-mono font-bold text-blue-600">
                                    {{ $hLoan->loan_code }}
                                </td>
                                <td class="px-6 py-4 font-medium text-slate-900">
                                    {{ $hLoan->worker?->full_name }}
                                </td>
                                <td class="px-6 py-4 text-slate-600">
                                    {{ $hLoan->worker?->office?->name }}
                                </td>
                                <td class="px-6 py-4 text-slate-600 whitespace-nowrap">
                                    {{ $hLoan->loan_date->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 text-slate-600 whitespace-nowrap">
                                    {{ $hLoan->actual_return_date ? $hLoan->actual_return_date->format('d/m/Y H:i') : 'En uso' }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($hLoan->status === 'activo')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800">Prestado</span>
                                    @elseif($hLoan->status === 'devuelto')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Devuelto</span>
                                    @elseif($hLoan->status === 'devuelto_con_incidencia')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">Con Incidencia</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('loans.show', $hLoan) }}" class="text-blue-600 hover:underline font-semibold">
                                        Ver
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
