@extends('layouts.app')

@section('title', 'Nuevo Trabajador')
@section('header_title', 'Registrar Nuevo Trabajador')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Alta de Trabajador</h2>
            <p class="text-xs text-slate-500">Ingrese los datos para que el trabajador pueda solicitar bienes</p>
        </div>
        <a href="{{ route('workers.index') }}" class="inline-flex items-center text-xs font-semibold text-slate-600 hover:text-slate-900">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i>
            <span>Volver</span>
        </a>
    </div>

    <form action="{{ route('workers.store') }}" method="POST" class="bg-white rounded-2xl p-6 shadow-xs border border-slate-200/80 space-y-4">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="dni" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                    DNI (8 Dígitos) <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="dni" id="dni" value="{{ old('dni') }}" required maxlength="8" placeholder="12345678"
                       class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm font-mono font-bold focus:bg-white focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600">
                @error('dni')
                    <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="office_id" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                    Oficina de Pertenencia <span class="text-rose-500">*</span>
                </label>
                <select name="office_id" id="office_id" required class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm focus:bg-white">
                    <option value="">-- Seleccione Oficina --</option>
                    @foreach($offices as $office)
                        <option value="{{ $office->id }}" {{ old('office_id') == $office->id ? 'selected' : '' }}>
                            {{ $office->name }}
                        </option>
                    @endforeach
                </select>
                @error('office_id')
                    <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="first_name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                    Nombres <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}" required placeholder="Juan"
                       class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm focus:bg-white focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600">
                @error('first_name')
                    <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="last_name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                    Apellidos <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" required placeholder="Pérez Gómez"
                       class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm focus:bg-white focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600">
                @error('last_name')
                    <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="position" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Cargo / Función</label>
                <input type="text" name="position" id="position" value="{{ old('position') }}" placeholder="Ej: Inspector de Obras"
                       class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm focus:bg-white focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600">
            </div>

            <div>
                <label for="phone" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Teléfono / Celular</label>
                <input type="text" name="phone" id="phone" value="{{ old('phone') }}" placeholder="987654321"
                       class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm focus:bg-white focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600">
            </div>

            <div class="sm:col-span-2">
                <label for="email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Correo Electrónico</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="trabajador@municipalidad.gob.pe"
                       class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm focus:bg-white focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600">
            </div>
        </div>

        <div class="pt-4 flex items-center justify-end space-x-3 border-t border-slate-100">
            <a href="{{ route('workers.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 font-semibold text-xs">
                Cancelar
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-sm transition-colors cursor-pointer">
                Guardar Trabajador
            </button>
        </div>
    </form>
</div>
@endsection
