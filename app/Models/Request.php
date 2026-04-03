<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    use HasFactory;

    protected $table = 'requests';

    protected $fillable = [
    'acquisition_process_id',
    'dependency_id',
    'status',
    'participates',
    'official_letter_number',
    'total_amount',
    'pdf_presupuesto_path',
    'pdf_comprobante_path',
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

public function details()
{
    return $this->hasMany(RequestDetail::class);
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
    $total = $this->requestDetails->sum(function($detail) {
        $subtotal = $detail->quantity * $detail->unit_price_at_request_time;
        $iva = $subtotal * 0.16;
        return $subtotal + $iva;
    });
    
    $this->total_amount = $total;
    return $this;
}
}