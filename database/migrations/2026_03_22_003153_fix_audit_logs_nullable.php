<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixAuditLogsNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::table('audit_logs', function (Blueprint $table) {
        $table->string('auditable_type')->nullable()->change();
        $table->unsignedBigInteger('auditable_id')->nullable()->change();
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
{
    Schema::table('audit_logs', function (Blueprint $table) {
        $table->string('auditable_type')->nullable(false)->change();
        $table->unsignedBigInteger('auditable_id')->nullable(false)->change();
    });
}
}
