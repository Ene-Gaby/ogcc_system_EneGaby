@extends('layouts.app')

@section('title', 'Ver Usuario - ' . $user->name)

@section('content')
    <div class="container">
        <h1>Ver Usuario: {{ $user->name }}</h1>
        <div class="card">
            <div class="card-body">
                <p><strong>Nombre:</strong> {{ $user->name }}</p>
                <p><strong>Usuario:</strong> {{ $user->username }}</p>
                <p><strong>Email:</strong> {{ $user->email }}</p>
                <p><strong>Rol:</strong> {{ ucfirst($user->role) }}</p>
                <p><strong>Creado:</strong> {{ $user->created_at->format('d/m/Y H:i') }}</p>
                <p><strong>Actualizado:</strong> {{ $user->updated_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>
        <a href="{{ route('users.index') }}" class="btn btn-secondary mt-3">Volver</a>
        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-primary mt-3">Editar</a>
    </div>
@endsection