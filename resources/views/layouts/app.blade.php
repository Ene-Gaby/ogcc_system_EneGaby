<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}?v={{ time() }}" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

</head>
<body>
    <div id="app">
       <nav class="navbar navbar-expand-md navbar-dark shadow-sm bg-primary">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    <img src="{{ asset('images/big_logo.png') }}" alt="Universidad de Los Andes" style="height: 40px;">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @else
                            <!-- Campana de notificaciones -->
                             <li class="nav-item">
                             <a class="nav-link" href="#">
                             <i class="fas fa-bell"></i> <!-- Icono de campana -->
                             </a>
                             </li>

                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                     <!-- Actualizar Clave -->
                                    <a class="dropdown-item" href="{{ route('password.change') }}">
                                        <i class="fas fa-key me-2"></i>Actualizar Clave
                                    </a> 
                                   
                                   <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt me-2"></i>{{ __('Cerrar Sesión') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main>
    @auth
        @php
            // Función helper para determinar si un enlace está activo
            function isActiveRoute($routeName) {
                return request()->routeIs($routeName) ? 'active' : '';
            }
            
            // Determinar el rol del usuario
            $userRole = Auth::user()->role;
        @endphp
        
        <div class="container-fluid">
            <div class="row">
                <!-- SIDEBAR MEJORADO -->
                <div class="col-md-2 bg-primary text-white" style="min-height: 92vh;">
    <div class="p-3">
        <!-- Panel (Título con estilo mejorado) -->
        <div class="panel-title">
            <a href="{{ route('home') }}" class="nav-link text-white d-flex align-items-center p-0 fw-bold">
                <i class="fas fa-file-contract me-2"></i>
                Sistema OGCC
            </a>
        </div>
        
        <!-- Gestión (para analista y administrador) -->
        @if(in_array($userRole, ['administrador', 'analista']))
            <div class="sidebar-section-title">Gestión</div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link text-white d-flex align-items-center py-2 px-3 sidebar-nav-link {{ isActiveRoute('acquisition-processes.*') }}" 
                       href="{{ route('acquisition-processes.index') }}">
                        <i class="fas fa-tasks me-3"></i>
                        Gestionar Procesos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white d-flex align-items-center py-2 px-3 sidebar-nav-link {{ isActiveRoute('rubros.*') }}" 
                       href="{{ route('rubros.index') }}">
                        <i class="fas fa-money-bill-wave me-3"></i>
                        Gestionar Rubros
                    </a>
                </li>
            </ul>
            <div class="sidebar-divider"></div>
        @endif
        
        <!-- Consultas (para supervisor, analista y administrador) -->
        @if(in_array($userRole, ['supervisor', 'administrador', 'analista']))
            <div class="sidebar-section-title">Consultas</div>
            <ul class="nav flex-column">
                @if($userRole === 'supervisor')
                    <li class="nav-item">
                        <a class="nav-link text-white d-flex align-items-center py-2 px-3 sidebar-nav-link {{ isActiveRoute('acquisition-processes.*') }}" 
                           href="{{ route('acquisition-processes.index') }}">
                            <i class="fas fa-eye me-3"></i>
                            Ver Procesos
                        </a>
                    </li>
                @endif
                <li class="nav-item">
                    <a class="nav-link text-white d-flex align-items-center py-2 px-3 sidebar-nav-link" 
                       href="#">
                        <i class="fas fa-chart-bar me-3"></i>
                        Reportes (Próximamente)
                    </a>
                </li>
            </ul>
            <div class="sidebar-divider"></div>
        @endif
        
        <!-- Administración (solo para administrador) -->
        @if($userRole === 'administrador')
            <div class="sidebar-section-title">Administración</div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link text-white d-flex align-items-center py-2 px-3 sidebar-nav-link {{ isActiveRoute('users.*') }}" 
                       href="{{ route('users.index') }}">
                        <i class="fas fa-users me-3"></i>
                        Gestionar Usuarios
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white d-flex align-items-center py-2 px-3 sidebar-nav-link {{ isActiveRoute('dependencies.*') }}" 
                       href="{{ route('dependencies.index') }}">
                        <i class="fas fa-building me-3"></i>
                        Gestionar Dependencias
                    </a>
                </li>
            </ul>
            <div class="sidebar-divider"></div>
        @endif
        
        <!-- Solicitudes -->
        <div class="sidebar-section-title">Solicitudes</div>
        <ul class="nav flex-column">
            @if(in_array($userRole, ['administrador', 'analista', 'supervisor']))
                <li class="nav-item">
                    <a class="nav-link text-white d-flex align-items-center py-2 px-3 sidebar-nav-link {{ isActiveRoute('requests.index') }}" 
                       href="{{ route('requests.index') }}">
                        <i class="fas fa-list-alt me-3"></i>
                        Ver Todas las Solicitudes
                    </a>
                </li>
            @endif
            
            @if($userRole === 'usuario')
                <li class="nav-item">
                    <a class="nav-link text-white d-flex align-items-center py-2 px-3 sidebar-nav-link {{ isActiveRoute('requests.open.processes') }}" 
                       href="{{ route('requests.open.processes') }}">
                        <i class="fas fa-folder-open me-3"></i>
                        Ver Procesos Disponibles
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white d-flex align-items-center py-2 px-3 sidebar-nav-link {{ isActiveRoute('requests.my.*') }}" 
                       href="{{ route('requests.my.index') }}">
                        <i class="fas fa-file-alt me-3"></i>
                        Mis Solicitudes
                    </a>
                </li>
            @endif
        </ul>
        
        <div class="sidebar-divider"></div>
        
        <!-- Cuenta -->
        <div class="sidebar-section-title">Cuenta</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link text-white d-flex align-items-center py-2 px-3 sidebar-nav-link {{ isActiveRoute('password.change') }}" 
                   href="{{ route('password.change') }}">
                    <i class="fas fa-key me-3"></i>
                    Actualizar Clave
                </a>
            </li>
        </ul>
        
        <!-- Ayuda -->
        <div class="sidebar-section-title">Ayuda</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link text-white d-flex align-items-center py-2 px-3 sidebar-nav-link" 
                   href="#" 
                   target="_blank">
                    <i class="fas fa-book me-3"></i>
                    Manual de Usuario
                </a>
            </li>
        </ul>
    </div>
</div>
                
                <!-- Contenido principal -->
                <div class="col-md-10 py-4">
                    @yield('content')
                </div>
            </div>
        </div>
    @else
        @yield('content')
    @endauth
</main>
    </div>
</body>
</html>