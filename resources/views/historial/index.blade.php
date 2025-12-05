@extends('layouts.app')

@section('title', 'Historial')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12 mb-3 d-flex justify-content-between align-items-center">
            <h3>Historial de eventos</h3>

            <form class="form-inline" method="GET" action="{{ route('historial.index') }}">
                <input type="text" name="action" value="{{ request('action') }}" placeholder="Acción" class="form-control form-control-sm" />
                <input type="text" name="entity" value="{{ request('entity') }}" placeholder="Entidad" class="form-control form-control-sm ml-1" />
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control form-control-sm ml-1" />
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control form-control-sm ml-1" />
                <button class="btn btn-sm btn-primary ml-1">Filtrar</button>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="table-responsive">
                <table class="table table-striped table-sm">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Fecha / Hora</th>
                            <th>Usuario</th>
                            <th>Acción</th>
                            <th>Entidad</th>
                            <th>Detalles</th>
                            <th>IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($events as $e)
                        <tr>
                            <td>{{ $e->id }}</td>
                            <td>{{ $e->created_at->format('Y-m-d H:i:s') }}</td>
                            <td>{{ $e->user ? $e->user->nombre ?? $e->user->email : 'Sistema' }}</td>
                            <td>{{ $e->action }}</td>
                            <td>{{ $e->entity }} {{ $e->entity_id ? '#'.$e->entity_id : '' }}</td>
                            <td style="max-width: 420px;">
                                @if($e->description)
                                    <div>{{ Str::limit($e->description, 180) }}</div>
                                @endif
                                @if($e->meta)
                                    <small class="text-muted">Meta: {{ json_encode($e->meta) }}</small>
                                @endif
                            </td>
                            <td>{{ $e->ip }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center">No hay eventos</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center">
                {{ $events->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
