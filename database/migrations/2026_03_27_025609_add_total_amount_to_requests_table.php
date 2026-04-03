<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTotalAmountToRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::table('requests', function (Blueprint $table) {
        $table->decimal('total_amount', 12, 2)->after('official_letter_number')->default(0.00);
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
        $table->dropColumn('total_amount');
    });
}
}
