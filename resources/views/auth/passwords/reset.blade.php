@extends('layouts.app')

@section('content')
<div class="container py-5" style="max-width:800px;">
  <h3>Restablecer contraseña</h3>

  {{-- Mostrar errores de validación --}}
  @if($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  {{-- Mensaje de status --}}
  @if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif

  <form method="POST" action="{{ route('password.update') }}" id="formReset">
    @csrf

    <input type="hidden" name="token" value="{{ request()->query('token') ?? $token ?? '' }}">

    <div class="mb-3">
      <label for="email" class="form-label">Correo</label>
      <input id="email" type="email" name="email" class="form-control" value="{{ old('email', request()->query('email') ?? $email ?? '') }}" required>
    </div>

    <div class="mb-3">
      <label for="password" class="form-label">Nueva contraseña</label>
      <input id="password" type="password" name="password" class="form-control" required>
      <div id="passwordHelp" class="form-text text-muted">Mínimo 6 caracteres y al menos 1 número.</div>
      <div id="passwordError" class="text-danger mt-1" style="display:none;"></div>
    </div>

    <div class="mb-3">
      <label for="password_confirmation" class="form-label">Confirmar contraseña</label>
      <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" required>
      <div id="confirmError" class="text-danger mt-1" style="display:none;"></div>
    </div>

    <button class="btn btn-primary" type="submit" id="submitBtn">Actualizar contraseña</button>
  </form>
</div>

<script>
  // Validación cliente (mejora UX)
  (function(){
    const pass = document.getElementById('password');
    const passConf = document.getElementById('password_confirmation');
    const passwordError = document.getElementById('passwordError');
    const confirmError = document.getElementById('confirmError');
    const submitBtn = document.getElementById('submitBtn');
    const form = document.getElementById('formReset');

    // regla: mínimo 6 y al menos 1 dígito
    const passRegex = /^(?=.*\d).{6,}$/;

    function checkPassword(){
      const v = pass.value;
      if (!passRegex.test(v)) {
        passwordError.style.display = 'block';
        passwordError.textContent = 'La contraseña debe tener mínimo 6 caracteres y al menos 1 número.';
        return false;
      } else {
        passwordError.style.display = 'none';
        passwordError.textContent = '';
        return true;
      }
    }

    function checkConfirm(){
      if (passConf.value !== pass.value) {
        confirmError.style.display = 'block';
        confirmError.textContent = 'Las contraseñas no coinciden.';
        return false;
      } else {
        confirmError.style.display = 'none';
        confirmError.textContent = '';
        return true;
      }
    }

    pass.addEventListener('input', function(){
      checkPassword();
      if (passConf.value.length) checkConfirm();
    });

    passConf.addEventListener('input', checkConfirm);

    form.addEventListener('submit', function(e){
      const ok1 = checkPassword();
      const ok2 = checkConfirm();
      if (!ok1 || !ok2) {
        e.preventDefault();
        // enfoque UX
        if (!ok1) pass.focus();
        else passConf.focus();
      } else {
        // opcional: deshabilitar botón para evitar doble submit
        submitBtn.disabled = true;
        submitBtn.textContent = 'Procesando...';
      }
    });
  })();
</script>
@endsection

