@extends('layouts.app')

@section('title', 'Solicitar Rubros - ' . $process->name)

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h3>Solicitar Rubros para: {{ $process->name }}</h3>
            <p><strong>Año Fiscal:</strong> {{ $process->fiscal_year }} | <strong>Estado:</strong> {{ ucfirst($process->status) }}</p>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('requests.store.details', $process->id) }}">
                @csrf
                <input type="hidden" name="acquisition_process_id" value="{{ $process->id }}">
                <input type="hidden" name="dependency_id" value="{{ Auth::user()->dependency->id }}">

                <h4>Rubros Disponibles</h4>
                <div class="table-responsive">
                    <table class="table table-striped">
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
<tbody id="rubro-list">
@foreach($process->rubros as $rubro)
    <tr data-rubro-id="{{ $rubro->id }}">
    <td>{{ $loop->index + 1 }}</td>
    <td>{{ $rubro->description }}</td>
    <td>{{ $rubro->presentation }}</td>
    <td class="text-right">{{ number_format($rubro->unit_price, 2, ',', '.') }}</td>
    <td>
        <input type="number"
               name="details[{{ $rubro->id }}][quantity]"
               class="quantity-input form-control text-right"
               value="{{ old("details.$rubro->id.quantity", 0) }}"
               min="0" step="1">
    </td>
    <td class="text-center">16,00%</td>
    <td class="iva text-right">0,00</td>
    <td class="total text-right">0,00</td>
    <td class="text-center">{{ $rubro->onapre_code ?? '' }}</td>
    <td class="text-center">{{ $rubro->onu_code ?? '' }}</td>
</tr>
@endforeach
</tbody>
                    </table>
                </div>

                <div class="row mt-4">
                    <div class="mt-3">
                        <strong>Total General de la Solicitud:</strong>
                        <span id="total-general" class="ml-2 font-weight-bold">0,00</span>
                    </div>
                    <div class="col-md-6 text-right">
                        <button type="submit" class="btn btn-primary" id="submit-btn">
                            <i class="fas fa-save"></i> Guardar y Continuar
                        </button>

                        <script>
                        document.querySelector('form').addEventListener('submit', function() {
                            document.getElementById('submit-btn').disabled = true;
                            document.getElementById('submit-btn').innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
                        });
                        </script>
                        <a href="{{ route('requests.open.processes') }}" class="btn btn-secondary btn-lg">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Script para calcular totales dinámicamente -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const rows = document.querySelectorAll('#rubro-list tr');
    const totalGeneralElement = document.getElementById('total-general');

    function updateTotals() {
    let totalGeneral = 0;
    document.querySelectorAll('#rubro-list tr').forEach(row => {
        const quantityInput = row.querySelector('.quantity-input');
        const quantity = parseFloat(quantityInput.value) || 0;
        const unitPrice = parseFloat(row.cells[3].textContent.replace(/[^0-9.,]/g, '').replace(',', '.')) || 0;
        const ivaRate = 0.16;

        const subtotal = quantity * unitPrice;
        const iva = (row.querySelector('input[type="checkbox"][name="details[' + row.dataset.rubroId + '][iva_exempt]"]')?.checked) ? 0 : (subtotal * ivaRate);
        const total = subtotal + iva;

        // Actualizar celdas
        row.querySelector('.iva').textContent = totalFormatter(iva);
        row.querySelector('.total').textContent = totalFormatter(total);

        totalGeneral += total;
    });

    document.getElementById('total-general').textContent = totalFormatter(totalGeneral);
}

function totalFormatter(value) {
    return new Intl.NumberFormat('es-VE', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(value);
}

// Escuchar cambios
document.addEventListener('input', function(e) {
    if (e.target.classList.contains('quantity-input')) {
        updateTotals();
    }
});

// Inicializar al cargar
document.addEventListener('DOMContentLoaded', updateTotals);
        }
    });

    // Inicializar los totales
    updateTotals();
});
</script>
@endsection