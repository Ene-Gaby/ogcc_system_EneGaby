<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAcquisitionProcessIdToRubrosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    Schema::table('rubros', function (Blueprint $table) {
        $table->foreignId('acquisition_process_id')->nullable()->constrained()->onDelete('set null');
    });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    Schema::table('rubros', function (Blueprint $table) {
        $table->dropForeign(['acquisition_process_id']);
        $table->dropColumn('acquisition_process_id');
    });
    }
}
