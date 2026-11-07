@extends('layouts.app')
@section('title', 'Agenda médica')
@section('content')
<section>
    <h1>Agenda médica</h1>
    <form method="GET" action="{{ route('citas.agenda') }}">
        @if ($rol !== 'Medico')
            <label for="id_medico">Médico</label>
            <select id="id_medico" name="id_medico"><option value="">Todos los médicos</option>
                @foreach ($medicos as $medico)
                    <option value="{{ $medico->id_medico }}" {{ $idMedico == $medico->id_medico ? 'selected' : '' }}>{{ $medico->apellidos }}, {{ $medico->nombres }}</option>
                @endforeach
            </select>
        @endif
        <label for="fecha">Fecha</label><input type="date" id="fecha" name="fecha" value="{{ $fecha }}" required>
        <label for="estado">Estado</label>
        <select id="estado" name="estado"><option value="">Todos</option>
            @foreach (['Programada','Confirmada','Atendida','Cancelada','Inasistencia'] as $estado)
                <option value="{{ $estado }}" {{ request('estado') === $estado ? 'selected' : '' }}>{{ $estado }}</option>
            @endforeach
        </select>
        <button type="submit">Consultar</button>
    </form>
</section>
<section>
    @if ($citas->isEmpty())
        <p>Sin citas para la fecha seleccionada.</p>
    @else
        <table><thead><tr><th>Hora</th><th>Código</th><th>Paciente</th><th>Médico</th><th>Especialidad</th><th>Estado</th><th>Motivo</th><th>Detalle</th></tr></thead>
            <tbody>@foreach ($citas as $cita)
                <tr><td>{{ substr((string) $cita->hora_inicio, 0, 5) }}</td><td>{{ $cita->codigo }}</td>
                    <td>{{ $cita->paciente->nombres }} {{ $cita->paciente->apellidos }}</td>
                    <td>{{ $cita->medico->nombres }} {{ $cita->medico->apellidos }}</td>
                    <td>{{ $cita->especialidad->nombre }}</td><td>{{ $cita->estado }}</td>
                    <td>{{ $cita->motivo }}</td><td><a href="{{ route('citas.show', $cita) }}">Ver</a></td></tr>
            @endforeach</tbody>
        </table>
    @endif
</section>
@endsection
