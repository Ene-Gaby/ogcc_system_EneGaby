@extends('layouts.app')

@section('title', 'Gestionar Dependencias')

@section('content')
    <div class="container">
        <h1>Gestionar Dependencias</h1>
        <a href="{{ route('dependencies.create') }}" class="btn btn-primary mb-3">Crear Nueva Dependencia</a>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Teléfono</th>
                    <th>Responsable</th>
                    <th>Estructura Organizativa</th>
                    <th>Usuario Administrador</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dependencies as $dependency)
                    <tr>
                        <td>{{ $dependency->name }}</td>
                        <td>{{ $dependency->phone }}</td>
                        <td>{{ $dependency->responsible }}</td>
                        <td>{{ $dependency->organizational_structure }}</td>
                        <td>{{ $dependency->user->name }}</td>
                        <td>
                            <a href="{{ route('dependencies.edit', $dependency->id) }}" class="btn btn-sm btn-primary">Editar</a>
                            <form action="{{ route('dependencies.destroy', $dependency->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Está seguro de que desea eliminar esta dependencia?')">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection