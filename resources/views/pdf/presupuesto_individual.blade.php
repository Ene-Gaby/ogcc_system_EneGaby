<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Presupuesto Individual</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .total { text-align: right; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Universidad de Los Andes</h2>
        <h3>Oficina General de Contrataciones y Compras (OGCC)</h3>
        <h4>Unidad de Fase Previa</h4>
        <h5>Presupuesto Individual - {{ $request->acquisitionProcess->name }}</h5>
    </div>
    <div class="content">
        <p><strong>Dependencia:</strong> {{ $request->dependency->name }}</p>
        <p><strong>Responsable:</strong> {{ $request->dependency->responsible }}</p>
        <p><strong>Fecha:</strong> {{ now()->format('d/m/Y') }}</p>
        <table>
            <thead>
                <tr>
                    <th>Descripción</th>
                    <th>Presentación</th>
                    <th>Cantidad</th>
                    <th>Precio Unit.</th>
                    <th>Subtotal</th>
                    <th>IVA</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($request->requestDetails as $detail)
                <tr>
                    <td>{{ $detail->acquisitionProcessRubro->rubro->description }}</td>
                    <td>{{ $detail->acquisitionProcessRubro->rubro->presentation }}</td>
                    <td>{{ $detail->quantity }}</td>
                    <td>{{ number_format($detail->unit_price_at_request_time, 2, ',', '.') }}</td>
                    <td>{{ number_format($detail->subtotal_calculated, 2, ',', '.') }}</td>
                    <td>{{ number_format($detail->iva_amount_calculated, 2, ',', '.') }}</td>
                    <td>{{ number_format($detail->total_calculated, 2, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <p class="total">TOTAL SOLICITADO: {{ number_format($request->total_amount, 2, ',', '.') }}</p>
    </div>
    <div class="footer">
        <p>______________________</p>
        <p>Firma Autorizada</p>
    </div>
</body>
</html>