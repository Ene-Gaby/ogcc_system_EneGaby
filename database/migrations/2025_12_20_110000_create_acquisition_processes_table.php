<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAcquisitionProcessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('acquisition_processes', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nombre del proceso (e.g., "Adquisición de Papelería")
            $table->text('description')->nullable(); // Descripción técnica
            $table->year('fiscal_year'); // Año Fiscal
            $table->date('start_date'); // Fecha de inicio del proceso
            $table->date('end_date'); // Fecha de cierre del proceso
            $table->enum('status', ['open', 'closed'])->default('open'); // Estado del proceso
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
        Schema::dropIfExists('acquisition_processes');
    }
}
