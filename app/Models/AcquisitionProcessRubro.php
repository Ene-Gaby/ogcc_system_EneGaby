<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcquisitionProcessRubro extends Model
{
    use HasFactory;

    protected $table = 'acquisition_process_rubro';

    protected $fillable = [
        'acquisition_process_id',
        'rubro_id',
        'price_override',
    ];

    // Relación: Un registro de pivot pertenece a un Proceso de Contratación
    public function acquisitionProcess()
    {
        return $this->belongsTo(AcquisitionProcess::class);
    }

    // Relación: Un registro de pivot pertenece a un Rubro
    public function rubro()
    {
        return $this->belongsTo(Rubro::class);
    }

    // Relación: Un registro de pivot tiene muchos Detalles de Solicitud
    public function requestDetails()
    {
        return $this->hasMany(RequestDetail::class);
    }
}