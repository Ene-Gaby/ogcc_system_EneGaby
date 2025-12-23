<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDependenciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dependencies', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nombre de la dependencia
            $table->string('phone')->nullable(); // Teléfonos
            $table->string('responsible'); // Responsable
            $table->string('organizational_structure'); // Estructura Organizativa
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Relación con User
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
        Schema::dropIfExists('dependencies');
    }
}
