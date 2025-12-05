<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Reporte Proyecto {{ $proyecto->id }}</title>
  <style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
    h2 { margin-bottom: 0; }
    .meta { color: #555; margin-bottom: 10px; }
    .imagenes img { max-width: 250px; margin-right:8px; margin-bottom:8px; }
  </style>
</head>
<body>
  <h2>{{ $proyecto->nombre }}</h2>
  <div class="meta">
    Fecha: {{ $proyecto->fecha_hora ? \Carbon\Carbon::parse($proyecto->fecha_hora)->format('d/m/Y H:i') : '-' }}<br>
    Estatus: {{ ucfirst(str_replace('_',' ',$proyecto->estatus)) }}<br>
    Código relacionado: {{ $proyecto->codigo ? $proyecto->codigo->titulo . ' (' . $proyecto->codigo->lenguaje . ')' : 'N/A' }}<br>
    Integrantes:
    @foreach($proyecto->integrantes as $u)
      - {{ $u->nombre }} ({{ $u->division }})<br>
    @endforeach
  </div>

  <h4>Descripción</h4>
  <div>{!! nl2br(e($proyecto->descripcion)) !!}</div>

  @if($proyecto->imagenes->count())
    <h4>Imágenes</h4>
    <div class="imagenes">
      @foreach($proyecto->imagenes as $img)
        <img src="{{ storage_path('app/public/' . $img->ruta) }}" alt="" />
      @endforeach
    </div>
  @endif
</body>
</html>
