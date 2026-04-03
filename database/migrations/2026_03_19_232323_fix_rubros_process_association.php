<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('rubros', function (Blueprint $table) {
            // 1. Añadir la columna para asociar el rubro a un proceso
            $table->foreignId('acquisition_process_id')
                ->nullable()
                ->constrained('acquisition_processes') // Referencia a la tabla 'acquisition_processes'
                ->onDelete('cascade'); // Si se borra el proceso, se borran sus rubros

            // 2. Asegurar que el nombre/descripción del rubro sea único (RN-08: "cada rubro" es una entidad única)
            $table->unique('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('rubros', function (Blueprint $table) {
            // Eliminar la restricción UNIQUE primero
            $table->dropUnique(['description']);

            // Luego eliminar la FK y la columna
            $table->dropForeign(['acquisition_process_id']);
            $table->dropColumn('acquisition_process_id');
        });
    }
};