@extends('layouts.app')

@section('title', 'Confirmar No Participación')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h3>Confirmar No Participación para: {{ $process->name }}</h3>
            <p><strong>Año Fiscal:</strong> {{ $process->fiscal_year }} | <strong>Estado:</strong> {{ ucfirst($process->status) }}</p>
        </div>
        <div class="card-body">
            <form action="{{ route('requests.confirm.non.participation', $process->id) }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="official_letter_number">Número de Oficio *</label>
                    <input type="text" name="official_letter_number" id="official_letter_number" class="form-control" required>
                </div>
                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-danger btn-lg">
                        <i class="fas fa-file-signature"></i> Confirmar No Participación
                    </button>
                    <a href="{{ route('requests.open.processes') }}" class="btn btn-secondary btn-lg">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection