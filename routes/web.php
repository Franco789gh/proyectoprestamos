<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\OfficeController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WorkerController;
use Illuminate\Support\Facades\Route;

// Rutas Públicas / Autenticación
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// Rutas Protegidas (Encargado y Administrador)
Route::middleware(['auth', 'role:admin,encargado'])->group(function () {
    Route::get('/', fn() => redirect()->route('dashboard'));
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Perfil de Usuario
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');

    // Búsqueda Rápida Global (Topbar & modal Ctrl+K)
    Route::get('/quick-search', [DashboardController::class, 'quickSearch'])->name('quick-search');

    // 1. Módulo de Préstamos y Devoluciones
    Route::get('/loans', [LoanController::class, 'index'])->name('loans.index');
    Route::get('/loans/create', [LoanController::class, 'create'])->name('loans.create');
    Route::post('/loans', [LoanController::class, 'store'])->name('loans.store');
    Route::get('/loans/{loan}', [LoanController::class, 'show'])->name('loans.show');
    Route::post('/loans/{loan}/extend', [LoanController::class, 'extend'])->name('loans.extend');
    Route::get('/loans/{loan}/pdf', [LoanController::class, 'downloadPdf'])->name('loans.pdf');
    Route::get('/loans/{loan}/stream-pdf', [LoanController::class, 'streamPdf'])->name('loans.stream-pdf');
    Route::get('/loans/{loan}/return', [LoanController::class, 'returnForm'])->name('loans.return');
    Route::post('/loans/{loan}/return', [LoanController::class, 'processReturn'])->name('loans.return.post');

    // 2. Módulo de Trabajadores (Solicitantes)
    Route::get('/workers/search/{dni}', [WorkerController::class, 'searchByDni'])->name('workers.search');
    Route::resource('workers', WorkerController::class);

    // 3. Módulo de Inventario de Equipos, Accesorios y Mantenimiento
    Route::get('/equipment/{equipment}/label', [EquipmentController::class, 'printLabel'])->name('equipment.label');
    Route::post('/equipment/{equipment}/maintenance', [EquipmentController::class, 'sendToMaintenance'])->name('equipment.maintenance.send');
    Route::post('/equipment/{equipment}/maintenance/{maintenance}/complete', [EquipmentController::class, 'completeMaintenance'])->name('equipment.maintenance.complete');
    Route::get('/equipment/{equipment}/accessories', [EquipmentController::class, 'getAccessories'])->name('equipment.accessories');
    Route::post('/equipment/{equipment}/accessories', [EquipmentController::class, 'addAccessory'])->name('equipment.accessories.store');
    Route::patch('/accessories/{accessory}/toggle', [EquipmentController::class, 'toggleAccessory'])->name('accessories.toggle');
    Route::delete('/accessories/{accessory}', [EquipmentController::class, 'deleteAccessory'])->name('accessories.destroy');
    Route::resource('equipment', EquipmentController::class);

    // 4. Módulo de Oficinas
    Route::resource('offices', OfficeController::class);

    // 5. Módulo de Reportes
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/pdf', [ReportController::class, 'exportPdf'])->name('reports.pdf');
    Route::get('/reports/csv', [ReportController::class, 'exportCsv'])->name('reports.csv');
});

// Rutas Exclusivas para el Administrador
Route::middleware(['auth', 'role:admin'])->group(function () {
    // 6. Módulo de Usuarios
    Route::resource('users', UserController::class)->except(['show', 'destroy']);
    Route::patch('/users/{user}/toggle', [UserController::class, 'toggleActive'])->name('users.toggle');

    // 7. Módulo de Auditoría
    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit.index');
});
