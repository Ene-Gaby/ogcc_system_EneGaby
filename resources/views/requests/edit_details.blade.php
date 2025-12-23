@extends('layouts.app')

@section('title', 'Editar Solicitud - ' . $request->acquisitionProcess->name)

@section('content')
    <div class="container">
        <h1 class="mb-4">Editar Solicitud para: {{ $request->acquisitionProcess->name }}</h1>
        <h3>Estado Actual: <span class="badge badge-{{ $request->status === 'draft' ? 'warning' : 'info' }}">{{ ucfirst($request->status) }}</span></h3>
        <form action="{{ route('requests.update.details', $request->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row">
                @foreach($request->requestDetails as $detail)
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title">{{ $detail->acquisitionProcessRubro->rubro->description }}</h6>
                                <p class="card-text">{{ $detail->acquisitionProcessRubro->rubro->presentation }}</p>
                                <p class="card-text"><strong>Precio Unitario:</strong> <span id="price_{{ $detail->id }}">{{ number_format($detail->unit_price_at_request_time, 2, ',', '.') }}</span></p>
                                <label for="quantity_{{ $detail->id }}">Cantidad:</label>
                                <input type="number"
                                       name="quantities[{{ $detail->id }}]"
                                       id="quantity_{{ $detail->id }}"
                                       class="form-control quantity-input"
                                       min="0"
                                       value="{{ old('quantities.' . $detail->id, $detail->quantity) }}"
                                       data-price="{{ $detail->unit_price_at_request_time }}"
                                       data-exempt="{{ $detail->iva_exempt_at_request_time ? 'true' : 'false' }}"
                                       data-id="{{ $detail->id }}">
                                <small class="form-text text-muted">Subtotal: <span id="subtotal_{{ $detail->id }}">{{ number_format($detail->subtotal_calculated, 2, ',', '.') }}</span></small>
                                <small class="form-text text-muted">IVA: <span id="iva_{{ $detail->id }}">{{ number_format($detail->iva_amount_calculated, 2, ',', '.') }}</span></small>
                                <small class="form-text text-muted">Total: <span id="total_{{ $detail->id }}">{{ number_format($detail->total_calculated, 2, ',', '.') }}</span></small>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <h4>Total General de la Solicitud: <span id="total-general">{{ number_format($request->total_amount, 2, ',', '.') }}</span></h4>
            <button type="submit" class="btn btn-primary">Actualizar Cantidades</button>
            <a href="{{ route('requests.my.index') }}" class="btn btn-secondary">Volver</a>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ivaRate = {{ config('app.iva_rate', 0.16) }}; // Obtener la tasa de IVA de la configuración de Laravel
            const quantityInputs = document.querySelectorAll('.quantity-input');

            function updateRowCalculations(inputElement) {
                const id = inputElement.dataset.id;
                const price = parseFloat(inputElement.dataset.price);
                const isExempt = inputElement.dataset.exempt === 'true';
                const quantity = parseInt(inputElement.value) || 0;

                const subtotal = quantity * price;
                const iva = isExempt ? 0 : subtotal * ivaRate;
                const total = subtotal + iva;

                document.getElementById(`subtotal_${id}`).textContent = subtotal.toLocaleString('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                document.getElementById(`iva_${id}`).textContent = iva.toLocaleString('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                document.getElementById(`total_${id}`).textContent = total.toLocaleString('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }

            function updateTotalGeneral() {
                let totalGeneral = 0;
                document.querySelectorAll('.quantity-input').forEach(input => {
                    const id = input.dataset.id;
                    totalGeneral += parseFloat(document.getElementById(`total_${id}`).textContent.replace('.', '').replace(',', '.'));
                });
                document.getElementById('total-general').textContent = totalGeneral.toLocaleString('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }

            quantityInputs.forEach(input => {
                input.addEventListener('input', function() {
                    updateRowCalculations(this);
                    updateTotalGeneral();
                });
                // Calcular inmediatamente al cargar la página
                updateRowCalculations(input);
            });
            updateTotalGeneral(); // Calcular total general inicial
        });
    </script>
@endsection