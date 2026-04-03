<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AcquisitionProcess;
use Carbon\Carbon;

class CloseExpiredProcesses extends Command
{
    protected $signature = 'processes:close-expired';
    protected $description = 'Cierra automáticamente los procesos cuya fecha de cierre ya pasó (al final del día)';

    public function handle()
    {
        // Fecha actual al FINAL del día (23:59:59)
        $todayEndOfDay = Carbon::today()->endOfDay();

        // Contar cuántos procesos se cerrarán
        $countToClose = AcquisitionProcess::where('status', 'open')
            ->whereNotNull('end_date')
            ->where('end_date', '<', $todayEndOfDay) // <-- clave: '<' (anterior al final del día)
            ->count();

        // Cerrarlos
        $updated = AcquisitionProcess::where('status', 'open')
            ->whereNotNull('end_date')
            ->where('end_date', '<', $todayEndOfDay)
            ->update(['status' => 'closed']);

        // Registrar en bitácora (opcional pero recomendado)
        if ($updated > 0) {
            \App\Models\AuditLog::create([
                'user_id' => null,
                'action' => 'auto_close_processes',
                'table_name' => 'acquisition_processes',
                'record_id' => null,
                'details' => json_encode([
                    'closed_count' => $updated,
                    'trigger_time' => now(),
                    'condition' => 'end_date < today_end_of_day'
                ]),
                'action_time' => now(),
            ]);
        }

        $this->info("✅ Searon {$updated} procesos expirados (de un total de {$countToClose} candidatos).");
    }
}