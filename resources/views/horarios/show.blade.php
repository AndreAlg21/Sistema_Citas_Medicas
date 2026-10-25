<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle del horario</title>
</head>
<body>
    @php
        $dias = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'];
    @endphp
    <h1>Detalle del horario</h1>
    <p><strong>Médico:</strong> {{ $horario->medico->nombres }} {{ $horario->medico->apellidos }}</p>
    <p><strong>Día:</strong> {{ $dias[$horario->dia_semana] ?? 'Día no válido' }}</p>
    <p><strong>Hora de inicio:</strong> {{ substr((string) $horario->hora_inicio, 0, 5) }}</p>
    <p><strong>Hora de fin:</strong> {{ substr((string) $horario->hora_fin, 0, 5) }}</p>
    <p><strong>Duración de cada cita:</strong> {{ $horario->duracion_cita_min }} minutos</p>
    <p><strong>Consultorio:</strong> {{ $horario->consultorio }}</p>
    <p><strong>Estado:</strong> {{ $horario->estado ? 'Activo' : 'Inactivo' }}</p>
    <p>
        <a href="{{ route('horarios.edit', $horario) }}">Editar horario</a> |
        <a href="{{ route('horarios.index') }}">Volver a la lista</a>
    </p>
</body>
</html>
