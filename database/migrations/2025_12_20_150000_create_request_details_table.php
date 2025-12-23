<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained()->onDelete('cascade'); // Relación con Request
            $table->foreignId('acquisition_process_rubro_id')->constrained('acquisition_process_rubro')->onDelete('cascade'); // Relación con la pivot
            $table->integer('quantity');
            $table->decimal('unit_price_at_request_time', 10, 2); // Precio del rubro en el momento de la solicitud
            $table->boolean('iva_exempt_at_request_time'); // Exento en el momento de la solicitud
            $table->decimal('iva_amount_calculated', 10, 2); // Calculado al guardar/actualizar
            $table->decimal('subtotal_calculated', 10, 2); // Calculado: quantity * unit_price
            $table->decimal('total_calculated', 10, 2); // Calculado: subtotal + iva
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
        Schema::dropIfExists('request_details');
    }
}
