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
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->string('loan_code')->unique()->index();
            $table->foreignId('worker_id')->constrained('workers')->cascadeOnDelete();
            $table->foreignId('equipment_id')->constrained('equipment')->cascadeOnDelete();
            $table->foreignId('loan_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('return_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('loan_date');
            $table->dateTime('expected_return_date');
            $table->dateTime('actual_return_date')->nullable();
            $table->text('purpose')->nullable();
            $table->text('loan_notes')->nullable();
            $table->text('return_notes')->nullable();
            $table->enum('equipment_return_condition', ['bueno', 'regular', 'danado'])->nullable();
            $table->enum('status', ['activo', 'devuelto', 'devuelto_con_incidencia', 'vencido', 'cancelado'])->default('activo')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
