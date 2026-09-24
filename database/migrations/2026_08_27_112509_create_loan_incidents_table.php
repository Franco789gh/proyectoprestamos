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
        Schema::create('loan_incidents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained('loans')->cascadeOnDelete();
            $table->enum('incident_type', ['accesorio_faltante', 'dano_equipo', 'retraso_grave', 'otro'])->default('otro');
            $table->text('description');
            $table->text('resolution_notes')->nullable();
            $table->enum('status', ['pendiente', 'resuelta', 'desestimada'])->default('pendiente');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_incidents');
    }
};
