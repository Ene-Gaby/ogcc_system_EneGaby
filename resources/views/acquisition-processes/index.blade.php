@extends('layouts.app')

@section('title', 'Gestionar Procesos de Contratación')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Gestionar Procesos de Contratación</h1>
            @can('create', \App\Models\AcquisitionProcess::class)
                <a href="{{ route('acquisition-processes.create') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-tasks"></i> Crear Nuevo Proceso
                </a>
            @endcan
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
                    <th>Nombre</th>
                    <th>Año Fiscal</th>
                    <th>Estado</th>
                    <th>Fechas</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($acquisitionProcesses as $process)
                    <tr>
                        <td>{{ $process->name }}</td>
                        <td>{{ $process->fiscal_year }}</td>
                        <td>
                            <span class="badge bg-{{ $process->status === 'open' ? 'success' : 'secondary' }}">
                                {{ ucfirst($process->status) }}
                            </span>
                        </td>
                        <td>{{ $process->start_date->format('d/m/Y') }} - {{ $process->end_date->format('d/m/Y') }}</td>
                        <td>
                            <!-- Botón Ver -->
                            <a href="{{ route('acquisition-processes.show', $process->id) }}" class="btn btn-info btn-sm">
                                <i class="fas fa-eye"></i> Ver
                            </a>
                            
                            <!-- Botón Editar -->
                            <a href="{{ route('acquisition-processes.edit', $process->id) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                            
                            <!-- Botón Eliminar CON CONFIRMACIÓN MEJORADA -->
                            <button type="button" class="btn btn-danger btn-sm" 
                                    onclick="confirmDeleteProcess({{ $process->id }}, '{{ addslashes($process->name) }}')">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                            
                            <!-- Botón Presupuesto Consolidado -->
                            <a href="{{ route('acquisition-processes.presupuesto.consolidado', $process->id) }}" 
                               class="btn btn-sm btn-primary">
                                <i class="fas fa-file-invoice-dollar"></i> Presupuesto
                            </a>
                            
                            <!-- Formulario oculto para eliminar -->
                            <form id="delete-process-form-{{ $process->id }}" 
                                  action="{{ route('acquisition-processes.destroy', $process->id) }}" 
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
        function confirmDeleteProcess(processId, processName) {
            if (confirm(`¿Está seguro de que desea eliminar el proceso "${processName}"?`)) {
                document.getElementById(`delete-process-form-${processId}`).submit();
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
    </style>
@endsection