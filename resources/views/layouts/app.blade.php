<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>@yield('title', 'SAC')</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
  /* Estilos del menú superior igual que la interfaz de usuarios */

    .nav-link { color: white;}
    .top-menu .nav-link:hover,
    .top-menu .nav-link.active,
    .top-menu .nav-link:focus { color: #000000 !important; text-decoration: none; }
  </style>
</head>
<body>
  <!-- Navbar simple (puedes personalizarlo) -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
      <img src="{{ asset('img/roboticaCir.png') }}" alt="Logo" style="height:80px; margin-right:8px;">
      <div class="collapse navbar-collapse">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0 menu-options">
                    <!-- Menu de opciones -->
                    <li class="nav-item"><a class="nav-link" href="/inicio">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="/usuarios">Usuarios</a></li>
                    <li class="nav-item"><a class="nav-link" href="/proyectos">Proyectos</a></li>
                    <li class="nav-item"><a class="nav-link" href="/librerias">Librerias</a></li>
                    <li class="nav-item"><a class="nav-link" href="/microcontroladores">Microcontroladores</a></li>
                    <li class="nav-item"><a class="nav-link" href="/calibracion">Calibracion</a></li>
                    <li class="nav-item"><a class="nav-link" href="/codigos">Codigos</a></li>
                    <li class="nav-item"><a class="nav-link" href="/historial">Historial</a></li>
                </ul>
        <ul class="navbar-nav ms-auto">
          @auth
            <li class="nav-item"><span class="nav-link">Hola, {{ auth()->user()->nombre }}</span></li>
            <li class="nav-item">
              <form method="POST" action="{{ route('logout') }}">@csrf
                <button class="btn btn-sm btn-light">Cerrar sesión</button>
              </form>
            </li>
          @endauth
          @guest
            <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Iniciar sesión</a></li>
          @endguest
        </ul>
      </div>
    </div>
  </nav>

  <main class="py-4">
    <div class="container">
      @yield('content')
    </div>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
