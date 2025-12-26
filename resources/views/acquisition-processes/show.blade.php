@extends('layouts.app')

@section('title', 'Ver Proceso - ' . $acquisitionProcess->name)

@section('content')
    <div class="container">
        <h1>Ver Proceso: {{ $acquisitionProcess->name }}</h1>
        <div class="card">
            <div class="card-body">
                <p><strong>Nombre:</strong> {{ $acquisitionProcess->name }}</p>
                <p><strong>Descripción:</strong> {{ $acquisitionProcess->description ?? 'No disponible' }}</p>
                <p><strong>Año Fiscal:</strong> {{ $acquisitionProcess->fiscal_year }}</p>
                <p><strong>Estado:</strong> <span class="badge badge-{{ $acquisitionProcess->status === 'open' ? 'success' : 'secondary' }}">{{ ucfirst($acquisitionProcess->status) }}</span></p>
                <p><strong>Fechas:</strong> {{ $acquisitionProcess->start_date->format('d/m/Y') }} - {{ $acquisitionProcess->end_date->format('d/m/Y') }}</p>
                <p><strong>Creado:</strong> {{ $acquisitionProcess->created_at->format('d/m/Y H:i') }}</p>
                <p><strong>Actualizado:</strong> {{ $acquisitionProcess->updated_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>
        <h3 class="mt-4">Rubros Asociados</h3>
        @if($acquisitionProcess->rubros->count() > 0)
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Descripción</th>
                        <th>Presentación</th>
                        <th>Precio</th>
                        <th>Exento IVA</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($acquisitionProcess->rubros as $rubro)
                        <tr>
                            <td>{{ $rubro->description }}</td>
                            <td>{{ $rubro->presentation }}</td>
                            <td>{{ number_format($rubro->unit_price, 2, ',', '.') }}</td>
                            <td>{{ $rubro->iva_exempt ? 'Sí' : 'No' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>No hay rubros asociados a este proceso.</p>
        @endif
        <a href="{{ route('acquisition-processes.index') }}" class="btn btn-secondary mt-3">Volver</a>
        <a href="{{ route('acquisition-processes.edit', $acquisitionProcess->id) }}" class="btn btn-primary mt-3">Editar</a>
    </div>
@endsection