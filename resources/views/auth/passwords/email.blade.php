@extends('layouts.app') <!-- o tu layout principal -->
@section('content')
<div class="container">
  <h3>¿Olvidaste tu contraseña?</h3>

  @if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif

  <form method="POST" action="{{ route('password.email') }}">
    @csrf
    <div class="mb-3">
      <label for="correo" class="form-label">Correo electrónico</label>
      <input id="correo" type="email" name="correo" class="form-control" required value="{{ old('correo') }}">
      @error('correo') <div class="text-danger">{{ $message }}</div> @enderror
    </div>
    <button class="btn btn-primary">Enviar enlace de recuperación</button>
  </form>
</div>
@endsection
