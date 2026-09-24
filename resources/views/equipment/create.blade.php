@extends('layouts.app')

@section('title', 'Nuevo Equipo')
@section('header_title', 'Registrar Equipo en Inventario')

@section('content')
<div class="max-w-3xl mx-auto space-y-6" x-data="{ accessories: [''] }">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Ficha de Alta de Equipo</h2>
            <p class="text-xs text-slate-500">Registre un nuevo bien con sus características técnicas y accesorios iniciales</p>
        </div>
        <a href="{{ route('equipment.index') }}" class="inline-flex items-center text-xs font-semibold text-slate-600 hover:text-slate-900">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i>
            <span>Volver</span>
        </a>
    </div>

    <form action="{{ route('equipment.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="bg-white rounded-2xl p-6 shadow-xs border border-slate-200/80 space-y-4">
            <h3 class="text-sm font-bold text-slate-900 pb-3 border-b border-slate-100">Datos Principales del Equipo</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="code" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Código Patrimonial / Interno <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="code" id="code" value="{{ old('code') }}" required placeholder="Ej: EQ-007"
                           class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm font-mono font-bold focus:bg-white focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600">
                    @error('code')
                        <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="category" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Categoría <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="category" id="category" value="{{ old('category', 'Audiovisual') }}" required placeholder="Ej: Audiovisual, Cómputo, Herramientas"
                           class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm focus:bg-white focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600">
                </div>

                <div class="sm:col-span-2">
                    <label for="name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Denominación / Nombre del Equipo <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required placeholder="Ej: Proyector Multimedia Epson"
                           class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm focus:bg-white focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600">
                    @error('name')
                        <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="brand" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Marca</label>
                    <input type="text" name="brand" id="brand" value="{{ old('brand') }}" placeholder="Ej: Epson, Dell, Bosch"
                           class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm focus:bg-white focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600">
                </div>

                <div>
                    <label for="model" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Modelo</label>
                    <input type="text" name="model" id="model" value="{{ old('model') }}" placeholder="Ej: PowerLite X49"
                           class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm focus:bg-white focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600">
                </div>

                <div>
                    <label for="serial_number" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Número de Serie</label>
                    <input type="text" name="serial_number" id="serial_number" value="{{ old('serial_number') }}" placeholder="Ej: EPS-99321-X49"
                           class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm font-mono focus:bg-white focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600">
                </div>

                <div>
                    <label for="physical_condition" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Condición Física</label>
                    <select name="physical_condition" id="physical_condition" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm focus:bg-white">
                        <option value="bueno" {{ old('physical_condition') === 'bueno' ? 'selected' : '' }}>Bueno / Óptimo</option>
                        <option value="regular" {{ old('physical_condition') === 'regular' ? 'selected' : '' }}>Regular</option>
                        <option value="malo" {{ old('physical_condition') === 'malo' ? 'selected' : '' }}>Malo / Con fallas</option>
                    </select>
                </div>

                <div>
                    <label for="status" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Situación Operativa</label>
                    <select name="status" id="status" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm focus:bg-white">
                        <option value="disponible" selected>Disponible para Préstamo</option>
                        <option value="mantenimiento">En Mantenimiento</option>
                        <option value="danado">Dañado</option>
                        <option value="baja">De Baja</option>
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label for="notes" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Notas Técnicas / Observaciones</label>
                    <textarea name="notes" id="notes" rows="2" placeholder="Especificaciones adicionales..."
                              class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm focus:bg-white focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Accesorios Iniciales -->
        <div class="bg-white rounded-2xl p-6 shadow-xs border border-slate-200/80 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Accesorios Iniciales</h3>
                    <p class="text-xs text-slate-500">Cables, controles, estuches, cargadores asociados a este equipo</p>
                </div>
                <button type="button" @click="accessories.push('')" class="text-xs font-bold text-blue-600 hover:underline flex items-center">
                    <i data-lucide="plus" class="w-3.5 h-3.5 mr-1"></i>
                    <span>Agregar Accesorio</span>
                </button>
            </div>

            <div class="space-y-2.5">
                <template x-for="(acc, index) in accessories" :key="index">
                    <div class="flex items-center space-x-2">
                        <input type="text" 
                               name="accessories[]" 
                               x-model="accessories[index]"
                               placeholder="Ej: Cable HDMI 5m / Control remoto / Cargador 65W"
                               class="flex-1 px-3.5 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs focus:bg-white focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600">
                        <button type="button" 
                                @click="accessories.splice(index, 1)" 
                                x-show="accessories.length > 1"
                                class="p-2 text-rose-500 hover:bg-rose-50 rounded-lg">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </div>
                </template>
            </div>
        </div>

        <div class="flex items-center justify-end space-x-3">
            <a href="{{ route('equipment.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 font-semibold text-xs">
                Cancelar
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-sm transition-colors cursor-pointer">
                Guardar Equipo
            </button>
        </div>
    </form>
</div>
@endsection
