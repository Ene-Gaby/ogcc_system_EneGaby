@extends('layouts.app')

@section('title', 'Procesos Abiertos')

@section('content')
    <div class="container">
        <h1 class="mb-4">Procesos de Contratación Abiertos</h1>
        @if($openProcesses->count() > 0)
            <div class="row">
                @foreach($openProcesses as $process)
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">{{ $process->name }}</h5>
                                <p class="card-text"><strong>Año Fiscal:</strong> {{ $process->fiscal_year }}</p>
                                <p class="card-text"><strong>Descripción:</strong> {{ Str::limit($process->description, 150) }}</p>
                                <p class="card-text"><small class="text-muted">Fechas: {{ $process->start_date->format('d/m/Y') }} - {{ $process->end_date->format('d/m/Y') }}</small></p>
                            </div>
                            <div class="card-footer">
                                <a href="{{ route('requests.create.for.process', $process->id) }}" class="btn btn-primary btn-block">Solicitar Rubros</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="alert alert-info">
                <p>No hay procesos de contratación abiertos disponibles en este momento.</p>
            </div>
        @endif
    </div>
@endsection