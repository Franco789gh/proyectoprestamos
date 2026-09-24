@extends('layouts.app')

@section('title', 'Editar Trabajador')
@section('header_title', 'Editar Datos de Trabajador')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Editar Trabajador</h2>
            <p class="text-xs text-slate-500">Actualizar información de {{ $worker->full_name }}</p>
        </div>
        <a href="{{ route('workers.index') }}" class="inline-flex items-center text-xs font-semibold text-slate-600 hover:text-slate-900">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i>
            <span>Volver</span>
        </a>
    </div>

    <form action="{{ route('workers.update', $worker) }}" method="POST" class="bg-white rounded-2xl p-6 shadow-xs border border-slate-200/80 space-y-4">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="dni" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                    DNI <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="dni" id="dni" value="{{ old('dni', $worker->dni) }}" required maxlength="8"
                       class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm font-mono font-bold focus:bg-white focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600">
                @error('dni')
                    <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="office_id" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                    Oficina <span class="text-rose-500">*</span>
                </label>
                <select name="office_id" id="office_id" required class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm focus:bg-white">
                    @foreach($offices as $office)
                        <option value="{{ $office->id }}" {{ old('office_id', $worker->office_id) == $office->id ? 'selected' : '' }}>
                            {{ $office->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="first_name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                    Nombres <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $worker->first_name) }}" required
                       class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm focus:bg-white focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600">
            </div>

            <div>
                <label for="last_name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                    Apellidos <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $worker->last_name) }}" required
                       class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm focus:bg-white focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600">
            </div>

            <div>
                <label for="position" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Cargo</label>
                <input type="text" name="position" id="position" value="{{ old('position', $worker->position) }}"
                       class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm focus:bg-white focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600">
            </div>

            <div>
                <label for="phone" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Teléfono</label>
                <input type="text" name="phone" id="phone" value="{{ old('phone', $worker->phone) }}"
                       class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm focus:bg-white focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600">
            </div>

            <div class="sm:col-span-2">
                <label for="email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Correo</label>
                <input type="email" name="email" id="email" value="{{ old('email', $worker->email) }}"
                       class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm focus:bg-white focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600">
            </div>

            <div class="sm:col-span-2">
                <label class="flex items-center space-x-2 text-xs font-semibold text-slate-700 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $worker->is_active) ? 'checked' : '' }}
                           class="w-4 h-4 rounded-md text-blue-600 border-slate-300">
                    <span>Trabajador Activo (Habilitado para solicitar préstamos)</span>
                </label>
            </div>
        </div>

        <div class="pt-4 flex items-center justify-end space-x-3 border-t border-slate-100">
            <a href="{{ route('workers.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 font-semibold text-xs">
                Cancelar
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-sm transition-colors cursor-pointer">
                Actualizar Trabajador
            </button>
        </div>
    </form>
</div>
@endsection
