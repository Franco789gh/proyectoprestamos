@extends('layouts.app')

@section('title', 'Editar Equipo ' . $equipment->code)
@section('header_title', 'Editar Equipo ' . $equipment->code)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Editar Datos del Equipo</h2>
            <p class="text-xs text-slate-500">Modifique las características técnicas o situación del bien</p>
        </div>
        <a href="{{ route('equipment.show', $equipment) }}" class="inline-flex items-center text-xs font-semibold text-slate-600 hover:text-slate-900">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i>
            <span>Volver</span>
        </a>
    </div>

    <form action="{{ route('equipment.update', $equipment) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-2xl p-6 shadow-xs border border-slate-200/80 space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="code" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Código Patrimonial <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="code" id="code" value="{{ old('code', $equipment->code) }}" required
                           class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm font-mono font-bold focus:bg-white focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600">
                    @error('code')
                        <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="category" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Categoría <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="category" id="category" value="{{ old('category', $equipment->category) }}" required
                           class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm focus:bg-white focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600">
                </div>

                <div class="sm:col-span-2">
                    <label for="name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Denominación <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name', $equipment->name) }}" required
                           class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm focus:bg-white focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600">
                    @error('name')
                        <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="brand" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Marca</label>
                    <input type="text" name="brand" id="brand" value="{{ old('brand', $equipment->brand) }}"
                           class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm focus:bg-white focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600">
                </div>

                <div>
                    <label for="model" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Modelo</label>
                    <input type="text" name="model" id="model" value="{{ old('model', $equipment->model) }}"
                           class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm focus:bg-white focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600">
                </div>

                <div>
                    <label for="serial_number" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Número de Serie</label>
                    <input type="text" name="serial_number" id="serial_number" value="{{ old('serial_number', $equipment->serial_number) }}"
                           class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm font-mono focus:bg-white focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600">
                </div>

                <div>
                    <label for="physical_condition" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Condición Física</label>
                    <select name="physical_condition" id="physical_condition" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm focus:bg-white">
                        <option value="bueno" {{ old('physical_condition', $equipment->physical_condition) === 'bueno' ? 'selected' : '' }}>Bueno / Óptimo</option>
                        <option value="regular" {{ old('physical_condition', $equipment->physical_condition) === 'regular' ? 'selected' : '' }}>Regular</option>
                        <option value="malo" {{ old('physical_condition', $equipment->physical_condition) === 'malo' ? 'selected' : '' }}>Malo / Con fallas</option>
                    </select>
                </div>

                <div>
                    <label for="status" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Situación Operativa</label>
                    <select name="status" id="status" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm focus:bg-white">
                        <option value="disponible" {{ old('status', $equipment->status) === 'disponible' ? 'selected' : '' }}>Disponible para Préstamo</option>
                        <option value="prestado" {{ old('status', $equipment->status) === 'prestado' ? 'selected' : '' }}>Prestado</option>
                        <option value="mantenimiento" {{ old('status', $equipment->status) === 'mantenimiento' ? 'selected' : '' }}>En Mantenimiento</option>
                        <option value="danado" {{ old('status', $equipment->status) === 'danado' ? 'selected' : '' }}>Dañado</option>
                        <option value="baja" {{ old('status', $equipment->status) === 'baja' ? 'selected' : '' }}>De Baja</option>
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label for="notes" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Notas / Observaciones</label>
                    <textarea name="notes" id="notes" rows="2"
                              class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm focus:bg-white focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600">{{ old('notes', $equipment->notes) }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end space-x-3">
            <a href="{{ route('equipment.show', $equipment) }}" class="px-4 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 font-semibold text-xs">
                Cancelar
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-sm transition-colors cursor-pointer">
                Actualizar Equipo
            </button>
        </div>
    </form>
</div>
@endsection
