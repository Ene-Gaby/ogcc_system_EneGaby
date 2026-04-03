<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Listado de No Participantes</title>
    <style>
        body { font-family: Arial, sans-serif; }
        h1 { text-align: center; color: #2c3e50; }
        .info { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #e74c3c; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        .footer { margin-top: 30px; text-align: center; font-size: 12px; }
        .no-data { text-align: center; color: #7f8c8d; font-style: italic; }
    </style>
</head>
<body>
    <h1>Listado de Dependencias No Participantes</h1>
    
    <div class="info">
        <p><strong>Proceso:</strong> {{ $process->name }}</p>
        <p><strong>Año Fiscal:</strong> {{ $process->fiscal_year }}</p>
        <p><strong>Total No Participantes:</strong> {{ $noParticipantes->count() }}</p>
    </div>

    @if($noParticipantes->count() > 0)
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
                @foreach($noParticipantes as $index => $req)
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
    @else
        <p class="no-data">No hay dependencias registradas como no participantes para este proceso.</p>
    @endif

    <div class="footer">
        <p>Generado el: {{ now()->format('d/m/Y H:i:s') }}</p>
        <p>Universidad de Los Andes - Oficina General de Contrataciones y Compras</p>
    </div>
</body>
</html>