@extends('layouts.app')

@section('title', 'Mis Solicitudes')

@section('content')
    <div class="container">
        <h1 class="mb-4">Mis Solicitudes</h1>
        
        <table class="table table-striped table-hover">
            <thead class="thead-dark">
                <tr>
                    <th>Proceso de Contratación</th>
                    <th>Estado</th>
                    <th>Participa</th>
                    <th>Total</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $request)
                    <tr>
                        <td>{{ $request->acquisitionProcess->name }}</td>
                        <td>
                            <span class="badge badge-{{ $request->status === 'submitted' ? 'success' : ($request->status === 'not_participating' ? 'danger' : 'warning') }}">
                                {{ ucfirst($request->status) }}
                            </span>
                        </td>
                        <td>{{ $request->participates ? 'Sí' : 'No' }}</td>
                        <td>{{ number_format($request->total_amount, 2, ',', '.') }}</td>
                        <td>{{ $request->created_at->format('d/m/Y') }}</td>
                        <td>
                            @if(in_array($request->status, ['draft', 'pending_decision']))
                                <a href="{{ route('requests.edit.details', $request->id) }}" class="btn btn-primary btn-sm">Editar</a>
                                
                                <form action="{{ route('requests.destroy', $request->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Está seguro de que desea eliminar esta solicitud?')">Eliminar</button>
                                </form>

                                <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#confirmParticipateModal{{ $request->id }}">
                                    Participar
                                </button>
                                <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#confirmNotParticipateModal{{ $request->id }}">
                                    No Participar
                                </button>
                            @endif

                            @if($request->status === 'submitted' && $request->participates)
                                <a href="{{ route('requests.presupuesto.individual', $request->id) }}" class="btn btn-info btn-sm" target="_blank">Presupuesto</a>
                            @endif

                            @if($request->status === 'submitted' || $request->status === 'not_participating')
                                <a href="{{ route('requests.comprobante', $request->id) }}" class="btn btn-secondary btn-sm" target="_blank">Comprobante</a>
                            @endif
                        </td>
                    </tr>

                    @include('requests.partials.modals_participation', ['request' => $request]) 
                    {{-- Nota: Si no usas un partial, aquí abajo siguen los modales que tenías --}}
                    
                    <div class="modal fade" id="confirmParticipateModal{{ $request->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Confirmar Participación</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form action="{{ route('requests.confirm.participation', $request->id) }}" method="POST">
                                    @csrf
                                    <div class="modal-body">
                                        <p>¿Está seguro de participar en <strong>{{ $request->acquisitionProcess->name }}</strong>?</p>
                                        <label>Número de Oficio (RN-06):</label>
                                        <input type="text" name="official_letter_number" class="form-control" required>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                        <button type="submit" class="btn btn-success">Confirmar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="confirmNotParticipateModal{{ $request->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Confirmar No Participación</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form action="{{ route('requests.confirm.non.participation', $request->id) }}" method="POST">
                                    @csrf
                                    <div class="modal-body">
                                        <p>¿Está seguro de NO participar en <strong>{{ $request->acquisitionProcess->name }}</strong>?</p>
                                        <label>Número de Oficio (RN-06):</label>
                                        <input type="text" name="official_letter_number" class="form-control" required>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                        <button type="submit" class="btn btn-danger">Confirmar No Participación</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                @empty
                    <tr>
                        <td colspan="6" class="text-center">No has creado ninguna solicitud aún.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection