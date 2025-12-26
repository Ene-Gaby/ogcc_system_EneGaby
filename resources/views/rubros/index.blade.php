@extends('layouts.app')

@section('title', 'Gestionar Rubros')

@section('content')
    <div class="container">
        <h1>Gestionar Rubros</h1>
        <a href="{{ route('rubros.create') }}" class="btn btn-primary mb-3">Crear Nuevo Rubro</a>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Descripción</th>
                    <th>Presentación</th>
                    <th>Precio Unitario</th>
                    <th>Exento IVA</th>
                    <th>Código ONAPRE</th>
                    <th>Código ONU</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rubros as $rubro)
                    <tr>
                        <td>{{ $rubro->description }}</td>
                        <td>{{ $rubro->presentation }}</td>
                        <td>{{ number_format($rubro->unit_price, 2, ',', '.') }}</td>
                        <td>{{ $rubro->iva_exempt ? 'Sí' : 'No' }}</td>
                        <td>{{ $rubro->onapre_code }}</td>
                        <td>{{ $rubro->onu_code }}</td>
                        <td>
                            <a href="{{ route('rubros.edit', $rubro->id) }}" class="btn btn-sm btn-primary">Editar</a>
                            <form action="{{ route('rubros.destroy', $rubro->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Está seguro de que desea eliminar este rubro?')">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection