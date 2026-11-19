<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Loan;
use App\Models\Office;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Conteo de Equipos por Estado
        $availableEquipment = Equipment::where('status', 'disponible')->count();
        $loanedEquipment = Equipment::where('status', 'prestado')->count();
        $maintenanceEquipment = Equipment::where('status', 'mantenimiento')->count();
        $damagedEquipment = Equipment::where('status', 'danado')->count();
        $totalEquipment = Equipment::count();

        // 2. Conteo de Préstamos
        $activeLoansCount = Loan::where('status', 'activo')->count();
        $overdueLoansCount = Loan::overdue()->count();
        $todayDueLoansCount = Loan::dueToday()->count();
        $totalWorkers = Worker::where('is_active', true)->count();

        // 3. Préstamos de hoy / recientes
        $recentLoans = Loan::with(['worker.office', 'equipment', 'loanUser', 'returnUser'])
            ->latest()
            ->take(8)
            ->get();

        // 4. Préstamos vencidos para alerta inmediata
        $overdueLoans = Loan::with(['worker.office', 'equipment', 'loanUser'])
            ->overdue()
            ->orderBy('expected_return_date', 'asc')
            ->take(5)
            ->get();

        // 5. Préstamos por vencer hoy
        $dueTodayLoans = Loan::with(['worker.office', 'equipment', 'loanUser'])
            ->dueToday()
            ->orderBy('expected_return_date', 'asc')
            ->get();

        // 6. Tendencia de préstamos últimos 7 días para Chart.js
        $chartDays = [];
        $chartLoansData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $chartDays[] = ucfirst($date->isoFormat('ddd D'));
            $chartLoansData[] = Loan::whereDate('loan_date', $date->toDateString())->count();
        }

        // 7. Top 5 Equipos con mayor rotación
        $topEquipment = Equipment::withCount('loans')
            ->orderByDesc('loans_count')
            ->take(5)
            ->get();

        // 8. Top Oficinas demandantes
        $topOffices = Office::withCount(['workers as loans_count' => function ($query) {
            $query->join('loans', 'workers.id', '=', 'loans.worker_id');
        }])->orderByDesc('loans_count')->take(5)->get();

        return view('dashboard', compact(
            'availableEquipment',
            'loanedEquipment',
            'maintenanceEquipment',
            'damagedEquipment',
            'totalEquipment',
            'activeLoansCount',
            'overdueLoansCount',
            'todayDueLoansCount',
            'totalWorkers',
            'recentLoans',
            'overdueLoans',
            'dueTodayLoans',
            'chartDays',
            'chartLoansData',
            'topEquipment',
            'topOffices'
        ));
    }

    /**
     * Búsqueda Rápida Global para el Topbar (Ctrl+K o barra superior)
     */
    public function quickSearch(Request $request)
    {
        $q = trim($request->input('q', ''));
        if (strlen($q) < 2) {
            return response()->json([
                'loans' => [],
                'equipment' => [],
                'workers' => []
            ]);
        }

        $loans = Loan::with(['worker', 'equipment'])
            ->where('loan_code', 'LIKE', "%{$q}%")
            ->orWhereHas('worker', function ($w) use ($q) {
                $w->where('dni', 'LIKE', "%{$q}%")
                  ->orWhere('first_name', 'LIKE', "%{$q}%")
                  ->orWhere('last_name', 'LIKE', "%{$q}%");
            })
            ->latest()
            ->take(5)
            ->get()
            ->map(fn($l) => [
                'id' => $l->id,
                'code' => $l->loan_code,
                'worker' => $l->worker?->full_name,
                'equipment' => $l->equipment?->name,
                'status' => $l->status,
                'url' => route('loans.show', $l),
            ]);

        $equipment = Equipment::where('code', 'LIKE', "%{$q}%")
            ->orWhere('name', 'LIKE', "%{$q}%")
            ->orWhere('brand', 'LIKE', "%{$q}%")
            ->take(5)
            ->get()
            ->map(fn($e) => [
                'id' => $e->id,
                'code' => $e->code,
                'name' => $e->name,
                'status' => $e->status,
                'url' => route('equipment.show', $e),
            ]);

        $workers = Worker::with('office')
            ->where('dni', 'LIKE', "%{$q}%")
            ->orWhere('first_name', 'LIKE', "%{$q}%")
            ->orWhere('last_name', 'LIKE', "%{$q}%")
            ->take(5)
            ->get()
            ->map(fn($w) => [
                'id' => $w->id,
                'dni' => $w->dni,
                'name' => $w->full_name,
                'office' => $w->office?->name,
                'url' => route('workers.edit', $w),
            ]);

        return response()->json([
            'loans' => $loans,
            'equipment' => $equipment,
            'workers' => $workers,
        ]);
    }
}
