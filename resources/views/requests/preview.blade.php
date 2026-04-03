@extends('layouts.app')

@section('title', 'Vista Previa de Solicitud')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h3 class="mb-0">Vista Previa de Solicitud</h3>
        </div>
        
        <div class="card-body">
            <!-- Información del Proceso -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5>Proceso</h5>
                    <p><strong>{{ $request->acquisitionProcess->name }}</strong></p>
                    <p><strong>Año Fiscal:</strong> {{ $request->acquisitionProcess->fiscal_year }}</p>
                    <p><strong>Dependencia:</strong> {{ $request->dependency->name ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6 text-right">
                    <h5>Estado</h5>
                    <span class="badge badge-warning">Borrador</span>
                    <p class="mt-2"><small>Revise los PDFs antes de enviar</small></p>
                </div>
            </div>
            
            <!-- Tabla de Rubros Solicitados -->
            <div class="table-responsive mb-4">
                <table class="table table-striped table-bordered">
                    <thead class="thead-dark">
                         
                            <th>N°</th>
                            <th>Rubro</th>
                            <th>Presentación</th>
                            <th>Cantidad</th>
                            <th>Precio Unitario</th>
                            <th>IVA %</th>
                            <th>IVA</th>
                            <th>Total</th>
                            <th>Código ONAPRE</th>
                            <th>Código ONU</th>
                         
                    </thead>
                    <tbody>
                        @php 
                            $counter = 1; 
                            $totalGeneral = 0;
                        @endphp
                        
                        @forelse($request->requestDetails as $detail)
                            @php
                                $rubro = $detail->acquisitionProcessRubro->rubro;
                                $subtotal = $detail->quantity * $detail->unit_price_at_request_time;
                                $iva = $subtotal * 0.16;
                                $total = $subtotal + $iva;
                                $totalGeneral += $total;
                            @endphp
                            <tr>
                                <td class="text-center">{{ $counter++ }}</td>
                                <td>{{ $rubro->description ?? 'N/A' }}</td>
                                <td>{{ $rubro->presentation ?? '-' }}</td>
                                <td class="text-right">{{ number_format($detail->quantity, 0, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($detail->unit_price_at_request_time, 2, ',', '.') }}</td>
                                <td class="text-center">16,00%</td>
                                <td class="text-right">{{ number_format($iva, 2, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($total, 2, ',', '.') }}</td>
                                <td class="text-center">{{ $rubro->onapre_code ?? '-' }}</td>
                                <td class="text-center">{{ $rubro->onu_code ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-danger">
                                    <strong>No hay rubros seleccionados. Por favor, regrese y seleccione al menos un rubro.</strong>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($request->requestDetails->count() > 0)
                    <tfoot>
                        <tr class="table-active font-weight-bold">
                            <td colspan="8" class="text-right">Total General de la Solicitud:</td>
                            <td colspan="2" class="text-right">{{ number_format($totalGeneral, 2, ',', '.') }} Bs.</td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
            
            <!-- PDFs Preview Section -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card border-info">
                        <div class="card-header bg-info text-white">
                            <i class="fas fa-file-pdf"></i> Presupuesto Base Individual
                        </div>
                        <div class="card-body text-center">
                            <p>Vista preliminar del presupuesto detallado</p>
                            <a href="{{ route('requests.presupuesto.individual', $request->id) }}" 
                               class="btn btn-info" 
                               target="_blank">
                                <i class="fas fa-eye"></i> Ver PDF Preliminar
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-success">
                        <div class="card-header bg-success text-white">
                            <i class="fas fa-file-pdf"></i> Comprobante de Participación
                        </div>
                        <div class="card-body text-center">
                            <p>Certificado de participación en el proceso</p>
                            <a href="{{ route('requests.comprobante', $request->id) }}" 
                               class="btn btn-success" 
                               target="_blank">
                                <i class="fas fa-eye"></i> Ver PDF Preliminar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Botones de Acción -->
            @if($request->requestDetails->count() > 0)
            <div class="alert alert-warning">
                <i class="fas fa-info-circle"></i> 
                Una vez que envíe la solicitud, no podrá modificar los datos. 
                Ambos PDFs quedarán guardados como definitivos.
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <a href="{{ route('requests.edit.details', $request->id) }}" 
                       class="btn btn-secondary btn-lg">
                        <i class="fas fa-edit"></i> Editar Cantidades
                    </a>
                </div>
                <div class="col-md-6 text-right">
                    <form action="{{ route('requests.submit', $request->id) }}" 
                          method="POST" 
                          onsubmit="return confirm('¿Está seguro de enviar la solicitud? Esta acción no se puede deshacer.')">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-check-circle"></i> Enviar Solicitud
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@if($request->requestDetails->count() == 0)
<script>
    setTimeout(function() {
        alert('No hay rubros seleccionados. Será redirigido para seleccionar rubros.');
        window.location.href = "{{ route('requests.create.details', $request->acquisition_process_id) }}";
    }, 3000);
</script>
@endif
@endsection