<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Office;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WorkerController extends Controller
{
    public function index(Request $request)
    {
        $query = Worker::with('office')->withCount('activeLoans');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('dni', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhereHas('office', function ($oq) use ($search) {
                      $oq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('office_id')) {
            $query->where('office_id', $request->input('office_id'));
        }

        $workers = $query->orderBy('last_name', 'asc')->paginate(10)->withQueryString();
        $offices = Office::where('is_active', true)->orderBy('name', 'asc')->get();

        return view('workers.index', compact('workers', 'offices'));
    }

    public function create()
    {
        $offices = Office::where('is_active', true)->orderBy('name', 'asc')->get();
        return view('workers.create', compact('offices'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'dni' => ['required', 'string', 'digits:8', 'unique:workers,dni'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'office_id' => ['required', 'exists:offices,id'],
            'position' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'is_active' => ['boolean'],
        ], [
            'dni.required' => 'El DNI es obligatorio.',
            'dni.digits' => 'El DNI debe tener exactamente 8 dígitos.',
            'dni.unique' => 'Ya existe un trabajador registrado con este DNI.',
            'first_name.required' => 'El nombre es obligatorio.',
            'last_name.required' => 'Los apellidos son obligatorios.',
            'office_id.required' => 'Debe seleccionar una oficina.',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $worker = Worker::create($validated);

        AuditLog::logAction(
            'CREACION_TRABAJADOR',
            "Se registró al trabajador: {$worker->full_name} (DNI: {$worker->dni}).",
            $worker,
            null,
            $worker->toArray()
        );

        return redirect()->route('workers.index')
            ->with('success', "Trabajador '{$worker->full_name}' registrado exitosamente.");
    }

    public function edit(Worker $worker)
    {
        $offices = Office::where('is_active', true)->orderBy('name', 'asc')->get();
        return view('workers.edit', compact('worker', 'offices'));
    }

    public function update(Request $request, Worker $worker)
    {
        $validated = $request->validate([
            'dni' => ['required', 'string', 'digits:8', Rule::unique('workers', 'dni')->ignore($worker->id)],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'office_id' => ['required', 'exists:offices,id'],
            'position' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'is_active' => ['boolean'],
        ], [
            'dni.required' => 'El DNI es obligatorio.',
            'dni.digits' => 'El DNI debe tener exactamente 8 dígitos.',
            'dni.unique' => 'Ya existe un trabajador registrado con este DNI.',
            'first_name.required' => 'El nombre es obligatorio.',
            'last_name.required' => 'Los apellidos son obligatorios.',
            'office_id.required' => 'Debe seleccionar una oficina.',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $before = $worker->toArray();

        $worker->update($validated);

        AuditLog::logAction(
            'ACTUALIZACION_TRABAJADOR',
            "Se actualizó al trabajador: {$worker->full_name} (DNI: {$worker->dni}).",
            $worker,
            $before,
            $worker->toArray()
        );

        return redirect()->route('workers.index')
            ->with('success', "Trabajador '{$worker->full_name}' actualizado exitosamente.");
    }

    public function destroy(Worker $worker)
    {
        if ($worker->loans()->count() > 0) {
            return back()->with('error', "No se puede eliminar al trabajador '{$worker->full_name}' porque tiene historial de préstamos.");
        }

        $before = $worker->toArray();
        $name = $worker->full_name;
        $worker->delete();

        AuditLog::logAction(
            'ELIMINACION_TRABAJADOR',
            "Se eliminó al trabajador: {$name}.",
            null,
            $before,
            null
        );

        return redirect()->route('workers.index')
            ->with('success', "Trabajador '{$name}' eliminado correctamente.");
    }

    /**
     * API Endpoint para búsqueda instantánea por DNI en el formulario de préstamo.
     */
    public function searchByDni(string $dni)
    {
        $worker = Worker::with('office')
            ->where('dni', trim($dni))
            ->first();

        if (!$worker) {
            return response()->json([
                'found' => false,
                'message' => 'No se encontró ningún trabajador con el DNI ingresado.',
            ], 404);
        }

        if (!$worker->is_active) {
            return response()->json([
                'found' => true,
                'worker' => $worker,
                'allowed' => false,
                'message' => "El trabajador {$worker->full_name} está registrado pero su estado es INACTIVO.",
            ]);
        }

        // Revisar si tiene préstamos vencidos
        $overdueLoansCount = $worker->loans()->overdue()->count();
        $activeLoansCount = $worker->loans()->where('status', 'activo')->count();

        return response()->json([
            'found' => true,
            'allowed' => true,
            'worker' => [
                'id' => $worker->id,
                'dni' => $worker->dni,
                'full_name' => $worker->full_name,
                'position' => $worker->position,
                'office_name' => $worker->office ? $worker->office->name : 'Sin Oficina',
                'active_loans' => $activeLoansCount,
                'overdue_loans' => $overdueLoansCount,
            ],
            'warning' => $overdueLoansCount > 0 
                ? "Atención: Este trabajador tiene {$overdueLoansCount} préstamo(s) VENCIDO(S) pendiente(s) de devolución." 
                : null,
        ]);
    }
}
