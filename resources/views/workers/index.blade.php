@extends('layouts.app')

@section('title', 'Trabajadores')
@section('header_title', 'Padrón de Trabajadores (Solicitantes)')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Padrón de Personal Habilitado</h2>
            <p class="text-xs text-slate-500">Trabajadores autorizados para solicitar préstamo de equipos institucionales</p>
        </div>
        <a href="{{ route('workers.create') }}" 
           class="inline-flex items-center px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-sm transition-colors cursor-pointer">
            <i data-lucide="user-plus" class="w-4 h-4 mr-2"></i>
            <span>Nuevo Trabajador</span>
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl p-4 shadow-xs border border-slate-200/80">
        <form action="{{ route('workers.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por DNI o nombres..." 
                       class="w-full pl-10 pr-4 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs focus:bg-white">
            </div>

            <div>
                <select name="office_id" class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs focus:bg-white">
                    <option value="">-- Todas las Oficinas --</option>
                    @foreach($offices as $office)
                        <option value="{{ $office->id }}" {{ request('office_id') == $office->id ? 'selected' : '' }}>
                            {{ $office->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center space-x-2">
                <button type="submit" class="w-full px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs">
                    Filtrar
                </button>
                @if(request()->hasAny(['search', 'office_id']))
                    <a href="{{ route('workers.index') }}" class="px-3 py-2 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-600 text-xs">
                        Limpiar
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 overflow-hidden">
        @if($workers->isEmpty())
            <div class="p-12 text-center space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                    <i data-lucide="users" class="w-6 h-6"></i>
                </div>
                <h4 class="text-sm font-semibold text-slate-700">No se encontraron trabajadores</h4>
                <p class="text-xs text-slate-500">No hay registros con los criterios buscados.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-3.5">DNI</th>
                            <th class="px-6 py-3.5">Apellidos y Nombres</th>
                            <th class="px-6 py-3.5">Oficina</th>
                            <th class="px-6 py-3.5">Cargo</th>
                            <th class="px-6 py-3.5 text-center">Préstamos Activos</th>
                            <th class="px-6 py-3.5">Estado</th>
                            <th class="px-6 py-3.5 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @foreach($workers as $worker)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 font-mono font-bold text-slate-900">
                                    {{ $worker->dni }}
                                </td>
                                <td class="px-6 py-4 font-semibold text-slate-900">
                                    {{ $worker->full_name }}
                                </td>
                                <td class="px-6 py-4 text-slate-600">
                                    {{ $worker->office?->name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-slate-600">
                                    {{ $worker->position ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($worker->active_loans_count > 0)
                                        <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-blue-100 text-blue-800">
                                            {{ $worker->active_loans_count }} en uso
                                        </span>
                                    @else
                                        <span class="text-slate-400">0</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($worker->is_active)
                                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">Activo</span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-200 text-slate-700">Inactivo</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right space-x-1 whitespace-nowrap">
                                    <a href="{{ route('workers.edit', $worker) }}" class="p-1.5 text-slate-500 hover:text-slate-900 hover:bg-slate-100 rounded-lg inline-flex" title="Editar">
                                        <i data-lucide="pencil" class="w-4 h-4"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($workers->hasPages())
                <div class="px-6 py-4 border-t border-slate-100">
                    {{ $workers->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
