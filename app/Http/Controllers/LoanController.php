<?php

namespace App\Http\Controllers;

use App\Models\Accessory;
use App\Models\AuditLog;
use App\Models\Equipment;
use App\Models\Loan;
use App\Models\LoanAccessory;
use App\Models\LoanIncident;
use App\Models\Worker;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LoanController extends Controller
{
    public function index(Request $request)
    {
        $query = Loan::with(['worker.office', 'equipment', 'loanUser', 'returnUser', 'incident']);

        // Filtro de búsqueda general
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('loan_code', 'like', "%{$search}%")
                  ->orWhereHas('worker', function ($wq) use ($search) {
                      $wq->where('dni', 'like', "%{$search}%")
                         ->orWhere('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%")
                         ->orWhereHas('office', function ($oq) use ($search) {
                             $oq->where('name', 'like', "%{$search}%");
                         });
                  })
                  ->orWhereHas('equipment', function ($eq) use ($search) {
                      $eq->where('code', 'like', "%{$search}%")
                         ->orWhere('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filtro por estado / tab
        $statusTab = $request->input('status', 'activos');

        switch ($statusTab) {
            case 'activos':
                $query->where('status', 'activo');
                break;
            case 'vencidos':
                $query->overdue();
                break;
            case 'devueltos':
                $query->where('status', 'devuelto');
                break;
            case 'incidencias':
                $query->where('status', 'devuelto_con_incidencia');
                break;
            case 'todos':
            default:
                // todos
                break;
        }

        // Conteos para tabs
        $counts = [
            'todos' => Loan::count(),
            'activos' => Loan::where('status', 'activo')->count(),
            'vencidos' => Loan::overdue()->count(),
            'devueltos' => Loan::where('status', 'devuelto')->count(),
            'incidencias' => Loan::where('status', 'devuelto_con_incidencia')->count(),
        ];

        $loans = $query->orderBy('created_at', 'desc')->paginate(12)->withQueryString();

        return view('loans.index', compact('loans', 'counts', 'statusTab'));
    }

    public function create(Request $request)
    {
        // Solo equipos que estén disponibles
        $availableEquipment = Equipment::with('activeAccessories')
            ->where('status', 'disponible')
            ->orderBy('code', 'asc')
            ->get();

        // Si se envió un pre-filtro de equipo
        $selectedEquipmentId = $request->input('equipment_id');

        return view('loans.create', compact('availableEquipment', 'selectedEquipmentId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'worker_id' => ['required', 'exists:workers,id'],
            'equipment_id' => ['required', 'exists:equipment,id'],
            'loan_date' => ['required', 'date'],
            'expected_return_date' => ['required', 'date', 'after_or_equal:loan_date'],
            'purpose' => ['required', 'string', 'max:500'],
            'loan_notes' => ['nullable', 'string', 'max:500'],
            'accessories' => ['nullable', 'array'],
            'accessories.*' => ['exists:accessories,id'],
        ], [
            'worker_id.required' => 'Debe ingresar y verificar el DNI del solicitante.',
            'equipment_id.required' => 'Debe seleccionar un equipo disponible.',
            'loan_date.required' => 'La fecha y hora de préstamo es obligatoria.',
            'expected_return_date.required' => 'La fecha y hora estimada de devolución es obligatoria.',
            'expected_return_date.after_or_equal' => 'La fecha de devolución no puede ser anterior a la fecha de salida.',
            'purpose.required' => 'El motivo o destino del préstamo es obligatorio.',
        ]);

        $loan = DB::transaction(function () use ($validated, $request) {
            // 1. Bloqueo pesimista del equipo para evitar condiciones de carrera
            $equipment = Equipment::lockForUpdate()->findOrFail($validated['equipment_id']);

            if ($equipment->status !== 'disponible') {
                throw ValidationException::withMessages([
                    'equipment_id' => "El equipo {$equipment->code} ya no se encuentra disponible (Estado actual: {$equipment->status}).",
                ]);
            }

            $worker = Worker::findOrFail($validated['worker_id']);
            if (!$worker->is_active) {
                throw ValidationException::withMessages([
                    'worker_id' => "El trabajador {$worker->full_name} se encuentra inactivo.",
                ]);
            }

            // 2. Generar código de préstamo único PRE-YYYY-000001
            $loanCode = Loan::generateLoanCode();

            // 3. Crear el préstamo
            $loan = Loan::create([
                'loan_code' => $loanCode,
                'worker_id' => $worker->id,
                'equipment_id' => $equipment->id,
                'loan_user_id' => Auth::id(),
                'loan_date' => Carbon::parse($validated['loan_date']),
                'expected_return_date' => Carbon::parse($validated['expected_return_date']),
                'purpose' => $validated['purpose'],
                'loan_notes' => $validated['loan_notes'] ?? null,
                'status' => 'activo',
            ]);

            // 4. Registrar los accesorios entregados
            $selectedAccessories = $validated['accessories'] ?? [];
            if (!empty($selectedAccessories)) {
                foreach ($selectedAccessories as $accId) {
                    LoanAccessory::create([
                        'loan_id' => $loan->id,
                        'accessory_id' => $accId,
                        'was_delivered' => true,
                        'was_returned' => false,
                    ]);
                }
            }

            // 5. Actualizar estado del equipo a PRESTADO
            $equipment->update(['status' => 'prestado']);

            // 6. Auditoría
            AuditLog::logAction(
                'REGISTRO_PRESTAMO',
                "Se registró el préstamo {$loan->loan_code} de {$equipment->code} ({$equipment->name}) para {$worker->full_name} ({$worker->office?->name}).",
                $loan,
                null,
                $loan->toArray()
            );

            return $loan;
        });

        return redirect()->route('loans.show', $loan)
            ->with('success', "¡Préstamo {$loan->loan_code} registrado correctamente!");
    }

    public function show(Loan $loan)
    {
        $loan->load([
            'worker.office',
            'equipment.accessories',
            'loanUser',
            'returnUser',
            'loanAccessories.accessory',
            'incidents',
        ]);

        return view('loans.show', compact($loan->loan_code ? 'loan' : 'loan'));
    }

    public function downloadPdf(Loan $loan)
    {
        $loan->load([
            'worker.office',
            'equipment',
            'loanUser',
            'returnUser',
            'loanAccessories.accessory',
            'incidents',
        ]);

        $pdf = Pdf::loadView('loans.pdf', compact('loan'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("Constancia_Prestamo_{$loan->loan_code}.pdf");
    }

    public function streamPdf(Loan $loan)
    {
        $loan->load([
            'worker.office',
            'equipment',
            'loanUser',
            'returnUser',
            'loanAccessories.accessory',
            'incidents',
        ]);

        $pdf = Pdf::loadView('loans.pdf', compact('loan'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream("Constancia_Prestamo_{$loan->loan_code}.pdf");
    }

    public function returnForm(Loan $loan)
    {
        if ($loan->status !== 'activo') {
            return redirect()->route('loans.show', $loan)
                ->with('warning', "Este préstamo ya se encuentra en estado '{$loan->status}'.");
        }

        $loan->load([
            'worker.office',
            'equipment',
            'loanAccessories.accessory',
        ]);

        return view('loans.return', compact('loan'));
    }

    public function processReturn(Request $request, Loan $loan)
    {
        if ($loan->status !== 'activo') {
            return redirect()->route('loans.show', $loan)
                ->with('error', "El préstamo no está activo.");
        }

        $validated = $request->validate([
            'equipment_return_condition' => ['required', 'in:bueno,regular,danado'],
            'returned_accessories' => ['nullable', 'array'],
            'returned_accessories.*' => ['exists:accessories,id'],
            'accessory_observations' => ['nullable', 'array'],
            'return_notes' => ['nullable', 'string', 'max:1000'],
            'has_incident' => ['nullable', 'boolean'],
            'incident_type' => ['nullable', 'required_if:has_incident,1', 'in:accesorio_faltante,dano_equipo,retraso_grave,otro'],
            'incident_description' => ['nullable', 'required_if:has_incident,1', 'string', 'max:1000'],
        ], [
            'equipment_return_condition.required' => 'Debe indicar el estado físico en que se recibe el equipo.',
            'incident_description.required_if' => 'Debe especificar el detalle de la incidencia.',
        ]);

        DB::transaction(function () use ($loan, $validated, $request) {
            $equipment = Equipment::lockForUpdate()->findOrFail($loan->equipment_id);
            $returnedAccessories = $validated['returned_accessories'] ?? [];
            $observations = $validated['accessory_observations'] ?? [];

            $hasMissingAccessories = false;
            $missingAccessoriesNames = [];

            // 1. Actualizar estado de devolución de cada accesorio que fue entregado
            foreach ($loan->loanAccessories as $loanAccessory) {
                $isReturned = in_array($loanAccessory->accessory_id, $returnedAccessories);
                $obs = $observations[$loanAccessory->accessory_id] ?? null;

                $loanAccessory->update([
                    'was_returned' => $isReturned,
                    'observation' => $obs,
                ]);

                if (!$isReturned) {
                    $hasMissingAccessories = true;
                    $missingAccessoriesNames[] = $loanAccessory->accessory->name ?? 'Accesorio ID ' . $loanAccessory->accessory_id;
                }
            }

            // 2. Determinar si hay incidencias
            $isDamaged = $validated['equipment_return_condition'] === 'danado';
            $manualIncident = $request->boolean('has_incident');
            $hasIncident = $hasMissingAccessories || $isDamaged || $manualIncident;

            $finalLoanStatus = $hasIncident ? 'devuelto_con_incidencia' : 'devuelto';

            // 3. Actualizar el Préstamo
            $loan->update([
                'actual_return_date' => Carbon::now(),
                'return_user_id' => Auth::id(),
                'equipment_return_condition' => $validated['equipment_return_condition'],
                'return_notes' => $validated['return_notes'] ?? null,
                'status' => $finalLoanStatus,
            ]);

            // 4. Crear registro de incidencia si corresponde
            if ($hasIncident) {
                $incidentType = 'otro';
                $descriptionParts = [];

                if ($hasMissingAccessories) {
                    $incidentType = 'accesorio_faltante';
                    $descriptionParts[] = 'Faltaron accesorios por devolver: ' . implode(', ', $missingAccessoriesNames) . '.';
                }

                if ($isDamaged) {
                    $incidentType = 'dano_equipo';
                    $descriptionParts[] = 'El equipo fue devuelto con daños físicos.';
                }

                if (!empty($validated['incident_description'])) {
                    $descriptionParts[] = $validated['incident_description'];
                }

                if (!empty($validated['incident_type'])) {
                    $incidentType = $validated['incident_type'];
                }

                LoanIncident::create([
                    'loan_id' => $loan->id,
                    'incident_type' => $incidentType,
                    'description' => implode("\n", $descriptionParts),
                    'status' => 'pendiente',
                ]);
            }

            // 5. Actualizar el estado del equipo
            if ($isDamaged) {
                $equipment->update([
                    'status' => 'danado',
                    'physical_condition' => 'malo',
                ]);
            } else {
                $equipment->update([
                    'status' => 'disponible',
                    'physical_condition' => $validated['equipment_return_condition'] === 'regular' ? 'regular' : 'bueno',
                ]);
            }

            // 6. Auditoría
            AuditLog::logAction(
                'REGISTRO_DEVOLUCION',
                "Se registró la devolución del préstamo {$loan->loan_code}. Estado final: {$finalLoanStatus}. Equipo {$equipment->code} marcado como {$equipment->status}.",
                $loan,
                null,
                $loan->fresh()->toArray()
            );
        });

        return redirect()->route('loans.show', $loan)
            ->with('success', "Devolución del préstamo {$loan->loan_code} confirmada y registrada correctamente.");
    }
}
