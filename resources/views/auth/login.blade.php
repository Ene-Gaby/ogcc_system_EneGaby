@extends('layouts.app')

@section('title', 'Iniciar Sesión')

@section('content')
<div class="d-flex justify-content-center align-items-center" style="min-height: 92vh; background: linear-gradient(135deg, #003366, #004080); width: 100%; margin: 0; padding: 0;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">
                <div class="card shadow-lg border-0 rounded-4 overflow-hidden" ...>
                    <div class="row g-0">
                    <!-- Imagen de la ULA -->
                    <div class="col-md-5 d-none d-md-block">
                        <img src="{{ asset('images/Edif_Administrativo_ULA.jpg') }}" class="img-fluid h-100" alt="Edificio Universidad de Los Andes" style="object-fit: cover;">
                    </div>
                    <!-- Formulario de Login -->
                    <div class="col-md-7 p-4">
                        <div class="text-center mb-4">
                            <img src="{{ asset('images/ula-logo.png') }}" alt="Logo Universidad de Los Andes" class="mb-3" style="height: 60px;">
                            <h4 class="fw-bold">Iniciar Sesión</h4>
                        </div>

                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <!-- Campo Usuario -->
                            <div class="mb-3">
                                <label for="username" class="form-label fw-semibold">Usuario</label>
                                <input id="username" type="text" class="form-control @error('username') is-invalid @enderror" name="username" value="{{ old('username') }}" required autocomplete="username" autofocus placeholder="Ingrese el usuario aquí">
                                @error('username')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <!-- Campo Contraseña -->
                            <div class="mb-3">
                                <label for="password" class="form-label fw-semibold">Contraseña</label>
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="Ingrese la contraseña aquí">
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <!-- Botón Ingresar -->
                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary btn-lg">Ingresar</button>
                            </div>

                            <!-- Enlace Recuperar Contraseña -->
                            <div class="text-center">
                                @if (Route::has('password.request'))
                                    <a class="btn btn-link text-decoration-none" href="{{ route('password.request') }}">
                                        {{ __('Recuperar Contraseña') }}
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Pie de Página -->
            <div class="text-center mt-3">
                <small class="text-white">Universidad de los Andes © Copyright 2025.</small>
            </div>
        </div>
    </div>
</div>
@endsection