@extends('layouts.app')

@section('title', 'Editar Solicitud')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h3>Editar Solicitud: {{ $request->acquisitionProcess->name }}</h3>
            <p><strong>Año Fiscal:</strong> {{ $request->acquisitionProcess->fiscal_year }} | 
               <strong>Estado:</strong> {{ ucfirst($request->status) }}</p>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('requests.update.details', $request->id) }}">
                @csrf
                @method('PUT')
                
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th>N°</th>
                                <th>Rubro</th>
                                <th>Presentación</th>
                                <th>Precio Unitario</th>
                                <th>Cantidad</th>
                                <th>IVA %</th>
                                <th>IVA</th>
                                <th>Total</th>
                                <th>Código ONAPRE</th>
                                <th>Código ONU</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($request->requestDetails as $detail)
                            @php
                                $rubro = $detail->acquisitionProcessRubro->rubro;
                                $subtotal = $detail->quantity * $detail->unit_price_at_request_time;
                                $iva = $subtotal * 0.16;
                                $total = $subtotal + $iva;
                            @endphp
                            <tr>
                                <td class="text-center">{{ $loop->index + 1 }}</td>
                                <td>{{ $rubro->description ?? 'N/A' }}</td>
                                <td class="text-center">{{ $rubro->presentation ?? '-' }}</td>
                                <td class="text-right">{{ number_format($detail->unit_price_at_request_time, 2, ',', '.') }}</td>
                                <td>
                                    <input type="number" 
                                           name="quantities[{{ $detail->id }}]" 
                                           class="form-control quantity-input" 
                                           value="{{ $detail->quantity }}"
                                           min="0"
                                           step="1"
                                           data-unit-price="{{ $detail->unit_price_at_request_time }}"
                                           data-detail-id="{{ $detail->id }}"
                                           style="width: 100px;">
                                </td>
                                <td class="text-center">16,00%</td>
                                <td class="iva-display-{{ $detail->id }} text-right">
                                    {{ number_format($iva, 2, ',', '.') }}
                                </td>
                                <td class="total-display-{{ $detail->id }} text-right">
                                    {{ number_format($total, 2, ',', '.') }}
                                </td>
                                <td class="text-center">{{ $rubro->onapre_code ?? '-' }}</td>
                                <td class="text-center">{{ $rubro->onu_code ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-active font-weight-bold">
                                <td colspan="7" class="text-right">Total General:</td>
                                <td colspan="3" id="total-general" class="text-right">
                                    {{ number_format($request->total_amount, 2, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-6">
                        <a href="{{ route('requests.preview', $request->id) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver a Vista Previa
                        </a>
                    </div>
                    <div class="col-md-6 text-right">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Actualizar Cantidades
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const quantityInputs = document.querySelectorAll('.quantity-input');
    
    function updateTotals() {
        let totalGeneral = 0;
        
        quantityInputs.forEach(input => {
            const quantity = parseFloat(input.value) || 0;
            const unitPrice = parseFloat(input.dataset.unitPrice);
            const detailId = input.dataset.detailId;
            
            const subtotal = quantity * unitPrice;
            const iva = subtotal * 0.16;
            const total = subtotal + iva;
            
            document.querySelector(`.iva-display-${detailId}`).textContent = formatNumber(iva);
            document.querySelector(`.total-display-${detailId}`).textContent = formatNumber(total);
            
            totalGeneral += total;
        });
        
        document.getElementById('total-general').textContent = formatNumber(totalGeneral);
    }
    
    function formatNumber(value) {
        return new Intl.NumberFormat('es-VE', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(value);
    }
    
    quantityInputs.forEach(input => {
        input.addEventListener('input', updateTotals);
    });
});
</script>
@endsection