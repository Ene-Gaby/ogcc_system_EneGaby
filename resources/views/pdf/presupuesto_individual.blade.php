<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Presupuesto Base Individual</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            color: #2c3e50;
        }
        .header h3 {
            margin: 5px 0;
            font-size: 14px;
            color: #666;
        }
        .info-section {
            margin-bottom: 20px;
            padding: 10px;
            background: #f5f5f5;
            font-size: 10px;
        }
        .info-section table {
            width: 100%;
        }
        .info-section td {
            padding: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #4CAF50;
            color: white;
            font-size: 10px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .total-row {
            font-weight: bold;
            background-color: #f2f2f2;
        }
        .signature-section {
            margin-top: 50px;
            text-align: center;
        }
        .signature-line {
            border-top: 1px solid #333;
            width: 60%;
            margin: 0 auto;
            padding-top: 8px;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 8px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        /* Solo mostrar marca de agua si NO es final */
        @if(!isset($is_final) || !$is_final)
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 50px;
            color: rgba(0,0,0,0.05);
            z-index: 1000;
        }
        @endif
    </style>
</head>
<body>
    @if(!isset($is_final) || !$is_final)
        <div class="watermark">VISTA PRELIMINAR</div>
    @endif
    
    <div class="header">
        <h1>Presupuesto Base Individual</h1>
        <h3>Sistema OGCC - Universidad de Los Andes</h3>
    </div>
    
    <div class="info-section">
        <table>
            <tr>
                <td width="25%"><strong>Proceso:</strong></td>
                <td width="35%">{{ $request->acquisitionProcess->name }}</td>
                <td width="20%"><strong>Año Fiscal:</strong></td>
                <td width="20%">{{ $request->acquisitionProcess->fiscal_year }}</td>
            </tr>
            <tr>
                <td><strong>Dependencia:</strong></td>
                <td>{{ $request->dependency->name ?? 'N/A' }}</td>
                <td><strong>Fecha:</strong></td>
                <td>{{ $today ?? now()->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td><strong>N° Solicitud:</strong></td>
                <td>{{ str_pad($request->id, 8, '0', STR_PAD_LEFT) }}</td>
                <td><strong>Estado:</strong></td>
                <td>{{ ucfirst($request->status) }}</td>
            </tr>
        </table>
    </div>
    
    <h4>Detalle de Rubros Solicitados</h4>
    <table>
        <thead>
            <tr>
                <th width="5%">N°</th>
                <th width="20%">Rubro</th>
                <th width="8%">Presentación</th>
                <th width="8%">Cantidad</th>
                <th width="12%">Precio Unitario</th>
                <th width="8%">IVA %</th>
                <th width="10%">IVA</th>
                <th width="12%">Total</th>
                <th width="10%">Código ONAPRE</th>
                <th width="7%">Código ONU</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $counter = 1; 
                $totalGeneral = 0;
            @endphp
            @foreach($request->requestDetails as $detail)
                @php
                    $rubro = $detail->acquisitionProcessRubro->rubro;
                    $subtotal = $detail->quantity * $detail->unit_price_at_request_time;
                    $iva = $subtotal * 0.16;
                    $total = $subtotal + $iva;
                    $totalGeneral += $total;
                @endphp
                <tr>
                    <td class="text-center">{{ $counter++ }}</td>
                    <td>{{ $rubro->description ?? 'N/A' }}</td>
                    <td class="text-center">{{ $rubro->presentation ?? '-' }}</td>
                    <td class="text-right">{{ number_format($detail->quantity, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($detail->unit_price_at_request_time, 2, ',', '.') }}</td>
                    <td class="text-center">16,00%</td>
                    <td class="text-right">{{ number_format($iva, 2, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($total, 2, ',', '.') }}</td>
                    <td class="text-center">{{ $rubro->onapre_code ?? '-' }}</td>
                    <td class="text-center">{{ $rubro->onu_code ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="8" class="text-right"><strong>TOTAL GENERAL:</strong></td>
                <td colspan="2" class="text-right"><strong>{{ number_format($totalGeneral, 2, ',', '.') }} Bs.</strong></td>
            </tr>
        </tfoot>
    </table>
    
    <!-- SECCIÓN DE FIRMA Y SELLO -->
    <div class="signature-section">
        <div class="signature-line"></div>
        <p style="margin-top: 8px; font-size: 11px;">
            <strong>Firma del Representante y Sello de la Dependencia</strong><br>
            {{ $request->dependency->name ?? 'Dependencia' }}
        </p>
    </div>
    
    <div class="footer">
        <p>Documento generado por el Sistema OGCC - {{ $today ?? now()->format('d/m/Y H:i:s') }}</p>
        <p>Código de Verificación: {{ md5($request->id . $request->created_at) }}</p>
        @if(!isset($is_final) || !$is_final)
            <p style="color: red;">*** ESTE ES UN DOCUMENTO PRELIMINAR - NO TIENE VALIDEZ OFICIAL ***</p>
        @endif
    </div>
</body>
</html>