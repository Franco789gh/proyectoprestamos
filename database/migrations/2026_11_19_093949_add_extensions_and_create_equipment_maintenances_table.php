<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Ampliaciones en préstamos
        Schema::table('loans', function (Blueprint $table) {
            $table->unsignedInteger('extensions_count')->default(0)->after('expected_return_date');
        });

        // 2. Tabla de mantenimientos de equipos
        Schema::create('equipment_maintenances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_id')->constrained('equipment')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('entry_date');
            $table->dateTime('exit_date')->nullable();
            $table->string('provider')->nullable();
            $table->text('reason');
            $table->text('notes')->nullable();
            $table->string('status', 20)->default('en_proceso'); // en_proceso, completado
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_maintenances');

        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn('extensions_count');
        });
    }
};
