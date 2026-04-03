@extends('layouts.app')

@section('title', '¿Desea Participar?')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h3>Proceso: {{ $process->name }}</h3>
            <p><strong>Año Fiscal:</strong> {{ $process->fiscal_year }} | <strong>Estado:</strong> {{ ucfirst($process->status) }}</p>
        </div>
        <div class="card-body text-center">
            <h4>¿Cómo desea proceder con este proceso?</h4>

            <!-- Opción 1: Participar -->
            <div class="mb-4 p-4 border rounded bg-light">
                <h5 class="text-success"><i class="fas fa-check-circle"></i> Participar</h5>
                <p>Enviar una solicitud con los rubros que requiere su dependencia.</p>
                <a href="{{ route('requests.create.details', $process->id) }}"
                   class="btn btn-success btn-lg">
                    <i class="fas fa-check-circle"></i> Registrar Participación
                </a>
            </div>

            <!-- Opción 2: No Participar -->
            <div class="p-4 border rounded bg-light">
                <h5 class="text-danger"><i class="fas fa-times-circle"></i> No Participar</h5>
                <p>Registrar formalmente que su dependencia no participará en este proceso.</p>
            <!-- Cambiamos el botón por un enlace -->
            <a href="{{ route('requests.show.confirm.non.participation', $process->id) }}" class="btn btn-danger btn-lg">
            <i class="fas fa-file-signature"></i> Registrar No Participación
            </a>
            </div>
        </div>
    </div>
</div>

<!-- Modal para No Participación -->
<div class="modal fade" id="confirmNotParticipateModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar No Participación</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('requests.confirm.non.participation', ['acquisitionProcess' => $process->id]) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Por favor, ingrese el número de oficio oficial que justifica la no participación:</p>
                    <div class="form-group">
                        <label for="official_letter_number">Número de Oficio</label>
                        <input type="text" name="official_letter_number" id="official_letter_number" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Confirmar No Participación</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection