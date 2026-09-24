@extends('layouts.app')

@section('title', 'Nuevo Usuario')
@section('header_title', 'Crear Usuario del Sistema')

@section('content')
<div class="max-w-xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Alta de Usuario</h2>
            <p class="text-xs text-slate-500">Cree una cuenta para un nuevo operador o administrador</p>
        </div>
        <a href="{{ route('users.index') }}" class="inline-flex items-center text-xs font-semibold text-slate-600 hover:text-slate-900">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i>
            <span>Volver</span>
        </a>
    </div>

    <form action="{{ route('users.store') }}" method="POST" class="bg-white rounded-2xl p-6 shadow-xs border border-slate-200/80 space-y-4">
        @csrf

        <div>
            <label for="name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                Nombre Completo <span class="text-rose-500">*</span>
            </label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required placeholder="Carlos Mendoza"
                   class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm focus:bg-white focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600">
            @error('name')
                <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                Correo Electrónico <span class="text-rose-500">*</span>
            </label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required placeholder="carlos@prestamos.gob.pe"
                   class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm focus:bg-white focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600">
            @error('email')
                <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                Contraseña Inicial <span class="text-rose-500">*</span>
            </label>
            <input type="password" name="password" id="password" required placeholder="Mínimo 6 caracteres"
                   class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm focus:bg-white focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600">
            @error('password')
                <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="role" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                Rol en el Sistema <span class="text-rose-500">*</span>
            </label>
            <select name="role" id="role" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm focus:bg-white">
                <option value="encargado" selected>Encargado (Operaciones de préstamos y devoluciones)</option>
                <option value="admin">Administrador (Acceso total, auditoría y usuarios)</option>
            </select>
        </div>

        <div class="pt-4 flex items-center justify-end space-x-3 border-t border-slate-100">
            <a href="{{ route('users.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 font-semibold text-xs">
                Cancelar
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-sm transition-colors cursor-pointer">
                Guardar Usuario
            </button>
        </div>
    </form>
</div>
@endsection
