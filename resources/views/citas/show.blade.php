@extends('layouts.app')
@section('title', 'Detalle de cita')
@section('content')
<section>
    <h1>Cita {{ $cita->codigo }}</h1>
    <p><strong>Paciente:</strong> {{ $cita->paciente->nombres }} {{ $cita->paciente->apellidos }}</p>
    <p><strong>Médico:</strong> {{ $cita->medico->nombres }} {{ $cita->medico->apellidos }}</p>
    <p><strong>Especialidad:</strong> {{ $cita->especialidad->nombre }}</p>
    <p><strong>Fecha:</strong> {{ $cita->fecha->format('d/m/Y') }}</p>
    <p><strong>Hora:</strong> {{ substr((string) $cita->hora_inicio, 0, 5) }} (bloque de 15 minutos)</p>
    <p><strong>Modalidad:</strong> {{ $cita->modalidad }}</p>
    <p><strong>Estado:</strong> {{ $cita->estado }}</p>
    @if ($cita->motivo)<p><strong>Motivo:</strong> {{ $cita->motivo }}</p>@endif
    @if ($cita->motivo_cancelacion)<p><strong>Motivo de cancelación:</strong> {{ $cita->motivo_cancelacion }}</p>@endif

    @if (in_array($cita->estado, ['Programada', 'Confirmada'], true) && auth()->user()->role?->nombre === 'Recepcionista')
        <a class="button" href="{{ route('citas.edit', $cita) }}">Reprogramar</a>
    @endif
    @if (in_array($cita->estado, ['Programada', 'Confirmada'], true) && in_array(auth()->user()->role?->nombre, ['Recepcionista', 'Administrador'], true))
        <form method="POST" action="{{ route('citas.cancelar', $cita) }}" class="inline">
            @csrf
            <label>Motivo de cancelación <input name="motivo_cancelacion" maxlength="200" required></label>
            <button type="submit">Cancelar cita</button>
        </form>
    @endif
    @if (in_array(auth()->user()->role?->nombre, ['Recepcionista', 'Administrador'], true))
        @if ($cita->estado === 'Programada')
            <form method="POST" action="{{ route('citas.estado', $cita) }}" class="inline">@csrf @method('PATCH')
                <input type="hidden" name="estado" value="Confirmada"><button type="submit">Confirmar</button>
            </form>
        @endif
        @if (in_array($cita->estado, ['Programada', 'Confirmada'], true))
            <form method="POST" action="{{ route('citas.estado', $cita) }}" class="inline">@csrf @method('PATCH')
                <input type="hidden" name="estado" value="Inasistencia"><button type="submit">Marcar inasistencia</button>
            </form>
        @endif
    @endif
    @if (auth()->user()->role?->nombre === 'Medico' && $cita->medico->puede_marcar_atendida && in_array($cita->estado, ['Programada', 'Confirmada'], true))
        <form method="POST" action="{{ route('citas.estado', $cita) }}" class="inline">@csrf @method('PATCH')
            <input type="hidden" name="estado" value="Atendida"><button type="submit">Marcar atendida</button>
        </form>
    @endif
    @if (auth()->user()->role?->nombre === 'Administrador' && $cita->estado === 'Cancelada')
        <form method="POST" action="{{ route('citas.estado', $cita) }}">@csrf @method('PATCH')
            <input type="hidden" name="estado" value="Programada">
            <label>Motivo para reabrir <input name="detalle" maxlength="250" required></label>
            <button type="submit">Reabrir cita</button>
        </form>
    @endif
</section>
<section>
    <h2>Historial de cambios</h2>
    @if ($cita->historial->isEmpty())
        <p>No hay eventos registrados.</p>
    @else
        <table><thead><tr><th>Fecha</th><th>Evento</th><th>Estados</th><th>Detalle</th><th>Usuario</th></tr></thead>
            <tbody>@foreach ($cita->historial->sortByDesc('fecha_evento') as $evento)
                <tr><td>{{ $evento->fecha_evento->format('d/m/Y H:i') }}</td><td>{{ $evento->evento }}</td>
                    <td>{{ $evento->estado_anterior ?? '—' }} → {{ $evento->estado_nuevo }}</td>
                    <td>{{ $evento->detalle }}</td><td>{{ $evento->usuario?->nombre_completo ?? 'Sistema' }}</td></tr>
            @endforeach</tbody>
        </table>
    @endif
</section>
@endsection
