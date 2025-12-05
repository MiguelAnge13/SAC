<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Librerías - SAC</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body { background: #f8f9fa; }
    .top-menu { background: #0d6efd; color: #fff; }
    .logo-img { height: 36px; }
    .grid { display:flex; gap:18px; flex-wrap:wrap; justify-content:center; align-items:flex-start; padding:30px 10px; }
    .card-lib {
      width:150px; height:150px; border-radius:12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);
      display:flex; align-items:center; justify-content:center; flex-direction:column; cursor:pointer;
      background: #fff; border: 1px solid #e6e6e6; transition: transform .12s ease, box-shadow .12s ease;
      position:relative;
    }
    .card-lib:hover { transform: translateY(-6px); box-shadow: 0 10px 20px rgba(0,0,0,0.12); }
    .card-lib img { max-width:72px; max-height:72px; object-fit:contain; }
    .card-lib .lbl { margin-top:8px; font-size:13px; color:#333; text-align:center; padding:0 6px; }
    .badge-lang { position:absolute; left:8px; top:8px; background:#0d6efd; color:#fff; font-size:11px; padding:3px 7px; border-radius:6px; }
    .controls { display:flex; gap:8px; align-items:center; justify-content:center; margin:18px 0; }
    .search { max-width:420px; }
    .file-input { display:none; }
    .empty-note { text-align:center; color:#666; padding:40px 0; }
    .nav-link { color: white;}
    .top-menu .nav-link:hover,
    .top-menu .nav-link.active,
    .top-menu .nav-link:focus { color: #000000 !important; text-decoration: none; }
  </style>
</head>
<body>

  <!-- Menú superior (igual que en otras vistas) -->
  <nav class="top-menu navbar navbar-expand-lg">
    <div class="container-fluid">
      <a class="navbar-brand d-flex align-items-center text-white" href="#">
        <img src="{{ asset('img/roboticaCir.png') }}" alt="Logo" style="height:80px; margin-right:8px;">
      </a>
    
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

      <div class="d-flex ms-auto align-items-center text-white">
        @auth
          <span class="me-3">Hola, {{ auth()->user()->nombre }}</span>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="btn btn-outline-light btn-sm" type="submit">Cerrar sesión</button>
          </form>
        @endauth
      </div>
    </div>
  </nav>

  <div class="container py-4">
    <h3 class="text-center mb-3">Librerías disponibles</h3>

    <div class="controls">
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevo">Nueva librería</button>

      <form action="{{ route('librerias.import') }}" method="POST" enctype="multipart/form-data" class="d-inline">
        @csrf
        <label class="btn btn-outline-secondary mb-0">
          Importar CSV
          <input type="file" name="csv" accept=".csv" onchange="this.form.submit()" hidden>
        </label>
      </form>

      <a class="btn btn-outline-success" href="{{ route('librerias.export') }}">Exportar CSV</a>

      <form class="d-inline" method="GET" action="{{ route('librerias.index') }}">
  <div class="input-group search" style="max-width:420px; margin-left:8px;">
    <input id="buscar" name="q" value="{{ request('q') }}" class="form-control form-control-sm" placeholder="Buscar librería / lenguaje">
    <button class="btn btn-outline-secondary" type="submit">Buscar</button>
    <button type="button" id="btnClearSearch" class="btn btn-outline-secondary" title="Limpiar" style="display:none;">✖</button>
  </div>
</form>

    </div>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
      <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if($librerias->count() == 0)
      <div class="empty-note">No hay librerías registradas. Importa un CSV o crea una nueva.</div>
    @endif

    <div id="grid" class="grid">
      @foreach($librerias as $lib)
        <div class="card-lib" data-id="{{ $lib->id }}" title="{{ $lib->nombre }}">
          <div class="badge-lang">{{ $lib->lenguaje ?? '—' }}</div>
          <img src="{{ $lib->icono ? asset('storage/'.$lib->icono) : asset('logo.png') }}" alt="">
          <div class="lbl">{{ \Illuminate\Support\Str::limit($lib->nombre, 22) }}</div>

          <div style="position:absolute; right:8px; bottom:8px;">
            <button class="btn btn-sm btn-link text-primary btn-editar" data-id="{{ $lib->id }}">Editar</button>
            @if(auth()->user()->esAdministrador())
              <form action="{{ route('librerias.destroy', $lib->id) }}" method="POST" class="d-inline ms-1 eliminar-mini">
                @csrf
                @method('DELETE')
                <button class="btn btn-sm btn-link text-danger" type="button" onclick="if(confirm('Eliminar librería?')) this.closest('form').submit();">Eliminar</button>
              </form>
            @endif
          </div>
        </div>
      @endforeach
    </div>

    <!-- Modal Nuevo / Editar -->
<div class="modal fade" id="modalNuevo" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <form id="formLibreria" method="POST" action="{{ route('librerias.store') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="_method" id="form_method" value="POST">
        <input type="hidden" name="id" id="lib_id" value="">
        <div class="modal-header">
          <h5 class="modal-title">Nueva librería</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Nombre</label>
              <input type="text" name="nombre" id="nombre" class="form-control" required>
            </div>
            <div class="col-md-3">
              <label class="form-label">Lenguaje</label>
              <input type="text" name="lenguaje" id="lenguaje" class="form-control">
            </div>
            <div class="col-md-3">
              <label class="form-label">Versión</label>
              <input type="text" name="version" id="version" class="form-control">
            </div>
            <div class="col-12">
              <label class="form-label">Descripción</label>
              <textarea name="descripcion" id="descripcion" class="form-control" rows="3"></textarea>
            </div>
            <div class="col-md-6">
              <label class="form-label">Icono (opcional)</label>
              <input type="file" name="icono" id="icono" class="form-control" accept="image/*">
              <div class="form-text">Icono que representará la librería.</div>
            </div>
            <div class="col-md-6 d-flex align-items-center">
              <div id="prevIcon" style="width:72px; height:72px; border-radius:8px; overflow:hidden; margin-left:auto;">
                <img id="prevImg" src="{{ asset('logo.png') }}" style="width:100%; height:100%; object-fit:contain;">
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button id="btnSubmit" type="submit" class="btn btn-primary">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>


  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <script>
  // editar: cargar datos via fetch (mapeo correcto)
  document.querySelectorAll('.btn-editar').forEach(btn => {
    btn.addEventListener('click', function(){
      const id = this.dataset.id;
      fetch("{{ url('/librerias') }}/" + id)
        .then(r => {
          if (!r.ok) throw new Error('Respuesta no OK');
          return r.json();
        })
        .then(data => {
          // Rellenar campos por su id correcto
          document.getElementById('lib_id').value = data.id ?? '';
          document.getElementById('nombre').value = data.nombre ?? '';
          document.getElementById('lenguaje').value = data.lenguaje ?? '';
          // ASIGNACIÓN CORRECTA:
          document.getElementById('version').value = data.version ?? '';       // version -> input version
          document.getElementById('descripcion').value = data.descripcion ?? ''; // descripcion -> textarea descripcion

          document.getElementById('prevImg').src = data.icono ? ("{{ asset('storage') }}/" + data.icono) : "{{ asset('logo.png') }}";

          document.getElementById('formLibreria').action = "{{ url('/librerias') }}/" + data.id;
          document.getElementById('form_method').value = 'PUT';
          document.getElementById('btnSubmit').innerText = 'Actualizar';

          // abrir modal
          new bootstrap.Modal(document.getElementById('modalNuevo')).show();
        })
        .catch(err => {
          console.error(err);
          alert('No se pudo cargar la librería: ' + (err.message || 'error'));
        });
    });
  });

  // preview icon (opcional)
  document.getElementById('icono')?.addEventListener('change', function(e){
    const f = e.target.files[0];
    if (!f) return;
    const r = new FileReader();
    r.onload = function(ev){ document.getElementById('prevImg').src = ev.target.result; };
    r.readAsDataURL(f);
  });

  // limpiar modal al cerrar (asegura que no quede texto antiguo)
  var modalNuevo = document.getElementById('modalNuevo');
  modalNuevo.addEventListener('hidden.bs.modal', function () {
    document.getElementById('formLibreria').reset();
    document.getElementById('formLibreria').action = "{{ route('librerias.store') }}";
    document.getElementById('form_method').value = 'POST';
    document.getElementById('lib_id').value = '';
    document.getElementById('prevImg').src = "{{ asset('logo.png') }}";
    document.getElementById('btnSubmit').innerText = 'Guardar';
  });
</script>

<script>
/* Búsqueda cliente: filtra tarjetas por nombre y lenguaje (case-insensitive) */
(function () {
  const input = document.getElementById('buscar');
  if (!input) return;

  const grid = document.getElementById('grid');
  const cards = () => Array.from(grid.querySelectorAll('.card-lib'));

  // debounce simple
  function debounce(fn, wait = 200) {
    let t;
    return (...args) => {
      clearTimeout(t);
      t = setTimeout(() => fn(...args), wait);
    };
  }

  function matchCard(card, q) {
    q = q.trim().toLowerCase();
    if (!q) return true;
    // nombre visible
    const lbl = card.querySelector('.lbl')?.textContent?.toLowerCase() || '';
    const lang = card.querySelector('.badge-lang')?.textContent?.toLowerCase() || '';
    const title = (card.getAttribute('title') || '').toLowerCase();
    return lbl.includes(q) || lang.includes(q) || title.includes(q);
  }

  const onInput = debounce(function () {
    const q = input.value;
    cards().forEach(card => {
      card.style.display = matchCard(card, q) ? '' : 'none';
    });
  }, 180);

  input.addEventListener('input', onInput);

  // si vienes con ?q=... desde servidor, aplicar filtro inicial
  @if(request('q'))
    input.value = {!! json_encode(request('q')) !!};
    input.dispatchEvent(new Event('input'));
  @endif

})();
</script>

<script>
(function(){
  const input = document.getElementById('buscar');
  const btn = document.getElementById('btnClearSearch');
  if (!input || !btn) return;
  function check() { btn.style.display = input.value ? '' : 'none'; }
  input.addEventListener('input', check);
  btn.addEventListener('click', () => { input.value = ''; check(); input.form.submit(); });
  check();
})();
</script>


</body>
</html>
