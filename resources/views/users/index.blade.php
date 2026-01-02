@extends('layouts.app')

@section('title', 'Gestionar Usuarios')

@section('content')
    <div class="container">
        <h1>Gestionar Usuarios</h1>
        <a href="{{ route('users.create') }}" class="btn btn-primary btn-lg">
            <i class="fas fa-user-plus"></i> Crear Nuevo Usuario
        </a>
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
                           <a href="{{ route('users.show', $user->id) }}" class="btn btn-info btn-sm">
                               <i class="fas fa-eye"></i> Ver
                           </a>
                           <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning btn-sm">
                               <i class="fas fa-edit"></i> Editar
                           </a>
                           <!-- Botón Eliminar CON CONFIRMACIÓN -->
                            <button type="button" class="btn btn-danger btn-sm" 
                                    onclick="confirmDelete({{ $user->id }}, '{{ $user->name }}')">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                            
                            <!-- Formulario oculto para eliminar -->
                            <form id="delete-form-{{ $user->id }}" 
                                  action="{{ route('users.destroy', $user->id) }}" 
                                  method="POST" 
                                  style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Script para confirmación de eliminación -->
    <script>
        function confirmDelete(userId, userName) {
            if (confirm(`¿Está seguro de que desea eliminar al usuario "${userName}"?`)) {
                // Si confirma, envía el formulario de eliminación
                document.getElementById(`delete-form-${userId}`).submit();
            }
        }
    </script>
@endsection