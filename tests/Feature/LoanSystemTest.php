<?php

namespace Tests\Feature;

use App\Models\Accessory;
use App\Models\Equipment;
use App\Models\Loan;
use App\Models\Office;
use App\Models\User;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoanSystemTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $encargado;
    protected Office $office;
    protected Worker $worker;
    protected Equipment $equipment;
    protected Accessory $accessory1;
    protected Accessory $accessory2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->encargado = User::create([
            'name' => 'Carlos Encargado',
            'email' => 'carlos@test.com',
            'password' => bcrypt('password'),
            'role' => 'encargado',
            'is_active' => true,
        ]);

        $this->office = Office::create([
            'name' => 'Abastecimiento',
            'code' => 'ABAST',
            'is_active' => true,
        ]);

        $this->worker = Worker::create([
            'dni' => '12345678',
            'first_name' => 'Juan',
            'last_name' => 'Pérez',
            'office_id' => $this->office->id,
            'is_active' => true,
        ]);

        $this->equipment = Equipment::create([
            'code' => 'EQ-001',
            'name' => 'Proyector Epson X49',
            'category' => 'Audiovisual',
            'physical_condition' => 'bueno',
            'status' => 'disponible',
        ]);

        $this->accessory1 = Accessory::create([
            'equipment_id' => $this->equipment->id,
            'name' => 'Cable HDMI',
            'is_active' => true,
        ]);

        $this->accessory2 = Accessory::create([
            'equipment_id' => $this->equipment->id,
            'name' => 'Control Remoto',
            'is_active' => true,
        ]);
    }

    /**
     * Caso 1: Flujo estándar de Préstamo y Devolución Conforme.
     */
    public function test_standard_loan_and_successful_return_flow(): void
    {
        $this->actingAs($this->encargado);

        // 1. Registrar Préstamo
        $response = $this->post(route('loans.store'), [
            'worker_id' => $this->worker->id,
            'equipment_id' => $this->equipment->id,
            'loan_date' => Carbon::now()->format('Y-m-d H:i:s'),
            'expected_return_date' => Carbon::now()->addHours(4)->format('Y-m-d H:i:s'),
            'purpose' => 'Capacitación del personal',
            'accessories' => [$this->accessory1->id, $this->accessory2->id],
        ]);

        $loan = Loan::first();
        $this->assertNotNull($loan);
        $this->assertStringStartsWith('PRE-', $loan->loan_code);
        $response->assertRedirect(route('loans.show', $loan));

        // Verificar que el equipo cambió a "prestado"
        $this->equipment->refresh();
        $this->assertEquals('prestado', $this->equipment->status);

        // 2. Realizar Devolución Conforme con todos los accesorios
        $returnResponse = $this->post(route('loans.return.post', $loan), [
            'equipment_return_condition' => 'bueno',
            'returned_accessories' => [$this->accessory1->id, $this->accessory2->id],
            'return_notes' => 'Devuelto en perfectas condiciones',
        ]);

        $loan->refresh();
        $this->assertEquals('devuelto', $loan->status);
        $this->assertNotNull($loan->actual_return_date);
        $this->assertEquals($this->encargado->id, $loan->return_user_id);

        // Verificar que el equipo volvió a "disponible"
        $this->equipment->refresh();
        $this->assertEquals('disponible', $this->equipment->status);
    }

    /**
     * Caso 2: Control de Disponibilidad: No permitir prestar un equipo ya prestado.
     */
    public function test_cannot_loan_an_unavailable_equipment(): void
    {
        $this->actingAs($this->encargado);

        $this->equipment->update(['status' => 'prestado']);

        $response = $this->post(route('loans.store'), [
            'worker_id' => $this->worker->id,
            'equipment_id' => $this->equipment->id,
            'loan_date' => Carbon::now()->format('Y-m-d H:i:s'),
            'expected_return_date' => Carbon::now()->addHours(2)->format('Y-m-d H:i:s'),
            'purpose' => 'Uso urgente',
        ]);

        $response->assertSessionHasErrors('equipment_id');
        $this->assertEquals(0, Loan::count());
    }

    /**
     * Caso 3: Devolución con Accesorio Faltante (Incidencia).
     */
    public function test_return_with_missing_accessory_records_incident(): void
    {
        $this->actingAs($this->encargado);

        // Crear préstamo con 2 accesorios
        $this->post(route('loans.store'), [
            'worker_id' => $this->worker->id,
            'equipment_id' => $this->equipment->id,
            'loan_date' => Carbon::now()->format('Y-m-d H:i:s'),
            'expected_return_date' => Carbon::now()->addHours(2)->format('Y-m-d H:i:s'),
            'purpose' => 'Presentación de informes',
            'accessories' => [$this->accessory1->id, $this->accessory2->id],
        ]);

        $loan = Loan::first();

        // Devolver solo el accesorio 1 (Falta el control remoto)
        $this->post(route('loans.return.post', $loan), [
            'equipment_return_condition' => 'bueno',
            'returned_accessories' => [$this->accessory1->id], // Falta $this->accessory2->id
            'return_notes' => 'No entregó el control remoto',
        ]);

        $loan->refresh();
        $this->assertEquals('devuelto_con_incidencia', $loan->status);
        $this->assertEquals(1, $loan->incidents()->count());
        $this->assertEquals('accesorio_faltante', $loan->incidents->first()->incident_type);
    }

    /**
     * Caso 4: Devolución con Equipo Dañado.
     */
    public function test_return_with_damaged_equipment_updates_status(): void
    {
        $this->actingAs($this->encargado);

        $this->post(route('loans.store'), [
            'worker_id' => $this->worker->id,
            'equipment_id' => $this->equipment->id,
            'loan_date' => Carbon::now()->format('Y-m-d H:i:s'),
            'expected_return_date' => Carbon::now()->addHours(2)->format('Y-m-d H:i:s'),
            'purpose' => 'Inspección',
            'accessories' => [$this->accessory1->id],
        ]);

        $loan = Loan::first();

        // Devolver marcando estado Físico "Dañado"
        $this->post(route('loans.return.post', $loan), [
            'equipment_return_condition' => 'danado',
            'returned_accessories' => [$this->accessory1->id],
            'return_notes' => 'Lente rajado por caída',
        ]);

        $loan->refresh();
        $this->assertEquals('devuelto_con_incidencia', $loan->status);

        $this->equipment->refresh();
        $this->assertEquals('danado', $this->equipment->status);
        $this->assertEquals('malo', $this->equipment->physical_condition);
    }

    /**
     * Caso 5: Detección de Préstamos Vencidos.
     */
    public function test_overdue_loans_detection(): void
    {
        $loan = Loan::create([
            'loan_code' => 'PRE-2026-000099',
            'worker_id' => $this->worker->id,
            'equipment_id' => $this->equipment->id,
            'loan_user_id' => $this->encargado->id,
            'loan_date' => Carbon::now()->subDays(2),
            'expected_return_date' => Carbon::now()->subDay(), // Venció ayer
            'purpose' => 'Prueba vencimiento',
            'status' => 'activo',
        ]);

        $this->assertTrue($loan->isOverdue());
        $this->assertEquals(1, Loan::overdue()->count());
    }

    /**
     * Caso 6: Búsqueda rápida de trabajador por DNI vía API.
     */
    public function test_fast_dni_search_api(): void
    {
        $this->actingAs($this->encargado);

        $response = $this->get(route('workers.search', ['dni' => '12345678']));

        $response->assertOk()
            ->assertJson([
                'found' => true,
                'allowed' => true,
                'worker' => [
                    'dni' => '12345678',
                    'full_name' => 'Juan Pérez',
                    'office_name' => 'Abastecimiento',
                ],
            ]);
    }

    /**
     * Caso 7: Generación de Constancia PDF.
     */
    public function test_pdf_generation(): void
    {
        $this->actingAs($this->encargado);

        $loan = Loan::create([
            'loan_code' => 'PRE-2026-000001',
            'worker_id' => $this->worker->id,
            'equipment_id' => $this->equipment->id,
            'loan_user_id' => $this->encargado->id,
            'loan_date' => Carbon::now(),
            'expected_return_date' => Carbon::now()->addHours(3),
            'purpose' => 'Capacitación en Sala 1',
            'status' => 'activo',
        ]);

        $response = $this->get(route('loans.pdf', $loan));
        $response->assertOk();
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
    }

    /**
     * Caso 8: Ampliación / Prórroga de Plazo de Préstamo.
     */
    public function test_loan_extension_increases_return_date_and_extensions_count(): void
    {
        $this->actingAs($this->encargado);

        $loan = Loan::create([
            'loan_code' => 'PRE-2026-000001',
            'worker_id' => $this->worker->id,
            'equipment_id' => $this->equipment->id,
            'loan_user_id' => $this->encargado->id,
            'loan_date' => Carbon::now(),
            'expected_return_date' => Carbon::now()->addHour(),
            'purpose' => 'Reunión de gerencia',
            'status' => 'activo',
        ]);

        $newDate = Carbon::now()->addHours(5)->format('Y-m-d H:i:s');

        $response = $this->post(route('loans.extend', $loan), [
            'new_expected_return_date' => $newDate,
            'extension_reason' => 'Se alargó la sesión del concejo',
        ]);

        $response->assertRedirect(route('loans.show', $loan));
        $loan->refresh();
        $this->assertEquals(1, $loan->extensions_count);
        $this->assertStringContainsString('Se alargó la sesión', $loan->loan_notes);
    }

    /**
     * Caso 9: Flujo de Gestión de Mantenimiento de Equipos.
     */
    public function test_equipment_maintenance_flow(): void
    {
        $this->actingAs($this->encargado);

        // 1. Enviar a mantenimiento
        $response = $this->post(route('equipment.maintenance.send', $this->equipment), [
            'reason' => 'Cambio de filtro y limpieza',
            'provider' => 'Epson Service',
        ]);

        $response->assertSessionHas('success');
        $this->equipment->refresh();
        $this->assertEquals('mantenimiento', $this->equipment->status);
        $this->assertEquals(1, $this->equipment->maintenances()->count());

        $maintenance = $this->equipment->currentMaintenance;
        $this->assertNotNull($maintenance);

        // 2. Finalizar mantenimiento y reincorporar a disponible
        $completeResponse = $this->post(route('equipment.maintenance.complete', [$this->equipment, $maintenance]), [
            'work_performed' => 'Filtro reemplazado y calibración completada',
            'physical_condition' => 'bueno',
        ]);

        $completeResponse->assertSessionHas('success');
        $this->equipment->refresh();
        $this->assertEquals('disponible', $this->equipment->status);
        $this->assertEquals('bueno', $this->equipment->physical_condition);
    }

    /**
     * Caso 10: Búsqueda Rápida Global en Topbar.
     */
    public function test_global_quick_search_api(): void
    {
        $this->actingAs($this->encargado);

        $response = $this->get(route('quick-search', ['q' => 'Epson']));
        $response->assertOk()
            ->assertJsonStructure(['loans', 'equipment', 'workers']);
    }

    /**
     * Caso 11: Vista de Etiqueta Patrimonial con QR.
     */
    public function test_equipment_qr_label_view(): void
    {
        $this->actingAs($this->encargado);

        $response = $this->get(route('equipment.label', $this->equipment));
        $response->assertOk()
            ->assertSee($this->equipment->code)
            ->assertSee('SISTEMA DE CONTROL PATRIMONIAL');
    }
}
