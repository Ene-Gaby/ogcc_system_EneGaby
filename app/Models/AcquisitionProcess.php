<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcquisitionProcess extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'fiscal_year',
        'start_date',
        'end_date',
        'status',
    ];

    protected $casts = [
    'start_date' => 'datetime',
    'end_date' => 'datetime',
];

    // Relación: Un Proceso de Contratación tiene muchas Solicitudes
    public function requests()
    {
        return $this->hasMany(Request::class);
    }

    // Relación: Un Proceso de Contratación tiene muchos Rubros (a través de la tabla pivot)
    /**
 * Relación: Un Proceso tiene muchos Rubros (directamente por acquisition_process_id)
 */
public function rubros()
{
    return $this->hasMany(Rubro::class, 'acquisition_process_id');
}

/**
 * Relación: A través de la tabla pivot (para compatibilidad)
 */
public function rubrosPivot()
{
    return $this->belongsToMany(Rubro::class, 'acquisition_process_rubro', 'acquisition_process_id', 'rubro_id')
                ->withTimestamps();
}

    //El cierre debe ser automático y basado en la fecha actual del servidor, no manual.

    public function isClosed()
{
    if ($this->status === 'closed') {
        return true;
    }

    if ($this->end_date) {
        $endDate = \Carbon\Carbon::parse($this->end_date)->startOfDay();
        $today = \Carbon\Carbon::today()->startOfDay();

        // Está cerrado solo si end_date es ANTERIOR a hoy (no incluyendo hoy)
        return $endDate->lt($today);
    }

    return false;
}
}