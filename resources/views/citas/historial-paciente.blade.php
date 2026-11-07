@extends('layouts.app')
@section('title', 'Historial de citas')
@section('content')
<section>
    <h1>Historial de citas</h1>
    <p>{{ $paciente->nombres }} {{ $paciente->apellidos }} — {{ $paciente->codigo }}</p>
    <form method="GET" action="{{ route('citas.historial-paciente', $paciente) }}">
        <label for="estado">Estado</label><select id="estado" name="estado"><option value="">Todos</option>
            @foreach (['Programada','Confirmada','Atendida','Cancelada','Inasistencia'] as $estado)
                <option value="{{ $estado }}" {{ request('estado') === $estado ? 'selected' : '' }}>{{ $estado }}</option>
            @endforeach
        </select>
        <label for="desde">Desde</label><input id="desde" type="date" name="desde" value="{{ request('desde') }}">
        <label for="hasta">Hasta</label><input id="hasta" type="date" name="hasta" value="{{ request('hasta') }}">
        <button type="submit">Filtrar</button>
    </form>
</section>
<section>
    @if ($citas->isEmpty())
        <p>Sin citas registradas para este paciente.</p>
    @else
        <table><thead><tr><th>Fecha y hora</th><th>Médico</th><th>Especialidad</th><th>Estado</th><th>Registrada</th><th>Eventos</th></tr></thead>
            <tbody>@foreach ($citas as $cita)
                <tr><td>{{ $cita->fecha->format('d/m/Y') }} {{ substr((string) $cita->hora_inicio, 0, 5) }}</td>
                    <td>{{ $cita->medico->nombres }} {{ $cita->medico->apellidos }}</td><td>{{ $cita->especialidad->nombre }}</td>
                    <td>{{ $cita->estado }}</td><td>{{ $cita->created_at?->format('d/m/Y H:i') }}</td>
                    <td>@foreach ($cita->historial->sortBy('fecha_evento') as $evento)
                        <div>{{ $evento->fecha_evento->format('d/m/Y H:i') }} — {{ $evento->evento }}: {{ $evento->detalle }}</div>
                    @endforeach</td></tr>
            @endforeach</tbody>
        </table>
    @endif
</section>
@endsection
