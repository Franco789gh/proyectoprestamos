@extends('layouts.app')

@section('title', 'Oficinas')
@section('header_title', 'Catálogo de Oficinas y Dependencias')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Catálogo de Oficinas</h2>
            <p class="text-xs text-slate-500">Estandarización de dependencias y áreas de la institución</p>
        </div>
        <a href="{{ route('offices.create') }}" 
           class="inline-flex items-center px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-sm transition-colors cursor-pointer">
            <i data-lucide="plus-circle" class="w-4 h-4 mr-2"></i>
            <span>Nueva Oficina</span>
        </a>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 overflow-hidden">
        @if($offices->isEmpty())
            <div class="p-12 text-center space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                    <i data-lucide="building" class="w-6 h-6"></i>
                </div>
                <h4 class="text-sm font-semibold text-slate-700">No hay oficinas registradas</h4>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-3.5">Código</th>
                            <th class="px-6 py-3.5">Nombre de la Oficina</th>
                            <th class="px-6 py-3.5">Descripción / Finalidad</th>
                            <th class="px-6 py-3.5 text-center">Personal Registrado</th>
                            <th class="px-6 py-3.5">Estado</th>
                            <th class="px-6 py-3.5 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @foreach($offices as $office)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 font-mono font-bold text-blue-600">
                                    {{ $office->code ?? '-' }}
                                </td>
                                <td class="px-6 py-4 font-bold text-slate-900">
                                    {{ $office->name }}
                                </td>
                                <td class="px-6 py-4 text-slate-600">
                                    {{ $office->description ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-2 py-0.5 rounded-full font-bold text-[11px] bg-slate-100 text-slate-700">
                                        {{ $office->workers_count }} personas
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($office->is_active)
                                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">Activa</span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-200 text-slate-700">Inactiva</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right space-x-1 whitespace-nowrap">
                                    <a href="{{ route('offices.edit', $office) }}" class="p-1.5 text-slate-500 hover:text-slate-900 hover:bg-slate-100 rounded-lg inline-flex" title="Editar">
                                        <i data-lucide="pencil" class="w-4 h-4"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($offices->hasPages())
                <div class="px-6 py-4 border-t border-slate-100">
                    {{ $offices->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
