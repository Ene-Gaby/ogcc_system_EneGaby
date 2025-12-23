<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dependency_id')->constrained()->onDelete('cascade'); // Relación con Dependency
            $table->foreignId('acquisition_process_id')->constrained()->onDelete('cascade'); // Relación con AcquisitionProcess
            $table->boolean('participates')->nullable(); // NULL = pendiente de decisión
            $table->string('official_letter_number')->nullable(); // RN-06
            $table->text('justification')->nullable(); // Justificación si no participa
            $table->decimal('total_amount', 10, 2)->default(0.00); // Total calculado
            $table->enum('status', ['draft', 'pending_decision', 'submitted', 'not_participating'])->default('draft'); // Estados refinados
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('requests');
    }
}
