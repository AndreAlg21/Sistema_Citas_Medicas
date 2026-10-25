<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Horarios de atención</title>
</head>
<body>
    <h1>Horarios de atención</h1>
    @if (session('success')) <p>{{ session('success') }}</p> @endif
    @if ($errors->any())
        <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    @endif
    <p>
        <a href="{{ route('horarios.create') }}">Registrar horario</a> |
        <a href="{{ route('inicio') }}">Volver al inicio</a>
    </p>

    @if ($horarios->isEmpty())
        <p>Todavía no hay horarios registrados.</p>
    @else
        @php
            $dias = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'];
        @endphp
        <table border="1" cellpadding="8">
            <thead>
                <tr>
                    <th>Médico</th><th>Día</th><th>Desde</th><th>Hasta</th>
                    <th>Duración de cita</th><th>Consultorio</th><th>Estado</th><th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($horarios as $horario)
                    <tr>
                        <td>{{ $horario->medico->nombres }} {{ $horario->medico->apellidos }}</td>
                        <td>{{ $dias[$horario->dia_semana] ?? 'Día no válido' }}</td>
                        <td>{{ substr((string) $horario->hora_inicio, 0, 5) }}</td>
                        <td>{{ substr((string) $horario->hora_fin, 0, 5) }}</td>
                        <td>{{ $horario->duracion_cita_min }} minutos</td>
                        <td>{{ $horario->consultorio }}</td>
                        <td>{{ $horario->estado ? 'Activo' : 'Inactivo' }}</td>
                        <td>
                            <a href="{{ route('horarios.show', $horario) }}">Ver</a>
                            <a href="{{ route('horarios.edit', $horario) }}">Editar</a>
                            @if ($horario->estado)
                                <form action="{{ route('horarios.destroy', $horario) }}" method="POST" style="display:inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('¿Deseas desactivar este horario?')">Desactivar</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $horarios->links() }}
    @endif
</body>
</html>
