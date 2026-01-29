<!DOCTYPE html>
<html>
<head>
    <title>Bitácoras de Auditoría</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Bitácoras de Auditoría</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Usuario</th>
                <th>Acción</th>
                <th>Tabla</th>
                <th>Registro ID</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $log)
                <tr>
                    <td>{{ $log->id }}</td>
                    <td>{{ $log->user->name ?? 'Sistema' }}</td>
                    <td>{{ $log->action }}</td>
                    <td>{{ $log->table_name }}</td>
                    <td>{{ $log->record_id }}</td>
                    <td>{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>