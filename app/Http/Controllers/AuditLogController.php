<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;   // Importación de la librería
use App\Models\AuditLog;          // Importación del modelo

class AuditLogController extends Controller
{
    public function generatePdf()
    {
        // 1. Verifica si el usuario tiene permiso para ver las bitácoras
        $this->authorize('viewAny', AuditLog::class); 

        // 2. Obtiene los logs más recientes incluyendo la relación con el usuario
        $logs = AuditLog::with('user')->latest()->get(); 

        // 3. Carga la vista 'pdf.audit_logs' y le pasa los datos de los logs
        $pdf = Pdf::loadView('pdf.audit_logs', ['logs' => $logs]);

        // 4. Inicia la descarga del archivo con un nombre específico
        return $pdf->download('bitacoras_auditoria.pdf');
    }
}
