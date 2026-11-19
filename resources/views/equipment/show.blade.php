@extends('layouts.app')

@section('title', 'Hoja de Vida ' . $equipment->code)
@section('header_title', 'Ficha Técnica y Trazabilidad — ' . $equipment->code)

@section('content')
<div class="max-w-5xl mx-auto space-y-6" x-data="{ maintenanceModalOpen: false, completeMaintenanceModalOpen: false }">
    <!-- Header Card -->
    <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-xs border border-slate-200/80 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="space-y-1.5">
            <div class="flex flex-wrap items-center gap-2.5">
                <span class="font-mono text-2xl font-black text-blue-600">{{ $equipment->code }}</span>
                @if($equipment->status === 'disponible')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                        <span class="w-2 h-2 rounded-full bg-emerald-600 mr-1.5"></span>
                        Disponible
                    </span>
                @elseif($equipment->status === 'prestado')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
                        <span class="w-2 h-2 rounded-full bg-blue-600 mr-1.5"></span>
                        Prestado en Uso
                    </span>
                @elseif($equipment->status === 'mantenimiento')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                        <span class="w-2 h-2 rounded-full bg-amber-600 mr-1.5 animate-pulse"></span>
                        En Mantenimiento
                    </span>
                @elseif($equipment->status === 'danado')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800">
                        <span class="w-2 h-2 rounded-full bg-rose-600 mr-1.5"></span>
                        Dañado / Averiado
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-slate-200 text-slate-700">
                        De Baja
                    </span>
                @endif
                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 font-mono">
                    {{ $equipment->category }}
                </span>
            </div>
            <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900">{{ $equipment->name }}</h2>
            <p class="text-xs text-slate-500 font-medium">{{ $equipment->brand }} {{ $equipment->model }} • Serie: {{ $equipment->serial_number ?? 'S/N' }}</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <!-- Imprimir Etiqueta QR -->
            <a href="{{ route('equipment.label', $equipment) }}" target="_blank"
               class="inline-flex items-center px-3.5 py-2.5 rounded-xl border border-slate-300 hover:bg-slate-50 text-slate-700 font-bold text-xs shadow-xs transition-colors">
                <i data-lucide="qr-code" class="w-4 h-4 mr-1.5 text-slate-600"></i>
                <span>Etiqueta QR</span>
            </a>

            <!-- Mantenimiento Action Buttons -->
            @if($equipment->status === 'disponible')
                <button type="button" @click="maintenanceModalOpen = true"
                        class="inline-flex items-center px-3.5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs shadow-xs transition-colors cursor-pointer">
                    <i data-lucide="wrench" class="w-4 h-4 mr-1.5"></i>
                    <span>A Mantenimiento</span>
                </button>

                <a href="{{ route('loans.create', ['equipment_id' => $equipment->id]) }}" 
                   class="inline-flex items-center px-4 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold text-xs shadow-md shadow-blue-500/20 transition-all cursor-pointer">
                    <i data-lucide="plus-circle" class="w-4 h-4 mr-1.5"></i>
                    <span>Prestar Este Equipo</span>
                </a>
            @elseif($equipment->status === 'mantenimiento')
                <button type="button" @click="completeMaintenanceModalOpen = true"
                        class="inline-flex items-center px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-500/20 transition-colors cursor-pointer">
                    <i data-lucide="check-circle" class="w-4 h-4 mr-1.5"></i>
                    <span>Finalizar Mantenimiento</span>
                </button>
            @endif

            <a href="{{ route('equipment.edit', $equipment) }}" class="inline-flex items-center px-3.5 py-2.5 rounded-xl border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-bold">
                <i data-lucide="pencil" class="w-4 h-4 mr-1.5"></i>
                <span>Editar</span>
            </a>

            <a href="{{ route('equipment.index') }}" class="inline-flex items-center px-3.5 py-2.5 rounded-xl border border-slate-200 text-slate-500 hover:text-slate-800 text-xs">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i>
                <span>Volver</span>
            </a>
        </div>
    </div>

    <!-- Banner si se encuentra en Mantenimiento -->
    @if($equipment->status === 'mantenimiento' && $equipment->currentMaintenance)
        @php $currMaint = $equipment->currentMaintenance; @endphp
        <div class="bg-amber-50 border border-amber-200 rounded-3xl p-6 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-start space-x-3.5">
                <div class="p-3 rounded-2xl bg-amber-500 text-white shadow-md shadow-amber-500/20 shrink-0">
                    <i data-lucide="wrench" class="w-5 h-5"></i>
                </div>
                <div>
                    <h4 class="text-sm font-extrabold text-amber-950">Equipo en Servicio Técnico / Mantenimiento</h4>
                    <p class="text-xs text-amber-800 mt-0.5">
                        Ingresó el {{ $currMaint->entry_date->format('d/m/Y H:i') }} • Motivo: <strong class="text-amber-950">{{ $currMaint->reason }}</strong>
                    </p>
                    @if($currMaint->provider)
                        <p class="text-[11px] text-amber-700 mt-0.5">Taller / Proveedor: {{ $currMaint->provider }}</p>
                    @endif
                </div>
            </div>

            <button type="button" @click="completeMaintenanceModalOpen = true"
                    class="inline-flex items-center px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow-xs transition-colors shrink-0 cursor-pointer">
                <i data-lucide="check" class="w-4 h-4 mr-1.5"></i>
                <span>Dar de Alta</span>
            </button>
        </div>
    @endif

    <!-- Specifications & Accessories -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Specs -->
        <div class="bg-white rounded-3xl p-6 shadow-xs border border-slate-200/80 space-y-4">
            <h3 class="font-bold text-sm text-slate-900 pb-3 border-b border-slate-100 flex items-center space-x-2">
                <i data-lucide="info" class="w-4 h-4 text-blue-600"></i>
                <span>Especificaciones Técnicas</span>
            </h3>

            <div class="space-y-2 text-xs">
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-500 font-medium">Número de Serie:</span>
                    <span class="font-mono font-bold text-slate-900">{{ $equipment->serial_number ?? 'S/N' }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-500 font-medium">Condición Física:</span>
                    <span class="capitalize font-bold {{ $equipment->physical_condition === 'bueno' ? 'text-emerald-700' : ($equipment->physical_condition === 'regular' ? 'text-amber-700' : 'text-rose-700') }}">
                        {{ $equipment->physical_condition }}
                    </span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-500 font-medium">Rotación / Veces Prestado:</span>
                    <span class="font-bold text-blue-600">{{ $equipment->loans->count() }} operaciones registradas</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-500 font-medium">Historial de Mantenimientos:</span>
                    <span class="font-bold text-slate-700">{{ $equipment->maintenances->count() }} intervenciones</span>
                </div>
                @if($equipment->notes)
                    <div class="py-1">
                        <span class="text-slate-500 font-medium block mb-1">Notas y Observaciones:</span>
                        <p class="p-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-700">{{ $equipment->notes }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Accessories -->
        <div class="bg-white rounded-3xl p-6 shadow-xs border border-slate-200/80 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="font-bold text-sm text-slate-900 flex items-center space-x-2">
                    <i data-lucide="package" class="w-4 h-4 text-blue-600"></i>
                    <span>Accesorios Asociados ({{ $equipment->accessories->count() }})</span>
                </h3>
            </div>

            <!-- Form para agregar accesorio rápido -->
            <form action="{{ route('equipment.accessories.store', $equipment) }}" method="POST" class="flex space-x-2">
                @csrf
                <input type="text" name="name" required placeholder="Nuevo accesorio (ej: Cable HDMI)..." 
                       class="flex-1 px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-200 text-xs focus:bg-white">
                <button type="submit" class="px-3.5 py-1.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition-colors cursor-pointer">
                    Agregar
                </button>
            </form>

            @if($equipment->accessories->isEmpty())
                <p class="text-xs text-slate-400 italic">No hay accesorios registrados para este equipo.</p>
            @else
                <div class="divide-y divide-slate-100 max-h-56 overflow-y-auto">
                    @foreach($equipment->accessories as $acc)
                        <div class="py-2.5 flex items-center justify-between text-xs">
                            <span class="font-semibold {{ $acc->is_active ? 'text-slate-900' : 'text-slate-400 line-through' }}">{{ $acc->name }}</span>
                            <div class="flex items-center space-x-1">
                                <form action="{{ route('accessories.toggle', $acc) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="p-1 text-xs text-slate-400 hover:text-slate-700" title="{{ $acc->is_active ? 'Desactivar' : 'Activar' }}">
                                        <i data-lucide="{{ $acc->is_active ? 'eye' : 'eye-off' }}" class="w-3.5 h-3.5"></i>
                                    </button>
                                </form>
                                <form action="{{ route('accessories.destroy', $acc) }}" method="POST" onsubmit="return confirm('¿Eliminar accesorio?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1 text-xs text-rose-400 hover:text-rose-600" title="Eliminar">
                                        <i data-lucide="trash" class="w-3.5 h-3.5"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Módulo: Historial de Mantenimientos Realizados -->
    @if($equipment->maintenances->isNotEmpty())
        <div class="bg-white rounded-3xl p-6 shadow-xs border border-slate-200/80 space-y-4">
            <div class="flex items-center space-x-2.5 pb-3 border-b border-slate-100">
                <i data-lucide="wrench" class="w-4 h-4 text-amber-600"></i>
                <h3 class="font-bold text-sm text-slate-900">Historial de Mantenimientos e Intervenciones Técnicas</h3>
            </div>

            <div class="divide-y divide-slate-100">
                @foreach($equipment->maintenances as $maint)
                    <div class="py-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-xs">
                        <div class="space-y-0.5">
                            <div class="flex items-center space-x-2">
                                <span class="font-semibold text-slate-900">{{ $maint->reason }}</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $maint->status === 'completado' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ ucfirst(str_replace('_', ' ', $maint->status)) }}
                                </span>
                            </div>
                            <p class="text-slate-500 text-[11px]">
                                Ingreso: <strong class="text-slate-700">{{ $maint->entry_date->format('d/m/Y H:i') }}</strong>
                                @if($maint->exit_date)
                                    • Alta: <strong class="text-slate-700">{{ $maint->exit_date->format('d/m/Y H:i') }}</strong>
                                @endif
                                @if($maint->provider)
                                    • Proveedor: {{ $maint->provider }}
                                @endif
                            </p>
                            @if($maint->notes)
                                <p class="text-slate-600 italic bg-slate-50 p-2 rounded-xl mt-1 text-[11px]">{{ $maint->notes }}</p>
                            @endif
                        </div>
                        <div class="text-[11px] text-slate-400 font-mono sm:text-right shrink-0">
                            Por: {{ $maint->user?->name ?? 'Sistema' }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Hoja de Vida / Historial Completo de Préstamos -->
    <div class="bg-white rounded-3xl shadow-xs border border-slate-200/80 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900">Hoja de Vida y Trazabilidad de Préstamos</h3>
                <p class="text-xs text-slate-500">Historial cronológico de todos los usuarios e incidencias asociadas a este bien</p>
            </div>
            <span class="text-xs font-bold text-blue-600 font-mono">{{ $equipment->loans->count() }} movimientos</span>
        </div>

        @if($equipment->loans->isEmpty())
            <div class="p-8 text-center text-xs text-slate-400">
                Este equipo aún no ha registrado ningún préstamo en el sistema.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-3.5">Código</th>
                            <th class="px-6 py-3.5">Solicitante</th>
                            <th class="px-6 py-3.5">Oficina</th>
                            <th class="px-6 py-3.5">Fecha Salida</th>
                            <th class="px-6 py-3.5">Fecha Devolución</th>
                            <th class="px-6 py-3.5">Estado Final</th>
                            <th class="px-6 py-3.5 text-right">Detalle</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @foreach($equipment->loans as $hLoan)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 font-mono font-bold text-blue-600">
                                    {{ $hLoan->loan_code }}
                                </td>
                                <td class="px-6 py-4 font-semibold text-slate-900">
                                    {{ $hLoan->worker?->full_name }}
                                </td>
                                <td class="px-6 py-4 text-slate-600">
                                    {{ $hLoan->worker?->office?->name }}
                                </td>
                                <td class="px-6 py-4 text-slate-600 whitespace-nowrap">
                                    {{ $hLoan->loan_date->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 text-slate-600 whitespace-nowrap">
                                    {{ $hLoan->actual_return_date ? $hLoan->actual_return_date->format('d/m/Y H:i') : 'En uso' }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($hLoan->status === 'activo')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800">Prestado</span>
                                    @elseif($hLoan->status === 'devuelto')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Devuelto</span>
                                    @elseif($hLoan->status === 'devuelto_con_incidencia')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">Con Incidencia</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('loans.show', $hLoan) }}" class="text-blue-600 hover:underline font-bold">
                                        Ver
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Modal Enviar a Mantenimiento -->
    <div x-show="maintenanceModalOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/70 backdrop-blur-xs p-4 flex justify-center items-center"
         style="display: none;">
        
        <div class="w-full max-w-lg bg-white rounded-3xl shadow-2xl border border-slate-200 overflow-hidden" 
             @click.outside="maintenanceModalOpen = false">
            <div class="p-6 bg-gradient-to-r from-amber-600 to-orange-600 text-white flex items-center justify-between">
                <div class="flex items-center space-x-2.5">
                    <i data-lucide="wrench" class="w-5 h-5 text-amber-200"></i>
                    <div>
                        <h3 class="text-sm font-bold">Enviar Equipo a Mantenimiento</h3>
                        <p class="text-[11px] text-amber-100">{{ $equipment->code }} — {{ $equipment->name }}</p>
                    </div>
                </div>
                <button @click="maintenanceModalOpen = false" class="text-amber-200 hover:text-white p-1">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form action="{{ route('equipment.maintenance.send', $equipment) }}" method="POST" class="p-6 space-y-4 text-xs">
                @csrf
                <div>
                    <label for="reason" class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Motivo / Falla o Trabajo Requerido <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="reason" id="reason" rows="3" required placeholder="Ej: Falla en lámpara de proyección, requiere cambio y calibración..."
                              class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs focus:bg-white focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600"></textarea>
                </div>

                <div>
                    <label for="provider" class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Proveedor / Taller de Servicio Técnico
                    </label>
                    <input type="text" name="provider" id="provider" placeholder="Ej: Soporte Técnico Especializado S.A.C."
                           class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs focus:bg-white focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600">
                </div>

                <div>
                    <label for="notes" class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Notas adicionales</label>
                    <input type="text" name="notes" id="notes" placeholder="Ej: Se envía con su maletín y cable original"
                           class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs focus:bg-white focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600">
                </div>

                <div class="pt-3 flex items-center justify-end space-x-2 border-t border-slate-100">
                    <button type="button" @click="maintenanceModalOpen = false" class="px-4 py-2 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 font-semibold text-xs">
                        Cancelar
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow-md shadow-amber-600/30 transition-all cursor-pointer">
                        Confirmar Envío
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Finalizar Mantenimiento -->
    @if($equipment->currentMaintenance)
        <div x-show="completeMaintenanceModalOpen" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/70 backdrop-blur-xs p-4 flex justify-center items-center"
             style="display: none;">
            
            <div class="w-full max-w-lg bg-white rounded-3xl shadow-2xl border border-slate-200 overflow-hidden" 
                 @click.outside="completeMaintenanceModalOpen = false">
                <div class="p-6 bg-gradient-to-r from-emerald-600 to-teal-600 text-white flex items-center justify-between">
                    <div class="flex items-center space-x-2.5">
                        <i data-lucide="check-circle" class="w-5 h-5 text-emerald-200"></i>
                        <div>
                            <h3 class="text-sm font-bold">Retorno de Mantenimiento</h3>
                            <p class="text-[11px] text-emerald-100">Reincorporar {{ $equipment->code }} a Disponibles</p>
                        </div>
                    </div>
                    <button @click="completeMaintenanceModalOpen = false" class="text-emerald-200 hover:text-white p-1">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <form action="{{ route('equipment.maintenance.complete', [$equipment, $equipment->currentMaintenance]) }}" method="POST" class="p-6 space-y-4 text-xs">
                    @csrf
                    <div>
                        <label for="work_performed" class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Informe de Trabajos Realizados
                        </label>
                        <textarea name="work_performed" id="work_performed" rows="3" placeholder="Ej: Se reemplazó la lámpara por repuesto original y se realizó limpieza interna..."
                                  class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs focus:bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600"></textarea>
                    </div>

                    <div>
                        <label for="physical_condition" class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Nueva Condición Física del Equipo <span class="text-rose-500">*</span>
                        </label>
                        <select name="physical_condition" id="physical_condition" required class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs focus:bg-white">
                            <option value="bueno" selected>Bueno / Óptimo (100% operativo)</option>
                            <option value="regular">Regular (Operativo con desgaste cosmético)</option>
                        </select>
                    </div>

                    <div class="pt-3 flex items-center justify-end space-x-2 border-t border-slate-100">
                        <button type="button" @click="completeMaintenanceModalOpen = false" class="px-4 py-2 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 font-semibold text-xs">
                            Cancelar
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-600/30 transition-all cursor-pointer">
                            Reincorporar a Disponibles
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
@endsection
