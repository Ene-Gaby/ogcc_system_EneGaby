@extends('layouts.app')

@section('title', 'Procesos Disponibles')

@section('content')
    <div class="container">
        <h1>Procesos Disponibles</h1>
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
                @forelse($openProcesses as $process)
                    <tr>
                        <td>{{ $process->name }}</td>
                        <td>{{ $process->fiscal_year }}</td>
                        <td><span class="badge badge-success">{{ ucfirst($process->status) }}</span></td>
                        <td>{{ $process->start_date->format('d/m/Y') }} - {{ $process->end_date->format('d/m/Y') }}</td>
                        <td>
                            <a href="{{ route('requests.create.for.process', $process->id) }}" class="btn btn-primary">Crear Solicitud</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">No hay procesos disponibles en este momento.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection