@extends('layouts.app')

@section('title', 'Ver Dependencia - ' . $dependency->name)

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Detalles de la Dependencia</h1>
            <a href="{{ route('dependencies.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
        
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-building me-2"></i>{{ $dependency->name }}
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted">Información Básica</h6>
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Nombre:</th>
                                <td>{{ $dependency->name }}</td>
                            </tr>
                            <tr>
                                <th>Teléfono:</th>
                                <td>{{ $dependency->phone ?? 'No especificado' }}</td>
                            </tr>
                            <tr>
                                <th>Responsable:</th>
                                <td>{{ $dependency->responsible }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Información Administrativa</h6>
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Estructura Organizativa:</th>
                                <td>{{ $dependency->organizational_structure }}</td>
                            </tr>
                            <tr>
                                <th>Usuario Administrador:</th>
                                <td>{{ $dependency->user->name ?? 'No asignado' }}</td>
                            </tr>
                            <tr>
                                <th>Creado:</th>
                                <td>{{ $dependency->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Actualizado:</th>
                                <td>{{ $dependency->updated_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('dependencies.edit', $dependency->id) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                    <form action="{{ route('dependencies.destroy', $dependency->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('¿Está seguro de eliminar esta dependencia?')">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection