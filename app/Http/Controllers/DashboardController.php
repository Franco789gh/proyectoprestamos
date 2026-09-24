<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Loan;
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
            ->take(10)
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
            'dueTodayLoans'
        ));
    }
}
