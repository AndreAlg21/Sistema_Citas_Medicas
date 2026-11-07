@extends('layouts.app')
@section('title', 'Nuevo horario')
@section('content')
<section class="page-heading"><span class="eyebrow">Administración · Agenda</span><h1>Nuevo horario</h1><p class="muted">Asigna un horario a un médico y una especialidad. Solo se permiten días laborables y horarios de atención de la clínica.</p></section>
@if ($medicos->isEmpty())
    <section><p>Primero registra un médico activo.</p><a class="button" href="{{ route('medicos.create') }}">Registrar médico</a></section>
@else
    <section>
        <form action="{{ route('horarios.store') }}" method="POST" class="stack-form">
            @csrf
            <label for="id_medico">Médico
                <select id="id_medico" name="id_medico" required><option value="">Selecciona un médico</option>
                    @foreach ($medicos as $medico)<option value="{{ $medico->id_medico }}" @selected(old('id_medico') == $medico->id_medico)>{{ $medico->nombres }} {{ $medico->apellidos }}</option>@endforeach
                </select>
            </label>
            <label for="id_especialidad">Especialidad
                <select id="id_especialidad" name="id_especialidad" required><option value="">Selecciona una especialidad</option>
                    @foreach ($especialidades as $especialidad)<option value="{{ $especialidad->id_especialidad }}" @selected(old('id_especialidad') == $especialidad->id_especialidad)>{{ $especialidad->nombre }}</option>@endforeach
                </select>
            </label>
            <label for="dia_semana">Día
                <select id="dia_semana" name="dia_semana" required><option value="">Selecciona un día</option>
                    @foreach ([1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes'] as $numero => $dia)<option value="{{ $numero }}" @selected(old('dia_semana') == $numero)>{{ $dia }}</option>@endforeach
                </select>
            </label>
            <label for="hora_inicio">Hora de inicio<input id="hora_inicio" name="hora_inicio" type="time" step="900" value="{{ old('hora_inicio', '07:00') }}" required></label>
            <label for="hora_fin">Hora de fin<input id="hora_fin" name="hora_fin" type="time" step="900" value="{{ old('hora_fin', '12:00') }}" required></label>
            <label for="duracion_cita_min">Separación de los turnos (minutos)<input id="duracion_cita_min" name="duracion_cita_min" type="number" min="15" max="15" value="15" readonly required></label>
            <label for="consultorio">Consultorio<input id="consultorio" name="consultorio" type="text" maxlength="40" value="{{ old('consultorio') }}" placeholder="Ej. Consultorio 1" required></label>
            <label for="estado">Estado<select id="estado" name="estado" required><option value="1" @selected(old('estado', '1') == '1')>Activo</option><option value="0" @selected(old('estado') == '0')>Inactivo</option></select></label>
            <div class="form-actions"><button type="submit" class="button">Guardar horario</button><a class="button button-secondary" href="{{ route('horarios.index') }}">Cancelar</a></div>
        </form>
    </section>
@endif
@endsection
