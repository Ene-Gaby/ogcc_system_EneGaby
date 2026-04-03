@extends('layouts.app')

@section('title', 'Ver Proceso - ' . $acquisitionProcess->name)

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Detalles del Proceso de Contratación</h1>
            <a href="{{ route('acquisition-processes.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
        
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-tasks me-2"></i>{{ $acquisitionProcess->name }}
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted">Información Básica</h6>
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Nombre:</th>
                                <td>{{ $acquisitionProcess->name }}</td>
                            </tr>
                            <tr>
                                <th>Descripción:</th>
                                <td>{{ $acquisitionProcess->description ?? 'No disponible' }}</td>
                            </tr>
                            <tr>
                                <th>Año Fiscal:</th>
                                <td>{{ $acquisitionProcess->fiscal_year }}</td>
                            </tr>
                            <tr>
                                <th>Estado:</th>
                                <td>
                                    <span class="badge bg-{{ $acquisitionProcess->status === 'open' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($acquisitionProcess->status) }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Fechas y Auditoría</h6>
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Fecha Inicio:</th>
                                <td>{{ $acquisitionProcess->start_date->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <th>Fecha Cierre:</th>
                                <td>{{ $acquisitionProcess->end_date->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <th>Creado:</th>
                                <td>{{ $acquisitionProcess->start_date ? $acquisitionProcess->start_date->format('d/m/Y') : 'No definida' }}</td>
                            </tr>
                            <tr>
                                <th>Actualizado:</th>
                                <td>{{ $acquisitionProcess->end_date ? $acquisitionProcess->end_date->format('d/m/Y') : 'No definida' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <hr>
                
                <h5 class="mt-4">
                    <i class="fas fa-money-bill-wave me-2"></i>Rubros Asociados
                    <span class="badge badge-info">{{ $acquisitionProcess->rubros()->count() }}</span>
                </h5>
                
                @if($acquisitionProcess->rubros->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-sm">
                            <thead>
                                <tr>
                                    <th>Descripción</th>
                                    <th>Presentación</th>
                                    <th>Precio Unitario</th>
                                    <th>Exento IVA</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($acquisitionProcess->rubros as $rubro)
                                    <tr>
                                        <td>{{ $rubro->description }}</td>
                                        <td>{{ $rubro->presentation }}</td>
                                        <td>{{ number_format($rubro->unit_price, 2, ',', '.') }} Bs.</td>
                                        <td>
                                            <span class="badge bg-{{ $rubro->iva_exempt ? 'success' : 'warning' }}">
                                                {{ $rubro->iva_exempt ? 'Sí' : 'No' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>No hay rubros asociados a este proceso.
                    </div>
                @endif
            </div>
            <div class="card-footer">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('acquisition-processes.edit', $acquisitionProcess->id) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Editar Proceso
                    </a>
                    <a href="{{ route('acquisition-processes.presupuesto.consolidado', $acquisitionProcess->id) }}" 
                       class="btn btn-primary">
                        <i class="fas fa-file-invoice-dollar"></i> Presupuesto Consolidado
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection