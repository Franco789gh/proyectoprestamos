@extends('layouts.app')

@section('title', 'Auditoría del Sistema')
@section('header_title', 'Bitácora y Auditoría de Seguridad')

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-xl font-bold text-slate-900">Auditoría y Trazabilidad de Operaciones</h2>
        <p class="text-xs text-slate-500">Registro inmutable de todas las acciones críticas ejecutadas por los usuarios</p>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl p-4 shadow-xs border border-slate-200/80">
        <form action="{{ route('audit.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div class="relative sm:col-span-2">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por descripción, usuario, IP..." 
                       class="w-full pl-10 pr-4 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs focus:bg-white">
            </div>

            <div>
                <select name="action" class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs focus:bg-white">
                    <option value="">-- Todas las Acciones --</option>
                    @foreach($actions as $act)
                        <option value="{{ $act }}" {{ request('action') == $act ? 'selected' : '' }}>{{ $act }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center space-x-2">
                <button type="submit" class="w-full px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs">
                    Filtrar
                </button>
                @if(request()->hasAny(['search', 'action', 'user_id', 'date']))
                    <a href="{{ route('audit.index') }}" class="px-3 py-2 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-600 text-xs">
                        Limpiar
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Logs Table -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 overflow-hidden">
        @if($logs->isEmpty())
            <div class="p-12 text-center text-xs text-slate-400">
                No hay registros de auditoría que coincidan con los filtros.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-3.5">Fecha y Hora</th>
                            <th class="px-6 py-3.5">Usuario Responsable</th>
                            <th class="px-6 py-3.5">Acción</th>
                            <th class="px-6 py-3.5">Detalle / Descripción</th>
                            <th class="px-6 py-3.5">Dirección IP</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @foreach($logs as $log)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-slate-500 font-mono text-[11px]">
                                    {{ $log->created_at->format('d/m/Y H:i:s') }}
                                </td>
                                <td class="px-6 py-4 font-semibold text-slate-900">
                                    {{ $log->user?->name ?? 'Sistema / Anónimo' }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800">
                                        {{ $log->action }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-slate-800">
                                    {{ $log->description }}
                                </td>
                                <td class="px-6 py-4 text-slate-400 font-mono text-[11px]">
                                    {{ $log->ip_address ?? '127.0.0.1' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($logs->hasPages())
                <div class="px-6 py-4 border-t border-slate-100">
                    {{ $logs->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
