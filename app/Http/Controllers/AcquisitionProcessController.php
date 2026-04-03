<?php

namespace App\Http\Controllers;

use App\Models\AcquisitionProcess;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class AcquisitionProcessController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('viewAny', AcquisitionProcess::class);
        $acquisitionProcesses = AcquisitionProcess::all();
        return view('acquisition-processes.index', compact('acquisitionProcesses'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', AcquisitionProcess::class);
        return view('acquisition-processes.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('create', AcquisitionProcess::class);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'fiscal_year' => 'required|integer|min:2000|max:2100',
            'start_date' => 'required|date|before_or_equal:end_date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|string|in:open,closed',
        ]);

        $acquisitionProcess = AcquisitionProcess::create($validated);

        return redirect()->route('acquisition-processes.index')->with('success', 'Proceso de Contratación creado exitosamente.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AcquisitionProcess  $acquisitionProcess
     * @return \Illuminate\Http\Response
     */
    public function show(AcquisitionProcess $acquisitionProcess)
    {
    $this->authorize('view', $acquisitionProcess);
    return view('acquisition-processes.show', compact('acquisitionProcess')); // ← Variable en minúsculas
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\AcquisitionProcess  $acquisitionProcess
     * @return \Illuminate\Http\Response
     */
    public function edit(AcquisitionProcess $acquisitionProcess)
    {
        $this->authorize('update', $acquisitionProcess);
        return view('acquisition-processes.edit', compact('acquisitionProcess'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AcquisitionProcess  $acquisitionProcess
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AcquisitionProcess $acquisitionProcess)
    {
        $this->authorize('update', $acquisitionProcess);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'fiscal_year' => 'required|integer|min:2000|max:2100',
            'start_date' => 'required|date|before_or_equal:end_date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|string|in:open,closed',
        ]);

        $acquisitionProcess->update($validated);

        return redirect()->route('acquisition-processes.index')->with('success', 'Proceso de Contratación actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AcquisitionProcess  $acquisitionProcess
     * @return \Illuminate\Http\Response
     */
    public function destroy(AcquisitionProcess $acquisitionProcess)
    {
        $this->authorize('delete', $acquisitionProcess);
        // Verificar si tiene solicitudes asociadas (opcional)
        if ($acquisitionProcess->requests()->count() > 0) {
             return redirect()->route('acquisition-processes.index')->with('error', 'No se puede eliminar el proceso porque tiene solicitudes asociadas.');
        }
        $acquisitionProcess->delete();

        return redirect()->route('acquisition-processes.index')->with('success', 'Proceso de Contratación eliminado exitosamente.');
    }

    // Método para generar presupuesto consolidado (Paso 6.2 o 7)
    public function generatePresupuestoConsolidado(AcquisitionProcess $process)
    {
        $this->authorize('view', $process); // Asegura que el usuario puede ver el proceso

        // Generamos el PDF usando la vista 'pdf.presupuesto_consolidado'
        $pdf = Pdf::loadView('pdf.presupuesto_consolidado', ['process' => $process]);

        // Retornamos el archivo para que el navegador lo descargue
        return $pdf->download('presupuesto_consolidado_' . $process->id . '.pdf');
    }

    public function generateParticipantesReport(AcquisitionProcess $process)
    {
    $this->authorize('view', $process);

    $participantes = $process->requests()
        ->where('participates', true)
        ->with('dependency')
        ->get();

    $pdf = Pdf::loadView('pdf.report_participantes', compact('process', 'participantes'));
    return $pdf->download("listado_participantes_{$process->id}.pdf");
    }

    public function generateNoParticipantesReport(AcquisitionProcess $process)
    {
    $this->authorize('view', $process);

    $noParticipantes = $process->requests()
        ->where('participates', false)
        ->with('dependency')
        ->get();

    $pdf = Pdf::loadView('pdf.report_no_participantes', compact('process', 'noParticipantes'));
    return $pdf->download("listado_no_participantes_{$process->id}.pdf");
    }
}