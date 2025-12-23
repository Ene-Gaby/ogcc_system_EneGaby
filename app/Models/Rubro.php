<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rubro extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'presentation',
        'unit_price',
        'iva_exempt',
        'onapre_code',
        'onu_code',
    ];

    // Relación: Un Rubro puede estar en muchos Procesos de Contratación
    public function acquisitionProcesses()
    {
        return $this->belongsToMany(AcquisitionProcess::class, 'acquisition_process_rubro');
    }
}