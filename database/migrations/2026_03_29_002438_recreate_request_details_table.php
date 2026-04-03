<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class RecreateRequestDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Primero, eliminar las llaves foráneas existentes
        Schema::table('request_details', function (Blueprint $table) {
            // Intentar eliminar las llaves foráneas si existen
            try {
                $table->dropForeign(['request_id']);
            } catch (\Exception $e) {
                // La llave foránea no existe, continuar
            }
            
            try {
                $table->dropForeign(['acquisition_process_rubro_id']);
            } catch (\Exception $e) {
                // La llave foránea no existe, continuar
            }
        });
        
        // 2. Eliminar la tabla existente
        Schema::dropIfExists('request_details');
        
        // 3. Crear la tabla con la estructura correcta
        Schema::create('request_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('request_id');
            $table->unsignedBigInteger('acquisition_process_rubro_id');
            $table->integer('quantity')->default(0);
            $table->decimal('unit_price_at_request_time', 15, 2);
            $table->boolean('iva_exempt_at_request_time')->default(false);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('iva_amount', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->timestamps();
            
            // Llaves foráneas
            $table->foreign('request_id')
                  ->references('id')
                  ->on('requests')
                  ->onDelete('cascade');
                  
            $table->foreign('acquisition_process_rubro_id')
                  ->references('id')
                  ->on('acquisition_process_rubro')
                  ->onDelete('cascade');
                  
            // Índices para mejorar rendimiento
            $table->index(['request_id']);
            $table->index(['acquisition_process_rubro_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // No implementamos down para evitar pérdida de datos accidental
        Schema::dropIfExists('request_details');
    }
}