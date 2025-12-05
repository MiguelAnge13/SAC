<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Inicio - SAC</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body { background:#fff; }
    .hero {
      min-height: calc(100vh - 56px);
      display:flex;
      align-items:center;
      justify-content:center;
      padding:40px;
    }
    .card-hero {
      max-width:760px;
      width:100%;
      text-align:center;
      padding:40px;
      border-radius:8px;
      box-shadow: 0 6px 24px rgba(0,0,0,0.06);
      background: #ffffff;
    }
    .avatar-wrap {
      width:200px;
      height:200px;
      border-radius:200px;
      margin: 0 auto 18px;
      overflow:hidden;
      display:flex;
      align-items:center;
      justify-content:center;
      border:6px solid #f1f1f1;
      background: #fafafa;
    }
    .avatar-wrap img { width:100%; height:100%; object-fit:cover; display:block;}
    .btn-upload {
      display:inline-block;
      margin-top:12px;
    }
    .info-division { color:#666; margin-top:14px; font-size:18px;}
    .welcome { font-weight:700; margin-bottom:6px; }
    .small-note { color:#888; font-size:13px; margin-top:8px; }
    @media (max-width:576px) {
      .avatar-wrap { width:150px; height:150px; }
    }
    .nav-link { color: white;}
    .top-menu .nav-link:hover,
    .top-menu .nav-link.active,
    .top-menu .nav-link:focus { color: #000000 !important; text-decoration: none; }
  </style>
</head>
<body>

  <!-- Menú superior simple (mantén el menú horizontal del proyecto si lo tienes) -->
  <nav class="navbar navbar-expand-lg" style="background:#0d6efd;">
    <div class="container">
      <a class="navbar-brand text-white" href="#">
        <img src="{{ asset('logo.png') }}" alt="Logo" style="height:32px; margin-right:8px;"> SAC
      </a>

      <div class="collapse navbar-collapse justify-content-end">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0 menu-options">
                    <!-- Menu de opciones -->
                    <li class="nav-item"><a class="nav-link" href="/inicio">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="/usuarios">Usuarios</a></li>
                    <li class="nav-item"><a class="nav-link" href="/proyectos">Proyectos</a></li>
                    <li class="nav-item"><a class="nav-link" href="/librerias">Librerias</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Microcontroladores</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Calibracion</a></li>
                    <li class="nav-item"><a class="nav-link" href="/codigos">Codigos</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Historial</a></li>
                </ul>
        <div class="ms-3 d-flex align-items-center">
          <span class="text-white me-2">Hola, {{ auth()->user()->nombre }}</span>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="btn btn-outline-light btn-sm">Cerrar sesión</button>
          </form>
        </div>
      </div>
    </div>
  </nav>

  <main class="hero">
    <div class="card-hero">
      <h2 class="welcome">Bienvenid@, {{ $usuario->nombre }}</h2>

      <div class="avatar-wrap">
        <img id="avatarImg" src="{{ $usuario->foto ? asset('storage/'.$usuario->foto) : asset('logo.png') }}" alt="foto de perfil">
      </div>

      <div class="info-division">
        {{ $usuario->division ?? 'Sin división' }}
      </div>

      <div class="small-note">Actualiza tu foto de perfil (formatos: jpg, png). Esta imagen se usará en todo el sistema.</div>

      <div class="mt-3">
        <!-- Formulario para subir foto -->
        <form id="formFoto" action="{{ route('inicio.foto') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <label class="btn btn-outline-primary btn-upload">
            Seleccionar imagen
            <input id="inputFoto" type="file" name="foto" accept="image/*" hidden>
          </label>

          <button id="btnEnviar" class="btn btn-primary ms-2" type="submit">Guardar foto</button>
        </form>
      </div>

      @if(session('success'))
        <div class="alert alert-success mt-3">{{ session('success') }}</div>
      @endif
      @if($errors->any())
        <div class="alert alert-danger mt-3">
          <ul class="mb-0">
            @foreach($errors->all() as $e)
              <li>{{ $e }}</li>
            @endforeach
          </ul>
        </div>
      @endif

    </div>
  </main>

  <script>
    // preview instantáneo al seleccionar archivo
    document.getElementById('inputFoto').addEventListener('change', function(e){
      const f = e.target.files[0];
      if (!f) return;
      if (!f.type.startsWith('image/')) return alert('Selecciona una imagen válida');
      const reader = new FileReader();
      reader.onload = function(ev){
        document.getElementById('avatarImg').src = ev.target.result;
      };
      reader.readAsDataURL(f);
    });

    // opcional: deshabilitar botón mientras se sube (mejorar UX)
    document.getElementById('formFoto').addEventListener('submit', function(){
      document.getElementById('btnEnviar').disabled = true;
      document.getElementById('btnEnviar').innerText = 'Subiendo...';
    });
  </script>

</body>
</html>
