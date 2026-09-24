<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Loan;
use App\Models\Office;
use App\Models\Worker;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $offices = Office::where('is_active', true)->orderBy('name', 'asc')->get();
        $equipments = Equipment::orderBy('code', 'asc')->get();

        $query = Loan::with(['worker.office', 'equipment', 'loanUser', 'returnUser', 'incidents']);

        if ($request->filled('date_from')) {
            $query->whereDate('loan_date', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('loan_date', '<=', $request->input('date_to'));
        }

        if ($request->filled('office_id')) {
            $query->whereHas('worker', function ($q) use ($request) {
                $q->where('office_id', $request->input('office_id'));
            });
        }

        if ($request->filled('equipment_id')) {
            $query->where('equipment_id', $request->input('equipment_id'));
        }

        if ($request->filled('status')) {
            if ($request->input('status') === 'vencido') {
                $query->overdue();
            } else {
                $query->where('status', $request->input('status'));
            }
        }

        $loans = $query->orderBy('loan_date', 'desc')->paginate(15)->withQueryString();

        // Resumen
        $summary = [
            'total' => $loans->total(),
            'devueltos' => (clone $query)->where('status', 'devuelto')->count(),
            'incidencias' => (clone $query)->where('status', 'devuelto_con_incidencia')->count(),
            'activos' => (clone $query)->where('status', 'activo')->count(),
        ];

        return view('reports.index', compact('loans', 'offices', 'equipments', 'summary'));
    }

    public function exportPdf(Request $request)
    {
        $query = Loan::with(['worker.office', 'equipment', 'loanUser', 'returnUser']);

        if ($request->filled('date_from')) {
            $query->whereDate('loan_date', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('loan_date', '<=', $request->input('date_to'));
        }

        if ($request->filled('office_id')) {
            $query->whereHas('worker', function ($q) use ($request) {
                $q->where('office_id', $request->input('office_id'));
            });
        }

        if ($request->filled('equipment_id')) {
            $query->where('equipment_id', $request->input('equipment_id'));
        }

        if ($request->filled('status')) {
            if ($request->input('status') === 'vencido') {
                $query->overdue();
            } else {
                $query->where('status', $request->input('status'));
            }
        }

        $loans = $query->orderBy('loan_date', 'desc')->get();
        $filters = $request->all();

        $pdf = Pdf::loadView('reports.pdf', compact('loans', 'filters'))
            ->setPaper('a4', 'landscape');

        $date = date('Ymd_His');
        return $pdf->download("Reporte_Prestamos_{$date}.pdf");
    }

    public function exportCsv(Request $request)
    {
        $query = Loan::with(['worker.office', 'equipment', 'loanUser', 'returnUser']);

        if ($request->filled('date_from')) {
            $query->whereDate('loan_date', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('loan_date', '<=', $request->input('date_to'));
        }

        if ($request->filled('office_id')) {
            $query->whereHas('worker', function ($q) use ($request) {
                $q->where('office_id', $request->input('office_id'));
            });
        }

        if ($request->filled('equipment_id')) {
            $query->where('equipment_id', $request->input('equipment_id'));
        }

        if ($request->filled('status')) {
            if ($request->input('status') === 'vencido') {
                $query->overdue();
            } else {
                $query->where('status', $request->input('status'));
            }
        }

        $loans = $query->orderBy('loan_date', 'desc')->get();

        $filename = "Reporte_Prestamos_" . date('Ymd_His') . ".csv";

        return new StreamedResponse(function () use ($loans) {
            $handle = fopen('php://output', 'w');
            
            // BOM UTF-8 para compatibilidad perfecta con Excel
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // Encabezados
            fputcsv($handle, [
                'Código Préstamo',
                'DNI Solicitante',
                'Trabajador',
                'Oficina',
                'Código Equipo',
                'Equipo',
                'Fecha Préstamo',
                'Fecha Devolución Estimada',
                'Fecha Devolución Real',
                'Estado',
                'Condición Devolución',
                'Encargado Entrega',
                'Encargado Recepción',
                'Motivo',
                'Observaciones',
            ], ';');

            foreach ($loans as $loan) {
                fputcsv($handle, [
                    $loan->loan_code,
                    $loan->worker?->dni,
                    $loan->worker?->full_name,
                    $loan->worker?->office?->name,
                    $loan->equipment?->code,
                    $loan->equipment?->name,
                    $loan->loan_date->format('d/m/Y H:i'),
                    $loan->expected_return_date->format('d/m/Y H:i'),
                    $loan->actual_return_date ? $loan->actual_return_date->format('d/m/Y H:i') : 'Pendiente',
                    ucfirst(str_replace('_', ' ', $loan->status)),
                    $loan->equipment_return_condition ? ucfirst($loan->equipment_return_condition) : '-',
                    $loan->loanUser?->name,
                    $loan->returnUser?->name ?? '-',
                    $loan->purpose,
                    $loan->loan_notes . ' ' . $loan->return_notes,
                ], ';');
            }

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
