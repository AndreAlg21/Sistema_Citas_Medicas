@extends('layouts.app')
@section('title', 'Disponibilidad de citas')
@section('content')
<section>
    <h1>Consultar disponibilidad</h1>
    <p class="muted">Atendemos de lunes a viernes, de 07:00 a 12:00 y de 14:00 a 18:00. Los turnos comienzan cada 15 minutos. El calendario muestra una ventana móvil de 14 días desde hoy.</p>
    <form method="GET" action="{{ route('citas.disponibilidad') }}">
        <label for="id_especialidad">Especialidad</label>
        <select id="id_especialidad" name="id_especialidad" required>
            <option value="">Selecciona una especialidad</option>
            @foreach ($especialidades as $especialidad)
                <option value="{{ $especialidad->id_especialidad }}" {{ request('id_especialidad') == $especialidad->id_especialidad ? 'selected' : '' }}>{{ $especialidad->nombre }}</option>
            @endforeach
        </select>
        <label for="fecha">Fecha</label>
        <input type="date" id="fecha" name="fecha" data-weekdays-only min="{{ today()->toDateString() }}" max="{{ today()->addDays(13)->toDateString() }}" value="{{ request('fecha') }}" required>
        <button type="submit">Buscar horarios</button>
    </form>
</section>
@if (request()->filled('fecha') && !$errors->any())
<section>
    <h2>Horarios disponibles</h2>
    @if (empty($opciones))
        <p>No hay horarios libres para esa fecha y esos filtros.</p>
        @if (!empty($sugerencias))
            <p>Próximas fechas con disponibilidad:</p>
            @foreach ($sugerencias as $fechaSugerida)
                <a class="button" href="{{ route('citas.disponibilidad', [
                    'id_especialidad' => request('id_especialidad'),
                    'fecha' => $fechaSugerida,
                ]) }}">{{ \Illuminate\Support\Carbon::parse($fechaSugerida)->format('d/m/Y') }}</a>
            @endforeach
        @else
            <p>No se encontraron fechas disponibles en las próximas dos semanas.</p>
        @endif
    @else
        <table>
            <thead><tr><th>Médico</th><th>Fecha</th><th>Hora</th><th>Consultorio</th><th>Acción</th></tr></thead>
            <tbody>
            @foreach ($opciones as $opcion)
                <tr>
                    <td>{{ $opcion['medico'] }}</td>
                    <td>{{ \Illuminate\Support\Carbon::parse($opcion['fecha'])->format('d/m/Y') }}</td>
                    <td>{{ $opcion['hora_inicio'] }}</td>
                    <td>{{ $opcion['consultorio'] }}</td>
                    <td>
                        @if (auth()->user()->role?->nombre === 'Paciente')
                            <form method="POST" action="{{ route('citas.reservar') }}">
                                @csrf
                                <input type="hidden" name="id_especialidad" value="{{ $opcion['id_especialidad'] }}">
                                <input type="hidden" name="id_medico" value="{{ $opcion['id_medico'] }}">
                                <input type="hidden" name="fecha" value="{{ $opcion['fecha'] }}">
                                <input type="hidden" name="hora_inicio" value="{{ $opcion['hora_inicio'] }}">
                                <label>Modalidad <select name="modalidad" required><option value="Presencial">Presencial</option><option value="Virtual">Virtual</option></select></label>
                                <label>Motivo (opcional) <input name="motivo" maxlength="200"></label>
                                <button type="submit">Reservar</button>
                            </form>
                        @elseif (auth()->user()->role?->nombre === 'Recepcionista')
                            <a class="button" href="{{ route('citas.create', ['id_especialidad'=>$opcion['id_especialidad'],'fecha'=>$opcion['fecha'],'hora_inicio'=>$opcion['hora_inicio']]) }}">Elegir horario</a>
                        @else
                            <span>Disponible</span>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
</section>
@endif
@endsection
