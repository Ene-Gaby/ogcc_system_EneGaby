<?php

namespace App\Http\Controllers;

use App\Models\Request;
use App\Models\RequestDetail;
use App\Models\AcquisitionProcess;
use App\Models\AcquisitionProcessRubro;
use App\Models\Dependency;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class RequestController extends Controller
{
    // Mostrar procesos abiertos disponibles para la dependencia del usuario actual
    public function listOpenProcesses()
    {
        $this->authorize('viewAny', AcquisitionProcess::class); // Verifica permiso general para ver procesos

        $userDependencyId = Auth::user()->dependency->id; // Asumiendo que un usuario 'usuario' tiene una dependencia asociada

        // Obtener procesos abiertos donde la dependencia NO haya creado ya una solicitud
        $openProcesses = AcquisitionProcess::where('status', 'open')
            ->whereDoesntHave('requests', function ($query) use ($userDependencyId) {
                $query->where('dependency_id', $userDependencyId);
            })
            ->get();

        return view('requests.list_open_processes', compact('openProcesses'));
    }

    // Mostrar formulario para crear solicitud dentro de un proceso específico
    public function createForProcess(AcquisitionProcess $process)
    {
        // Verificar que el proceso esté abierto y que la dependencia aún no tenga una solicitud para él
        $this->authorize('create', Request::class);
        $userDependencyId = Auth::user()->dependency->id;

        $existingRequest = Request::where('acquisition_process_id', $process->id)
                                    ->where('dependency_id', $userDependencyId)
                                    ->first();

        if ($existingRequest) {
            // Redirigir o mostrar error si ya existe una solicitud
            abort(403, 'Ya ha creado una solicitud para este proceso.');
        }

        if ($process->status !== 'open') {
            abort(403, 'El proceso ya no está abierto.');
        }

        // Cargar los rubros específicos del proceso
        $rubros = $process->rubros; // Asumiendo la relación belongsToMany

        return view('requests.create_for_process', compact('process', 'rubros'));
    }

    // Almacenar la solicitud y sus detalles
    public function storeForProcess(HttpRequest $request, AcquisitionProcess $process)
    {
        $this->authorize('create', Request::class);
        $userDependencyId = Auth::user()->dependency->id;

        // Validar datos recibidos (ejemplo básico)
        $request->validate([
            'rubro_quantities' => 'required|array',
            'rubro_quantities.*' => 'nullable|integer|min:0', // Permitir nulos si no se envía cantidad
        ]);

        // Crear la solicitud principal
        $requestModel = Request::create([
            'dependency_id' => $userDependencyId,
            'acquisition_process_id' => $process->id,
            'status' => 'pending_decision', // Inicialmente pendiente de decisión
        ]);

        // Crear los detalles de la solicitud (RequestDetail) con cantidades
        $rubroQuantities = $request->input('rubro_quantities', []);
        foreach ($rubroQuantities as $acquisitionProcessRubroId => $quantity) {
            if ($quantity > 0) { // Solo crear detalle si la cantidad es mayor a 0
                $acquisitionProcessRubro = AcquisitionProcessRubro::findOrFail($acquisitionProcessRubroId);

                $requestDetail = new RequestDetail();
                $requestDetail->request_id = $requestModel->id;
                $requestDetail->acquisition_process_rubro_id = $acquisitionProcessRubroId;
                $requestDetail->quantity = $quantity;
                // Guardamos los valores del rubro en el momento de la solicitud
                $requestDetail->unit_price_at_request_time = $acquisitionProcessRubro->price_override ?? $acquisitionProcessRubro->rubro->unit_price;
                $requestDetail->iva_exempt_at_request_time = $acquisitionProcessRubro->rubro->iva_exempt;

                // Calculamos y guardamos los campos calculados
                $requestDetail->recalculate(); // Este método está definido en el modelo RequestDetail
                $requestDetail->save();
            }
        }

        // Recalcular el total de la solicitud principal
        $requestModel->recalculateTotal(); // Este método está definido en el modelo Request

        return redirect()->route('requests.edit.details', $requestModel->id)
                         ->with('success', 'Solicitud creada. Por favor, ingrese las cantidades.');
    }

    // Mostrar formulario para editar detalles (cantidades) de una solicitud propia
    public function editDetails(Request $request)
    {
        $this->authorize('update', $request); // Verifica permiso para actualizar esta solicitud específica

        // Cargar solicitud con sus detalles y rubros del proceso
        $request->load('requestDetails.acquisitionProcessRubro.rubro');

        return view('requests.edit_details', compact('request'));
    }

    // Actualizar detalles (cantidades) de una solicitud propia
    public function updateDetails(HttpRequest $request, Request $model)
    {
        $this->authorize('update', $model);

        // Validar datos recibidos
        $request->validate([
            'quantities' => 'required|array',
            'quantities.*' => 'integer|min:0',
        ]);

        // Actualizar cantidades en RequestDetail y recalcular
        $quantities = $request->input('quantities', []);
        foreach ($model->requestDetails as $detail) {
            if (isset($quantities[$detail->id])) {
                $detail->quantity = $quantities[$detail->id];
                $detail->recalculate(); // Recalcula subtotal, IVA, total
                $detail->save();
            }
        }

        // Recalcular el total de la solicitud principal
        $model->recalculateTotal();

        return redirect()->back()->with('success', 'Detalles actualizados.');
    }

    // Confirmar participación
    public function confirmParticipation(HttpRequest $request, Request $model)
    {
        $this->authorize('participate', $model);

        $request->validate([
            'official_letter_number' => 'required|string|max:255', // RN-06
        ]);

        $model->update([
            'participates' => true,
            'official_letter_number' => $request->official_letter_number,
            'status' => 'submitted', // O 'participating', según tu flujo
        ]);

        // Aquí puedes llamar a la lógica para generar el PDF de participación
        // return $this->generateComprobante($model->id); // O redirigir y generar después

        return redirect()->route('requests.comprobante', $model->id)
                         ->with('success', 'Participación confirmada.');
    }

    // Confirmar no participación
    public function confirmNonParticipation(HttpRequest $request, Request $model)
    {
        $this->authorize('notParticipate', $model);

        $request->validate([
            'official_letter_number' => 'required|string|max:255', // RN-06
        ]);

        $model->update([
            'participates' => false,
            'official_letter_number' => $request->official_letter_number,
            'status' => 'not_participating',
        ]);

        // Aquí puedes llamar a la lógica para generar el PDF de no participación
        // return $this->generateComprobante($model->id); // O redirigir y generar después

        return redirect()->route('requests.comprobante', $model->id)
                         ->with('success', 'No participación confirmada.');
    }

    // Mostrar lista de solicitudes propias
    public function myRequests()
    {
        $userDependencyId = Auth::user()->dependency->id;
        $requests = Request::where('dependency_id', $userDependencyId)->get();

        return view('requests.my_requests', compact('requests'));
    }

    // Generar comprobante de participación/no participación en PDF
    public function generateComprobante(Request $request)
    {
        $this->authorize('view', $request); // Verifica permiso para ver esta solicitud específica

        // Lógica para generar PDF usando dompdf
        // $pdf = Pdf::loadView('pdf.comprobante', ['request' => $request]);
        // return $pdf->download('comprobante_'.$request->id.'.pdf');

        // Por ahora, retornamos una vista de ejemplo
        return view('pdf.comprobante', compact('request'));
    }

    // Generar presupuesto individual en PDF
    public function generatePresupuestoIndividual(Request $request)
    {
        $this->authorize('view', $request); // Verifica permiso para ver esta solicitud específica

        if (!$request->participates) {
            abort(403, 'No se puede generar presupuesto individual para una solicitud que no participa.');
        }

        // Lógica para generar PDF usando dompdf
        // $pdf = Pdf::loadView('pdf.presupuesto_individual', ['request' => $request]);
        // return $pdf->download('presupuesto_individual_'.$request->id.'.pdf');

        // Por ahora, retornamos una vista de ejemplo
        return view('pdf.presupuesto_individual', compact('request'));
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

}