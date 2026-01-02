@extends('layouts.app')

@section('title', 'Gestionar Dependencias')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Gestionar Dependencias</h1>
            <a href="{{ route('dependencies.create') }}" class="btn btn-primary btn-lg">
                <i class="fas fa-building"></i> Crear Nueva Dependencia
            </a>
        </div>
        
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        
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
                        <td>{{ $dependency->phone ?? '-' }}</td>
                        <td>{{ $dependency->responsible }}</td>
                        <td>{{ $dependency->organizational_structure }}</td>
                        <td>{{ $dependency->user->name ?? 'No asignado' }}</td>
                        <td>
                            <!-- Botón Ver -->
                            <a href="{{ route('dependencies.show', $dependency->id) }}" class="btn btn-info btn-sm">
                                <i class="fas fa-eye"></i> Ver
                            </a>
                            
                            <!-- Botón Editar -->
                            <a href="{{ route('dependencies.edit', $dependency->id) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                            
                            <!-- Botón Eliminar CON CONFIRMACIÓN -->
                            <button type="button" class="btn btn-danger btn-sm" 
                                    onclick="confirmDelete({{ $dependency->id }}, '{{ addslashes($dependency->name) }}')">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                            
                            <!-- Formulario oculto para eliminar -->
                            <form id="delete-form-{{ $dependency->id }}" 
                                  action="{{ route('dependencies.destroy', $dependency->id) }}" 
                                  method="POST" 
                                  style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Script para confirmación de eliminación -->
    <script>
        function confirmDelete(dependencyId, dependencyName) {
            if (confirm(`¿Está seguro de que desea eliminar la dependencia "${dependencyName}"?`)) {
                document.getElementById(`delete-form-${dependencyId}`).submit();
            }
        }
    </script>
@endsection