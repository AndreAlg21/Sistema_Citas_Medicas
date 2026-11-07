@extends('layouts.app')
@section('title', 'Inicio')
@section('content')
@php($rol = auth()->user()->role?->nombre)
<section class="welcome-panel">
    <span class="eyebrow">Panel de trabajo · {{ $rol }}</span>
    <h1>Hola, {{ auth()->user()->nombre_completo }}</h1>
    <p class="muted">¿Qué necesitas gestionar hoy?</p>
</section>
<div class="dashboard-grid">
    @if ($rol === 'Administrador')
        <a class="action-card" href="{{ route('usuarios.index') }}"><span>♙</span><strong>Usuarios</strong><small>Cuentas y accesos</small></a>
        <a class="action-card" href="{{ route('roles.index') }}"><span>⚙</span><strong>Roles y permisos</strong><small>Configura accesos</small></a>
        <a class="action-card" href="{{ route('especialidades.index') }}"><span>✳</span><strong>Especialidades</strong><small>Servicios médicos</small></a>
        <a class="action-card" href="{{ route('medicos.index') }}"><span>✚</span><strong>Médicos</strong><small>Perfiles profesionales</small></a>
        <a class="action-card" href="{{ route('horarios.index') }}"><span>◷</span><strong>Horarios</strong><small>Jornadas por especialidad</small></a>
        <a class="action-card" href="{{ route('pacientes.index') }}"><span>♙</span><strong>Pacientes</strong><small>Fichas e historial</small></a>
        <a class="action-card" href="{{ route('citas.agenda') }}"><span>▦</span><strong>Agenda de citas</strong><small>Consulta la jornada</small></a>
        <a class="action-card" href="{{ route('reportes.index') }}"><span>▤</span><strong>Reportes</strong><small>Resumen de actividad</small></a>
    @elseif ($rol === 'Recepcionista')
        <a class="action-card" href="{{ route('citas.disponibilidad') }}"><span>◷</span><strong>Disponibilidad</strong><small>Busca un turno libre</small></a>
        <a class="action-card" href="{{ route('citas.create') }}"><span>＋</span><strong>Registrar cita</strong><small>Busca al paciente por carnet</small></a>
        <a class="action-card" href="{{ route('citas.agenda') }}"><span>▦</span><strong>Agenda</strong><small>Consulta las citas del día</small></a>
        <a class="action-card" href="{{ route('pacientes.index') }}"><span>♙</span><strong>Pacientes</strong><small>Fichas e historial</small></a>
        <a class="action-card" href="{{ route('reportes.index') }}"><span>▤</span><strong>Reportes</strong><small>Resumen de citas</small></a>
    @elseif ($rol === 'Medico')
        <a class="action-card" href="{{ route('citas.agenda') }}"><span>▦</span><strong>Mi agenda</strong><small>Consulta tus próximas citas</small></a>
    @elseif ($rol === 'Paciente')
        <a class="action-card" href="{{ route('citas.disponibilidad') }}"><span>◷</span><strong>Reservar cita</strong><small>Busca un horario disponible</small></a>
        <a class="action-card" href="{{ route('portal.citas') }}"><span>▦</span><strong>Mis citas</strong><small>Revisa tus reservas</small></a>
        <a class="action-card" href="{{ route('portal.perfil') }}"><span>♙</span><strong>Mi perfil</strong><small>Actualiza tus datos</small></a>
        <a class="action-card" href="{{ route('portal.historial-medico') }}"><span>▤</span><strong>Mi historial médico</strong><small>Consulta tu información</small></a>
    @endif
</div>
@endsection
