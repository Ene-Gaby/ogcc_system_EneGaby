@extends('layouts.app')

@section('title', 'Gestionar Usuarios')

@section('content')
    <div class="container">
        <h1>Gestionar Usuarios</h1>
        <a href="{{ route('users.create') }}" class="btn btn-primary mb-3">Crear Nuevo Usuario</a>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Usuario</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->username }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ ucfirst($user->role) }}</td>
                        <td>
                           <a href="{{ route('users.show', $user->id) }}" class="btn btn-sm btn-info">Ver</a>
                           <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-primary">Editar</a>
                           <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display:inline;">
                               @csrf
                               @method('DELETE')
                               <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Está seguro de que desea eliminar este usuario?')">Eliminar</button>
                           </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection