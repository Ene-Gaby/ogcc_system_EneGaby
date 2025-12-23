@extends('layouts.app')

@section('title', 'Solicitar Rubros - ' . $process->name)

@section('content')
    <div class="container">
        <h1 class="mb-4">Solicitar Rubros para: {{ $process->name }}</h1>
        <h3>{{ $process->description }}</h3>
        <p><strong>Año Fiscal:</strong> {{ $process->fiscal_year }}</p>
        <form action="{{ route('requests.store.for.process', $process->id) }}" method="POST">
            @csrf
            <div class="row">
                @foreach($rubros as $rubro)
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title">{{ $rubro->description }}</h6>
                                <p class="card-text">{{ $rubro->presentation }}</p>
                                <p class="card-text"><strong>Precio Unitario:</strong> <span id="price_{{ $rubro->id }}">{{ number_format($rubro->unit_price, 2, ',', '.') }}</span></p>
                                <label for="quantity_{{ $rubro->id }}">Cantidad:</label>
                                <input type="number"
                                       name="rubro_quantities[{{ $rubro->id }}]"
                                       id="quantity_{{ $rubro->id }}"
                                       class="form-control quantity-input"
                                       min="0"
                                       value="{{ old('rubro_quantities.' . $rubro->id, 0) }}"
                                       data-price="{{ $rubro->unit_price }}"
                                       data-exempt="{{ $rubro->iva_exempt ? 'true' : 'false' }}"
                                       data-id="{{ $rubro->id }}">
                                <small class="form-text text-muted">Subtotal: <span id="subtotal_{{ $rubro->id }}">0,00</span></small>
                                <small class="form-text text-muted">IVA: <span id="iva_{{ $rubro->id }}">0,00</span></small>
                                <small class="form-text text-muted">Total: <span id="total_{{ $rubro->id }}">0,00</span></small>
                                <small class="form-text text-muted">Código ONAPRE: {{ $rubro->onapre_code }}</small>
                                <small class="form-text text-muted">Código ONU: {{ $rubro->onu_code }}</small>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <h4>Total General de la Solicitud: <span id="total-general">0,00</span></h4>
            <button type="submit" class="btn btn-success">Guardar y Continuar</button>
            <a href="{{ route('requests.open.processes') }}" class="btn btn-secondary">Cancelar</a>
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