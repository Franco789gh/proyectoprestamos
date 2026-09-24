<?php

namespace Database\Seeders;

use App\Models\Accessory;
use App\Models\Equipment;
use App\Models\Office;
use App\Models\User;
use App\Models\Worker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Usuarios del sistema
        $admin = User::firstOrCreate(
            ['email' => 'admin@prestamos.gob.pe'],
            [
                'name' => 'Administrador General',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        $encargado = User::firstOrCreate(
            ['email' => 'carlos@prestamos.gob.pe'],
            [
                'name' => 'Carlos Mendoza (Encargado)',
                'password' => Hash::make('encargado123'),
                'role' => 'encargado',
                'is_active' => true,
            ]
        );

        // 2. Oficinas de la institución
        $officesData = [
            ['name' => 'Abastecimiento y Control Patrimonial', 'code' => 'ABAST', 'description' => 'Unidad encargada de logística y adquisiciones'],
            ['name' => 'Tesorería', 'code' => 'TESOR', 'description' => 'Unidad financiera y recaudación'],
            ['name' => 'Recursos Humanos', 'code' => 'RRHH', 'description' => 'Gestión del talento humano y planillas'],
            ['name' => 'Secretaría General', 'code' => 'SECGEN', 'description' => 'Trámite documentario y archivo central'],
            ['name' => 'Informática y Sistemas', 'code' => 'INFO', 'description' => 'Soporte técnico, redes y desarrollo tecnológico'],
            ['name' => 'Obras Públicas y Desarrollo Urbano', 'code' => 'OBRAS', 'description' => 'Supervisión y ejecución de obras civiles'],
            ['name' => 'Contabilidad', 'code' => 'CONT', 'description' => 'Registro y control contable y presupuestal'],
        ];

        $offices = [];
        foreach ($officesData as $data) {
            $offices[$data['code']] = Office::create($data);
        }

        // 3. Trabajadores (Solicitantes)
        $workersData = [
            [
                'dni' => '12345678',
                'first_name' => 'Juan',
                'last_name' => 'Pérez Gómez',
                'office_id' => $offices['ABAST']->id,
                'position' => 'Especialista en Adquisiciones',
                'phone' => '987654321',
                'email' => 'jperez@municipalidad.gob.pe',
            ],
            [
                'dni' => '87654321',
                'first_name' => 'María',
                'last_name' => 'López Castro',
                'office_id' => $offices['RRHH']->id,
                'position' => 'Coordinadora de Capacitaciones',
                'phone' => '912345678',
                'email' => 'mlopez@municipalidad.gob.pe',
            ],
            [
                'dni' => '45678912',
                'first_name' => 'Pedro',
                'last_name' => 'Díaz Rojas',
                'office_id' => $offices['OBRAS']->id,
                'position' => 'Inspector de Obras',
                'phone' => '998877665',
                'email' => 'pdiaz@municipalidad.gob.pe',
            ],
            [
                'dni' => '78912345',
                'first_name' => 'Ana',
                'last_name' => 'Gómez Silva',
                'office_id' => $offices['INFO']->id,
                'position' => 'Analista de Sistemas',
                'phone' => '944556677',
                'email' => 'agomez@municipalidad.gob.pe',
            ],
            [
                'dni' => '32165498',
                'first_name' => 'Carlos',
                'last_name' => 'Ruiz Morales',
                'office_id' => $offices['TESOR']->id,
                'position' => 'Cajero Principal',
                'phone' => '933221100',
                'email' => 'cruiz@municipalidad.gob.pe',
            ],
            [
                'dni' => '65498732',
                'first_name' => 'Lucía',
                'last_name' => 'Fernández Vega',
                'office_id' => $offices['CONT']->id,
                'position' => 'Analista Contable',
                'phone' => '955667788',
                'email' => 'lfernandez@municipalidad.gob.pe',
            ],
        ];

        foreach ($workersData as $wData) {
            Worker::create($wData);
        }

        // 4. Equipos e Inventario con Accesorios
        $equipmentsData = [
            [
                'code' => 'EQ-001',
                'name' => 'Proyector Multimedia Epson',
                'brand' => 'Epson',
                'model' => 'PowerLite X49',
                'serial_number' => 'X49-EPS-88239',
                'category' => 'Audiovisual',
                'physical_condition' => 'bueno',
                'status' => 'disponible',
                'notes' => 'Proyector 3600 lúmenes, resolución XGA, en óptimas condiciones.',
                'accessories' => [
                    'Cable HDMI de 5 metros',
                    'Cable de alimentación 220V',
                    'Control remoto original (con pilas)',
                    'Extensión eléctrica de 10 metros con supresor',
                    'Maletín acolchado de transporte',
                ],
            ],
            [
                'code' => 'EQ-002',
                'name' => 'Laptop Dell Latitude',
                'brand' => 'Dell',
                'model' => 'Latitude 5420',
                'serial_number' => 'DELL-LAT-5420-009',
                'category' => 'Cómputo',
                'physical_condition' => 'bueno',
                'status' => 'disponible',
                'notes' => 'Intel Core i7 11va Gen, 16GB RAM, SSD 512GB.',
                'accessories' => [
                    'Cargador original USB-C 65W',
                    'Mouse inalámbrico Logitech',
                    'Adaptador multipuerto USB-C a HDMI / VGA',
                    'Mochila porta-laptop institucional',
                ],
            ],
            [
                'code' => 'EQ-003',
                'name' => 'Taladro Percutor Industrial',
                'brand' => 'Bosch',
                'model' => 'GSB 13 RE Professional',
                'serial_number' => 'BOSCH-PER-650W-331',
                'category' => 'Herramientas',
                'physical_condition' => 'bueno',
                'status' => 'disponible',
                'notes' => 'Taladro percutor 650W de uso rudo para obras y mantenimiento.',
                'accessories' => [
                    'Llave para mandril / portabrocas',
                    'Empuñadura auxiliar antivibración',
                    'Tope de profundidad metálico',
                    'Juego de 5 brocas para concreto y mampostería',
                    'Maletín rígido de transporte',
                ],
            ],
            [
                'code' => 'EQ-004',
                'name' => 'Cámara Fotográfica Réflex',
                'brand' => 'Canon',
                'model' => 'EOS Rebel T7',
                'serial_number' => 'CAN-EOS-T7-49102',
                'category' => 'Audiovisual / Comunicaciones',
                'physical_condition' => 'bueno',
                'status' => 'disponible',
                'notes' => 'Sensor 24.1 MP para coberturas de eventos institucionales.',
                'accessories' => [
                    'Lente Canon EF-S 18-55mm IS II con tapa',
                    'Batería LP-E10 original',
                    'Cargador de pared LC-E10E',
                    'Memoria SD Sandisk Extreme 64GB',
                    'Trípode profesional de aluminio',
                    'Estuche acolchado impermeable',
                ],
            ],
            [
                'code' => 'EQ-005',
                'name' => 'Proyector Portátil ViewSonic',
                'brand' => 'ViewSonic',
                'model' => 'M1 Mini Plus',
                'serial_number' => 'VS-M1-MINI-7721',
                'category' => 'Audiovisual',
                'physical_condition' => 'bueno',
                'status' => 'disponible',
                'notes' => 'Proyector de bolsillo ultra-portátil con batería integrada.',
                'accessories' => [
                    'Cable HDMI plano',
                    'Cable USB-C a USB-A para carga',
                    'Control remoto ultraplano',
                    'Mini trípode articulado',
                ],
            ],
            [
                'code' => 'EQ-006',
                'name' => 'Medidor Láser de Distancia',
                'brand' => 'Bosch',
                'model' => 'GLM 50 C Professional',
                'serial_number' => 'BOSCH-GLM-50C-901',
                'category' => 'Topografía / Herramientas',
                'physical_condition' => 'bueno',
                'status' => 'disponible',
                'notes' => 'Rango hasta 50 metros con Bluetooth para planos y mediciones en obra.',
                'accessories' => [
                    'Funda protectora para cinturón',
                    '2 pilas alcalinas AAA',
                    'Correa de mano',
                ],
            ],
        ];

        foreach ($equipmentsData as $eqData) {
            $accessories = $eqData['accessories'];
            unset($eqData['accessories']);

            $equipment = Equipment::create($eqData);

            foreach ($accessories as $accName) {
                Accessory::create([
                    'equipment_id' => $equipment->id,
                    'name' => $accName,
                    'is_active' => true,
                ]);
            }
        }
    }
}
