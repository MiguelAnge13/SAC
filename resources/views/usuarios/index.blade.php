<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestión de Usuarios - SAC</title>

    <!-- Bootstrap 5 CSS (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons (opcional) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        /* Estilos del menú horizontal */
        .top-menu {
            background: #0d6efd;
            color: white;
            padding: 8px 16px;
        }
        .top-menu a, .top-menu button {
            color: white;
        }
        .logo-img {
            height: 40px;
            width: auto;
        }
        .menu-options {
            gap: 8px;
        }
        .tabla-usuarios thead th {
            vertical-align: middle;
        }
        .disabled-admin {
            pointer-events: none;
            opacity: 0.6;
        }
    </style>
</head>
<body class="bg-light">

    <!-- Menú horizontal -->
    <nav class="top-menu navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <!-- Reemplaza src con la ruta real de tu logo -->
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

                <!-- Área derecha (usuario autenticado / logout) -->
                <div class="d-flex align-items-center">
                    @auth
                        <span class="me-2">Hola, {{ auth()->user()->nombre }}</span>
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

    <!-- Contenido -->
    <div class="container my-4">

        <!-- Mensajes flash -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">
                <strong>Errores:</strong>
                <ul class="mb-0">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Título -->
        <div class="row align-items-center mb-3">
            <div class="col">
                <h2 class="mb-0">Equipo de Robótica</h2>
                <p class="text-muted">Administración de usuarios registrados en el sistema.</p>
            </div>

            <!-- Botón agregar usuario (solo administradores) -->
            <div class="col-auto">
                @auth
                    @if(auth()->user()->esAdministrador())
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalAgregarUsuario">
                            <i class="bi bi-person-plus-fill"></i> Agregar usuario
                        </button>
                    @endif
                @endauth
            </div>
        </div>

        <!-- Tabla de usuarios -->
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover tabla-usuarios mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nombre</th>
                                <th>División</th>
                                <th>Correo</th>
                                <th>Tipo</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($usuarios as $usuario)
                                <tr>
                                    <td>{{ $usuario->nombre }}</td>
                                    <td>{{ $usuario->division ?? '-' }}</td>
                                    <td>{{ $usuario->correo }}</td>
                                    <td>
                                        @if($usuario->tipo === 'administrador')
                                            <span class="badge bg-primary">Administrador</span>
                                        @else
                                            <span class="badge bg-secondary">Estudiante</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <!-- Actualizar (solo admin) -->
                                        @auth
                                            @if(auth()->user()->esAdministrador())
                                                <button
                                                    class="btn btn-sm btn-outline-primary me-1 btn-actualizar"
                                                    data-id="{{ $usuario->id }}"
                                                    data-nombre="{{ $usuario->nombre }}"
                                                    data-division="{{ $usuario->division }}"
                                                    data-correo="{{ $usuario->correo }}"
                                                    data-tipo="{{ $usuario->tipo }}"
                                                    title="Actualizar usuario"
                                                    data-bs-toggle="tooltip"
                                                >
                                                    <i class="bi bi-pencil-square"></i> Actualizar
                                                </button>
                                            @endif
                                        @endauth

                                        <!-- Eliminar (solo admin) - nunca eliminar administradores -->
                                        @auth
                                            @if(auth()->user()->esAdministrador())
                                                @if($usuario->tipo === 'administrador')
                                                    <!-- botón deshabilitado para administradores -->
                                                    <button class="btn btn-sm btn-outline-danger disabled-admin" title="No se puede eliminar a un administrador">
                                                        <i class="bi bi-trash"></i> Eliminar
                                                    </button>
                                                @else
                                                    <form action="{{ route('usuarios.destroy', $usuario->id) }}" method="POST" class="d-inline eliminar-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="btn btn-sm btn-outline-danger btn-eliminar" data-nombre="{{ $usuario->nombre }}">
                                                            <i class="bi bi-trash"></i> Eliminar
                                                        </button>
                                                    </form>
                                                @endif
                                            @endif
                                        @endauth
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">No hay usuarios registrados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Paginación (si usas paginate en controlador, reemplaza $usuarios->links()) -->
        {{-- {{ $usuarios->links() }} --}}
    </div>

    <!-- Modal: Agregar Usuario -->
    <div class="modal fade" id="modalAgregarUsuario" tabindex="-1" aria-labelledby="modalAgregarUsuarioLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('usuarios.store') }}" method="POST" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAgregarUsuarioLabel">Agregar nuevo usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre completo</label>
                        <input type="text" name="nombre" id="nombre" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="division" class="form-label">División</label>
                        <input type="text" name="division" id="division" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="correo" class="form-label">Correo</label>
                        <input type="email" name="correo" id="correo" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" name="password" id="password" class="form-control" required minlength="6">
                    </div>
                    <div class="mb-3">
                        <label for="tipo" class="form-label">Tipo de usuario</label>
                        <select name="tipo" id="tipo" class="form-select" required>
                            <option value="estudiante">Estudiante</option>
                            <option value="administrador">Administrador</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Registrar usuario</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Actualizar Usuario -->
    <div class="modal fade" id="modalActualizarUsuario" tabindex="-1" aria-labelledby="modalActualizarUsuarioLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="formActualizarUsuario" method="POST" class="modal-content">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="modalActualizarUsuarioLabel">Actualizar usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_usuario" id="update_id">
                    <div class="mb-3">
                        <label for="update_nombre" class="form-label">Nombre completo</label>
                        <input type="text" name="nombre" id="update_nombre" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="update_division" class="form-label">División</label>
                        <input type="text" name="division" id="update_division" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="update_correo" class="form-label">Correo</label>
                        <input type="email" name="correo" id="update_correo" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="update_tipo" class="form-label">Tipo de usuario</label>
                        <select name="tipo" id="update_tipo" class="form-select" required>
                            <option value="estudiante">Estudiante</option>
                            <option value="administrador">Administrador</option>
                        </select>
                        <div class="form-text">Si cambias a administrador, no podrá ser eliminado después.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap 5 JS (CDN) y dependencias -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Habilitar tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Abrir modal de actualizar y rellenar campos
        document.querySelectorAll('.btn-actualizar').forEach(function(btn){
            btn.addEventListener('click', function(){
                var id = this.dataset.id;
                var nombre = this.dataset.nombre || '';
                var division = this.dataset.division || '';
                var correo = this.dataset.correo || '';
                var tipo = this.dataset.tipo || 'estudiante';

                // llenar formulario
                document.getElementById('update_id').value = id;
                document.getElementById('update_nombre').value = nombre;
                document.getElementById('update_division').value = division;
                document.getElementById('update_correo').value = correo;
                document.getElementById('update_tipo').value = tipo;

                // establecer action del form (ruta PUT)
                var form = document.getElementById('formActualizarUsuario');
                form.action = "{{ url('/usuarios') }}/" + id;

                // mostrar modal
                var modal = new bootstrap.Modal(document.getElementById('modalActualizarUsuario'));
                modal.show();
            });
        });

        // Confirmación de eliminación (botones)
        document.querySelectorAll('.btn-eliminar').forEach(function(btn){
            btn.addEventListener('click', function(){
                var nombre = this.dataset.nombre || 'este usuario';
                if (confirm('¿Estás seguro de eliminar a "' + nombre + '"? Esta acción es irreversible.')) {
                    // si confirma, enviar el form padre
                    var form = this.closest('form');
                    if (form) form.submit();
                }
            });
        });

        // Prevención: si no hay sesión o no es admin, botones no aparecen (ya controlado en Blade)
    </script>
</body>
</html>
