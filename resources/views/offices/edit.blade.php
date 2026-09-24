@extends('layouts.app')

@section('title', 'Editar Oficina')
@section('header_title', 'Editar Oficina')

@section('content')
<div class="max-w-xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Editar Oficina</h2>
            <p class="text-xs text-slate-500">Actualizar datos de {{ $office->name }}</p>
        </div>
        <a href="{{ route('offices.index') }}" class="inline-flex items-center text-xs font-semibold text-slate-600 hover:text-slate-900">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i>
            <span>Volver</span>
        </a>
    </div>

    <form action="{{ route('offices.update', $office) }}" method="POST" class="bg-white rounded-2xl p-6 shadow-xs border border-slate-200/80 space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label for="name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                Nombre de la Oficina <span class="text-rose-500">*</span>
            </label>
            <input type="text" name="name" id="name" value="{{ old('name', $office->name) }}" required
                   class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm focus:bg-white focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600">
            @error('name')
                <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="code" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Código / Siglas</label>
            <input type="text" name="code" id="code" value="{{ old('code', $office->code) }}"
                   class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm font-mono focus:bg-white focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600">
        </div>

        <div>
            <label for="description" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Descripción</label>
            <textarea name="description" id="description" rows="2"
                      class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm focus:bg-white focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600">{{ old('description', $office->description) }}</textarea>
        </div>

        <div>
            <label class="flex items-center space-x-2 text-xs font-semibold text-slate-700 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $office->is_active) ? 'checked' : '' }}
                       class="w-4 h-4 rounded-md text-blue-600 border-slate-300">
                <span>Oficina Activa</span>
            </label>
        </div>

        <div class="pt-4 flex items-center justify-end space-x-3 border-t border-slate-100">
            <a href="{{ route('offices.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 font-semibold text-xs">
                Cancelar
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-sm transition-colors cursor-pointer">
                Actualizar Oficina
            </button>
        </div>
    </form>
</div>
@endsection
