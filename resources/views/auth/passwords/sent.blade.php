@extends('layouts.app')

@section('content')
<div class="container py-5">
  <div class="card mx-auto" style="max-width:700px">
    <div class="card-body text-center">
      <h4>Correo enviado</h4>
      <p class="mb-3">Hemos enviado un enlace para restablecer la contraseña al correo que proporcionaste. Revisa la bandeja de entrada (y la carpeta de spam).</p>

      <p class="small text-muted">Si no recibes el correo en unos minutos, solicita uno nuevo desde la página de recuperación.</p>

      <div class="mt-3">
        <a href="{{ route('password.request') }}" class="btn btn-outline-primary">Solicitar otro enlace</a>
        <a href="{{ route('login') }}" class="btn btn-primary ms-2">Volver al inicio de sesión</a>
      </div>
    </div>
  </div>
</div>
@endsection
