@extends('layouts.app')

@section('title', 'Inicio')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2>Bienvenido, {{ Auth::user()->name }}</h2>
                </div>
                <div class="card-body">
                    <p>Has iniciado sesión correctamente.</p>
                    <p>Tu rol es: <strong>{{ ucfirst(Auth::user()->role) }}</strong></p>
                    <!-- Opciones basadas en el rol -->
                    @if(Auth::user()->role === 'usuario')
                        <a href="{{ route('requests.open.processes') }}" class="btn btn-primary">Ver Procesos Disponibles</a>
                        <a href="{{ route('requests.my.index') }}" class="btn btn-info">Mis Solicitudes</a>
                    @elseif(in_array(Auth::user()->role, ['analista', 'supervisor']))
                        <a href="{{ route('requests.index') }}" class="btn btn-primary">Ver Todas las Solicitudes</a>
                        <a href="{{ route('acquisition-processes.index') }}" class="btn btn-info">Gestionar Procesos</a>
                    @elseif(Auth::user()->role === 'administrador')
                        <a href="{{ route('users.index') }}" class="btn btn-primary">Gestionar Usuarios</a>
                        <a href="{{ route('dependencies.index') }}" class="btn btn-info">Gestionar Dependencias</a>
                        <a href="{{ route('acquisition-processes.index') }}" class="btn btn-success">Gestionar Procesos</a>
                        <a href="{{ route('rubros.index') }}" class="btn btn-warning">Gestionar Rubros</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection