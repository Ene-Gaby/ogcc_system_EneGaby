@extends('layouts.app')

@section('title', 'Mis Solicitudes')

@section('content')
    <div class="container">
        <h1 class="mb-4">Mis Solicitudes</h1>
        @if($requests->count() > 0)
            <table class="table table-striped table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>Proceso</th>
                        <th>Estado</th>
                        <th>Participa</th>
                        <th>Total</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($requests as $request)
                        <tr>
                            <td>{{ $request->acquisitionProcess->name }}</td>
                            <td><span class="badge badge-{{ $request->status === 'submitted' ? 'success' : ($request->status === 'not_participating' ? 'danger' : 'warning') }}">{{ ucfirst($request->status) }}</span></td>
                            <td>{{ $request->participates ? 'Sí' : 'No' }}</td>
                            <td>{{ number_format($request->total_amount, 2, ',', '.') }}</td>
                            <td>{{ $request->created_at->format('d/m/Y') }}</td>
                            <td>
                                @if(in_array($request->status, ['draft', 'pending_decision']))
                                    <a href="{{ route('requests.edit.details', $request->id) }}" class="btn btn-primary btn-sm">Editar</a>
                                    <!-- Botón para confirmar participación -->
                                    <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#confirmParticipateModal{{ $request->id }}">
                                        Participar
                                    </button>
                                    <!-- Botón para confirmar no participación -->
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

                        <!-- Modal Confirmar Participación -->
                        <div class="modal fade" id="confirmParticipateModal{{ $request->id }}" tabindex="-1" role="dialog" aria-labelledby="confirmParticipateModalLabel{{ $request->id }}" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="confirmParticipateModalLabel{{ $request->id }}">Confirmar Participación</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <form action="{{ route('requests.confirm.participation', $request->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-body">
                                            <p>¿Está seguro de que desea PARTICIPAR en el proceso <strong>{{ $request->acquisitionProcess->name }}</strong>?</p>
                                            <label for="official_letter_number_participate_{{ $request->id }}">Número de Oficio de Participación (RN-06):</label>
                                            <input type="text" name="official_letter_number" id="official_letter_number_participate_{{ $request->id }}" class="form-control" required>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                            <button type="submit" class="btn btn-success">Confirmar Participación</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Confirmar No Participación -->
                        <div class="modal fade" id="confirmNotParticipateModal{{ $request->id }}" tabindex="-1" role="dialog" aria-labelledby="confirmNotParticipateModalLabel{{ $request->id }}" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="confirmNotParticipateModalLabel{{ $request->id }}">Confirmar No Participación</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <form action="{{ route('requests.confirm.non.participation', $request->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-body">
                                            <p>¿Está seguro de que desea NO PARTICIPAR en el proceso <strong>{{ $request->acquisitionProcess->name }}</strong>?</p>
                                            <label for="official_letter_number_not_participate_{{ $request->id }}">Número de Oficio de No Participación (RN-06):</label>
                                            <input type="text" name="official_letter_number" id="official_letter_number_not_participate_{{ $request->id }}" class="form-control" required>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                            <button type="submit" class="btn btn-danger">Confirmar No Participación</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="alert alert-info">
                <p>Aún no ha creado ninguna solicitud.</p>
            </div>
        @endif
    </div>
@endsection