@extends('layouts.app')
@section('title', 'Horarios de atención')
@section('content')
<section class="page-heading"><span class="eyebrow">Administración · Agenda médica</span><h1>Horarios de atención</h1><p class="muted">Configura la atención por médico, especialidad y día. La clínica abre de lunes a viernes en dos franjas: 07:00–12:00 y 14:00–18:00.</p><a class="button" href="{{ route('horarios.create') }}">＋ Nuevo horario</a></section>
<section>
    @if ($horarios->isEmpty())
        <div class="empty-state"><span>◷</span><h2>Aún no hay horarios</h2><p class="muted">Registra el primer horario de atención para habilitar turnos.</p><a class="button" href="{{ route('horarios.create') }}">Crear horario</a></div>
    @else
        @php($dias = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes'])
        <div class="table-wrap"><table><thead><tr><th>Médico</th><th>Especialidad</th><th>Día</th><th>Horario</th><th>Turnos</th><th>Consultorio</th><th>Estado</th><th>Acciones</th></tr></thead><tbody>
        @foreach ($horarios as $horario)
            <tr>
                <td>{{ $horario->medico->nombres }} {{ $horario->medico->apellidos }}</td>
                <td>{{ $horario->especialidad?->nombre ?? 'General' }}</td>
                <td>{{ $dias[$horario->dia_semana] ?? 'Día no válido' }}</td>
                <td>{{ substr((string) $horario->hora_inicio, 0, 5) }}–{{ substr((string) $horario->hora_fin, 0, 5) }}</td>
                <td>Cada {{ $horario->duracion_cita_min }} min</td><td>{{ $horario->consultorio }}</td>
                <td><span class="status-pill {{ $horario->estado ? 'status-active' : 'status-inactive' }}">{{ $horario->estado ? 'Activo' : 'Inactivo' }}</span></td>
                <td class="table-actions"><a class="button button-small button-secondary" href="{{ route('horarios.show', $horario) }}">Ver</a><a class="button button-small button-secondary" href="{{ route('horarios.edit', $horario) }}">Editar</a>
                    @if ($horario->estado)<form action="{{ route('horarios.destroy', $horario) }}" method="POST" class="inline">@csrf @method('DELETE')<button class="button-small button-danger" type="submit" onclick="return confirm('¿Deseas desactivar este horario?')">Desactivar</button></form>@endif
                </td>
            </tr>
        @endforeach
        </tbody></table></div>
        {{ $horarios->links() }}
    @endif
</section>
@endsection
