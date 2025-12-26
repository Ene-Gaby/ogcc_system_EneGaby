@extends('layouts.app')

@section('title', 'Ver Todas las Solicitudes')

@section('content')
    <div class="container">
        <h1>Ver Todas las Solicitudes</h1>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Dependencia</th>
                    <th>Proceso de Contratación</th>
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
                        <td>{{ $request->dependency->name }}</td>
                        <td>{{ $request->acquisitionProcess->name }}</td>
                        <td><span class="badge badge-{{ $request->status === 'submitted' ? 'success' : ($request->status === 'not_participating' ? 'danger' : 'warning') }}">{{ ucfirst($request->status) }}</span></td>
                        <td>{{ $request->participates ? 'Sí' : 'No' }}</td>
                        <td>{{ number_format($request->total_amount, 2, ',', '.') }}</td>
                        <td>{{ $request->created_at->format('d/m/Y') }}</td>
                        <td>
                            <a href="{{ route('requests.show', $request->id) }}" class="btn btn-sm btn-info">Ver</a>
                            <a href="{{ route('requests.edit', $request->id) }}" class="btn btn-sm btn-primary">Editar</a>
                            <form action="{{ route('requests.destroy', $request->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Está seguro de que desea eliminar esta solicitud?')">Eliminar</button>
                            </form>
                            <a href="{{ route('requests.comprobante', $request->id) }}" class="btn btn-sm btn-secondary" target="_blank">Comprobante</a>
                            @if($request->participates)
                                <a href="{{ route('requests.presupuesto.individual', $request->id) }}" class="btn btn-sm btn-warning" target="_blank">Presupuesto</a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection