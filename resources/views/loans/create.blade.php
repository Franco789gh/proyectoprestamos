@extends('layouts.app')

@section('title', 'Nuevo Préstamo')
@section('header_title', 'Registro de Nuevo Préstamo')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="loanForm()">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Formulario de Préstamo Rápido</h2>
            <p class="text-xs text-slate-500">Ingrese el DNI del solicitante y seleccione el equipo a entregar</p>
        </div>
        <a href="{{ route('loans.index') }}" class="inline-flex items-center text-xs font-semibold text-slate-600 hover:text-slate-900">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i>
            <span>Volver a la lista</span>
        </a>
    </div>

    @if($availableEquipment->isEmpty())
        <div class="p-6 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 flex items-start space-x-4">
            <div class="p-2 rounded-xl bg-amber-100 text-amber-700 shrink-0">
                <i data-lucide="alert-triangle" class="w-6 h-6"></i>
            </div>
            <div class="space-y-1">
                <h3 class="font-bold text-sm">No hay equipos disponibles en inventario</h3>
                <p class="text-xs text-amber-800">Actualmente todos los equipos se encuentran prestados, en mantenimiento o dados de baja.</p>
                <div class="pt-2">
                    <a href="{{ route('equipment.create') }}" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold">
                        <i data-lucide="plus" class="w-3.5 h-3.5 mr-1"></i>
                        Registrar Nuevo Equipo
                    </a>
                </div>
            </div>
        </div>
    @endif

    <form action="{{ route('loans.store') }}" method="POST" class="space-y-6" @submit="handleSubmit">
        @csrf

        <!-- 1. DATOS DEL SOLICITANTE -->
        <div class="bg-white rounded-2xl p-6 shadow-xs border border-slate-200/80 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center space-x-2.5">
                    <div class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xs">
                        1
                    </div>
                    <h3 class="text-sm font-bold text-slate-900">Trabajador Solicitante</h3>
                </div>
                <a href="{{ route('workers.create') }}" target="_blank" class="text-xs font-semibold text-blue-600 hover:underline flex items-center">
                    <i data-lucide="user-plus" class="w-3.5 h-3.5 mr-1"></i>
                    <span>+ Registrar Nuevo Trabajador</span>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="sm:col-span-1">
                    <label for="dni_search" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        DNI del Trabajador <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="text" 
                               id="dni_search" 
                               x-model="dni" 
                               @input="checkDniLength" 
                               @keydown.enter.prevent="searchWorker"
                               maxlength="8" 
                               placeholder="Ej: 12345678"
                               class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 font-mono text-sm font-bold tracking-wider focus:bg-white focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600">
                        <button type="button" 
                                @click="searchWorker" 
                                :disabled="loadingWorker || dni.length < 8"
                                class="absolute right-1.5 top-1.5 px-3 py-1 rounded-lg bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white text-xs font-semibold transition-colors cursor-pointer">
                            <span x-show="!loadingWorker">Buscar</span>
                            <span x-show="loadingWorker">...</span>
                        </button>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1">Escriba los 8 dígitos para autocompletar</p>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Información del Solicitante
                    </label>

                    <!-- Tarjeta del Trabajador Encontrado -->
                    <div x-show="worker" class="p-3.5 rounded-xl bg-blue-50/60 border border-blue-200/80 flex items-start justify-between gap-3">
                        <div class="space-y-1">
                            <div class="flex items-center space-x-2">
                                <span class="font-bold text-sm text-slate-900" x-text="worker ? worker.full_name : ''"></span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Activo</span>
                            </div>
                            <p class="text-xs text-slate-600">
                                Oficina: <span class="font-semibold text-slate-900" x-text="worker ? worker.office_name : ''"></span>
                                <span x-show="worker && worker.position"> • Cargo: <span x-text="worker ? worker.position : ''"></span></span>
                            </p>
                            <!-- Advertencia si tiene préstamos vencidos -->
                            <div x-show="workerWarning" class="p-2 rounded-lg bg-rose-100/80 border border-rose-300 text-rose-900 text-xs font-semibold flex items-center space-x-1.5 mt-2">
                                <i data-lucide="alert-triangle" class="w-4 h-4 text-rose-600 shrink-0"></i>
                                <span x-text="workerWarning"></span>
                            </div>
                        </div>
                        <button type="button" @click="resetWorker" class="text-slate-400 hover:text-slate-600 text-xs p-1">
                            Cambiar
                        </button>
                    </div>

                    <!-- Mensaje si no se encuentra -->
                    <div x-show="!worker && notFoundMessage" class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600 shrink-0"></i>
                            <span x-text="notFoundMessage"></span>
                        </div>
                        <a href="{{ route('workers.create') }}" target="_blank" class="font-bold underline text-rose-900 hover:text-rose-950">
                            Registrar ahora
                        </a>
                    </div>

                    <!-- Placeholder si no se ha buscado -->
                    <div x-show="!worker && !notFoundMessage" class="p-3.5 rounded-xl bg-slate-50 border border-dashed border-slate-200 text-slate-400 text-xs text-center">
                        Ingrese el DNI para verificar al trabajador en el sistema.
                    </div>
                </div>
            </div>

            <input type="hidden" name="worker_id" :value="worker ? worker.id : ''" required>
            @error('worker_id')
                <p class="text-xs text-rose-600 font-semibold">{{ $message }}</p>
            @enderror
        </div>

        <!-- 2. SELECCIÓN DE EQUIPO Y ACCESORIOS -->
        <div class="bg-white rounded-2xl p-6 shadow-xs border border-slate-200/80 space-y-4">
            <div class="flex items-center space-x-2.5 pb-3 border-b border-slate-100">
                <div class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xs">
                    2
                </div>
                <h3 class="text-sm font-bold text-slate-900">Equipo y Accesorios</h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="equipment_id" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Seleccionar Equipo Disponible <span class="text-rose-500">*</span>
                    </label>
                    <select name="equipment_id" 
                            id="equipment_id" 
                            x-model="selectedEquipmentId" 
                            @change="loadAccessories" 
                            required
                            class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm focus:bg-white focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600">
                        <option value="">-- Seleccione un equipo --</option>
                        @foreach($availableEquipment as $eq)
                            <option value="{{ $eq->id }}" {{ (old('equipment_id', $selectedEquipmentId) == $eq->id) ? 'selected' : '' }}>
                                {{ $eq->code }} — {{ $eq->name }} ({{ $eq->brand }} {{ $eq->model }})
                            </option>
                        @endforeach
                    </select>
                    @error('equipment_id')
                        <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Accesorios a Entregar (Checklist)
                    </label>

                    <div x-show="loadingAccessories" class="p-4 text-center text-xs text-slate-400">
                        Cargando accesorios asociados...
                    </div>

                    <div x-show="!loadingAccessories && accessories.length > 0" class="space-y-2 max-h-48 overflow-y-auto p-2 bg-slate-50 rounded-xl border border-slate-200">
                        <template x-for="acc in accessories" :key="acc.id">
                            <label class="flex items-center space-x-2.5 p-2 rounded-lg hover:bg-white transition-colors cursor-pointer border border-transparent hover:border-slate-200">
                                <input type="checkbox" 
                                       name="accessories[]" 
                                       :value="acc.id" 
                                       checked
                                       class="w-4 h-4 rounded-md text-blue-600 border-slate-300 focus:ring-blue-500">
                                <span class="text-xs font-medium text-slate-800" x-text="acc.name"></span>
                            </label>
                        </template>
                    </div>

                    <div x-show="!loadingAccessories && accessories.length === 0 && selectedEquipmentId" class="p-3 text-xs text-slate-500 italic bg-slate-50 rounded-xl border border-slate-200">
                        Este equipo no tiene accesorios registrados.
                    </div>

                    <div x-show="!selectedEquipmentId" class="p-3 text-xs text-slate-400 bg-slate-50 rounded-xl border border-dashed border-slate-200 text-center">
                        Seleccione un equipo para ver sus accesorios disponibles.
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. FECHAS Y HORARIOS -->
        <div class="bg-white rounded-2xl p-6 shadow-xs border border-slate-200/80 space-y-4">
            <div class="flex items-center space-x-2.5 pb-3 border-b border-slate-100">
                <div class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xs">
                    3
                </div>
                <h3 class="text-sm font-bold text-slate-900">Período de Préstamo y Devolución</h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="loan_date" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Fecha y Hora de Salida <span class="text-rose-500">*</span>
                    </label>
                    <input type="datetime-local" 
                           name="loan_date" 
                           id="loan_date" 
                           value="{{ old('loan_date', \Carbon\Carbon::now()->format('Y-m-d\TH:i')) }}" 
                           required
                           class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm focus:bg-white focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600">
                    @error('loan_date')
                        <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="expected_return_date" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Fecha y Hora Estimada de Devolución <span class="text-rose-500">*</span>
                    </label>
                    <input type="datetime-local" 
                           name="expected_return_date" 
                           id="expected_return_date" 
                           value="{{ old('expected_return_date', \Carbon\Carbon::now()->hour < 17 ? \Carbon\Carbon::now()->setTime(17, 30)->format('Y-m-d\TH:i') : \Carbon\Carbon::tomorrow()->setTime(9, 0)->format('Y-m-d\TH:i')) }}" 
                           required
                           class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm focus:bg-white focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600">
                    @error('expected_return_date')
                        <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- 4. MOTIVO Y OBSERVACIONES -->
        <div class="bg-white rounded-2xl p-6 shadow-xs border border-slate-200/80 space-y-4">
            <div class="flex items-center space-x-2.5 pb-3 border-b border-slate-100">
                <div class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xs">
                    4
                </div>
                <h3 class="text-sm font-bold text-slate-900">Motivo y Observaciones</h3>
            </div>

            <div>
                <label for="purpose" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                    Motivo / Destino de Uso <span class="text-rose-500">*</span>
                </label>
                <input type="text" 
                       name="purpose" 
                       id="purpose" 
                       value="{{ old('purpose') }}" 
                       required 
                       placeholder="Ej: Capacitación de personal en Auditorio / Inspección de obra en Campo"
                       class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm focus:bg-white focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600">
                @error('purpose')
                    <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="loan_notes" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                    Observaciones de Entrega (Opcional)
                </label>
                <textarea name="loan_notes" 
                          id="loan_notes" 
                          rows="2" 
                          placeholder="Indique cualquier detalle adicional sobre las condiciones de entrega..."
                          class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm focus:bg-white focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600">{{ old('loan_notes') }}</textarea>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-end space-x-4 pt-2">
            <a href="{{ route('loans.index') }}" class="px-5 py-3 rounded-xl border border-slate-200 hover:bg-slate-100 text-slate-700 font-semibold text-sm transition-colors">
                Cancelar
            </a>
            <button type="submit" 
                    :disabled="!worker" 
                    class="px-8 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white font-bold text-sm shadow-lg shadow-blue-600/30 transition-all flex items-center space-x-2 cursor-pointer">
                <i data-lucide="check" class="w-5 h-5"></i>
                <span>REGISTRAR PRÉSTAMO</span>
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function loanForm() {
    return {
        dni: '',
        worker: null,
        workerWarning: null,
        notFoundMessage: null,
        loadingWorker: false,
        selectedEquipmentId: '{{ old('equipment_id', $selectedEquipmentId ?? '') }}',
        accessories: [],
        loadingAccessories: false,

        init() {
            if (this.selectedEquipmentId) {
                this.loadAccessories();
            }
        },

        checkDniLength() {
            this.dni = this.dni.replace(/\D/g, '');
            if (this.dni.length === 8) {
                this.searchWorker();
            }
        },

        async searchWorker() {
            if (this.dni.length !== 8) return;

            this.loadingWorker = true;
            this.notFoundMessage = null;

            try {
                const response = await fetch(`/workers/search/${this.dni}`);
                const data = await response.json();

                if (response.ok && data.found) {
                    if (!data.allowed) {
                        this.worker = null;
                        this.notFoundMessage = data.message;
                    } else {
                        this.worker = data.worker;
                        this.workerWarning = data.warning;
                    }
                } else {
                    this.worker = null;
                    this.notFoundMessage = data.message || 'Trabajador no encontrado.';
                }
            } catch (error) {
                this.worker = null;
                this.notFoundMessage = 'Error al consultar el DNI.';
            } finally {
                this.loadingWorker = false;
                setTimeout(() => { if (window.lucide) window.lucide.createIcons(); }, 100);
            }
        },

        resetWorker() {
            this.worker = null;
            this.workerWarning = null;
            this.notFoundMessage = null;
            this.dni = '';
        },

        async loadAccessories() {
            if (!this.selectedEquipmentId) {
                this.accessories = [];
                return;
            }

            this.loadingAccessories = true;
            try {
                const response = await fetch(`/equipment/${this.selectedEquipmentId}/accessories`);
                const data = await response.json();
                this.accessories = data.accessories || [];
            } catch (error) {
                this.accessories = [];
            } finally {
                this.loadingAccessories = false;
            }
        },

        handleSubmit(e) {
            if (!this.worker) {
                e.preventDefault();
                alert('Debe verificar el DNI de un trabajador habilitado antes de registrar el préstamo.');
            }
        }
    }
}
</script>
@endpush
