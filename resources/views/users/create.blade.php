@extends('layouts.app')

@section('title', 'Crear Usuario')

@section('content')
    <div class="container">
        <h1>Crear Usuario</h1>
        <form action="{{ route('users.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Nombre</label>
                <input type="text" name="name" id="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="username" class="form-label">Usuario</label>
                <input type="text" name="username" id="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="role" class="form-label">Rol</label>
                <select name="role" id="role" class="form-select" required>
                    <option value="">Seleccione un rol</option>
                    <option value="administrador">Administrador</option>
                    <option value="analista">Analista</option>
                    <option value="supervisor">Supervisor</option>
                    <option value="usuario">Usuario</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Guardar</button>
            <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
@endsection