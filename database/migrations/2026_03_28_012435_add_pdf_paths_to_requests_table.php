<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPdfPathsToRequestsTable extends Migration
{
    public function up()
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->string('pdf_presupuesto_path')->nullable()->after('total_amount');
            $table->string('pdf_comprobante_path')->nullable()->after('pdf_presupuesto_path');
        });
    }

    public function down()
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->dropColumn(['pdf_presupuesto_path', 'pdf_comprobante_path']);
        });
    }
}