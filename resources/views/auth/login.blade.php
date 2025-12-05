<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar sesión - SAC</title>

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body { background: #f4f6f9; }
        .card-login { max-width: 420px; margin: 6rem auto; }
        .logo-small { height: 250px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card card-login shadow-sm">
            <div class="card-body">
                <div class="text-center mb-4">
                    <img src="{{ asset('img/roboticaCir.png') }}" alt="Logo" class="logo-small mb-2">
                    <h4 class="mb-0">Iniciar sesión</h4>
                    <p class="text-muted small mb-0">Ingresa con tu correo y contraseña</p>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('login.post') }}" method="POST" novalidate>
                    @csrf

                    <div class="mb-3">
                        <label for="correo" class="form-label">Correo</label>
                        <input type="email" name="correo" id="correo" class="form-control @error('correo') is-invalid @enderror" value="{{ old('correo') }}" required autofocus>
                        @error('correo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" name="remember" id="remember" class="form-check-input">
                        <label class="form-check-label" for="remember">Recordarme</label>
                    </div>

                    <div class="d-grid">
                        <button class="btn btn-primary">Iniciar sesión</button>
                    </div>
                </form>

                <div class="text-center mt-3">
                    <a href="{{ route('password.request') }}" class="small">¿Olvidaste tu contraseña?</a>
                </div>

            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
