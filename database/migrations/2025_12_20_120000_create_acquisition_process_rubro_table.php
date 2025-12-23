<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAcquisitionProcessRubroTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('acquisition_process_rubro', function (Blueprint $table) {
            $table->id();
            $table->foreignId('acquisition_process_id')->constrained()->onDelete('cascade');
            $table->foreignId('rubro_id')->constrained()->onDelete('cascade');
            $table->decimal('price_override', 10, 2)->nullable(); // Precio específico para este proceso (opcional)
            $table->timestamps();

            // Clave única para evitar duplicados proceso-rubro
            $table->unique(['acquisition_process_id', 'rubro_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('acquisition_process_rubro');
    }
}
