@extends('layouts.app')

@section('title', 'Inventario de Equipos')
@section('header_title', 'Inventario de Equipos y Herramientas')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Catálogo de Bienes Prestables</h2>
            <p class="text-xs text-slate-500">Gestión de inventario de equipos tecnológicos, herramientas y audiovisuales</p>
        </div>
        <a href="{{ route('equipment.create') }}" 
           class="inline-flex items-center px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-sm transition-colors cursor-pointer">
            <i data-lucide="plus-circle" class="w-4 h-4 mr-2"></i>
            <span>Nuevo Equipo</span>
        </a>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white rounded-2xl p-4 shadow-xs border border-slate-200/80">
        <form action="{{ route('equipment.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div class="sm:col-span-2 relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </div>
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Buscar por código, nombre, marca, serie..." 
                       class="w-full pl-10 pr-4 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs focus:bg-white focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600">
            </div>

            <div>
                <select name="status" class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs focus:bg-white">
                    <option value="">-- Todos los Estados --</option>
                    <option value="disponible" {{ request('status') === 'disponible' ? 'selected' : '' }}>Disponible</option>
                    <option value="prestado" {{ request('status') === 'prestado' ? 'selected' : '' }}>Prestado</option>
                    <option value="mantenimiento" {{ request('status') === 'mantenimiento' ? 'selected' : '' }}>En Mantenimiento</option>
                    <option value="danado" {{ request('status') === 'danado' ? 'selected' : '' }}>Dañado</option>
                    <option value="baja" {{ request('status') === 'baja' ? 'selected' : '' }}>De Baja</option>
                </select>
            </div>

            <div class="flex items-center space-x-2">
                <button type="submit" class="w-full px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs transition-colors">
                    Filtrar
                </button>
                @if(request()->hasAny(['search', 'status', 'category']))
                    <a href="{{ route('equipment.index') }}" class="px-3 py-2 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-600 text-xs">
                        Limpiar
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 overflow-hidden">
        @if($equipments->isEmpty())
            <div class="p-12 text-center space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                    <i data-lucide="monitor-off" class="w-6 h-6"></i>
                </div>
                <h4 class="text-sm font-semibold text-slate-700">No se encontraron equipos</h4>
                <p class="text-xs text-slate-500">No hay registros que coincidan con la búsqueda.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50/75 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-3.5">Código</th>
                            <th class="px-6 py-3.5">Denominación del Bien</th>
                            <th class="px-6 py-3.5">Marca / Modelo</th>
                            <th class="px-6 py-3.5">Categoría</th>
                            <th class="px-6 py-3.5 text-center">Accesorios</th>
                            <th class="px-6 py-3.5">Condición Física</th>
                            <th class="px-6 py-3.5">Estado / Situación</th>
                            <th class="px-6 py-3.5 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @foreach($equipments as $eq)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-6 py-4 font-mono font-bold text-blue-600">
                                    <a href="{{ route('equipment.show', $eq) }}" class="hover:underline">
                                        {{ $eq->code }}
                                    </a>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-900">{{ $eq->name }}</div>
                                    @if($eq->serial_number)
                                        <div class="text-[11px] text-slate-400 font-mono">S/N: {{ $eq->serial_number }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-slate-600">
                                    {{ $eq->brand ?? '-' }} {{ $eq->model }}
                                </td>
                                <td class="px-6 py-4 text-slate-600">
                                    <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 text-[11px] font-medium">
                                        {{ $eq->category }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-2 py-0.5 rounded-full font-bold text-[11px] bg-slate-100 text-slate-700">
                                        {{ $eq->active_accessories_count }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="capitalize font-medium {{ $eq->physical_condition === 'bueno' ? 'text-emerald-700' : ($eq->physical_condition === 'regular' ? 'text-amber-700' : 'text-rose-700') }}">
                                        {{ $eq->physical_condition }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($eq->status === 'disponible')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                            Disponible
                                        </span>
                                    @elseif($eq->status === 'prestado')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-blue-100 text-blue-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500 mr-1.5"></span>
                                            Prestado
                                        </span>
                                    @elseif($eq->status === 'mantenimiento')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-amber-100 text-amber-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5"></span>
                                            Mantenimiento
                                        </span>
                                    @elseif($eq->status === 'danado')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-rose-100 text-rose-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500 mr-1.5"></span>
                                            Dañado
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-slate-200 text-slate-700">
                                            De Baja
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right space-x-1 whitespace-nowrap">
                                    @if($eq->status === 'disponible')
                                        <a href="{{ route('loans.create', ['equipment_id' => $eq->id]) }}" 
                                           class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg inline-flex" 
                                           title="Prestar este equipo">
                                            <i data-lucide="plus-circle" class="w-4 h-4"></i>
                                        </a>
                                    @endif
                                    <a href="{{ route('equipment.show', $eq) }}" class="p-1.5 text-slate-500 hover:text-slate-900 hover:bg-slate-100 rounded-lg inline-flex" title="Ver Hoja de Vida">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>
                                    <a href="{{ route('equipment.edit', $eq) }}" class="p-1.5 text-slate-500 hover:text-slate-900 hover:bg-slate-100 rounded-lg inline-flex" title="Editar">
                                        <i data-lucide="pencil" class="w-4 h-4"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($equipments->hasPages())
                <div class="px-6 py-4 border-t border-slate-100">
                    {{ $equipments->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
