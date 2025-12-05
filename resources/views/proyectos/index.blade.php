<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Proyectos - SAC</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    body { background: #f8f9fa; }
    .top-menu { background: #0d6efd; color: #fff; }
    .logo-img { height: 36px; }
    .sidebar { height: calc(100vh - 56px); overflow-y: auto; background: #f1f3f5; border-right: 1px solid #dcdcdc; }
    .lista-proyectos .item { padding: 14px 12px; border-bottom: 1px solid #dcdcdc; cursor: pointer; }
    .lista-proyectos .item:hover { background: #e9ecef; }
    .lista-proyectos .item.active { background: #dee2ff; font-weight: 600; }
    .editor { min-height: 220px; }
    .nav-link { color: white;}
    .top-menu .nav-link:hover,
    .top-menu .nav-link.active,
    .top-menu .nav-link:focus { color: #000000 !important; text-decoration: none; }
  </style>
</head>
<body>

  <!-- Menú superior (igual que en usuarios) -->
  <nav class="top-menu navbar navbar-expand-lg">
    <div class="container-fluid">
      <a class="navbar-brand d-flex align-items-center" href="#">
        <img src="{{ asset('img/roboticaCir.png') }}" alt="Logo" style="height:80px; margin-right:8px;">
      </a>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuHorizontal" aria-controls="menuHorizontal" aria-expanded="false" aria-label="Mostrar menú">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="menuHorizontal">
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

        <div class="d-flex align-items-center">
          @auth
            <span class="me-2 text-white">Hola, {{ auth()->user()->nombre }}</span>
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button class="btn btn-outline-light btn-sm" type="submit">Cerrar sesión</button>
            </form>
          @endauth
        </div>
      </div>
    </div>
  </nav>

  <div class="container-fluid">
    <div class="row">

      <!-- Lateral: lista vertical de proyectos -->
      <div class="col-3 p-0 sidebar">
        <h5 class="p-3 mb-0">Proyectos</h5>
        <div class="lista-proyectos">
          @forelse($proyectos as $p)
            <div class="item d-flex justify-content-between align-items-center"
                 data-id="{{ $p->id }}" data-nombre="{{ $p->nombre }}">
              <div>
                {{ $p->nombre }}
                <div class="small text-muted">{{ $p->fecha_hora ? \Carbon\Carbon::parse($p->fecha_hora)->format('d/m/Y H:i') : '-' }}</div>
              </div>
              <div class="pe-2">
                @if(auth()->user()->esAdministrador())
                  <form action="{{ route('proyectos.destroy', $p->id) }}" method="POST" class="d-inline eliminar-mini">
                    @csrf
                    @method('DELETE')
                    <button title="Eliminar" class="btn btn-link btn-sm text-danger p-0" type="button"><i class="bi bi-trash"></i></button>
                  </form>
                @endif
              </div>
            </div>
          @empty
            <div class="p-3 text-muted">No hay proyectos registrados.</div>
          @endforelse
        </div>
      </div>

      <!-- Contenido: formulario -->
      <div class="col-9 p-4">
        @if(session('success'))
          <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
          <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <h4>Registro / edición de proyectos</h4>
        <p class="text-muted">Selecciona un proyecto a la izquierda para editar o completa el formulario para crear uno nuevo.</p>

        <div class="card">
          <div class="card-body">
            <form id="formProyecto" action="{{ route('proyectos.store') }}" method="POST" enctype="multipart/form-data">
              @csrf
              <input type="hidden" id="proyecto_id" name="id" value="">
              <input type="hidden" name="_method" id="form_method" value="POST">

              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label">Nombre del proyecto</label>
                  <input type="text" name="nombre" id="nombre" class="form-control" required>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Estatus</label>
                  <select name="estatus" id="estatus" class="form-select" required>
                    <option value="pendiente">Pendiente</option>
                    <option value="en_progreso">En progreso</option>
                    <option value="completado">Completado</option>
                  </select>
                </div>
              </div>

              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label">Integrantes</label>
                  <select name="integrantes[]" id="integrantes" class="form-select" multiple>
                    @foreach($estudiantes as $est)
                      <option value="{{ $est->id }}">{{ $est->nombre }} — {{ $est->division ?? '' }}</option>
                    @endforeach
                  </select>
                  <div class="form-text">Selecciona todos los estudiantes que participaron.</div>
                </div>

                <div class="col-md-3">
                  <label class="form-label">Fecha y hora</label>
                  <input type="datetime-local" name="fecha_hora" id="fecha_hora" class="form-control">
                </div>

                <div class="col-md-3">
                  <label class="form-label">Código utilizado</label>
                  <select name="codigo_id" id="codigo_id" class="form-select">
                    <option value="">-- Ninguno --</option>
                    @foreach($codigos as $c)
                      <option value="{{ $c->id }}">{{ $c->titulo }} ({{ $c->lenguaje }})</option>
                    @endforeach
                  </select>
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label">Descripción</label>
                <textarea name="descripcion" id="descripcion" class="form-control" rows="6"></textarea>
              </div>

              <div class="mb-3">
                <label class="form-label">Imágenes (opcional)</label>
                <input type="file" name="imagenes[]" id="imagenes" class="form-control" accept="image/*" multiple>
                <div class="form-text">Puedes subir varias imágenes (max 5MB cada una).</div>
              </div>

              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <button id="btnGuardar" type="submit" class="btn btn-primary">Guardar</button>
                  <button id="btnActualizar" type="submit" class="btn btn-success d-none">Actualizar</button>

                  @if(auth()->user()->esAdministrador())
                    <button id="btnEliminar" type="button" class="btn btn-danger ms-2 d-none">Eliminar</button>
                    <a id="btnPdf" class="btn btn-outline-secondary ms-2 d-none" href="#">Descargar PDF</a>
                  @endif
                </div>

                <div>
                  <button id="btnNuevo" type="button" class="btn btn-outline-secondary">Nuevo reporte</button>
                </div>
              </div>
            </form>
          </div>
        </div>

      </div> <!-- fin col derecho -->
    </div>
  </div>

  <!-- Bootstrap y scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    const items = document.querySelectorAll('.lista-proyectos .item');
    const form = document.getElementById('formProyecto');
    const formMethod = document.getElementById('form_method');
    const proyectoId = document.getElementById('proyecto_id');
    const btnGuardar = document.getElementById('btnGuardar');
    const btnActualizar = document.getElementById('btnActualizar');
    const btnEliminar = document.getElementById('btnEliminar');
    const btnPdf = document.getElementById('btnPdf');
    const btnNuevo = document.getElementById('btnNuevo');

    function clearActive() {
      document.querySelectorAll('.lista-proyectos .item').forEach(i => i.classList.remove('active'));
    }

    document.querySelectorAll('.lista-proyectos .item').forEach(item => {
      item.addEventListener('click', function(){
        clearActive();
        this.classList.add('active');
        const id = this.dataset.id;

        fetch("{{ url('/proyectos') }}/" + id)
          .then(r => r.json())
          .then(data => {
            proyectoId.value = data.id;
            document.getElementById('nombre').value = data.nombre || '';
            document.getElementById('descripcion').value = data.descripcion || '';
            document.getElementById('estatus').value = data.estatus || 'pendiente';
            document.getElementById('codigo_id').value = data.codigo_id || '';
            document.getElementById('fecha_hora').value = data.fecha_hora ? new Date(data.fecha_hora).toISOString().slice(0,16) : '';

            // seleccion multiple: limpiar y setear
            const integrantesSelect = document.getElementById('integrantes');
            for (let i=0;i<integrantesSelect.options.length;i++){
              integrantesSelect.options[i].selected = false;
            }
            (data.integrantes || []).forEach(u => {
              for (let i=0;i<integrantesSelect.options.length;i++){
                if (integrantesSelect.options[i].value == u.id) {
                  integrantesSelect.options[i].selected = true;
                }
              }
            });

            // cambiar a modo actualización
            form.action = "{{ url('/proyectos') }}/" + data.id;
            formMethod.value = 'PUT';
            btnGuardar.classList.add('d-none');
            btnActualizar.classList.remove('d-none');
            if (btnEliminar) btnEliminar.classList.remove('d-none');
            if (btnPdf) {
              btnPdf.classList.remove('d-none');
              btnPdf.href = "{{ url('/proyectos') }}/" + data.id + "/pdf";
            }

          }).catch(err=>{
            console.error(err);
            alert('No se pudo cargar el proyecto.');
          });
      });

      // mini eliminar desde la lista
      const eliminarMini = item.querySelector('.eliminar-mini button');
      if (eliminarMini) {
        eliminarMini.addEventListener('click', function(e){
          e.stopPropagation();
          if (confirm('¿Deseas eliminar este proyecto?')) {
            this.closest('form').submit();
          }
        });
      }
    });

    // Nuevo reporte
    btnNuevo.addEventListener('click', function(){
      clearActive();
      proyectoId.value = '';
      form.reset();
      form.action = "{{ route('proyectos.store') }}";
      formMethod.value = 'POST';
      btnGuardar.classList.remove('d-none');
      btnActualizar.classList.add('d-none');
      if (btnEliminar) btnEliminar.classList.add('d-none');
      if (btnPdf) btnPdf.classList.add('d-none');
    });

    // Botón eliminar grande (desde formulario)
    if (btnEliminar) {
      btnEliminar.addEventListener('click', function(){
        const id = proyectoId.value;
        if (!id) return alert('No hay proyecto seleccionado.');
        if (!confirm('¿Confirmas eliminar este proyecto?')) return;

        const tempForm = document.createElement('form');
        tempForm.method = 'POST';
        tempForm.action = "{{ url('/proyectos') }}/" + id;

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
