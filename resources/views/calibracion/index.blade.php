@extends('layouts.app')
@section('title','Calibración')

@section('content')
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Calibración de servomotores</h2>
    <div>
      <form method="POST" action="{{ route('calibracion.setCantidad') }}" class="d-flex align-items-center">
        @csrf
        <label class="me-2 mb-0">Cantidad servos:</label>
        <input type="number" name="cantidad" min="1" max="255" value="{{ $cantidad }}" class="form-control form-control-sm me-2" style="width:100px;">
        <button class="btn btn-sm btn-outline-primary">Actualizar</button>
      </form>
    </div>
  </div>

  <div class="row">
    <!-- izquierda: controles -->
    <div class="col-md-5">
      <div class="card mb-3">
        <div class="card-body">
          <h5 class="card-title">Controles</h5>

          <div class="mb-3">
            <label class="form-label">Selecciona servomotor</label>
            <select id="servoSelect" class="form-select">
              @for($i=1;$i<=$cantidad;$i++)
                <option value="{{ $i }}">Servo #{{ $i }}</option>
              @endfor
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Ángulo (°)</label>
            <input id="angleInput" type="number" min="-360" max="360" value="0" class="form-control">
            <div class="form-text">Ingresa el ángulo deseado para el servo seleccionado.</div>
          </div>

          <div class="mb-3">
            <label class="form-label">Nota (opcional)</label>
            <input id="notaInput" type="text" class="form-control" placeholder="Observación breve">
          </div>

          <div class="d-grid">
            <button id="btnCalibrar" class="btn btn-primary">Calibrar</button>
          </div>

          <div id="statusMsg" class="mt-3"></div>
        </div>
      </div>
    </div>

    <!-- derecha: historial -->
    <div class="col-md-7">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Historial de calibraciones (sesión actual)</h5>
          <div class="table-responsive">
            <table class="table table-striped" id="historialTable">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Servo</th>
                  <th>Ángulo</th>
                  <th>Fecha / Hora</th>
                  <th>Nota</th>
                </tr>
              </thead>
              <tbody>
                @foreach($historial as $h)
                  <tr data-id="{{ $h->id }}">
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $h->servo_num }}</td>
                    <td>{{ $h->angulo }}</td>
                    <td>{{ $h->created_at->format('Y-m-d H:i:s') }}</td>
                    <td>{{ $h->nota }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>

          <div id="emptyHint" class="text-muted" @if(count($historial)>0) style="display:none" @endif>
            No hay calibraciones registradas en esta sesión.
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
  const btn = document.getElementById('btnCalibrar');
  const servoSelect = document.getElementById('servoSelect');
  const angleInput = document.getElementById('angleInput');
  const notaInput = document.getElementById('notaInput');
  const statusMsg = document.getElementById('statusMsg');
  const table = document.getElementById('historialTable').querySelector('tbody');
  const emptyHint = document.getElementById('emptyHint');

  btn.addEventListener('click', function(){
    const servo = parseInt(servoSelect.value, 10);
    const angle = parseInt(angleInput.value, 10);
    const nota = notaInput.value;

    if (isNaN(angle)) {
      statusMsg.innerHTML = '<div class="alert alert-danger">Ingresa un ángulo válido.</div>';
      return;
    }

    statusMsg.innerHTML = '<div class="alert alert-info">Enviando calibración...</div>';
    btn.disabled = true;

    fetch("{{ route('calibracion.store') }}", {
      method: 'POST',
      headers: {
        'Content-Type':'application/json',
        'X-CSRF-TOKEN':'{{ csrf_token() }}',
        'Accept':'application/json'
      },
      body: JSON.stringify({ servo_num: servo, angulo: angle, nota: nota })
    })
    .then(r => r.json().then(j => ({ok: r.ok, status: r.status, body: j})))
    .then(resp => {
      btn.disabled = false;
      if (!resp.ok) {
        if (resp.status === 422 && resp.body.errors) {
          const errs = resp.body.errors;
          const first = Object.values(errs)[0][0];
          statusMsg.innerHTML = '<div class="alert alert-danger">'+ first +'</div>';
        } else {
          statusMsg.innerHTML = '<div class="alert alert-danger">Error desconocido.</div>';
        }
        return;
      }

      const cal = resp.body.calibracion;
      // añadir fila al inicio del historial
      const row = document.createElement('tr');
      row.innerHTML = '<td>--</td><td>'+ cal.servo_num +'</td><td>'+ cal.angulo +'</td><td>'+ (new Date(cal.created_at)).toLocaleString() +'</td><td>'+ (cal.nota||'') +'</td>';
      table.insertBefore(row, table.firstChild);

      // limpiar inputs / mensaje
      statusMsg.innerHTML = '<div class="alert alert-success">Calibración registrada.</div>';
      notaInput.value = '';
      angleInput.value = 0;
      emptyHint.style.display = 'none';
      // reenumerar índices
      Array.from(table.querySelectorAll('tr')).forEach((r,i)=> { r.children[0].textContent = i+1; });
    })
    .catch(err => {
      btn.disabled = false;
      statusMsg.innerHTML = '<div class="alert alert-danger">Error de conexión.</div>';
      console.error(err);
    });
  });
});
</script>
@endsection