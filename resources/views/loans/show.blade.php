@extends('layouts.app')

@section('title', 'Detalle de Préstamo ' . $loan->loan_code)
@section('header_title', 'Préstamo ' . $loan->loan_code)

@section('content')
<div class="max-w-5xl mx-auto space-y-6" x-data="{ extendModalOpen: false }">
    @php
        $cleanPhone = preg_replace('/[^0-9]/', '', $loan->worker?->phone ?? '');
        $statusNotice = $loan->isOverdue() ? 'VENCIDO' : 'programado para hoy';
        $waText = urlencode("Estimado(a) {$loan->worker?->full_name}, le saludamos del Área de Control Patrimonial. Le recordamos que su préstamo {$loan->loan_code} del bien {$loan->equipment?->name} se encuentra {$statusNotice}. Fecha pactada de entrega: {$loan->expected_return_date->format('d/m/Y H:i')}. Muchas gracias.");
    @endphp

    <!-- Header Banner -->
    <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-xs border border-slate-200/80 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="space-y-1.5">
            <div class="flex flex-wrap items-center gap-2.5">
                <span class="font-mono text-xl sm:text-2xl font-black text-blue-600 tracking-tight">{{ $loan->loan_code }}</span>
                @if($loan->status === 'activo')
                    @if($loan->isOverdue())
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800">
                            <span class="w-2 h-2 rounded-full bg-rose-600 mr-1.5 animate-ping"></span>
                            Vencido ({{ $loan->expected_return_date->diffForHumans() }})
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
                            <span class="w-2 h-2 rounded-full bg-blue-600 mr-1.5"></span>
                            Prestado / En Curso
                        </span>
                    @endif
                @elseif($loan->status === 'devuelto')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                        <span class="w-2 h-2 rounded-full bg-emerald-600 mr-1.5"></span>
                        Devuelto Conforme
                    </span>
                @elseif($loan->status === 'devuelto_con_incidencia')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                        <span class="w-2 h-2 rounded-full bg-amber-600 mr-1.5"></span>
                        Devuelto con Incidencia
                    </span>
                @endif

                @if(($loan->extensions_count ?? 0) > 0)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-100 text-purple-800">
                        <i data-lucide="history" class="w-3 h-3 mr-1"></i>
                        Prorrogado {{ $loan->extensions_count }} {{ $loan->extensions_count == 1 ? 'vez' : 'veces' }}
                    </span>
                @endif
            </div>
            <p class="text-xs text-slate-500">
                Registrado el {{ $loan->created_at->format('d/m/Y \a \l\a\s H:i') }} por <span class="font-semibold text-slate-700">{{ $loan->loanUser?->name }}</span>
            </p>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-wrap items-center gap-2">
            @if($loan->status === 'activo')
                <!-- Botón WhatsApp -->
                @if(!empty($cleanPhone))
                    <a href="https://wa.me/51{{ $cleanPhone }}?text={{ $waText }}" 
                       target="_blank"
                       class="inline-flex items-center px-3.5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm transition-all cursor-pointer"
                       title="Avisar al trabajador por WhatsApp">
                        <i data-lucide="message-circle" class="w-4 h-4 mr-1.5"></i>
                        <span>WhatsApp</span>
                    </a>
                @endif

                <!-- Botón Ampliar Plazo -->
                <button type="button" 
                        @click="extendModalOpen = true"
                        class="inline-flex items-center px-3.5 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs shadow-sm transition-all cursor-pointer">
                    <i data-lucide="calendar-plus" class="w-4 h-4 mr-1.5"></i>
                    <span>Ampliar Plazo</span>
                </button>

                <!-- Botón Recepcionar Devolución -->
                <a href="{{ route('loans.return', $loan) }}" 
                   class="inline-flex items-center px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-sm transition-all cursor-pointer">
                    <i data-lucide="check-square" class="w-4 h-4 mr-1.5"></i>
                    <span>Recepcionar</span>
                </a>
            @endif

            <a href="{{ route('loans.stream-pdf', $loan) }}" target="_blank" 
               class="inline-flex items-center px-3.5 py-2.5 rounded-xl border border-slate-300 hover:bg-slate-50 text-slate-700 font-semibold text-xs transition-colors">
                <i data-lucide="printer" class="w-4 h-4 mr-1.5"></i>
                <span>Imprimir Constancia</span>
            </a>

            <a href="{{ route('loans.pdf', $loan) }}" 
               class="inline-flex items-center px-3.5 py-2.5 rounded-xl border border-slate-300 hover:bg-slate-50 text-slate-700 font-semibold text-xs transition-colors">
                <i data-lucide="download" class="w-4 h-4 mr-1.5"></i>
                <span>PDF</span>
            </a>

            <a href="{{ route('loans.index') }}" 
               class="inline-flex items-center px-3.5 py-2.5 rounded-xl border border-slate-200 text-slate-500 hover:text-slate-800 text-xs">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i>
                <span>Volver</span>
            </a>
        </div>
    </div>

    <!-- Grid Details: Solicitante y Equipo -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Card 1: Solicitante -->
        <div class="bg-white rounded-3xl p-6 shadow-xs border border-slate-200/80 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center space-x-2">
                    <i data-lucide="user" class="w-5 h-5 text-blue-600"></i>
                    <h3 class="font-bold text-sm text-slate-900">Datos del Solicitante</h3>
                </div>
                @if(!empty($cleanPhone))
                    <a href="https://wa.me/51{{ $cleanPhone }}?text={{ $waText }}" target="_blank" class="text-xs font-bold text-emerald-600 hover:underline flex items-center">
                        <i data-lucide="message-circle" class="w-3.5 h-3.5 mr-1"></i>
                        Contactar
                    </a>
                @endif
            </div>

            <div class="space-y-2 text-xs">
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-500 font-medium">Trabajador:</span>
                    <span class="font-bold text-slate-900 text-right">{{ $loan->worker?->full_name }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-500 font-medium">DNI:</span>
                    <span class="font-mono font-bold text-slate-800">{{ $loan->worker?->dni }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-500 font-medium">Oficina:</span>
                    <span class="font-semibold text-blue-700 text-right">{{ $loan->worker?->office?->name }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-500 font-medium">Cargo:</span>
                    <span class="text-slate-800 text-right">{{ $loan->worker?->position ?? '-' }}</span>
                </div>
                <div class="flex justify-between py-1">
                    <span class="text-slate-500 font-medium">Teléfono / Email:</span>
                    <span class="text-slate-800 text-right">{{ $loan->worker?->phone ?? '-' }} / {{ $loan->worker?->email ?? '-' }}</span>
                </div>
            </div>
        </div>

        <!-- Card 2: Equipo con Código QR de Verificación -->
        <div class="bg-white rounded-3xl p-6 shadow-xs border border-slate-200/80 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center space-x-2">
                    <i data-lucide="monitor" class="w-5 h-5 text-blue-600"></i>
                    <h3 class="font-bold text-sm text-slate-900">Datos del Bien Prestado</h3>
                </div>
                <a href="{{ route('equipment.show', $loan->equipment) }}" class="text-xs font-bold text-blue-600 hover:underline">
                    Hoja de Vida
                </a>
            </div>

            <div class="flex items-start space-x-4">
                <div class="space-y-2 text-xs flex-1">
                    <div class="flex justify-between py-1 border-b border-slate-50">
                        <span class="text-slate-500 font-medium">Código:</span>
                        <span class="font-mono font-bold text-blue-700">{{ $loan->equipment?->code }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-50">
                        <span class="text-slate-500 font-medium">Denominación:</span>
                        <span class="font-bold text-slate-900 text-right">{{ $loan->equipment?->name }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-50">
                        <span class="text-slate-500 font-medium">Marca y Modelo:</span>
                        <span class="text-slate-800 text-right">{{ $loan->equipment?->brand }} {{ $loan->equipment?->model }}</span>
                    </div>
                    <div class="flex justify-between py-1">
                        <span class="text-slate-500 font-medium">N° de Serie:</span>
                        <span class="font-mono text-slate-800 text-right">{{ $loan->equipment?->serial_number ?? 'S/N' }}</span>
                    </div>
                </div>

                <!-- Mini QR para verificación digital -->
                <div class="text-center p-2 rounded-2xl bg-slate-50 border border-slate-200/80 shrink-0">
                    <canvas id="loanQrCanvas" class="w-20 h-20 mx-auto"></canvas>
                    <span class="text-[9px] font-mono text-slate-400 mt-1 block">QR Digital</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Accesorios Entregados vs Devueltos -->
    <div class="bg-white rounded-3xl p-6 shadow-xs border border-slate-200/80 space-y-4">
        <div class="flex items-center space-x-2 pb-3 border-b border-slate-100">
            <i data-lucide="package-check" class="w-5 h-5 text-blue-600"></i>
            <h3 class="font-bold text-sm text-slate-900">Checklist de Accesorios</h3>
        </div>

        @if($loan->loanAccessories->isEmpty())
            <p class="text-xs text-slate-500 italic">No se registraron accesorios específicos para este préstamo.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-100">
                        <tr>
                            <th class="px-4 py-2.5">Accesorio</th>
                            <th class="px-4 py-2.5">Entregado en Salida</th>
                            <th class="px-4 py-2.5">Devuelto en Recepción</th>
                            <th class="px-4 py-2.5">Observaciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($loan->loanAccessories as $acc)
                            <tr>
                                <td class="px-4 py-3 font-bold text-slate-800">{{ $acc->accessory?->name }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700">
                                        <i data-lucide="check" class="w-3 h-3 mr-1"></i> Sí entregado
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    @if($loan->status === 'activo')
                                        <span class="text-slate-400 font-medium">Pendiente de devolución</span>
                                    @else
                                        @if($acc->was_returned)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-800">
                                                <i data-lucide="check" class="w-3 h-3 mr-1"></i> Devuelto
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-rose-100 text-rose-800 font-bold">
                                                <i data-lucide="x" class="w-3 h-3 mr-1"></i> NO DEVUELTO (FALTANTE)
                                            </span>
                                        @endif
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-slate-600">{{ $acc->observation ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Período, Motivo e Historial de Operadores -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Tiempos y Motivo -->
        <div class="bg-white rounded-3xl p-6 shadow-xs border border-slate-200/80 space-y-4">
            <div class="flex items-center space-x-2 pb-3 border-b border-slate-100">
                <i data-lucide="clock-3" class="w-5 h-5 text-blue-600"></i>
                <h3 class="font-bold text-sm text-slate-900">Período y Finalidad</h3>
            </div>

            <div class="space-y-2 text-xs">
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-500 font-medium">Fecha y Hora de Salida:</span>
                    <span class="font-semibold text-slate-900">{{ $loan->loan_date->format('d/m/Y H:i') }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-500 font-medium">Devolución Estimada:</span>
                    <span class="font-bold {{ $loan->isOverdue() ? 'text-rose-600' : 'text-slate-900' }}">
                        {{ $loan->expected_return_date->format('d/m/Y H:i') }}
                    </span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-500 font-medium">Devolución Efectiva:</span>
                    <span class="font-semibold text-slate-900">
                        {{ $loan->actual_return_date ? $loan->actual_return_date->format('d/m/Y H:i') : 'Pendiente de entrega' }}
                    </span>
                </div>
                <div class="py-1">
                    <span class="text-slate-500 font-medium block mb-1">Motivo / Destino:</span>
                    <p class="p-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 font-medium">
                        {{ $loan->purpose }}
                    </p>
                </div>
                @if($loan->loan_notes)
                    <div class="py-1">
                        <span class="text-slate-500 font-medium block mb-1">Observaciones e Historial de Prórrogas:</span>
                        <p class="p-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-700 whitespace-pre-line font-mono text-[11px]">
                            {{ $loan->loan_notes }}
                        </p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Operadores & Devolución -->
        <div class="bg-white rounded-3xl p-6 shadow-xs border border-slate-200/80 space-y-4">
            <div class="flex items-center space-x-2 pb-3 border-b border-slate-100">
                <i data-lucide="shield-check" class="w-5 h-5 text-blue-600"></i>
                <h3 class="font-bold text-sm text-slate-900">Auditoría y Responsables</h3>
            </div>

            <div class="space-y-2 text-xs">
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-500 font-medium">Encargado que Entregó:</span>
                    <span class="font-bold text-slate-900">{{ $loan->loanUser?->name ?? 'Sistema' }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-500 font-medium">Encargado que Recepcionó:</span>
                    <span class="font-bold text-slate-900">{{ $loan->returnUser?->name ?? 'Pendiente' }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-500 font-medium">Condición en Devolución:</span>
                    <span class="font-semibold text-slate-800 capitalize">{{ $loan->equipment_return_condition ?? '-' }}</span>
                </div>
                @if($loan->return_notes)
                    <div class="py-1">
                        <span class="text-slate-500 font-medium block mb-1">Observaciones en Devolución:</span>
                        <p class="p-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-700">
                            {{ $loan->return_notes }}
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Registro de Incidencias si existen -->
    @if($loan->incidents->isNotEmpty())
        <div class="bg-amber-50 rounded-3xl p-6 shadow-xs border border-amber-200 space-y-4">
            <div class="flex items-center space-x-2 pb-3 border-b border-amber-200/60">
                <i data-lucide="alert-triangle" class="w-5 h-5 text-amber-700"></i>
                <h3 class="font-bold text-sm text-amber-950">Bitácora de Incidencias Registradas</h3>
            </div>

            <div class="space-y-3">
                @foreach($loan->incidents as $inc)
                    <div class="p-4 rounded-2xl bg-white border border-amber-200 space-y-2 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-amber-900 uppercase tracking-wider text-[11px]">
                                {{ str_replace('_', ' ', $inc->incident_type) }}
                            </span>
                            <span class="px-2 py-0.5 rounded-full font-bold text-[10px] bg-amber-100 text-amber-800">
                                {{ ucfirst($inc->status) }}
                            </span>
                        </div>
                        <p class="text-slate-800 whitespace-pre-line">{{ $inc->description }}</p>
                        @if($inc->resolution_notes)
                            <div class="pt-2 border-t border-slate-100 text-slate-600">
                                <span class="font-semibold">Resolución:</span> {{ $inc->resolution_notes }}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Modal para Ampliar Plazo / Prórroga -->
    <div x-show="extendModalOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/70 backdrop-blur-xs p-4 flex justify-center items-center"
         style="display: none;">
        
        <div class="w-full max-w-lg bg-white rounded-3xl shadow-2xl border border-slate-200 overflow-hidden" 
             @click.outside="extendModalOpen = false">
            <div class="p-6 bg-gradient-to-r from-purple-900 to-indigo-900 text-white flex items-center justify-between">
                <div class="flex items-center space-x-2.5">
                    <i data-lucide="calendar-plus" class="w-5 h-5 text-purple-300"></i>
                    <div>
                        <h3 class="text-sm font-bold">Ampliar Plazo de Préstamo</h3>
                        <p class="text-[11px] text-purple-200">Prórroga para {{ $loan->loan_code }}</p>
                    </div>
                </div>
                <button @click="extendModalOpen = false" class="text-purple-300 hover:text-white p-1">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form action="{{ route('loans.extend', $loan) }}" method="POST" class="p-6 space-y-4 text-xs">
                @csrf
                <div class="p-3 bg-purple-50 rounded-2xl border border-purple-200 text-purple-950 space-y-1">
                    <p>Fecha pactada actual: <strong class="font-mono">{{ $loan->expected_return_date->format('d/m/Y H:i') }}</strong></p>
                    <p class="text-[11px] text-purple-700">La nueva fecha debe ser posterior al horario actual.</p>
                </div>

                <div>
                    <label for="new_expected_return_date" class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Nueva Fecha y Hora de Devolución <span class="text-rose-500">*</span>
                    </label>
                    <input type="datetime-local" 
                           name="new_expected_return_date" 
                           id="new_expected_return_date" 
                           required 
                           value="{{ $loan->expected_return_date->addHours(2)->format('Y-m-d\TH:i') }}"
                           class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold focus:bg-white focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600">
                </div>

                <div>
                    <label for="extension_reason" class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Motivo de la Ampliación <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="extension_reason" 
                              id="extension_reason" 
                              rows="3" 
                              required 
                              placeholder="Ej: La capacitación se extendió por 2 horas adicionales..."
                              class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs focus:bg-white focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600"></textarea>
                </div>

                <div class="pt-3 flex items-center justify-end space-x-2 border-t border-slate-100">
                    <button type="button" @click="extendModalOpen = false" class="px-4 py-2 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 font-semibold text-xs">
                        Cancelar
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs shadow-md shadow-purple-600/30 transition-all cursor-pointer">
                        Confirmar Ampliación
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const loanUrl = "{{ route('loans.show', $loan) }}";
    if (window.generateQRCode) {
        window.generateQRCode('loanQrCanvas', loanUrl, { width: 80, margin: 0 });
    }
});
</script>
@endpush
