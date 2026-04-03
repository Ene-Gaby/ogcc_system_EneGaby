@extends('layouts.app')

@section('title', 'Mis Solicitudes')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h3 class="mb-0">Mis Solicitudes</h3>
        </div>
        
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
            
            @if($requests->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th>ID</th>
                                <th>Proceso</th>
                                <th>Año Fiscal</th>
                                <th>Fecha Solicitud</th>
                                <th>Total (Bs.)</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($requests as $solicitud)
                                <tr>
                                    <td class="text-center">{{ str_pad($solicitud->id, 8, '0', STR_PAD_LEFT) }}</td>
                                    <td>{{ $solicitud->acquisitionProcess->name ?? 'N/A' }}</td>
                                    <td class="text-center">{{ $solicitud->acquisitionProcess->fiscal_year ?? 'N/A' }}</td>
                                    <td class="text-center">{{ \Carbon\Carbon::parse($solicitud->created_at)->format('d/m/Y H:i') }}</td>
                                    <td class="text-right">{{ number_format($solicitud->total_amount, 2, ',', '.') }}</td>
                                    <td class="text-center">
                                        @if($solicitud->status == 'submitted')
                                            <span class="badge bg-success text-white px-3 py-2" style="background-color: #28a745; color: #ffffff;">
                                                <i class="fas fa-check-circle"></i> Enviada
                                            </span>
                                        @elseif($solicitud->status == 'draft')
                                            <span class="badge bg-warning text-dark px-3 py-2" style="background-color: #ffc107; color: #212529;">
                                                <i class="fas fa-edit"></i> Borrador
                                            </span>
                                        @elseif($solicitud->status == 'not_participating')
                                            <span class="badge bg-secondary text-white px-3 py-2" style="background-color: #6c757d; color: #ffffff;">
                                                <i class="fas fa-times-circle"></i> No Participa
                                            </span>
                                        @else
                                            <span class="badge bg-info text-white px-3 py-2">
                                                {{ ucfirst($solicitud->status) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($solicitud->status == 'submitted')
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('requests.presupuesto.definitivo', $solicitud->id) }}" 
                                                   class="btn btn-sm btn-info" 
                                                   target="_blank"
                                                   title="Descargar Presupuesto">
                                                    <i class="fas fa-file-pdf"></i> Presupuesto
                                                </a>
                                                <a href="{{ route('requests.comprobante.definitivo', $solicitud->id) }}" 
                                                   class="btn btn-sm btn-success" 
                                                   target="_blank"
                                                   title="Descargar Comprobante">
                                                    <i class="fas fa-file-pdf"></i> Comprobante
                                                </a>
                                            </div>
                                        @elseif($solicitud->status == 'draft')
                                            <a href="{{ route('requests.preview', $solicitud->id) }}" 
                                               class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i> Continuar Edición
                                            </a>
                                        @elseif($solicitud->status == 'not_participating')
                                            <a href="{{ route('requests.comprobante.no.participacion', $solicitud->id) }}" 
                                                class="btn btn-sm btn-secondary" 
                                                target="_blank">
                                                <i class="fas fa-file-pdf"></i> Ver Comprobante
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle fa-2x mb-2"></i>
                    <p>No tiene solicitudes registradas.</p>
                    <a href="{{ route('requests.open.processes') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Ver Procesos Disponibles
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection