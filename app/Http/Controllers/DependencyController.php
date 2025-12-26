<?php

namespace App\Http\Controllers;

use App\Models\Dependency;
use Illuminate\Http\Request;

class DependencyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('viewAny', Dependency::class);
        $dependencies = Dependency::all();
        return view('dependencies.index', compact('dependencies'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', Dependency::class);
        return view('dependencies.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('create', Dependency::class);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:255',
            'responsible' => 'required|string|max:255',
            'organizational_structure' => 'required|string|max:255',
            'user_id' => 'required|exists:users,id', // Asumiendo que cada dependencia tiene un usuario administrador
        ]);

        $dependency = Dependency::create($validated);

        return redirect()->route('dependencies.index')->with('success', 'Dependencia creada exitosamente.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Dependency  $dependency
     * @return \Illuminate\Http\Response
     */
    public function show(Dependency $dependency)
    {
        $this->authorize('view', $dependency);
        return view('dependencies.show', compact('dependency'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Dependency  $dependency
     * @return \Illuminate\Http\Response
     */
    public function edit(Dependency $dependency)
    {
        $this->authorize('update', $dependency);
        return view('dependencies.edit', compact('dependency'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Dependency  $dependency
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Dependency $dependency)
    {
        $this->authorize('update', $dependency);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:255',
            'responsible' => 'required|string|max:255',
            'organizational_structure' => 'required|string|max:255',
            'user_id' => 'required|exists:users,id',
        ]);

        $dependency->update($validated);

        return redirect()->route('dependencies.index')->with('success', 'Dependencia actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Dependency  $dependency
     * @return \Illuminate\Http\Response
     */
    public function destroy(Dependency $dependency)
    {
        $this->authorize('delete', $dependency);
        $dependency->delete();

        return redirect()->route('dependencies.index')->with('success', 'Dependencia eliminada exitosamente.');
    }
}