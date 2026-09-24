@extends('layouts.app')

@section('title', 'Usuarios y Roles')
@section('header_title', 'Administración de Usuarios')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Usuarios del Sistema</h2>
            <p class="text-xs text-slate-500">Gestión de cuentas y niveles de acceso (Administrador / Encargado)</p>
        </div>
        <a href="{{ route('users.create') }}" 
           class="inline-flex items-center px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-sm transition-colors cursor-pointer">
            <i data-lucide="user-plus" class="w-4 h-4 mr-2"></i>
            <span>Nuevo Usuario</span>
        </a>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3.5">Nombre</th>
                        <th class="px-6 py-3.5">Correo Electrónico</th>
                        <th class="px-6 py-3.5">Rol</th>
                        <th class="px-6 py-3.5">Estado</th>
                        <th class="px-6 py-3.5 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @foreach($users as $u)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 font-bold text-slate-900">
                                {{ $u->name }}
                            </td>
                            <td class="px-6 py-4 text-slate-600 font-mono">
                                {{ $u->email }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $u->role === 'admin' ? 'bg-indigo-100 text-indigo-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ ucfirst($u->role) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($u->is_active)
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">Activo</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-200 text-slate-700">Inactivo</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right space-x-1 whitespace-nowrap">
                                <a href="{{ route('users.edit', $u) }}" class="p-1.5 text-slate-500 hover:text-slate-900 hover:bg-slate-100 rounded-lg inline-flex" title="Editar">
                                    <i data-lucide="pencil" class="w-4 h-4"></i>
                                </a>
                                @if($u->id !== auth()->id())
                                    <form action="{{ route('users.toggle', $u) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="p-1.5 text-slate-500 hover:text-slate-900 hover:bg-slate-100 rounded-lg inline-flex" title="{{ $u->is_active ? 'Desactivar' : 'Activar' }}">
                                            <i data-lucide="{{ $u->is_active ? 'user-x' : 'user-check' }}" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
