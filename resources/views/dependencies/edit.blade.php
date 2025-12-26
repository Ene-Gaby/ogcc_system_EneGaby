@extends('layouts.app')

@section('title', 'Editar Dependencia - ' . $dependency->name)

@section('content')
    <div class="container">
        <h1>Editar Dependencia: {{ $dependency->name }}</h1>
        <form action="{{ route('dependencies.update', $dependency->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="name" class="form-label">Nombre</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $dependency->name) }}" required>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Teléfono</label>
                <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone', $dependency->phone) }}">
            </div>
            <div class="mb-3">
                <label for="responsible" class="form-label">Responsable</label>
                <input type="text" name="responsible" id="responsible" class="form-control" value="{{ old('responsible', $dependency->responsible) }}" required>
            </div>
            <div class="mb-3">
                <label for="organizational_structure" class="form-label">Estructura Organizativa</label>
                <input type="text" name="organizational_structure" id="organizational_structure" class="form-control" value="{{ old('organizational_structure', $dependency->organizational_structure) }}" required>
            </div>
            <div class="mb-3">
                <label for="user_id" class="form-label">Usuario Administrador</label>
                <select name="user_id" id="user_id" class="form-select" required>
                    <option value="">Seleccione un usuario</option>
                    @foreach(\App\Models\User::all() as $user)
                        <option value="{{ $user->id }}" {{ $dependency->user_id === $user->id ? 'selected' : '' }}>
                            {{ $user->name }} ({{ $user->username }})
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Actualizar</button>
            <a href="{{ route('dependencies.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
@endsection