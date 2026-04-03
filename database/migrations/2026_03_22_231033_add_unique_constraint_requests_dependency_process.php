<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniqueConstraintRequestsDependencyProcess extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::table('requests', function (Blueprint $table) {
        $table->unique(['dependency_id', 'acquisition_process_id'], 'unique_dependency_process');
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
{
    Schema::table('requests', function (Blueprint $table) {
        $table->dropUnique('unique_dependency_process');
    });
}
}
