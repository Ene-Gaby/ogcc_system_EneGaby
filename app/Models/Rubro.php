<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rubro extends Model
{
    protected $fillable = [
        'description',
        'presentation',
        'unit_price',
        'iva_exempt',
        'onapre_code',
        'onu_code',
        'acquisition_process_id',
    ];

    public function acquisitionProcess()
    {
        return $this->belongsTo(AcquisitionProcess::class, 'acquisition_process_id');
    }

    public function acquisitionProcessRubro()
{
    return $this->hasOne(AcquisitionProcessRubro::class, 'rubro_id')
                ->where('acquisition_process_id', request()->route('process'));
}

    // Validación: Un rubro no puede estar en dos procesos
    public static function boot()
    {
        parent::boot();

        static::saving(function ($rubro) {
            if ($rubro->isDirty('acquisition_process_id') && $rubro->acquisition_process_id) {
                // Verificar si este rubro ya está asociado a otro proceso
                $existing = self::where('id', '!=', $rubro->id)
                    ->where('acquisition_process_id', $rubro->acquisition_process_id)
                    ->exists();

                if ($existing) {
                    throw new \Exception('Este rubro ya está asociado a otro proceso de contratación.');
                }
            }
        });
    }
}