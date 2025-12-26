@extends('layouts.app')

@section('title', 'Crear Dependencia')

@section('content')
    <div class="container">
        <h1>Crear Dependencia</h1>
        <form action="{{ route('dependencies.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Nombre</label>
                <input type="text" name="name" id="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Teléfono</label>
                <input type="text" name="phone" id="phone" class="form-control">
            </div>
            <div class="mb-3">
                <label for="responsible" class="form-label">Responsable</label>
                <input type="text" name="responsible" id="responsible" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="organizational_structure" class="form-label">Estructura Organizativa</label>
                <input type="text" name="organizational_structure" id="organizational_structure" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="user_id" class="form-label">Usuario Administrador</label>
                <select name="user_id" id="user_id" class="form-select" required>
                    <option value="">Seleccione un usuario</option>
                    @foreach(\App\Models\User::all() as $user)
                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->username }})</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Guardar</button>
            <a href="{{ route('dependencies.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
@endsection