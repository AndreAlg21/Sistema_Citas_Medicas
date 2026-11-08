@extends('layouts.app')
@section('title', 'Bitácora del sistema')
@section('content')
<section class="page-heading">
    <span class="eyebrow">Administración · Seguridad</span>
    <h1>Bitácora del sistema</h1>
    <p class="muted">Registro de accesos y acciones con hora de Bolivia, usuario e IP de origen.</p>
</section>

<section>
    <form method="GET" action="{{ route('bitacora.index') }}" class="audit-filters">
        <label for="usuario">Usuario
            <input id="usuario" type="search" name="usuario" value="{{ $filtros['usuario'] ?? '' }}" placeholder="Nombre o nombre de usuario">
        </label>
        <label for="accion">Acción
            <input id="accion" type="search" name="accion" value="{{ $filtros['accion'] ?? '' }}" placeholder="Ej.: registró o consultó">
        </label>
        <label for="ip">Dirección IP
            <input id="ip" type="search" name="ip" value="{{ $filtros['ip'] ?? '' }}" placeholder="Ej.: 192.168.1.10">
        </label>
        <div class="form-actions">
            <button type="submit">Buscar</button>
            <a class="button button-secondary" href="{{ route('bitacora.index') }}">Limpiar</a>
        </div>
    </form>
</section>

<section>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Fecha y hora (Bolivia)</th><th>Nombre / usuario</th><th>Acción</th><th>Módulo</th><th>Registro</th><th>IP</th></tr></thead>
            <tbody>
                @forelse ($bitacoras as $entrada)
                    <tr>
                        <td>{{ $entrada->fecha_hora->copy()->setTimezone('America/La_Paz')->format('d/m/Y H:i:s') }}</td>
                        <td>
                            @if ($entrada->usuario)
                                <strong>{{ $entrada->usuario->nombre_completo }}</strong><br>
                                <span class="muted">{{ $entrada->usuario->nombre_usuario }}</span>
                            @else
                                No identificado
                            @endif
                        </td>
                        <td>{{ $entrada->accion }}</td>
                        <td>{{ $entrada->tabla_afectada }}</td>
                        <td>{{ $entrada->id_registro_afectado ?: '—' }}</td>
                        <td>{{ $entrada->direccion_ip ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty-state">Todavía no hay movimientos registrados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $bitacoras->links() }}
</section>
@endsection
