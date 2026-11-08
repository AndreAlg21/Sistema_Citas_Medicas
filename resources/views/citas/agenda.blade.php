@extends('layouts.app')
@section('title', 'Agenda médica')
@section('content')
<section>
    <h1>Agenda médica</h1>
    @if ($sinPerfilMedico)
        <div class="notice">Esta cuenta todavía no está vinculada a un perfil médico. El administrador debe asignarle un perfil para que aparezcan citas en esta agenda.</div>
    @endif
    <form method="GET" action="{{ route('citas.agenda') }}">
        @if ($rol !== 'Medico')
            <label for="medico">Buscar médico por nombre, código o ID</label>
            <input id="medico" type="search" name="medico" value="{{ request('medico') }}" placeholder="Ej.: Ana Fernández o MED-002">
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
