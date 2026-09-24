@extends('layouts.app')

@section('title', 'Recepcionar Devolución ' . $loan->loan_code)
@section('header_title', 'Recepción y Verificación de Devolución')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="returnForm()">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Verificación Física de Devolución</h2>
            <p class="text-xs text-slate-500">Compruebe físicamente cada accesorio y el estado del equipo antes de confirmar</p>
        </div>
        <a href="{{ route('loans.show', $loan) }}" class="inline-flex items-center text-xs font-semibold text-slate-600 hover:text-slate-900">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i>
            <span>Volver al detalle</span>
        </a>
    </div>

    <!-- Loan Resume Card -->
    <div class="bg-blue-900 text-white rounded-2xl p-6 shadow-md space-y-3">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 pb-3 border-b border-blue-800">
            <div class="flex items-center space-x-3">
                <span class="font-mono text-xl font-black">{{ $loan->loan_code }}</span>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-700 text-blue-100">Préstamo Activo</span>
            </div>
            <div class="text-xs text-blue-200">
                Fecha programada: <span class="font-semibold text-white">{{ $loan->expected_return_date->format('d/m/Y H:i') }}</span>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
            <div>
                <p class="text-blue-300">Solicitante:</p>
                <p class="font-bold text-sm text-white">{{ $loan->worker?->full_name }}</p>
                <p class="text-blue-200">DNI: {{ $loan->worker?->dni }} • {{ $loan->worker?->office?->name }}</p>
            </div>
            <div>
                <p class="text-blue-300">Equipo a Recibir:</p>
                <p class="font-bold text-sm text-white">{{ $loan->equipment?->name }}</p>
                <p class="text-blue-200 font-mono">{{ $loan->equipment?->code }} ({{ $loan->equipment?->brand }} {{ $loan->equipment?->model }})</p>
            </div>
        </div>
    </div>

    <form action="{{ route('loans.return.post', $loan) }}" method="POST" class="space-y-6">
        @csrf

        <!-- 1. VERIFICACIÓN DE ACCESORIOS ENTREGADOS -->
        <div class="bg-white rounded-2xl p-6 shadow-xs border border-slate-200/80 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center space-x-2.5">
                    <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-xs">
                        1
                    </div>
                    <h3 class="text-sm font-bold text-slate-900">Verificación de Accesorios Entregados</h3>
                </div>
                <button type="button" @click="checkAllAccessories" class="text-xs font-semibold text-blue-600 hover:underline">
                    Marcar todos como devueltos
                </button>
            </div>

            @if($loan->loanAccessories->isEmpty())
                <p class="text-xs text-slate-500 italic bg-slate-50 p-3 rounded-xl">No se entregaron accesorios adicionales en este préstamo.</p>
            @else
                <div class="divide-y divide-slate-100">
                    @foreach($loan->loanAccessories as $acc)
                        <div class="py-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <label class="flex items-center space-x-3 cursor-pointer">
                                <input type="checkbox" 
                                       name="returned_accessories[]" 
                                       value="{{ $acc->accessory_id }}" 
                                       checked
                                       @change="updateIncidentStatus"
                                       class="w-5 h-5 rounded-md text-emerald-600 border-slate-300 focus:ring-emerald-500 acc-checkbox">
                                <div>
                                    <span class="text-xs font-bold text-slate-900">{{ $acc->accessory?->name }}</span>
                                    <p class="text-[11px] text-slate-400">Marque la casilla si fue recibido físicamente</p>
                                </div>
                            </label>
                            <div class="w-full sm:w-64">
                                <input type="text" 
                                       name="accessory_observations[{{ $acc->accessory_id }}]" 
                                       placeholder="Observación opcional..."
                                       class="w-full px-3 py-1.5 rounded-lg bg-slate-50 border border-slate-200 text-xs focus:bg-white focus:ring-1 focus:ring-blue-500">
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- 2. ESTADO FÍSICO DEL EQUIPO RECIBIDO -->
        <div class="bg-white rounded-2xl p-6 shadow-xs border border-slate-200/80 space-y-4">
            <div class="flex items-center space-x-2.5 pb-3 border-b border-slate-100">
                <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-xs">
                    2
                </div>
                <h3 class="text-sm font-bold text-slate-900">Estado Físico del Equipo Recibido</h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <label class="flex items-center space-x-3 p-3.5 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-emerald-50/50 hover:border-emerald-300 transition-colors cursor-pointer">
                    <input type="radio" 
                           name="equipment_return_condition" 
                           value="bueno" 
                           checked 
                           x-model="condition" 
                           @change="updateIncidentStatus"
                           class="w-4 h-4 text-emerald-600 border-slate-300 focus:ring-emerald-500">
                    <div>
                        <span class="text-xs font-bold text-emerald-900">Bueno / Óptimo</span>
                        <p class="text-[11px] text-slate-500">Limpio y funcionando correctamente</p>
                    </div>
                </label>

                <label class="flex items-center space-x-3 p-3.5 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-amber-50/50 hover:border-amber-300 transition-colors cursor-pointer">
                    <input type="radio" 
                           name="equipment_return_condition" 
                           value="regular" 
                           x-model="condition" 
                           @change="updateIncidentStatus"
                           class="w-4 h-4 text-amber-600 border-slate-300 focus:ring-amber-500">
                    <div>
                        <span class="text-xs font-bold text-amber-900">Regular</span>
                        <p class="text-[11px] text-slate-500">Desgaste menor sin daño funcional</p>
                    </div>
                </label>

                <label class="flex items-center space-x-3 p-3.5 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-rose-50/50 hover:border-rose-300 transition-colors cursor-pointer">
                    <input type="radio" 
                           name="equipment_return_condition" 
                           value="danado" 
                           x-model="condition" 
                           @change="updateIncidentStatus"
                           class="w-4 h-4 text-rose-600 border-slate-300 focus:ring-rose-500">
                    <div>
                        <span class="text-xs font-bold text-rose-900">Dañado / Averiado</span>
                        <p class="text-[11px] text-slate-500">Golpes, fallas o roturas</p>
                    </div>
                </label>
            </div>
            @error('equipment_return_condition')
                <p class="text-xs text-rose-600 font-semibold">{{ $message }}</p>
            @enderror
        </div>

        <!-- 3. DETALLE DE INCIDENCIA (Si aplica) -->
        <div x-show="hasIncident" class="bg-rose-50 rounded-2xl p-6 shadow-xs border border-rose-200 space-y-4" style="display: none;">
            <div class="flex items-center space-x-2.5 pb-3 border-b border-rose-200/80">
                <i data-lucide="alert-triangle" class="w-5 h-5 text-rose-600"></i>
                <div>
                    <h3 class="text-sm font-bold text-rose-950">⚠️ Devolución con Incidencia</h3>
                    <p class="text-xs text-rose-700">Se ha detectado un faltante de accesorio o daño físico. El préstamo quedará registrado como Devuelto con Incidencia.</p>
                </div>
            </div>

            <input type="hidden" name="has_incident" :value="hasIncident ? 1 : 0">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="incident_type" class="block text-xs font-semibold text-rose-900 uppercase tracking-wider mb-1.5">
                        Tipo de Incidencia
                    </label>
                    <select name="incident_type" id="incident_type" 
                            class="w-full px-3.5 py-2.5 rounded-xl bg-white border border-rose-300 text-xs focus:ring-2 focus:ring-rose-500/20 focus:border-rose-600">
                        <option value="accesorio_faltante">Accesorio Faltante / No Devuelto</option>
                        <option value="dano_equipo">Equipo Dañado o Averiado</option>
                        <option value="retraso_grave">Retraso Injustificado Grave</option>
                        <option value="otro">Otra Incidencia</option>
                    </select>
                </div>

                <div>
                    <label for="incident_description" class="block text-xs font-semibold text-rose-900 uppercase tracking-wider mb-1.5">
                        Descripción Detallada de la Incidencia
                    </label>
                    <textarea name="incident_description" 
                              id="incident_description" 
                              rows="2" 
                              placeholder="Indique con claridad el bien faltante, daño observado o explicación..."
                              class="w-full px-3.5 py-2.5 rounded-xl bg-white border border-rose-300 text-xs focus:ring-2 focus:ring-rose-500/20 focus:border-rose-600"></textarea>
                </div>
            </div>
        </div>

        <!-- 4. OBSERVACIONES DE RECEPCIÓN -->
        <div class="bg-white rounded-2xl p-6 shadow-xs border border-slate-200/80 space-y-4">
            <div class="flex items-center space-x-2.5 pb-3 border-b border-slate-100">
                <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-xs">
                    3
                </div>
                <h3 class="text-sm font-bold text-slate-900">Observaciones Finales de Recepción</h3>
            </div>

            <div>
                <textarea name="return_notes" 
                          id="return_notes" 
                          rows="2" 
                          placeholder="Ej: Se devuelve el equipo en buen estado con todos sus accesorios completos..."
                          class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm focus:bg-white focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600"></textarea>
            </div>

            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-600 flex items-center justify-between">
                <span>Encargado que recepciona: <strong class="text-slate-900">{{ auth()->user()->name }}</strong></span>
                <span>Fecha y Hora: <strong class="text-slate-900">{{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</strong></span>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-end space-x-4 pt-2">
            <a href="{{ route('loans.show', $loan) }}" class="px-5 py-3 rounded-xl border border-slate-200 hover:bg-slate-100 text-slate-700 font-semibold text-sm transition-colors">
                Cancelar
            </a>
            <button type="submit" 
                    class="px-8 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm shadow-lg shadow-emerald-600/30 transition-all flex items-center space-x-2 cursor-pointer">
                <i data-lucide="check-circle" class="w-5 h-5"></i>
                <span>CONFIRMAR DEVOLUCIÓN</span>
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function returnForm() {
    return {
        condition: 'bueno',
        hasIncident: false,

        init() {
            this.updateIncidentStatus();
        },

        checkAllAccessories() {
            document.querySelectorAll('.acc-checkbox').forEach(cb => cb.checked = true);
            this.updateIncidentStatus();
        },

        updateIncidentStatus() {
            const checkboxes = document.querySelectorAll('.acc-checkbox');
            let hasUnchecked = false;
            checkboxes.forEach(cb => {
                if (!cb.checked) hasUnchecked = true;
            });

            const isDamaged = this.condition === 'danado';
            this.hasIncident = hasUnchecked || isDamaged;
            setTimeout(() => { if (window.lucide) window.lucide.createIcons(); }, 100);
        }
    }
}
</script>
@endpush
