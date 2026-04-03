<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestDetail extends Model
{
    protected $table = 'request_details';
    
    protected $fillable = [
        'request_id',
        'acquisition_process_rubro_id',
        'quantity',
        'unit_price_at_request_time',
        'iva_exempt_at_request_time',
        'subtotal',
        'iva_amount',
        'total',
    ];
    
    protected $casts = [
        'quantity' => 'integer',
        'unit_price_at_request_time' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'iva_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'iva_exempt_at_request_time' => 'boolean',
    ];
    
    public function request()
    {
        return $this->belongsTo(Request::class);
    }
    
    public function acquisitionProcessRubro()
    {
        return $this->belongsTo(AcquisitionProcessRubro::class, 'acquisition_process_rubro_id');
    }
    
    /**
     * Recalcula los valores del detalle basado en cantidad y precio
     * 
     * @return $this
     */
    public function recalculate()
    {
        $subtotal = $this->quantity * $this->unit_price_at_request_time;
        $iva = $this->iva_exempt_at_request_time ? 0 : ($subtotal * 0.16);
        $total = $subtotal + $iva;
        
        $this->subtotal = $subtotal;
        $this->iva_amount = $iva;
        $this->total = $total;
        
        return $this;
    }
}