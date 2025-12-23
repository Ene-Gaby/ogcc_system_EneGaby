<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    use HasFactory;

    protected $fillable = [
        'dependency_id',
        'acquisition_process_id',
        'participates',
        'official_letter_number',
        'justification',
        'total_amount',
        'status',
    ];

    // Relación: Una Solicitud pertenece a una Dependencia
    public function dependency()
    {
        return $this->belongsTo(Dependency::class);
    }

    // Relación: Una Solicitud pertenece a un Proceso de Contratación
    public function acquisitionProcess()
    {
        return $this->belongsTo(AcquisitionProcess::class);
    }

    // Relación: Una Solicitud tiene muchos Detalles (RequestDetails)
    public function requestDetails()
    {
        return $this->hasMany(RequestDetail::class);
    }

    // Relación: Una Solicitud puede ser auditada
    public function auditLogs()
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }

    // Método para recalcular el total de la solicitud
    public function recalculateTotal()
    {
        $this->total_amount = $this->requestDetails->sum('total_calculated');
        $this->save();
    }
}