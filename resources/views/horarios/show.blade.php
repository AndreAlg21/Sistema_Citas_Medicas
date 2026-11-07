@extends('layouts.app')
@section('title', 'Detalle del horario')
@section('content')
@php($dias = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes'])
<section class="page-heading"><span class="eyebrow">Administración · Agenda médica</span><h1>Detalle del horario</h1><p class="muted">Información de la jornada configurada.</p></section>
<section class="detail-grid">
    <div><span class="eyebrow">Médico</span><strong>{{ $horario->medico->nombres }} {{ $horario->medico->apellidos }}</strong></div>
    <div><span class="eyebrow">Especialidad</span><strong>{{ $horario->especialidad?->nombre ?? 'General' }}</strong></div>
    <div><span class="eyebrow">Día</span><strong>{{ $dias[$horario->dia_semana] ?? 'Día no válido' }}</strong></div>
    <div><span class="eyebrow">Atención</span><strong>{{ substr((string) $horario->hora_inicio, 0, 5) }}–{{ substr((string) $horario->hora_fin, 0, 5) }}</strong></div>
    <div><span class="eyebrow">Turnos</span><strong>Cada {{ $horario->duracion_cita_min }} minutos</strong></div>
    <div><span class="eyebrow">Consultorio</span><strong>{{ $horario->consultorio }}</strong></div>
    <div><span class="eyebrow">Estado</span><strong>{{ $horario->estado ? 'Activo' : 'Inactivo' }}</strong></div>
    <div class="form-actions"><a class="button" href="{{ route('horarios.edit', $horario) }}">Editar horario</a><a class="button button-secondary" href="{{ route('horarios.index') }}">Volver a horarios</a></div>
</section>
@endsection
