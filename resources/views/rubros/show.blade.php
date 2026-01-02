@extends('layouts.app')

@section('title', 'Ver Rubro - ' . $rubro->description)

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Detalles del Rubro</h1>
            <a href="{{ route('rubros.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
        
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-money-bill-wave me-2"></i>{{ $rubro->description }}
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted">Información Básica</h6>
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Descripción:</th>
                                <td>{{ $rubro->description }}</td>
                            </tr>
                            <tr>
                                <th>Presentación:</th>
                                <td>{{ $rubro->presentation }}</td>
                            </tr>
                            <tr>
                                <th>Precio Unitario:</th>
                                <td>{{ number_format($rubro->unit_price, 2, ',', '.') }} Bs.</td>
                            </tr>
                            <tr>
                                <th>Exento IVA:</th>
                                <td>
                                    <span class="badge bg-{{ $rubro->iva_exempt ? 'success' : 'warning' }}">
                                        {{ $rubro->iva_exempt ? 'Sí' : 'No' }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Códigos y Auditoría</h6>
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Código ONAPRE:</th>
                                <td><code>{{ $rubro->onapre_code }}</code></td>
                            </tr>
                            <tr>
                                <th>Código ONU:</th>
                                <td><code>{{ $rubro->onu_code }}</code></td>
                            </tr>
                            <tr>
                                <th>Creado:</th>
                                <td>{{ $rubro->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Actualizado:</th>
                                <td>{{ $rubro->updated_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('rubros.edit', $rubro->id) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                    <button type="button" class="btn btn-danger" 
                            onclick="confirmDeleteRubro({{ $rubro->id }}, '{{ addslashes($rubro->description) }}')">
                        <i class="fas fa-trash"></i> Eliminar
                    </button>
                    <form id="delete-rubro-form-show-{{ $rubro->id }}" 
                          action="{{ route('rubros.destroy', $rubro->id) }}" 
                          method="POST" 
                          style="display: none;">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmDeleteRubro(rubroId, rubroDescription) {
            if (confirm(`¿Está seguro de que desea eliminar el rubro "${rubroDescription}"?`)) {
                document.getElementById(`delete-rubro-form-show-${rubroId}`).submit();
            }
        }
    </script>
@endsection