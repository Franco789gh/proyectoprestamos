@extends('layouts.app')

@section('title', 'Mi Perfil')
@section('header_title', 'Perfil de Usuario')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-xs border border-slate-200/80">
        <div class="flex items-center space-x-4 pb-6 border-b border-slate-100">
            <div class="w-16 h-16 rounded-2xl bg-blue-600 text-white flex items-center justify-center font-bold text-2xl shadow-lg shadow-blue-500/20">
                {{ substr($user->name, 0, 1) }}
            </div>
            <div>
                <h2 class="text-xl font-bold text-slate-900">{{ $user->name }}</h2>
                <div class="flex items-center space-x-2 mt-1">
                    <span class="text-xs text-slate-500">{{ $user->email }}</span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $user->role === 'admin' ? 'bg-indigo-100 text-indigo-800' : 'bg-blue-100 text-blue-800' }}">
                        {{ ucfirst($user->role) }}
                    </span>
                </div>
            </div>
        </div>

        <form action="{{ route('profile.update') }}" method="POST" class="mt-6 space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Nombre Completo</label>
                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                       class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm focus:bg-white focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600">
            </div>

            <div>
                <label for="email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Correo Electrónico (No editable)</label>
                <input type="email" value="{{ $user->email }}" disabled
                       class="w-full px-3.5 py-2.5 rounded-xl bg-slate-100 border border-slate-200 text-sm text-slate-500 cursor-not-allowed">
            </div>

            <div class="pt-4 border-t border-slate-100 space-y-4">
                <h3 class="text-sm font-bold text-slate-900">Cambiar Contraseña (Opcional)</h3>

                <div>
                    <label for="current_password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Contraseña Actual</label>
                    <input type="password" name="current_password" id="current_password"
                           class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm focus:bg-white focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="new_password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Nueva Contraseña</label>
                        <input type="password" name="new_password" id="new_password"
                               class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm focus:bg-white focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600">
                    </div>

                    <div>
                        <label for="new_password_confirmation" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Confirmar Nueva Contraseña</label>
                        <input type="password" name="new_password_confirmation" id="new_password_confirmation"
                               class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm focus:bg-white focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600">
                    </div>
                </div>
            </div>

            <div class="pt-4 flex justify-end">
                <button type="submit" class="inline-flex items-center px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm shadow-sm transition-colors">
                    <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
