<?php

namespace App\Http\Controllers;

use App\Models\Accessory;
use App\Models\AuditLog;
use App\Models\Equipment;
use App\Models\EquipmentMaintenance;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EquipmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Equipment::withCount(['activeAccessories', 'loans']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        $equipments = $query->orderBy('code', 'asc')->paginate(10)->withQueryString();
        $categories = Equipment::distinct()->pluck('category')->filter()->values();

        return view('equipment.index', compact('equipments', 'categories'));
    }

    public function create()
    {
        return view('equipment.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:equipment,code'],
            'name' => ['required', 'string', 'max:255'],
            'brand' => ['nullable', 'string', 'max:100'],
            'model' => ['nullable', 'string', 'max:100'],
            'serial_number' => ['nullable', 'string', 'max:100'],
            'category' => ['required', 'string', 'max:100'],
            'physical_condition' => ['required', 'in:bueno,regular,malo'],
            'status' => ['required', 'in:disponible,prestado,mantenimiento,danado,baja'],
            'notes' => ['nullable', 'string'],
            'accessories' => ['nullable', 'array'],
            'accessories.*' => ['nullable', 'string', 'max:255'],
        ], [
            'code.required' => 'El código del equipo es obligatorio.',
            'code.unique' => 'Ya existe un equipo con este código patrimonial/interno.',
            'name.required' => 'La denominación del equipo es obligatoria.',
            'category.required' => 'La categoría es obligatoria.',
        ]);

        $accessoriesList = $validated['accessories'] ?? [];
        unset($validated['accessories']);

        $equipment = Equipment::create($validated);

        // Crear accesorios asociados si se proporcionaron
        if (!empty($accessoriesList)) {
            foreach ($accessoriesList as $accName) {
                if (!empty(trim($accName))) {
                    Accessory::create([
                        'equipment_id' => $equipment->id,
                        'name' => trim($accName),
                        'is_active' => true,
                    ]);
                }
            }
        }

        AuditLog::logAction(
            'CREACION_EQUIPO',
            "Se registró el equipo: {$equipment->code} - {$equipment->name}.",
            $equipment,
            null,
            $equipment->toArray()
        );

        return redirect()->route('equipment.show', $equipment)
            ->with('success', "Equipo '{$equipment->code} - {$equipment->name}' registrado con éxito.");
    }

    public function show(Equipment $equipment)
    {
        $equipment->load([
            'accessories',
            'maintenances.user',
            'loans' => function ($q) {
                $q->with(['worker.office', 'loanUser', 'returnUser'])->latest();
            },
        ]);

        return view('equipment.show', compact('equipment'));
    }

    public function edit(Equipment $equipment)
    {
        return view('equipment.edit', compact('equipment'));
    }

    public function update(Request $request, Equipment $equipment)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', Rule::unique('equipment', 'code')->ignore($equipment->id)],
            'name' => ['required', 'string', 'max:255'],
            'brand' => ['nullable', 'string', 'max:100'],
            'model' => ['nullable', 'string', 'max:100'],
            'serial_number' => ['nullable', 'string', 'max:100'],
            'category' => ['required', 'string', 'max:100'],
            'physical_condition' => ['required', 'in:bueno,regular,malo'],
            'status' => ['required', 'in:disponible,prestado,mantenimiento,danado,baja'],
            'notes' => ['nullable', 'string'],
        ], [
            'code.required' => 'El código del equipo es obligatorio.',
            'code.unique' => 'Ya existe un equipo con este código patrimonial/interno.',
            'name.required' => 'La denominación del equipo es obligatoria.',
        ]);

        $before = $equipment->toArray();
        $equipment->update($validated);

        AuditLog::logAction(
            'ACTUALIZACION_EQUIPO',
            "Se actualizaron los datos del equipo: {$equipment->code} - {$equipment->name}.",
            $equipment,
            $before,
            $equipment->toArray()
        );

        return redirect()->route('equipment.show', $equipment)
            ->with('success', "Equipo '{$equipment->code}' actualizado con éxito.");
    }

    public function destroy(Equipment $equipment)
    {
        if ($equipment->status === 'prestado') {
            return back()->with('error', "No se puede eliminar un equipo que actualmente se encuentra prestado.");
        }

        if ($equipment->loans()->count() > 0) {
            return back()->with('error', "No se puede eliminar un equipo con historial de préstamos registrado. Puede cambiar su estado a 'Baja'.");
        }

        $before = $equipment->toArray();
        $code = $equipment->code;
        $equipment->delete();

        AuditLog::logAction(
            'ELIMINACION_EQUIPO',
            "Se eliminó el equipo: {$code}.",
            null,
            $before,
            null
        );

        return redirect()->route('equipment.index')
            ->with('success', "Equipo '{$code}' eliminado correctamente.");
    }

    /**
     * Endpoint API para consultar accesorios activos del equipo al seleccionarlo en el formulario de préstamo.
     */
    public function getAccessories(Equipment $equipment)
    {
        $accessories = $equipment->activeAccessories()
            ->select('id', 'name', 'description')
            ->get();

        return response()->json([
            'equipment_id' => $equipment->id,
            'code' => $equipment->code,
            'name' => $equipment->name,
            'status' => $equipment->status,
            'is_available' => $equipment->status === 'disponible',
            'accessories' => $accessories,
        ]);
    }

    public function addAccessory(Request $request, Equipment $equipment)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $accessory = $equipment->accessories()->create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_active' => true,
        ]);

        AuditLog::logAction(
            'CREACION_ACCESORIO',
            "Se agregó el accesorio '{$accessory->name}' al equipo {$equipment->code}.",
            $equipment
        );

        return back()->with('success', "Accesorio '{$accessory->name}' agregado correctamente.");
    }

    public function toggleAccessory(Accessory $accessory)
    {
        $accessory->is_active = !$accessory->is_active;
        $accessory->save();

        return back()->with('success', "Estado del accesorio actualizado.");
    }

    public function deleteAccessory(Accessory $accessory)
    {
        $name = $accessory->name;
        $equipment = $accessory->equipment;
        $accessory->delete();

        AuditLog::logAction(
            'ELIMINACION_ACCESORIO',
            "Se eliminó el accesorio '{$name}' del equipo {$equipment->code}.",
            $equipment
        );

        return back()->with('success', "Accesorio '{$name}' eliminado.");
    }

    /**
     * Enviar equipo a servicio técnico / mantenimiento
     */
    public function sendToMaintenance(Request $request, Equipment $equipment)
    {
        if ($equipment->status === 'prestado') {
            return back()->with('error', 'No se puede enviar a mantenimiento un equipo que se encuentra actualmente prestado.');
        }

        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
            'provider' => ['nullable', 'string', 'max:200'],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [
            'reason.required' => 'Debe ingresar el motivo del mantenimiento o falla detectada.',
        ]);

        \DB::transaction(function () use ($equipment, $validated) {
            $equipment->update(['status' => 'mantenimiento']);

            $equipment->maintenances()->create([
                'user_id' => auth()->id(),
                'entry_date' => now(),
                'provider' => $validated['provider'] ?? null,
                'reason' => $validated['reason'],
                'notes' => $validated['notes'] ?? null,
                'status' => 'en_proceso',
            ]);

            AuditLog::logAction(
                'ENVIO_MANTENIMIENTO',
                "Se envió a mantenimiento el equipo {$equipment->code} - {$equipment->name}. Motivo: {$validated['reason']}",
                $equipment
            );
        });

        return back()->with('success', "Equipo {$equipment->code} enviado a mantenimiento correctamente.");
    }

    /**
     * Retornar equipo de mantenimiento y dejarlo disponible
     */
    public function completeMaintenance(Request $request, Equipment $equipment, EquipmentMaintenance $maintenance)
    {
        $validated = $request->validate([
            'work_performed' => ['nullable', 'string', 'max:500'],
            'physical_condition' => ['required', 'in:bueno,regular'],
        ]);

        \DB::transaction(function () use ($equipment, $maintenance, $validated) {
            $notes = trim(($maintenance->notes ?? '') . ($validated['work_performed'] ? "\nTrabajo realizado: " . $validated['work_performed'] : ''));

            $maintenance->update([
                'exit_date' => now(),
                'notes' => $notes,
                'status' => 'completado',
            ]);

            $equipment->update([
                'status' => 'disponible',
                'physical_condition' => $validated['physical_condition'],
            ]);

            AuditLog::logAction(
                'RETORNO_MANTENIMIENTO',
                "Retorno de mantenimiento del equipo {$equipment->code}. Condición física: {$validated['physical_condition']}.",
                $equipment
            );
        });

        return back()->with('success', "Mantenimiento finalizado con éxito. El equipo {$equipment->code} vuelve a estar DISPONIBLE.");
    }

    /**
     * Vista de impresión de etiqueta patrimonial con código QR
     */
    public function printLabel(Equipment $equipment)
    {
        $equipment->load('accessories');
        return view('equipment.label', compact('equipment'));
    }
}
