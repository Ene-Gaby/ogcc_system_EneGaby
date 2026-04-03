<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Comprobante de No Participación</title>
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
        .header h2 {
            margin: 5px 0;
            font-size: 18px;
            color: #dc3545;
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
            border: 2px solid #dc3545;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
        }
        .certificate h2 {
            color: #dc3545;
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
        .official-letter {
            margin: 20px 0;
            padding: 15px;
            background: #e9ecef;
            border-left: 4px solid #dc3545;
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
        /* Solo mostrar marca de agua si NO es final */
        @if(!isset($is_final) || !$is_final)
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 60px;
            color: rgba(0,0,0,0.1);
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
        <h1>Comprobante de No Participación</h1>
        <h2>Declaración Formal de No Participación</h2>
        <div class="logo">Sistema OGCC - Universidad de Los Andes</div>
    </div>
    
    <div class="content">
        <div class="certificate">
            <h2>CERTIFICADO DE NO PARTICIPACIÓN</h2>
            <p>Se hace constar que la dependencia <strong>{{ $request->dependency->name ?? 'N/A' }}</strong></p>
            <p>ha registrado formalmente su <strong style="color: #dc3545;">NO PARTICIPACIÓN</strong> en el proceso de compra:</p>
            <h3>{{ $request->acquisitionProcess->name }}</h3>
            <p><strong>Año Fiscal:</strong> {{ $request->acquisitionProcess->fiscal_year }}</p>
            <p><strong>Fecha de Registro:</strong> {{ $today ?? now()->format('d/m/Y') }}</p>
        </div>
        
        @if($request->official_letter_number)
        <div class="official-letter">
            <p><strong>Número de Oficio:</strong> {{ $request->official_letter_number }}</p>
            <p>La presente no participación queda formalmente registrada mediante el oficio N° {{ $request->official_letter_number }}.</p>
        </div>
        @endif
        
        <div class="details">
            <h4>Información de la Declaración</h4>
            <table>
                <tr>
                    <td width="40%"><strong>N° de Registro:</strong></td>
                    <td>{{ str_pad($request->id, 8, '0', STR_PAD_LEFT) }}</td>
                    <td width="30%"><strong>Monto Total:</strong></td>
                    <td><strong>0,00 Bs.</strong></td>
                </tr>
                <tr>
                    <td><strong>Estado:</strong></td>
                    <td colspan="3">
                        <span style="color: #dc3545;">✗ No Participa</span>
                    </td>
                </tr>
            </table>
        </div>
        
        <div class="signature">
            <p>_________________________________</p>
            <p>Firma y Sello del Representante de la Dependencia</p>
            <p>{{ $request->dependency->name ?? 'Dependencia' }}</p>
            <p style="margin-top: 20px;">_________________________________</p>
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