<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Códigos - SAC</title>

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    body { background: #f8f9fa; }
    .top-menu { background: #0d6efd; color: #fff; }
    .logo-img { height: 36px; }
    .sidebar { height: calc(100vh - 56px); overflow-y: auto; background: #f1f3f5; border-right: 1px solid #dcdcdc; }
    .lista-codigos .item { padding: 18px 12px; border-bottom: 1px solid #dcdcdc; cursor: pointer; }
    .lista-codigos .item:hover { background: #e9ecef; }
    .lista-codigos .item.active { background: #dee2ff; font-weight: 600; }
    .editor { min-height: 320px; }
    /* Estilos del menú superior igual que la interfaz de usuarios */
    .top-menu .nav-link {color: #eaeaea; font-weight: 500; padding: 10px 14px; }
    .top-menu .nav-link:hover,
    .top-menu .nav-link.active,
    .top-menu .nav-link:focus { color: #000000 !important; text-decoration: none; }

  </style>
</head>
<body>

  <!-- Menú superior (logo + 10 opciones) -->
<nav class="top-menu navbar navbar-expand-lg">
  <div class="container-fluid">
    <a class="navbar-brand d-flex align-items-center" href="#">
      <img src="{{ asset('logo.png') }}" alt="Logo" class="logo-img me-2">
      <strong>SAC</strong>
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuHorizontal" aria-controls="menuHorizontal" aria-expanded="false" aria-label="Mostrar menú">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="menuHorizontal">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0 menu-options">
            <!-- Menu de opciones -->
            <li class="nav-item"><a class="nav-link" href="#">Inicio</a></li>
            <li class="nav-item"><a class="nav-link" href="/usuarios">Usuarios</a></li>
            <li class="nav-item"><a class="nav-link" href="#">Proyectos</a></li>
            <li class="nav-item"><a class="nav-link" href="#">Librerias</a></li>
            <li class="nav-item"><a class="nav-link" href="#">Microcontroladores</a></li>
            <li class="nav-item"><a class="nav-link" href="#">Calibracion</a></li>
            <li class="nav-item"><a class="nav-link" href="/codigos">Codigos</a></li>
            <li class="nav-item"><a class="nav-link" href="#">Historial</a></li>
        </ul>

      <div class="d-flex align-items-center">
        @auth
          <span class="me-2 text-white">Hola, {{ auth()->user()->nombre }}</span>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="btn btn-outline-light btn-sm" type="submit">Cerrar sesión</button>
          </form>
        @else
          <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm">Iniciar sesión</a>
        @endauth
      </div>
    </div>
  </div>
</nav>


  <div class="container-fluid">
    <div class="row">

      <!-- Barra lateral: lista vertical de códigos -->
      <div class="col-3 p-0 sidebar">
        <h5 class="p-3 mb-0">Códigos</h5>
        <div class="lista-codigos">
          @forelse($codigos as $c)
            <div class="item d-flex justify-content-between align-items-center"
                 data-id="{{ $c->id }}"
                 data-titulo="{{ $c->titulo }}"
            >
              <div>
                {{ $c->titulo ?? 'Sin título' }}
                <div class="small text-muted"> {{ $c->lenguaje ?? '-' }} </div>
              </div>
              <div class="pe-2">
                @if(auth()->user()->esAdministrador())
                  <!-- icono eliminar pequeño (también hay botón grande en el formulario) -->
                  <form action="{{ route('codigos.destroy', $c->id) }}" method="POST" class="d-inline eliminar-mini">
                    @csrf
                    @method('DELETE')
                    <button title="Eliminar" class="btn btn-link btn-sm text-danger p-0" type="button">
                      <i class="bi bi-trash"></i>
                    </button>
                  </form>
                @endif
              </div>
            </div>
          @empty
            <div class="p-3 text-muted">No hay códigos guardados.</div>
          @endforelse
        </div>
      </div>

      <!-- Contenido principal: formulario / editor -->
      <div class="col-9 p-4">
        <!-- Mensajes -->
        @if(session('success'))
          <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
          <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <h4>Editor de código</h4>
        <p class="text-muted">Selecciona un código a la izquierda para editar o completa el formulario para crear uno nuevo.</p>

        <div class="card">
          <div class="card-body">
            <!-- FORMULARIO: por defecto crea; al seleccionar cambia action y método -->
            <form id="formCodigo" action="{{ route('codigos.store') }}" method="POST">
              @csrf
              <input type="hidden" name="_method" id="form_method" value="POST">
              <input type="hidden" name="id" id="codigo_id" value="">

              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label">Título</label>
                  <input type="text" name="titulo" id="titulo" class="form-control" placeholder="Nombre corto (ej. Ejemplo 1)">
                </div>
                <div class="col-md-3">
                  <label class="form-label">Lenguaje</label>
                  <select name="lenguaje" id="lenguaje" class="form-select">
                    <option value="">--Selecciona--</option>
                    <option>PHP</option>
                    <option>JavaScript</option>
                    <option>Python</option>
                    <option>C++</option>
                    <option>Java</option>
                    <option>HTML</option>
                    <option>CSS</option>
                    <option>Otro</option>
                  </select>
                </div>
                <div class="col-md-3">
                  <label class="form-label">Fecha</label>
                  <input type="date" name="fecha" id="fecha" class="form-control">
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label">Código</label>
                <textarea name="codigo" id="codigo_text" class="form-control editor" rows="12" placeholder="Escribe tu código aquí..."></textarea>
              </div>

              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <button id="btnGuardar" type="submit" class="btn btn-primary">Guardar</button>
                  <button id="btnActualizar" type="submit" class="btn btn-success d-none">Actualizar</button>

                  @if(auth()->user()->esAdministrador())
                    <button id="btnEliminar" type="button" class="btn btn-danger ms-2 d-none">Eliminar</button>
                  @endif
                </div>

                <div>
                  <button id="btnNuevo" type="button" class="btn btn-outline-secondary">Nuevo código</button>
                </div>
              </div>
            </form>
          </div>
        </div>

      </div> <!-- fin col derecho -->
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    // Variables DOM
    const listaItems = document.querySelectorAll('.lista-codigos .item');
    const form = document.getElementById('formCodigo');
    const formMethodInput = document.getElementById('form_method');
    const codigoIdInput = document.getElementById('codigo_id');
    const tituloInput = document.getElementById('titulo');
    const lenguajeInput = document.getElementById('lenguaje');
    const fechaInput = document.getElementById('fecha');
    const codigoText = document.getElementById('codigo_text');

    const btnGuardar = document.getElementById('btnGuardar');
    const btnActualizar = document.getElementById('btnActualizar');
    const btnEliminar = document.getElementById('btnEliminar');
    const btnNuevo = document.getElementById('btnNuevo');

    // Helper: quitar clase active
    function clearActive() {
      document.querySelectorAll('.lista-codigos .item').forEach(el => el.classList.remove('active'));
    }

    // Manejar click en cada item (cargar a la derecha)
    document.querySelectorAll('.lista-codigos .item').forEach(item => {
      item.addEventListener('click', function(){
        clearActive();
        this.classList.add('active');
        const id = this.dataset.id;

        // Petición fetch al servidor para obtener el código
        fetch("{{ url('/codigos') }}/" + id)
          .then(r => r.json())
          .then(data => {
            // rellenar formulario
            codigoIdInput.value = data.id;
            tituloInput.value = data.titulo || '';
            lenguajeInput.value = data.lenguaje || '';
            fechaInput.value = data.fecha || '';
            codigoText.value = data.codigo || '';

            // cambiar formulario a modo actualización
            form.action = "{{ url('/codigos') }}/" + data.id;
            formMethodInput.value = 'PUT';
            btnGuardar.classList.add('d-none');
            btnActualizar.classList.remove('d-none');

            @if(auth()->user()->esAdministrador())
              btnEliminar.classList.remove('d-none');
            @endif
          })
          .catch(err => {
            console.error(err);
            alert('No se pudo cargar el código seleccionado.');
          });
      });

      // pequeña función para el botón eliminar mini en la lista lateral
      const eliminarMini = item.querySelector('.eliminar-mini button');
      if (eliminarMini) {
        eliminarMini.addEventListener('click', function(e){
          e.stopPropagation();
          if (confirm('¿Seguro que deseas eliminar este código?')) {
            this.closest('form').submit();
          }
        });
      }
    });

    // Botón Nuevo: limpiar formulario y volver a modo crear
    btnNuevo.addEventListener('click', function(){
      clearActive();
      codigoIdInput.value = '';
      tituloInput.value = '';
      lenguajeInput.value = '';
      fechaInput.value = '';
      codigoText.value = '';

      form.action = "{{ route('codigos.store') }}";
      formMethodInput.value = 'POST';
      btnGuardar.classList.remove('d-none');
      btnActualizar.classList.add('d-none');
      if (btnEliminar) btnEliminar.classList.add('d-none');
    });

    // Botón Eliminar grande (desde el formulario)
    if (btnEliminar) {
      btnEliminar.addEventListener('click', function(){
        const id = codigoIdInput.value;
        if (!id) return alert('No hay código seleccionado.');
        if (!confirm('¿Confirmas eliminar el código seleccionado?')) return;

        // Crear un form dinámico para enviar DELETE con CSRF
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        // Usaremos el form real (redireccionaremos con un método alternativo)
        // Creamos un form temporal
        const tempForm = document.createElement('form');
        tempForm.method = 'POST';
        tempForm.action = "{{ url('/codigos') }}/" + id;

        const inputToken = document.createElement('input');
        inputToken.type = 'hidden';
        inputToken.name = '_token';
        inputToken.value = '{{ csrf_token() }}';
        tempForm.appendChild(inputToken);

        const inputMethod = document.createElement('input');
        inputMethod.type = 'hidden';
        inputMethod.name = '_method';
        inputMethod.value = 'DELETE';
        tempForm.appendChild(inputMethod);

        document.body.appendChild(tempForm);
        tempForm.submit();
      });
    }
  </script>

</body>
</html>
