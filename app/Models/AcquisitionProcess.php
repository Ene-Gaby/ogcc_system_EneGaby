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
    public function rubros()
    {
        return $this->belongsToMany(Rubro::class, 'acquisition_process_rubro');
    }
}