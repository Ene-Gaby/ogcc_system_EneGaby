@extends('layouts.app')

@section('title', 'Gestionar Rubros')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Gestionar Rubros</h1>
            <a href="{{ route('rubros.create') }}" class="btn btn-primary btn-lg">
                <i class="fas fa-money-bill-wave"></i> Crear Nuevo Rubro
            </a>
        </div>
        
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Descripción</th>
                    <th>Presentación</th>
                    <th>Precio Unitario</th>
                    <th>Exento IVA</th>
                    <th>Código ONAPRE</th>
                    <th>Código ONU</th>
                    <th>Proceso Asociado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rubros as $rubro)
                    <tr>
                        <td>{{ $rubro->description }}</td>
                        <td>{{ $rubro->presentation }}</td>
                        <td>{{ number_format($rubro->unit_price, 2, ',', '.') }} Bs.</td>
                        <td>
                            <span class="badge bg-{{ $rubro->iva_exempt ? 'success' : 'warning' }}">
                                {{ $rubro->iva_exempt ? 'Sí' : 'No' }}
                            </span>
                        </td>
                        <td><code>{{ $rubro->onapre_code }}</code></td>
                        <td><code>{{ $rubro->onu_code }}</code></td>
                        <td>{{ $rubro->acquisitionProcess ? $rubro->acquisitionProcess->name : 'No asignado' }}</td>
                        <td>
                            <!-- Botón Ver -->
                            <a href="{{ route('rubros.show', $rubro->id) }}" class="btn btn-info btn-sm">
                                <i class="fas fa-eye"></i> Ver
                            </a>
                            
                            <!-- Botón Editar -->
                            <a href="{{ route('rubros.edit', $rubro->id) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                            
                            <!-- Botón Eliminar CON CONFIRMACIÓN -->
                            <button type="button" class="btn btn-danger btn-sm" 
                                    onclick="confirmDeleteRubro({{ $rubro->id }}, '{{ addslashes($rubro->description) }}')">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                            
                            <!-- Formulario oculto para eliminar -->
                            <form id="delete-rubro-form-{{ $rubro->id }}" 
                                  action="{{ route('rubros.destroy', $rubro->id) }}" 
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
        function confirmDeleteRubro(rubroId, rubroDescription) {
            if (confirm(`¿Está seguro de que desea eliminar el rubro "${rubroDescription}"?`)) {
                document.getElementById(`delete-rubro-form-${rubroId}`).submit();
            }
        }
    </script>
    
    <!-- Estilos para los botones -->
    <style>
        .btn {
            margin: 2px;
        }
        .btn-sm {
            min-width: 70px;
        }
        .badge {
            font-size: 0.85em;
            padding: 0.35em 0.65em;
        }
        .table td {
            vertical-align: middle;
        }
        code {
            background-color: #f8f9fa;
            padding: 2px 6px;
            border-radius: 4px;
            font-family: monospace;
        }
    </style>
@endsection