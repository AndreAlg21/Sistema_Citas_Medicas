@extends('layouts.app')
@section('title', 'Reportes')
@section('content')
<section>
    <h1>Reportes administrativos</h1>
    <form method="GET" action="{{ route('reportes.index') }}">
        <label for="tipo">Tipo de reporte</label>
        <select id="tipo" name="tipo">
            <option value="citas" {{ $tipo === 'citas' ? 'selected' : '' }}>Citas</option>
            @if (auth()->user()->role?->nombre === 'Administrador')
                <option value="pacientes" {{ $tipo === 'pacientes' ? 'selected' : '' }}>Pacientes registrados</option>
            @endif
        </select>
        <label for="fecha_desde">Desde</label><input id="fecha_desde" type="date" name="fecha_desde" value="{{ $desde }}" required>
        <label for="fecha_hasta">Hasta</label><input id="fecha_hasta" type="date" name="fecha_hasta" value="{{ $hasta }}" required>
        @if ($tipo === 'citas')
            <label for="id_medico">Médico</label>
            <select id="id_medico" name="id_medico"><option value="">Todos</option>
                @foreach ($medicos as $medico)
                    <option value="{{ $medico->id_medico }}" {{ ($filtros['id_medico'] ?? '') == $medico->id_medico ? 'selected' : '' }}>{{ $medico->apellidos }}, {{ $medico->nombres }}</option>
                @endforeach
            </select>
            <label for="id_especialidad">Especialidad</label>
            <select id="id_especialidad" name="id_especialidad"><option value="">Todas</option>
                @foreach ($especialidades as $especialidad)
                    <option value="{{ $especialidad->id_especialidad }}" {{ ($filtros['id_especialidad'] ?? '') == $especialidad->id_especialidad ? 'selected' : '' }}>{{ $especialidad->nombre }}</option>
                @endforeach
            </select>
            <label for="estado">Estado</label>
            <select id="estado" name="estado"><option value="">Todos</option>
                @foreach (['Programada','Confirmada','Atendida','Cancelada','Inasistencia'] as $estado)
                    <option value="{{ $estado }}" {{ ($filtros['estado'] ?? '') === $estado ? 'selected' : '' }}>{{ $estado }}</option>
                @endforeach
            </select>
        @endif
        <button type="submit">Generar reporte</button>
    </form>
</section>
<section>
    <h2>Totales</h2>
    @foreach ($totales as $estado => $cantidad)
        <span><strong>{{ $estado }}:</strong> {{ $cantidad }} &nbsp;</span>
    @endforeach
</section>
<section>
    @if ($tipo === 'pacientes')
        @if ($pacientes->isEmpty())
            <p>Sin pacientes registrados en ese período.</p>
        @else
            <table><thead><tr><th>Código</th><th>Paciente</th><th>CI</th><th>Registro</th><th>Estado</th></tr></thead>
                <tbody>@foreach ($pacientes as $paciente)
                    <tr><td>{{ $paciente->codigo }}</td><td>{{ $paciente->apellidos }}, {{ $paciente->nombres }}</td><td>{{ $paciente->ci }}</td><td>{{ $paciente->created_at?->format('d/m/Y') }}</td><td>{{ $paciente->estado ? 'Activo' : 'Inactivo' }}</td></tr>
                @endforeach</tbody>
            </table>
        @endif
    @else
        @if ($citas->isEmpty())
            <p>Sin resultados para los filtros indicados.</p>
        @else
            <table><thead><tr><th>Fecha</th><th>Hora</th><th>Código</th><th>Paciente</th><th>Médico</th><th>Especialidad</th><th>Estado</th></tr></thead>
                <tbody>@foreach ($citas as $cita)
                    <tr><td>{{ $cita->fecha->format('d/m/Y') }}</td><td>{{ substr((string) $cita->hora_inicio, 0, 5) }}</td><td>{{ $cita->codigo }}</td><td>{{ $cita->paciente->nombres }} {{ $cita->paciente->apellidos }}</td><td>{{ $cita->medico->apellidos }} {{ $cita->medico->nombres }}</td><td>{{ $cita->especialidad->nombre }}</td><td>{{ $cita->estado }}</td></tr>
                @endforeach</tbody>
            </table>
        @endif
    @endif
</section>
@endsection
