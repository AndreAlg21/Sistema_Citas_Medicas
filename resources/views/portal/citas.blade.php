@extends('layouts.app')
@section('title', 'Mis citas')
@section('content')
<section>
    <h1>Mis citas</h1>
    @if ($citas->isEmpty())
        <p>Aún no tienes citas registradas.</p>
    @else
        @foreach (['Programada','Confirmada','Atendida','Cancelada','Inasistencia'] as $estado)
            <h2>{{ $estado }}</h2>
            @php($grupo = $citas->get($estado, collect()))
            @if ($grupo->isEmpty())
                <p class="muted">No hay citas en este estado.</p>
            @else
                <table><thead><tr><th>Fecha y hora</th><th>Médico</th><th>Especialidad</th><th>Modalidad</th><th>Acciones</th></tr></thead>
                    <tbody>@foreach ($grupo as $cita)
                        <tr>
                            <td>{{ $cita->fecha->format('d/m/Y') }} {{ substr((string) $cita->hora_inicio, 0, 5) }}</td>
                            <td>{{ $cita->medico->nombres }} {{ $cita->medico->apellidos }}</td>
                            <td>{{ $cita->especialidad->nombre }}</td><td>{{ $cita->modalidad }}</td>
                            <td>
                                @if (in_array($cita->estado, ['Programada','Confirmada'], true))
                                    <form method="POST" action="{{ route('citas.cancelar', $cita) }}">
                                        @csrf
                                        <label>Motivo <input name="motivo_cancelacion" maxlength="200" required></label>
                                        <button type="submit">Solicitar cancelación</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach</tbody>
                </table>
            @endif
        @endforeach
    @endif
</section>
@endsection
