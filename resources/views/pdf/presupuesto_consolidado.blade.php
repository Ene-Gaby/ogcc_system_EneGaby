<!DOCTYPE html>
<html>
<head>
    <title>Presupuesto Consolidado - {{ $process->name }}</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Presupuesto Consolidado</h1>
    <p><strong>Proceso:</strong> {{ $process->name }}</p>
    <p><strong>Año Fiscal:</strong> {{ $process->fiscal_year }}</p>
    <p><strong>Estado:</strong> {{ ucfirst($process->status) }}</p>
    <table>
        <thead>
            <tr>
                <th>Dependencia</th>
                <th>Rubro</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
                <th>Subtotal</th>
                <th>IVA</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalConsolidado = 0;
            @endphp
            @foreach($process->requests as $request)
                @foreach($request->details as $detail)
                    <tr>
                        <td>{{ $request->dependency->name }}</td>
                        <td>{{ $detail->rubro->description }}</td>
                        <td>{{ $detail->quantity }}</td>
                        <td>{{ number_format($detail->rubro->unit_price, 2, ',', '.') }}</td>
                        <td>{{ number_format($detail->subtotal, 2, ',', '.') }}</td>
                        <td>{{ number_format($detail->iva, 2, ',', '.') }}</td>
                        <td>{{ number_format($detail->total, 2, ',', '.') }}</td>
                    </tr>
                    @php
                        $totalConsolidado += $detail->total;
                    @endphp
                @endforeach
            @endforeach
        </tbody>
    </table>
    <h3>Total Consolidado: {{ number_format($totalConsolidado, 2, ',', '.') }}</h3>
</body>
</html>