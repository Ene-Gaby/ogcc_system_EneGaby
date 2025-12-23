<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuditLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->morphs('auditable'); // Permite auditar cualquier modelo
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Quién realizó la acción
            $table->string('action'); // Qué acción (created, updated, deleted, participated, not_participated)
            $table->json('old_values')->nullable(); // Valores anteriores (RN-12)
            $table->json('new_values')->nullable(); // Valores nuevos (RN-12)
            $table->timestamp('action_time')->useCurrent(); // Cuándo ocurrió (RN-12)
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
        Schema::dropIfExists('audit_logs');
    }
}
