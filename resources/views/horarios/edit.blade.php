@extends('layouts.app')
@section('title', 'Editar horario')
@section('content')
<section class="page-heading"><span class="eyebrow">Administración · Agenda</span><h1>Editar horario</h1><p class="muted">Modifica los días o las horas en que el médico atiende esta especialidad.</p></section>
<section>
    <form action="{{ route('horarios.update', $horario) }}" method="POST" class="stack-form">
        @csrf @method('PUT')
        <label for="id_medico">Médico<select id="id_medico" name="id_medico" required>@foreach ($medicos as $medico)<option value="{{ $medico->id_medico }}" @selected(old('id_medico', $horario->id_medico) == $medico->id_medico)>{{ $medico->nombres }} {{ $medico->apellidos }}</option>@endforeach</select></label>
        <label for="id_especialidad">Especialidad<select id="id_especialidad" name="id_especialidad" required>@foreach ($especialidades as $especialidad)<option value="{{ $especialidad->id_especialidad }}" @selected(old('id_especialidad', $horario->id_especialidad) == $especialidad->id_especialidad)>{{ $especialidad->nombre }}</option>@endforeach</select></label>
        <label for="dia_semana">Día<select id="dia_semana" name="dia_semana" required>@foreach ([1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes'] as $numero => $dia)<option value="{{ $numero }}" @selected(old('dia_semana', $horario->dia_semana) == $numero)>{{ $dia }}</option>@endforeach</select></label>
        <label for="hora_inicio">Hora de inicio<input id="hora_inicio" name="hora_inicio" type="time" step="900" value="{{ old('hora_inicio', substr((string) $horario->hora_inicio, 0, 5)) }}" required></label>
        <label for="hora_fin">Hora de fin<input id="hora_fin" name="hora_fin" type="time" step="900" value="{{ old('hora_fin', substr((string) $horario->hora_fin, 0, 5)) }}" required></label>
        <label for="duracion_cita_min">Separación de los turnos (minutos)<input id="duracion_cita_min" name="duracion_cita_min" type="number" min="15" max="15" value="15" readonly required></label>
        <label for="consultorio">Consultorio<input id="consultorio" name="consultorio" type="text" maxlength="40" value="{{ old('consultorio', $horario->consultorio) }}" required></label>
        <label for="estado">Estado<select id="estado" name="estado" required><option value="1" @selected(old('estado', (string) (int) $horario->estado) == '1')>Activo</option><option value="0" @selected(old('estado', (string) (int) $horario->estado) == '0')>Inactivo</option></select></label>
        <div class="form-actions"><button type="submit" class="button">Guardar cambios</button><a class="button button-secondary" href="{{ route('horarios.index') }}">Cancelar</a></div>
    </form>
</section>
@endsection
