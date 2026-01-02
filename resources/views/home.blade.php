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
                <div class="card-body text-center">
                    <p>Has iniciado sesión correctamente.</p>
                    <p>Tu rol es: <strong>{{ ucfirst(Auth::user()->role) }}</strong></p>
                    
                    <img src="{{ asset('images/RectoradoULA.png') }}" alt="Rectorado de la Universidad de Los Andes" class="img-fluid mb-4 rounded shadow-sm">
                    
                    <div class="d-flex justify-content-center">
                        <a href="#" class="btn btn-secondary shadow-sm" target="_blank">
                            <i class="fas fa-book me-2"></i> Manual de Usuario
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection