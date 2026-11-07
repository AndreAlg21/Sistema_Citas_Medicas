@extends('layouts.app')
@section('title', 'Reprogramar cita')
@section('content')
<section>
    <h1>Reprogramar cita {{ $cita->codigo }}</h1>
    <p>Paciente: {{ $cita->paciente->nombres }} {{ $cita->paciente->apellidos }}</p>
    <p>Médico: {{ $cita->medico->nombres }} {{ $cita->medico->apellidos }}</p>
    <p>Actual: {{ $cita->fecha->format('d/m/Y') }} a las {{ substr((string) $cita->hora_inicio, 0, 5) }}</p>
    <form method="GET" action="{{ route('citas.edit', $cita) }}">
        <label for="fecha">Nueva fecha</label>
        <input type="date" id="fecha" name="fecha" data-weekdays-only min="{{ today()->toDateString() }}" max="{{ today()->addDays(13)->toDateString() }}" value="{{ request('fecha') }}" required>
        <button type="submit">Buscar horarios</button>
    </form>
</section>
@if ($fecha && !$errors->any())
<section>
    <h2>Espacios libres para este médico</h2>
    @if (empty($opciones))
        <p>No hay espacios disponibles para esa fecha.</p>
    @else
        <table><thead><tr><th>Hora</th><th>Consultorio</th><th>Acción</th></tr></thead>
            <tbody>@foreach ($opciones as $opcion)
                <tr><td>{{ $opcion['hora_inicio'] }}</td><td>{{ $opcion['consultorio'] }}</td>
                    <td><form method="POST" action="{{ route('citas.update', $cita) }}">
                        @csrf @method('PUT')
                        <input type="hidden" name="id_especialidad" value="{{ $cita->id_especialidad }}">
                        <input type="hidden" name="fecha" value="{{ $fecha }}">
                        <input type="hidden" name="hora_inicio" value="{{ $opcion['hora_inicio'] }}">
                        <button type="submit">Elegir este horario</button>
                    </form></td></tr>
            @endforeach</tbody>
        </table>
    @endif
</section>
@endif
@endsection
