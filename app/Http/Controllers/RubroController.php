<?php

namespace App\Http\Controllers;
use App\Models\AcquisitionProcess;

use App\Models\Rubro;
use Illuminate\Http\Request;

class RubroController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('viewAny', Rubro::class);
        $rubros = Rubro::all();
        return view('rubros.index', compact('rubros'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    $acquisitionProcesses = AcquisitionProcess::all();
    return view('rubros.create', compact('acquisitionProcesses'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    $validatedData = $request->validate([
        'description' => 'required|string|max:255',
        'presentation' => 'required|string|max:255',
        'unit_price' => 'required|numeric|min:0',
        'iva_exempt' => 'required|boolean',
        'onapre_code' => 'required|string|digits:10|unique:rubros,onapre_code',
        'onu_code' => 'required|string|digits:8|unique:rubros,onu_code',
        'acquisition_process_id' => 'nullable|exists:acquisition_processes,id',
    ]);

    try {
        $rubro = Rubro::create($validatedData);
        return redirect()->route('rubros.index')->with('success', 'Rubro creado exitosamente.');
    } catch (\Exception $e) {
        return back()->withErrors(['error' => $e->getMessage()]);
    }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Rubro  $rubro
     * @return \Illuminate\Http\Response
     */
    public function show(Rubro $rubro)
    {
        $this->authorize('view', $rubro);
        return view('rubros.show', compact('rubro'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Rubro  $rubro
     * @return \Illuminate\Http\Response
     */
    public function edit(Rubro $rubro)
    {
        $this->authorize('update', $rubro);
        return view('rubros.edit', compact('rubro'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Rubro  $rubro
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Rubro $rubro)
{
    // 1. Autorización
    $this->authorize('update', $rubro);

    // 2. Validación explícita y segura
    $validatedData = $request->validate([
        'description' => 'required|string|max:255',
        'presentation' => 'required|string|max:255',
        'unit_price' => 'required|numeric|min:0',
        'iva_exempt' => 'required|boolean',
        'onapre_code' => [
            'required',
            'string',
            'digits:10',
            Rule::unique('rubros', 'onapre_code')->ignore($rubro->id),
        ],
        'onu_code' => [
            'required',
            'string',
            'digits:8',
            Rule::unique('rubros', 'onu_code')->ignore($rubro->id),
        ],
    ]);

    // 3. Actualizar el rubro
    $rubro->update($validatedData);

    // 4. Redirigir con éxito
    return redirect()->route('rubros.index')
        ->with('success', 'Rubro actualizado exitosamente.');
}

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Rubro  $rubro
     * @return \Illuminate\Http\Response
     */
    public function destroy(Rubro $rubro)
    {
        $this->authorize('delete', $rubro);
        $rubro->delete();

        return redirect()->route('rubros.index')->with('success', 'Rubro eliminado exitosamente.');
    }
}