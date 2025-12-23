<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprobante de Participación/No Participación</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        .content { margin: 20px 0; }
        .footer { margin-top: 20px; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Universidad de Los Andes</h2>
        <h3>Oficina General de Contrataciones y Compras (OGCC)</h3>
        <h4>Unidad de Fase Previa</h4>
    </div>
    <div class="content">
        <p><strong>Proceso de Contratación:</strong> {{ $request->acquisitionProcess->name }}</p>
        <p><strong>Dependencia:</strong> {{ $request->dependency->name }}</p>
        <p><strong>Responsable:</strong> {{ $request->dependency->responsible }}</p>
        <p><strong>Fecha:</strong> {{ now()->format('d/m/Y') }}</p>
        <p><strong>Número de Oficio:</strong> {{ $request->official_letter_number }}</p>
        <p><strong>Decisión:</strong> {{ $request->participates ? 'PARTICIPA' : 'NO PARTICIPA' }}</p>
        <!-- Puedes agregar más detalles según sea necesario -->
    </div>
    <div class="footer">
        <p>______________________</p>
        <p>Firma Autorizada</p>
    </div>
</body>
</html>