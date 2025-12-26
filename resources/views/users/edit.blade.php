@extends('layouts.app')

@section('title', 'Editar Usuario - ' . $user->name)

@section('content')
    <div class="container">
        <h1>Editar Usuario: {{ $user->name }}</h1>
        <form action="{{ route('users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="name" class="form-label">Nombre</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $user->name) }}" required>
            </div>
            <div class="mb-3">
                <label for="username" class="form-label">Usuario</label>
                <input type="text" name="username" id="username" class="form-control" value="{{ old('username', $user->username) }}" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $user->email) }}" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Nueva Contraseña (opcional)</label>
                <input type="password" name="password" id="password" class="form-control">
            </div>
            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirmar Nueva Contraseña</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
            </div>
            <div class="mb-3">
                <label for="role" class="form-label">Rol</label>
                <select name="role" id="role" class="form-select" required>
                    <option value="">Seleccione un rol</option>
                    <option value="administrador" {{ $user->role === 'administrador' ? 'selected' : '' }}>Administrador</option>
                    <option value="analista" {{ $user->role === 'analista' ? 'selected' : '' }}>Analista</option>
                    <option value="supervisor" {{ $user->role === 'supervisor' ? 'selected' : '' }}>Supervisor</option>
                    <option value="usuario" {{ $user->role === 'usuario' ? 'selected' : '' }}>Usuario</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Actualizar</button>
            <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
@endsection