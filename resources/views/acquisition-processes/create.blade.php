@extends('layouts.app')

@section('title', 'Crear Proceso de Contratación')

@section('content')
    <div class="container">
        <h1>Crear Proceso de Contratación</h1>
        <form action="{{ route('acquisition-processes.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Nombre</label>
                <input type="text" name="name" id="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Descripción</label>
                <textarea name="description" id="description" class="form-control"></textarea>
            </div>
            <div class="mb-3">
                <label for="fiscal_year" class="form-label">Año Fiscal</label>
                <input type="number" name="fiscal_year" id="fiscal_year" class="form-control" min="2000" max="2100" required>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="start_date" class="form-label">Fecha de Inicio</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="end_date" class="form-label">Fecha de Cierre</label>
                    <input type="date" name="end_date" id="end_date" class="form-control" required>
                </div>
            </div>
            <div class="mb-3">
                <label for="status" class="form-label">Estado</label>
                <select name="status" id="status" class="form-select" required>
                    <option value="">Seleccione un estado</option>
                    <option value="open">Abierto</option>
                    <option value="closed">Cerrado</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Guardar</button>
            <a href="{{ route('acquisition-processes.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
@endsection