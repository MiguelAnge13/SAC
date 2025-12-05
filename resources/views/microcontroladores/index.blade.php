@extends('layouts.app')
@section('title','Microcontroladores')

@section('content')
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Microcontroladores</h2>
    <div>
      <small class="text-muted">Estado en tiempo real (polling cada 3s)</small>
    </div>
  </div>

  <div class="mb-3">
    <form id="simForm" method="POST" action="{{ route('microcontroladores.simular') }}">
      @csrf
      <div class="row g-2">
        <div class="col-md-2">
          <input name="port" class="form-control" placeholder="COM3" required>
        </div>
        <div class="col-md-2">
          <input name="vendor_id" class="form-control" placeholder="vendor_id">
        </div>
        <div class="col-md-2">
          <input name="product_id" class="form-control" placeholder="product_id">
        </div>
        <div class="col-md-3">
          <input name="modelo" class="form-control" placeholder="Modelo / Descripción">
        </div>
        <div class="col-md-3 d-flex">
          <button class="btn btn-outline-success me-2">Simular conectar</button>
          <button id="btnRefresh" type="button" class="btn btn-outline-secondary">Actualizar ahora</button>
        </div>
      </div>
    </form>
  </div>

  <div class="card">
    <div class="card-body">
      <table class="table table-striped" id="tblMicro">
        <thead>
          <tr>
            <th>#</th>
            <th>Serial</th>
            <th>Vendor</th>
            <th>Product</th>
            <th>Puerto</th>
            <th>Modelo</th>
            <th>Última conexión</th>
            <th>Primera conexión</th>
            <th>Estado</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
      <div id="empty" class="text-muted" style="display:none">No hay microcontroladores registrados.</div>
    </div>
  </div>
</div>

<script>
  const API_LIST = "{{ url('/api/microcontroladores') }}";
  const DELETE_URL = "{{ url('/api/microcontroladores') }}"; // /{id}
  const REFRESH_INTERVAL = 3000; // ms

  async function fetchList() {
    try {
      const res = await fetch(API_LIST);
      const json = await res.json();
      renderList(json.data || []);
    } catch (e) {
      console.error('Error al obtener lista', e);
    }
  }

  function renderList(list) {
    const tbody = document.querySelector('#tblMicro tbody');
    tbody.innerHTML = '';
    if (!list.length) {
      document.getElementById('empty').style.display = '';
      return;
    } else {
      document.getElementById('empty').style.display = 'none';
    }
    list.forEach((m,i) => {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>${i+1}</td>
        <td>${m.serial ?? ''}</td>
        <td>${m.vendor_id ?? ''}</td>
        <td>${m.product_id ?? ''}</td>
        <td>${m.port ?? ''}</td>
        <td>${m.modelo ?? ''}</td>
        <td>${m.ultima_conexion_at ? new Date(m.ultima_conexion_at).toLocaleString() : ''}</td>
        <td>${m.primera_conexion_at ? new Date(m.primera_conexion_at).toLocaleString() : ''}</td>
        <td>${m.conectado ? '<span class="badge bg-success">Conectado</span>' : '<span class="badge bg-secondary">Desconectado</span>'}</td>
        <td>
          @can('delete', App\Models\Microcontrolador::class)
          <button class="btn btn-sm btn-danger" onclick="deleteMC(${m.id})">Eliminar</button>
          @endcan
        </td>
      `;
      tbody.appendChild(tr);
    });
  }

  async function deleteMC(id) {
    if (!confirm('¿Eliminar este microcontrolador?')) return;
    try {
      const res = await fetch(DELETE_URL + '/' + id, {
        method: 'DELETE',
        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
      });
      if (res.ok) {
        fetchList();
      } else {
        alert('Error al eliminar');
      }
    } catch (e) {
      alert('Error de conexión');
    }
  }

  document.getElementById('btnRefresh').addEventListener('click', fetchList);

  // polling
  fetchList();
  setInterval(fetchList, REFRESH_INTERVAL);
</script>
@endsection
