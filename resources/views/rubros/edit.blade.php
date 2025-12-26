@extends('layouts.app')

@section('title', 'Editar Rubro - ' . $rubro->description)

@section('content')
    <div class="container">
        <h1>Editar Rubro: {{ $rubro->description }}</h1>
        <form action="{{ route('rubros.update', $rubro->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="description" class="form-label">Descripción</label>
                <input type="text" name="description" id="description" class="form-control" value="{{ old('description', $rubro->description) }}" required>
            </div>
            <div class="mb-3">
                <label for="presentation" class="form-label">Presentación (Unidad, Caja, etc.)</label>
                <input type="text" name="presentation" id="presentation" class="form-control" value="{{ old('presentation', $rubro->presentation) }}" required>
            </div>
            <div class="mb-3">
                <label for="unit_price" class="form-label">Precio Unitario</label>
                <input type="number" step="0.01" name="unit_price" id="unit_price" class="form-control" value="{{ old('unit_price', $rubro->unit_price) }}" required>
            </div>
            <div class="mb-3">
                <label for="iva_exempt" class="form-label">Exento de IVA</label>
                <select name="iva_exempt" id="iva_exempt" class="form-select" required>
                    <option value="0" {{ $rubro->iva_exempt ? '' : 'selected' }}>No</option>
                    <option value="1" {{ $rubro->iva_exempt ? 'selected' : '' }}>Sí</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="onapre_code" class="form-label">Código ONAPRE (10 dígitos)</label>
                <input type="text" name="onapre_code" id="onapre_code" class="form-control" value="{{ old('onapre_code', $rubro->onapre_code) }}" required pattern="\d{10}" maxlength="10">
            </div>
            <div class="mb-3">
                <label for="onu_code" class="form-label">Código ONU (8 dígitos)</label>
                <input type="text" name="onu_code" id="onu_code" class="form-control" value="{{ old('onu_code', $rubro->onu_code) }}" required pattern="\d{8}" maxlength="8">
            </div>
            <button type="submit" class="btn btn-primary">Actualizar</button>
            <a href="{{ route('rubros.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
@endsection