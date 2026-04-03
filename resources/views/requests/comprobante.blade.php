<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Comprobante de Participación</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            margin: 40px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #2c3e50;
        }
        .logo {
            font-size: 14px;
            margin-top: 10px;
            color: #666;
        }
        .content {
            margin: 30px 0;
        }
        .certificate {
            border: 2px solid #4CAF50;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
        }
        .certificate h2 {
            color: #4CAF50;
            margin: 0 0 10px 0;
        }
        .details {
            margin: 20px 0;
            padding: 15px;
            background: #f9f9f9;
        }
        .details table {
            width: 100%;
        }
        .details td {
            padding: 8px;
        }
        .signature {
            margin-top: 50px;
            text-align: center;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 60px;
            color: rgba(0,0,0,0.1);
            z-index: 1000;
        }
        .qr-code {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    @if(!isset($is_final) || !$is_final)
        <div class="watermark">VISTA PRELIMINAR</div>
    @endif
    
    <div class="header">
        <h1>Comprobante de Participación</h1>
        <div class="logo">Sistema OGCC - Universidad de Los Andes</div>
    </div>
    
    <div class="content">
        <div class="certificate">
            <h2>CERTIFICADO DE PARTICIPACIÓN</h2>
            <p>Se hace constar que la dependencia <strong>{{ $request->dependency->name ?? 'N/A' }}</strong></p>
            <p>ha registrado formalmente su participación en el proceso de compra:</p>
            <h3>{{ $request->acquisitionProcess->name }}</h3>
            <p><strong>Año Fiscal:</strong> {{ $request->acquisitionProcess->fiscal_year }}</p>
            <p><strong>Fecha de Registro:</strong> {{ $today ?? now()->format('d/m/Y H:i:s') }}</p>
        </div>
        
        <div class="details">
            <h4>Resumen de la Solicitud</h4>
            <table>
                <tr>
                    <td><strong>N° de Solicitud:</strong></td>
                    <td>{{ str_pad($request->id, 8, '0', STR_PAD_LEFT) }}</td>
                    <td><strong>Monto Total:</strong></td>
                    <td><strong>{{ number_format($request->total_amount, 2, ',', '.') }} Bs.</strong></td>
                </tr>
                <tr>
                    <td><strong>Rubros Solicitados:</strong></td>
                    <td colspan="3">{{ $request->requestDetails->count() }} rubro(s)</td>
                </tr>
                <tr>
                    <td><strong>Estado:</strong></td>
                    <td colspan="3">
                        @if($request->status == 'submitted')
                            <span style="color: green;">✓ Confirmado y Enviado</span>
                        @elseif($request->status == 'draft')
                            <span style="color: orange;">● En Edición</span>
                        @endif
                    </td>
                </tr>
            </table>
        </div>
        
        <div class="signature">
            <p>_________________________________</p>
            <p>Firma y Sello de la Dependencia</p>
            <p style="margin-top: 10px;">_________________________________</p>
            <p>Autorización del Sistema OGCC</p>
        </div>
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