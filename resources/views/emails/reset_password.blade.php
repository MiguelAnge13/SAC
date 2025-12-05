<!doctype html>
<html>
<head><meta charset="utf-8"></head>
<body>
  <!-- resources/views/emails/reset_password.blade.php -->
<p>Hola {{ $user->nombre }},</p>

<p>Has solicitado restablecer tu contraseña. Haz clic en el siguiente enlace (se abrirá en la misma pestaña del navegador):</p>

<a href="{{ $url }}">{{ $url }}</a>

<p>Si no solicitaste este cambio, ignora este correo.</p>

</body>
</html>


