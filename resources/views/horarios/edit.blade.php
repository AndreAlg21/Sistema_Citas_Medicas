<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar horario</title>
</head>
<body>
    <h1>Editar horario</h1>
    @if ($errors->any())
        <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    @endif

    <form action="{{ route('horarios.update', $horario) }}" method="POST">
        @csrf
        @method('PUT')
        <p>
            <label for="id_medico">Médico</label><br>
            <select id="id_medico" name="id_medico" required>
                @foreach ($medicos as $medico)
                    <option value="{{ $medico->id_medico }}" @selected(old('id_medico', $horario->id_medico) == $medico->id_medico)>
                        {{ $medico->nombres }} {{ $medico->apellidos }}
                    </option>
                @endforeach
            </select>
        </p>
        <p>
            <label for="dia_semana">Día de la semana</label><br>
            <select id="dia_semana" name="dia_semana" required>
                @foreach ([1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'] as $numero => $dia)
                    <option value="{{ $numero }}" @selected(old('dia_semana', $horario->dia_semana) == $numero)>{{ $dia }}</option>
                @endforeach
            </select>
        </p>
        <p>
            <label for="hora_inicio">Hora de inicio</label><br>
            <input id="hora_inicio" name="hora_inicio" type="time" value="{{ old('hora_inicio', substr((string) $horario->hora_inicio, 0, 5)) }}" required>
        </p>
        <p>
            <label for="hora_fin">Hora de fin</label><br>
            <input id="hora_fin" name="hora_fin" type="time" value="{{ old('hora_fin', substr((string) $horario->hora_fin, 0, 5)) }}" required>
        </p>
        <p>
            <label for="duracion_cita_min">Duración de cada cita (minutos)</label><br>
            <input id="duracion_cita_min" name="duracion_cita_min" type="number" min="5" max="240" value="{{ old('duracion_cita_min', $horario->duracion_cita_min) }}" required>
        </p>
        <p>
            <label for="consultorio">Consultorio</label><br>
            <input id="consultorio" name="consultorio" type="text" maxlength="40" value="{{ old('consultorio', $horario->consultorio) }}" required>
        </p>
        <p>
            <label for="estado">Estado</label><br>
            <select id="estado" name="estado" required>
                <option value="1" @selected(old('estado', (string) (int) $horario->estado) == '1')>Activo</option>
                <option value="0" @selected(old('estado', (string) (int) $horario->estado) == '0')>Inactivo</option>
            </select>
        </p>
        <button type="submit">Guardar cambios</button>
        <a href="{{ route('horarios.index') }}">Cancelar</a>
    </form>
</body>
</html>
