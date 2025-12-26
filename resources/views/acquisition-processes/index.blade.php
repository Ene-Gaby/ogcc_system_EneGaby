@extends('layouts.app')

@section('title', 'Gestionar Procesos de Contratación')

@section('content')
    <div class="container">
        <h1>Gestionar Procesos de Contratación</h1>
        @can('create', \App\Models\AcquisitionProcess::class)
            <a href="{{ route('acquisition-processes.create') }}" class="btn btn-primary mb-3">Crear Nuevo Proceso</a>
        @endcan
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
                        <td><span class="badge badge-{{ $process->status === 'open' ? 'success' : 'secondary' }}">{{ ucfirst($process->status) }}</span></td>
                        <td>{{ $process->start_date->format('d/m/Y') }} - {{ $process->end_date->format('d/m/Y') }}</td>
                        <td>
                            <a href="{{ route('acquisition-processes.show', $process->id) }}" class="btn btn-sm btn-info">Ver</a>
                            <a href="{{ route('acquisition-processes.edit', $process->id) }}" class="btn btn-sm btn-primary">Editar</a>
                            <form action="{{ route('acquisition-processes.destroy', $process->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Está seguro de que desea eliminar este proceso?')">Eliminar</button>
                            </form>
                            <a href="{{ route('acquisition-processes.presupuesto.consolidado', $process->id) }}" class="btn btn-sm btn-warning">Presupuesto Consolidado</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection