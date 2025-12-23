<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_id',
        'acquisition_process_rubro_id',
        'quantity',
        'unit_price_at_request_time',
        'iva_exempt_at_request_time',
        'iva_amount_calculated',
        'subtotal_calculated',
        'total_calculated',
    ];

    // Relación: Un Detalle pertenece a una Solicitud
    public function request()
    {
        return $this->belongsTo(Request::class);
    }

    // Relación: Un Detalle pertenece a un Rubro específico de un Proceso
    public function acquisitionProcessRubro()
    {
        return $this->belongsTo(AcquisitionProcessRubro::class);
    }

    // Método para calcular subtotal
    public function calculateSubtotal()
    {
        return $this->quantity * $this->unit_price_at_request_time;
    }

    // Método para calcular IVA
    public function calculateIva()
    {
        if ($this->iva_exempt_at_request_time) {
            return 0;
        }
        return $this->calculateSubtotal() * (config('app.iva_rate', 0.16)); // Usamos una configuración global para el IVA
    }

    // Método para calcular total
    public function calculateTotal()
    {
        return $this->calculateSubtotal() + $this->calculateIva();
    }

    // Método para calcular y actualizar todos los campos calculados
    public function recalculate()
    {
        $this->subtotal_calculated = $this->calculateSubtotal();
        $this->iva_amount_calculated = $this->calculateIva();
        $this->total_calculated = $this->calculateTotal();
    }
}