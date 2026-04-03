<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Listado de Participantes</title>
    <style>
        body { font-family: Arial, sans-serif; }
        h1 { text-align: center; color: #2c3e50; }
        .info { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #3498db; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        .footer { margin-top: 30px; text-align: center; font-size: 12px; }
    </style>
</head>
<body>
    <h1>Listado de Dependencias Participantes</h1>
    
    <div class="info">
        <p><strong>Proceso:</strong> {{ $process->name }}</p>
        <p><strong>Año Fiscal:</strong> {{ $process->fiscal_year }}</p>
        <p><strong>Total Participantes:</strong> {{ $participantes->count() }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>N°</th>
                <th>Dependencia</th>
                <th>Responsable</th>
                <th>N° Oficio</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody>
            @foreach($participantes as $index => $req)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $req->dependency->name }}</td>
                    <td>{{ $req->dependency->responsible }}</td>
                    <td>{{ $req->official_letter_number }}</td>
                    <td>{{ $req->created_at->format('d/m/Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Generado el: {{ now()->format('d/m/Y H:i:s') }}</p>
        <p>Universidad de Los Andes - Oficina General de Contrataciones y Compras</p>
    </div>
</body>
</html>