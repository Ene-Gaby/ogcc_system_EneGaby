<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRubrosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rubros', function (Blueprint $table) {
            $table->id();
            $table->string('description'); // Descripción del rubro
            $table->string('presentation'); // Presentación (Unidad, Caja, etc.)
            $table->decimal('unit_price', 10, 2); // Precio unitario
            $table->boolean('iva_exempt')->default(false); // Exento de IVA
            $table->string('onapre_code')->unique(); // Código ONAPRE (RN-01, RN-02)
            $table->string('onu_code')->unique(); // Código ONU (RN-01, RN-02)
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
        Schema::dropIfExists('rubros');
    }
}
