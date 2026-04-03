<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Request;
use App\Models\RequestDetail;
use App\Models\AcquisitionProcess;
use App\Models\AcquisitionProcessRubro;
use App\Models\Dependency;
use App\Models\Rubro;
use App\Models\AuditLog;
use Illuminate\Http\Request as HttpRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class RequestController extends Controller
{
    // Mostrar procesos abiertos disponibles para la dependencia del usuario actual
    public function listOpenProcesses()
    {
        $this->authorize('viewAny', AcquisitionProcess::class);

    // Obtener la dependencia del usuario actual
    $userDependency = Auth::user()->dependency;

    if (!$userDependency) {
        return redirect()->route('home')->with('error', 'No se puede continuar porque no tienes una dependencia asociada.');
    }

    $userDependencyId = $userDependency->id;

    // Log para depuración
    \Log::info("Usuario: " . Auth::user()->name . ", Dependencia ID: " . $userDependencyId);

    // Obtener procesos abiertos donde la dependencia NO haya creado ya una solicitud
        $openProcesses = AcquisitionProcess::whereIn('status', ['open', 'Open'])
        ->whereDoesntHave('requests', function ($query) use ($userDependencyId) {
            $query->where('dependency_id', $userDependencyId);
        })
        ->get();

    // Log para depuración
    \Log::info("Procesos disponibles: " . $openProcesses->count());

        return view('requests.list_open_processes', compact('openProcesses'));
    }

public function createDetails(AcquisitionProcess $process)
{
    $this->authorize('viewAny', AcquisitionProcess::class);

    // Verificar que el usuario tenga dependencia
    $userDependency = Auth::user()->dependency;
    if (!$userDependency) {
        abort(403, 'El usuario no está asociado a una dependencia.');
    }

    // ✅ VALIDACIÓN PARA VENEZUELA (sin depender del servidor)
    $todayVenezuela = \Carbon\Carbon::now('America/Caracas')->toDateString();
    $processEndDate = $process->end_date ?? '';

    if ($process->status !== 'open' || ($processEndDate && $processEndDate < $todayVenezuela)) {
        abort(403, 'Este proceso ya ha sido cerrado y no acepta nuevas solicitudes.');
    }

    // Verificar que la dependencia no haya enviado ya una solicitud
    if ($process->requests()->where('dependency_id', $userDependency->id)->exists()) {
        return back()->withErrors(['error' => 'Ya ha registrado una decisión para este proceso.']);
    }

    // Filtrar rubros por el proceso actual
    //$rubros = Rubro::where('acquisition_process_id', $process->id)->get();

    // Obtener rubros del proceso usando la relación
    $rubros = $process->rubros;

    return view('requests.create_details', compact('process', 'rubros'));
}

    // Mostrar formulario para crear solicitud dentro de un proceso específico
    public function createForProcess(AcquisitionProcess $process)
{
    $this->authorize('viewAny', AcquisitionProcess::class);

    // Verificar que el usuario tenga dependencia
    $userDependency = Auth::user()->dependency;
    if (!$userDependency) {
        abort(403, 'El usuario no está asociado a una dependencia.');
    }

    // ✅ VALIDACIÓN PARA VENEZUELA (sin depender del servidor)
    $todayVenezuela = \Carbon\Carbon::now('America/Caracas')->toDateString();
    $processEndDate = $process->end_date ?? '';

    if ($process->status !== 'open' || ($processEndDate && $processEndDate < $todayVenezuela)) {
        abort(403, 'Este proceso ya ha sido cerrado y no acepta nuevas solicitudes.');
    }

    // Verificar que la dependencia no haya enviado ya una solicitud
    if ($process->requests()->where('dependency_id', $userDependency->id)->exists()) {
        abort(409, 'Ya ha enviado una solicitud para este proceso.');
    }

    // Filtrar rubros por el proceso actual
    $rubros = Rubro::where('acquisition_process_id', $process->id)->get();

    return view('requests.create_for_process', compact('process', 'rubros'));
}


/**
 * Almacena los detalles de la solicitud y redirige a vista previa
 * 
 * @param HttpRequest $request
 * @param AcquisitionProcess $process
 * @return \Illuminate\Http\RedirectResponse
 */
public function storeDetails(HttpRequest $request, AcquisitionProcess $process)
{
    $this->authorize('create', Request::class);
    
    $userDependency = Auth::user()->dependency;
    if (!$userDependency) {
        abort(403, 'El usuario no está asociado a una dependencia.');
    }
    
    // Validar fecha de cierre del proceso
    $todayVenezuela = Carbon::now('America/Caracas')->toDateString();
    $processEndDate = $process->end_date ?? '';
    if ($process->status !== 'open' || ($processEndDate && $processEndDate < $todayVenezuela)) {
        abort(403, 'Este proceso ya ha sido cerrado y no acepta nuevas solicitudes.');
    }
    
    // Verificar que no exista solicitud previa
    if ($process->requests()->where('dependency_id', $userDependency->id)->exists()) {
        abort(409, 'Ya ha registrado una decisión para este proceso.');
    }
    
    // Validar: al menos un rubro con cantidad > 0
    $request->validate([
        'details' => 'required|array',
        'details.*.quantity' => 'integer|min:0',
    ]);
    
    $hasItems = false;
    foreach ($request->input('details', []) as $rubroId => $data) {
        if (($data['quantity'] ?? 0) > 0) {
            $hasItems = true;
            break;
        }
    }
    
    if (!$hasItems) {
        throw ValidationException::withMessages([
            'details' => 'Debe seleccionar al menos un rubro con cantidad mayor a cero.'
        ]);
    }
    
    // Crear solicitud en transacción
    $newRequest = null;
    DB::transaction(function () use ($request, $process, $userDependency, &$newRequest) {
        // Crear solicitud principal en estado draft
        $newRequest = Request::create([
            'acquisition_process_id' => $process->id,
            'dependency_id' => $userDependency->id,
            'status' => 'draft',
            'participates' => true,
            'official_letter_number' => null,
            'total_amount' => 0,
        ]);
        
        // Crear detalles para cada rubro con cantidad > 0
        $detailsData = $request->input('details', []);
        $totalGeneral = 0;
        
        foreach ($process->rubros as $rubro) {
            $quantity = $detailsData[$rubro->id]['quantity'] ?? 0;
            
            if ($quantity > 0) {
                // Obtener el pivot AcquisitionProcessRubro
                $apr = AcquisitionProcessRubro::where('rubro_id', $rubro->id)
                    ->where('acquisition_process_id', $process->id)
                    ->first();
                
                if (!$apr) {
                    \Log::error("No se encontró AcquisitionProcessRubro", [
                        'rubro_id' => $rubro->id,
                        'process_id' => $process->id,
                        'rubro_description' => $rubro->description
                    ]);
                    continue;
                }
                
                // CALCULAR VALORES CORRECTAMENTE
                $subtotal = $quantity * $rubro->unit_price;
                $iva = $subtotal * 0.16; // 16% IVA
                $total = $subtotal + $iva;
                
                // Crear el detalle con todos los campos calculados
                $detail = $newRequest->requestDetails()->create([
                    'acquisition_process_rubro_id' => $apr->id,
                    'quantity' => $quantity,
                    'unit_price_at_request_time' => $rubro->unit_price,
                    'iva_exempt_at_request_time' => $rubro->iva_exempt ?? false,
                    'subtotal' => $subtotal,
                    'iva_amount' => $iva,
                    'total' => $total,
                ]);
                
                $totalGeneral += $total;
                
                \Log::info("✅ Detalle creado correctamente", [
                    'detail_id' => $detail->id,
                    'rubro' => $rubro->description,
                    'quantity' => $quantity,
                    'unit_price' => $rubro->unit_price,
                    'subtotal' => $subtotal,
                    'iva' => $iva,
                    'total' => $total
                ]);
            }
        }
        
        // Actualizar el total general de la solicitud
        $newRequest->total_amount = $totalGeneral;
        $newRequest->save();
        
        \Log::info("✅ Solicitud creada exitosamente", [
            'request_id' => $newRequest->id,
            'details_count' => $newRequest->requestDetails()->count(),
            'total_amount' => $totalGeneral,
            'status' => $newRequest->status
        ]);
    });
    
    return redirect()->route('requests.preview', $newRequest->id)
        ->with('success', 'Solicitud creada. Revise los detalles antes de enviar.');
}

/**
     * Muestra la vista previa con los PDFs generados
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function preview(Request $request)
{
    $this->authorize('update', $request);
    
    // Cargar todas las relaciones necesarias
    $request->load([
        'dependency',
        'acquisitionProcess',
        'requestDetails' => function($query) {
            $query->with(['acquisitionProcessRubro' => function($q) {
                $q->with('rubro');
            }]);
        }
    ]);
    
    // Debug: Verificar si hay detalles
    \Log::info('Vista previa - Detalles cargados', [
        'request_id' => $request->id,
        'details_count' => $request->requestDetails->count(),
        'details' => $request->requestDetails->map(function($detail) {
            return [
                'id' => $detail->id,
                'quantity' => $detail->quantity,
                'rubro_desc' => $detail->acquisitionProcessRubro->rubro->description ?? 'NULL',
                'onapre' => $detail->acquisitionProcessRubro->rubro->onapre_code ?? 'NULL',
                'onu' => $detail->acquisitionProcessRubro->rubro->onu_code ?? 'NULL',
            ];
        })
    ]);
    
    return view('requests.preview', compact('request'));
}
    
/**
 * Envía la solicitud definitivamente (cambia estado a submitted y guarda PDFs)
 * 
 * @param HttpRequest $httpRequest
 * @param Request $request
 * @return \Illuminate\Http\RedirectResponse
 */
public function submit(HttpRequest $httpRequest, Request $request)
{
    // Verificar que el usuario puede actualizar esta solicitud
    if ($request->dependency_id !== Auth::user()->dependency_id) {
        abort(403, 'No tiene permiso para enviar esta solicitud.');
    }
    
    // Verificar que esté en estado draft
    if ($request->status !== 'draft') {
        return back()->withErrors(['error' => 'Esta solicitud ya ha sido enviada.']);
    }
    
    // Verificar que tenga al menos un detalle
    if ($request->requestDetails->isEmpty()) {
        return back()->withErrors(['error' => 'Debe seleccionar al menos un rubro.']);
    }
    
    // Iniciar transacción
    DB::transaction(function () use ($request) {
        // Actualizar estado
        $request->update([
            'status' => 'submitted',
            'updated_at' => now(),
        ]);
        
        // Generar timestamp para los PDFs definitivos
        $timestamp = now()->format('Ymd_His');
        
        // Asegurar que la carpeta existe
        if (!Storage::disk('public')->exists('pdfs')) {
            Storage::disk('public')->makeDirectory('pdfs');
        }
        
        // Generar y guardar PDF de Presupuesto Individual
        $pdfPresupuesto = Pdf::loadView('pdf.presupuesto_individual', [
            'request' => $request,
            'today' => Carbon::now('America/Caracas')->format('d/m/Y'),
            'is_final' => true,
        ]);
        
        $presupuestoPath = "pdfs/presupuesto_{$request->id}_{$timestamp}.pdf";
        Storage::disk('public')->put($presupuestoPath, $pdfPresupuesto->output());
        
        // Generar y guardar PDF de Comprobante
        $pdfComprobante = Pdf::loadView('pdf.comprobante', [
            'request' => $request,
            'today' => Carbon::now('America/Caracas')->format('d/m/Y'),
            'is_final' => true,
        ]);
        
        $comprobantePath = "pdfs/comprobante_{$request->id}_{$timestamp}.pdf";
        Storage::disk('public')->put($comprobantePath, $pdfComprobante->output());
        
        // Guardar rutas de PDFs en la solicitud
        $request->update([
            'pdf_presupuesto_path' => $presupuestoPath,
            'pdf_comprobante_path' => $comprobantePath,
        ]);
        
        // Registrar en bitácora
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'submit_request',
            'auditable_type' => Request::class,
            'auditable_id' => $request->id,
            'table_name' => 'requests',
            'record_id' => $request->id,
            'details' => json_encode([
                'status_before' => 'draft',
                'status_after' => 'submitted',
                'pdf_presupuesto' => $presupuestoPath,
                'pdf_comprobante' => $comprobantePath,
                'total_amount' => $request->total_amount,
            ]),
            'action_time' => now(),
        ]);
    });
    
    return redirect()->route('requests.my.index')
        ->with('success', 'Solicitud enviada exitosamente. Los PDFs han sido generados y guardados.');
}

public function storeForProcess(HttpRequest $request, AcquisitionProcess $process)
{
    $this->authorize('create', Request::class);

    $userDependency = Auth::user()->dependency;
    if (!$userDependency) {
        abort(403, 'El usuario no está asociado a una dependencia.');
    }

    // ✅ VALIDACIÓN PARA VENEZUELA
    $todayVenezuela = \Carbon\Carbon::now('America/Caracas')->toDateString();
    $processEndDate = $process->end_date ?? '';
    if ($process->status !== 'open' || ($processEndDate && $processEndDate < $todayVenezuela)) {
        abort(40, 'Este proceso ya ha sido cerrado y no acepta nuevas solicitudes.');
    }

    // ✅ Verificar unicidad: una dependencia solo puede tener una solicitud por proceso
    if ($process->requests()->where('dependency_id', $userDependency->id)->exists()) {
        abort(409, 'Ya ha registrado una decisión para este proceso.');
    }

    // ✅ Validar datos
    $request->validate([
        'details' => 'required|array',
        'details.*.quantity' => 'integer|min:1', // al menos 1 unidad    
    ]);
    // Iniciar transacción para garantizar consistencia
    DB::transaction(function () use ($request, $process, $userDependency) {
        // Crear solicitud principal
        $newRequest = Request::create([
            'acquisition_process_id' => $process->id,
            'dependency_id' => $userDependency->id,
            'status' => 'draft',
            'participates' => true,
            'official_letter_number' => $request->input('official_letter_number', ''),
        ]);

        // Crear detalles
        $detailsData = $request->input('details', []);
foreach ($process->rubros as $rubro) {
    $quantity = $detailsData[$rubro->id]['quantity'] ?? 0;
    if ($quantity > 0) {
        // Buscar el pivot EXACTO
        $apr = AcquisitionProcessRubro::where('rubro_id', $rubro->id)
                                     ->where('acquisition_process_id', $process->id)
                                     ->first();
        if (!$apr) {
            abort(404, "Rubro {$rubro->id} no asociado al proceso {$process->id}");
        }

        $detail = $newRequest->requestDetails()->create([
            'acquisition_process_rubro_id' => $apr->id,
            'quantity' => $quantity,
            'unit_price_at_request_time' => $rubro->unit_price,
            'iva_exempt_at_request_time' => $rubro->iva_exempt,
        ]);
        $detail->recalculate();
        $detail->save();
    }
}

        $newRequest->recalculateTotal();
        $newRequest->save();
    });

    // ✅ Redirigir a vista de edición (vista previa)
    return redirect()
        ->route('requests.edit.details', $newRequest->id)
        ->with('success', 'Solicitud creada. Revise los detalles y genere los PDFs.');
}

/**
 * Mostrar formulario para editar detalles (cantidades) de una solicitud propia
 * 
 * @param Request $request
 * @return \Illuminate\View\View
 */
public function editDetails(Request $request)
{
    // Verificar que el usuario puede actualizar esta solicitud
    if ($request->dependency_id !== Auth::user()->dependency_id) {
        abort(403, 'No tiene permiso para editar esta solicitud.');
    }
    
    // Verificar que la solicitud esté en draft
    if ($request->status !== 'draft') {
        abort(403, 'No se puede editar una solicitud ya enviada.');
    }
    
    // Cargar solicitud con sus detalles y rubros del proceso
    $request->load([
        'requestDetails.acquisitionProcessRubro.rubro',
        'acquisitionProcess'
    ]);
    
    return view('requests.edit_details', compact('request'));
}

    /**
 * Actualizar detalles (cantidades) de una solicitud propia
 * 
 * @param HttpRequest $httpRequest
 * @param Request $request
 * @return \Illuminate\Http\RedirectResponse
 */
public function updateDetails(HttpRequest $httpRequest, Request $request)
{
    // Verificar que el usuario puede actualizar esta solicitud
    if ($request->dependency_id !== Auth::user()->dependency_id) {
        abort(403, 'No tiene permiso para editar esta solicitud.');
    }
    
    // Validar que la solicitud esté en draft
    if ($request->status !== 'draft') {
        return back()->withErrors(['error' => 'No se puede modificar una solicitud ya enviada.']);
    }
    
    // Validar datos recibidos
    $httpRequest->validate([
        'quantities' => 'required|array',
        'quantities.*' => 'integer|min:0',
    ]);
    
    // Actualizar cantidades en RequestDetail y recalcular
    $quantities = $httpRequest->input('quantities', []);
    $totalGeneral = 0;
    $detallesActualizados = 0;
    
    foreach ($request->requestDetails as $detail) {
        if (isset($quantities[$detail->id])) {
            $newQuantity = $quantities[$detail->id];
            
            if ($newQuantity > 0) {
                // Actualizar cantidad
                $detail->quantity = $newQuantity;
                
                // Recalcular valores
                $subtotal = $detail->quantity * $detail->unit_price_at_request_time;
                $iva = $subtotal * 0.16;
                $total = $subtotal + $iva;
                
                $detail->subtotal = $subtotal;
                $detail->iva_amount = $iva;
                $detail->total = $total;
                $detail->save();
                
                $totalGeneral += $total;
                $detallesActualizados++;
            } else {
                // Si la cantidad es 0, eliminar el detalle
                $detail->delete();
            }
        } else {
            $totalGeneral += $detail->total;
        }
    }
    
    // Recalcular el total de la solicitud principal
    $request->total_amount = $totalGeneral;
    $request->save();
    
    // Registrar en bitácora
    AuditLog::create([
        'user_id' => Auth::id(),
        'action' => 'update_request_details',
        'auditable_type' => Request::class,
        'auditable_id' => $request->id,
        'table_name' => 'requests',
        'record_id' => $request->id,
        'details' => json_encode([
            'updated_details_count' => $detallesActualizados,
            'new_total' => $totalGeneral,
        ]),
        'action_time' => now(),
    ]);
    
    return redirect()->route('requests.preview', $request->id)
        ->with('success', 'Cantidades actualizadas correctamente.');
}

    
    public function confirmNonParticipation(\Illuminate\Http\Request $httpRequest, AcquisitionProcess $acquisitionProcess)
{
    // 1. Autorización: Solo el usuario de la dependencia puede hacer esto.
    $this->authorize('create', Request::class);

    // 2. Obtener la dependencia del usuario actual
    $userDependency = Auth::user()->dependency;
    if (!$userDependency) {
        abort(403, 'El usuario no está asociado a una dependencia.');
    }

    // 3. Validar que el proceso esté abierto
    if ($acquisitionProcess->status !== 'open') {
        abort(400, 'El proceso ya ha sido cerrado.');
    }

    // 4. Validar que la dependencia NO haya enviado ya una solicitud para este proceso
    if ($acquisitionProcess->requests()->where('dependency_id', $userDependency->id)->exists()) {
        return back()->withErrors(['error' => 'Ya ha registrado una decisión para este proceso.']);
    }

    // 5. Validar el número de oficio
    $httpRequest->validate([
        'official_letter_number' => 'required|string|max:255',
    ]);

    // Crear la nueva solicitud con status 'not_participating'
    $newRequest = null;
    DB::transaction(function () use ($httpRequest, $acquisitionProcess, $userDependency, &$newRequest) {
        $newRequest = Request::create([
            'dependency_id' => $userDependency->id,
            'acquisition_process_id' => $acquisitionProcess->id,
            'official_letter_number' => $httpRequest->input('official_letter_number'),
            'status' => 'not_participating',
            'participates' => false,
            'total_amount' => 0.00,
        ]);
        
        // Generar PDF definitivo de No Participación
        $timestamp = now()->format('Ymd_His');
        
        if (!Storage::disk('public')->exists('pdfs')) {
            Storage::disk('public')->makeDirectory('pdfs');
        }
        
        $pdfComprobante = Pdf::loadView('pdf.comprobante_no_participacion', [
            'request' => $newRequest,
            'today' => Carbon::now('America/Caracas')->format('d/m/Y'),
            'is_final' => true,
        ]);
        
        $comprobantePath = "pdfs/comprobante_no_participacion_{$newRequest->id}_{$timestamp}.pdf";
        Storage::disk('public')->put($comprobantePath, $pdfComprobante->output());
        
        // Guardar ruta del PDF
        $newRequest->update([
            'pdf_comprobante_path' => $comprobantePath,
        ]);
    });

    // 6. Registrar en la bitácora de auditoría
    AuditLog::create([
        'user_id' => Auth::id(),
        'action' => 'register_non_participation',
        'auditable_type' => 'App\\Models\\Request',
        'auditable_id' => $newRequest->id,
        'table_name' => 'requests',
        'record_id' => $newRequest->id,
        'details' => json_encode([
            'dependency_id' => $userDependency->id,
            'acquisition_process_id' => $acquisitionProcess->id,
            'official_letter_number' => $httpRequest->input('official_letter_number'),
            'status' => 'not_participating',
            'participates' => false,
        ]),
        'action_time' => now(),
    ]);

    // 7. 🔥 REDIRIGIR A MIS SOLICITUDES (NO al comprobante)
    return redirect()->route('requests.my.index')
        ->with('success', 'No participación registrada exitosamente. Puede ver el comprobante en "Mis Solicitudes".');
}

/**
 * Generar Comprobante de NO Participación DEFINITIVO
 * 
 * @param Request $request
 * @return \Illuminate\Http\Response
 */
public function downloadComprobanteNoParticipacion(Request $request)
{
    $this->authorize('view', $request);
    
    // Verificar que es una solicitud de no participación
    if ($request->status !== 'not_participating') {
        abort(403, 'Este comprobante es solo para solicitudes de no participación.');
    }
    
    // Cargar relaciones necesarias
    $request->load([
        'dependency',
        'acquisitionProcess'
    ]);
    
    $pdf = Pdf::loadView('pdf.comprobante_no_participacion', [
        'request' => $request,
        'today' => Carbon::now('America/Caracas')->format('d/m/Y'),
        'is_final' => true,  // DEFINITIVO - Sin marca de agua
    ]);
    
    return $pdf->download("comprobante_no_participacion_{$request->id}.pdf");
}

    // Mostrar lista de solicitudes propias
    public function myRequests()
    {
    // 1. Verificar que el usuario esté autenticado (ya lo hace el middleware 'auth')
    // 2. Obtener la dependencia del usuario actual
    $userDependency = Auth::user()->dependency;

    // 3. Verificar si el usuario tiene una dependencia asociada
    if (!$userDependency) {
        // Si no tiene dependencia, redirigir con un mensaje de error
        return redirect()->route('home')->with('error', 'No se puede continuar porque no tienes una dependencia asociada.');
    }

    // 4. Obtener el ID de la dependencia del usuario
    $userDependencyId = $userDependency->id;

    // 5. Obtener las solicitudes del usuario actual (basadas en la dependencia)
    $requests = Request::where('dependency_id', $userDependencyId)->get();

    // 6. Retornar la vista con las solicitudes del usuario
    return view('requests.my_requests', compact('requests'));
    }

    
        /**
 * Display a listing of the resource.
 *
 * @return \Illuminate\Http\Response
 */
public function index()
{
    $this->authorize('viewAny', Request::class); // Verifica permiso para ver todas las solicitudes

    // Obtener todas las solicitudes con sus relaciones
    $requests = Request::with(['dependency', 'acquisitionProcess'])->get();

    return view('requests.index', compact('requests'));
}
public function showConfirmNonParticipationForm(AcquisitionProcess $process)
{
    $this->authorize('create', Request::class);

    // Verificar que el usuario tenga dependencia
    $userDependency = Auth::user()->dependency;
    if (!$userDependency) {
        abort(403, 'El usuario no está asociado a una dependencia.');
    }

    // ✅ VALIDACIÓN PARA VENEZUELA (sin depender del servidor)
    $todayVenezuela = \Carbon\Carbon::now('America/Caracas')->toDateString();
    $processEndDate = $process->end_date ?? '';

    if ($process->status !== 'open' || ($processEndDate && $processEndDate < $todayVenezuela)) {
        abort(403, 'Este proceso ya ha sido cerrado y no acepta nuevas solicitudes.');
    }

    // Verificar que la dependencia no haya enviado ya una solicitud
    if ($process->requests()->where('dependency_id', $userDependency->id)->exists()) {
        return back()->withErrors(['error' => 'Ya ha registrado una decisión para este proceso.']);
    }

    return view('requests.confirm_non_participation_form', compact('process'));
}

// Generar Presupuesto Base Individual (vista preliminar)
public function generatePresupuestoIndividual(Request $request)
{
    $this->authorize('view', $request);
    
    if (!$request->participates) {
        abort(403, 'No se puede generar presupuesto para una solicitud que no participa.');
    }
    
    $request->load([
        'dependency',
        'acquisitionProcess',
        'requestDetails.acquisitionProcessRubro.rubro'
    ]);
    
    $pdf = Pdf::loadView('pdf.presupuesto_individual', [
        'request' => $request,
        'today' => Carbon::now('America/Caracas')->format('d/m/Y'),
        'is_final' => false,  // 🔥 Vista preliminar con marca de agua
    ]);
    
    return $pdf->stream("presupuesto_solicitud_{$request->id}_preliminar.pdf");
}

// Generar Comprobante de Participación (vista preliminar)
public function generateComprobante(Request $request)
{
    $this->authorize('view', $request);
    
    $pdf = Pdf::loadView('pdf.comprobante', [
        'request' => $request,
        'today' => Carbon::now('America/Caracas')->format('d/m/Y'),
        'is_final' => false,  // 🔥 Vista preliminar con marca de agua
    ]);
    
    return $pdf->stream("comprobante_solicitud_{$request->id}_preliminar.pdf");
}

/**
 * Descargar Presupuesto Individual DEFINITIVO (desde Mis Solicitudes)
 * 
 * @param Request $request
 * @return \Illuminate\Http\Response
 */
public function downloadPresupuestoDefinitivo(Request $request)
{
    $this->authorize('view', $request);
    
    if (!$request->participates) {
        abort(403, 'No se puede generar presupuesto para una solicitud que no participa.');
    }
    
    // Verificar que la solicitud está enviada
    if ($request->status !== 'submitted') {
        // Si no está enviada, mostrar la preliminar
        return $this->generatePresupuestoIndividual($request);
    }
    
    // Si tiene PDF guardado, descargarlo
    if ($request->pdf_presupuesto_path && Storage::disk('public')->exists($request->pdf_presupuesto_path)) {
        return response()->download(storage_path('app/public/' . $request->pdf_presupuesto_path), "presupuesto_solicitud_{$request->id}.pdf");
    }
    
    // Si no hay PDF guardado, generar uno nuevo (sin marca de agua)
    $request->load([
        'dependency',
        'acquisitionProcess',
        'requestDetails.acquisitionProcessRubro.rubro'
    ]);
    
    $pdf = Pdf::loadView('pdf.presupuesto_individual', [
        'request' => $request,
        'today' => Carbon::now('America/Caracas')->format('d/m/Y'),
        'is_final' => true,  // DEFINITIVO - Sin marca de agua
    ]);
    
    return $pdf->download("presupuesto_solicitud_{$request->id}.pdf");
}

/**
 * Descargar Comprobante de Participación DEFINITIVO (desde Mis Solicitudes)
 * 
 * @param Request $request
 * @return \Illuminate\Http\Response
 */
public function downloadComprobanteDefinitivo(Request $request)
{
    $this->authorize('view', $request);
    
    // Verificar que la solicitud está enviada
    if ($request->status !== 'submitted') {
        // Si no está enviada, mostrar la preliminar
        return $this->generateComprobante($request);
    }
    
    // Si tiene PDF guardado, descargarlo
    if ($request->pdf_comprobante_path && Storage::disk('public')->exists($request->pdf_comprobante_path)) {
        return response()->download(storage_path('app/public/' . $request->pdf_comprobante_path), "comprobante_solicitud_{$request->id}.pdf");
    }
    
    // Si no hay PDF guardado, generar uno nuevo (sin marca de agua)
    $pdf = Pdf::loadView('pdf.comprobante', [
        'request' => $request,
        'today' => Carbon::now('America/Caracas')->format('d/m/Y'),
        'is_final' => true,  // DEFINITIVO - Sin marca de agua
    ]);
    
    return $pdf->download("comprobante_solicitud_{$request->id}.pdf");
}

// Confirmar y enviar solicitud (transición a estado final)
public function confirmParticipation(HttpRequest $request, Request $model)
{
    $this->authorize('participate', $model);

    // Validar que tenga al menos un detalle
    if ($model->requestDetails->isEmpty()) {
        return back()->withErrors(['error' => 'Debe seleccionar al menos un rubro.']);
    }

    // Actualizar estado a "submitted"
    $model->update([
        'status' => 'submitted',
        'participates' => true,
        'updated_at' => now(),
    ]);

    // Registrar en bitácora
    AuditLog::create([
        'user_id' => Auth::id(),
        'action' => 'submit_request',
        'auditable_type' => Request::class,
        'auditable_id' => $model->id,
        'table_name' => 'requests',
        'record_id' => $model->id,
        'details' => json_encode([
            'status_before' => 'draft',
            'status_after' => 'submitted',
            'dependency_id' => $model->dependency_id,
            'acquisition_process_id' => $model->acquisition_process_id,
        ]),
        'action_time' => now(),
    ]);

    // ✅ Generar PDFs definitivos (con timestamp para evitar sobrescritura)
    $timestamp = now()->format('Ymd_His');
    $pdfPresupuesto = Pdf::loadView('pdf.presupuesto_individual', ['request' => $model]);
    $pdfPresupuesto->save(storage_path("app/public/pdfs/presupuesto_{$model->id}_{$timestamp}.pdf"));

    $pdfComprobante = Pdf::loadView('pdf.comprobante', ['request' => $model]);
    $pdfComprobante->save(storage_path("app/public/pdfs/comprobante_{$model->id}_{$timestamp}.pdf"));

    // Redirigir a Mis Solicitudes
    return redirect()
        ->route('requests.my.index')
        ->with('success', 'Solicitud enviada exitosamente. ¡Gracias por participar!');
}

}