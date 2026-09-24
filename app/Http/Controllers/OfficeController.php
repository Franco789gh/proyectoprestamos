<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Office;
use Illuminate\Http\Request;

class OfficeController extends Controller
{
    public function index(Request $request)
    {
        $query = Office::withCount('workers');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $offices = $query->orderBy('name', 'asc')->paginate(10)->withQueryString();

        return view('offices.index', compact('offices'));
    }

    public function create()
    {
        return view('offices.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ], [
            'name.required' => 'El nombre de la oficina es obligatorio.',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $office = Office::create($validated);

        AuditLog::logAction(
            'CREACION_OFICINA',
            "Se creó la oficina: {$office->name} (Código: {$office->code}).",
            $office,
            null,
            $office->toArray()
        );

        return redirect()->route('offices.index')
            ->with('success', "Oficina '{$office->name}' registrada exitosamente.");
    }

    public function edit(Office $office)
    {
        return view('offices.edit', compact('office'));
    }

    public function update(Request $request, Office $office)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ], [
            'name.required' => 'El nombre de la oficina es obligatorio.',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $before = $office->toArray();

        $office->update($validated);

        AuditLog::logAction(
            'ACTUALIZACION_OFICINA',
            "Se actualizó la oficina: {$office->name}.",
            $office,
            $before,
            $office->toArray()
        );

        return redirect()->route('offices.index')
            ->with('success', "Oficina '{$office->name}' actualizada exitosamente.");
    }

    public function destroy(Office $office)
    {
        if ($office->workers()->count() > 0) {
            return back()->with('error', "No se puede eliminar la oficina '{$office->name}' porque tiene trabajadores asociados.");
        }

        $before = $office->toArray();
        $officeName = $office->name;
        $office->delete();

        AuditLog::logAction(
            'ELIMINACION_OFICINA',
            "Se eliminó la oficina: {$officeName}.",
            null,
            $before,
            null
        );

        return redirect()->route('offices.index')
            ->with('success', "Oficina '{$officeName}' eliminada correctamente.");
    }
}
