@extends('layouts.app')
@section('title', 'Mi historial médico')
@section('content')
<section>
    <h1>Mi historial médico</h1>
    @if ($paciente->informacionMedica)
        <p><strong>Tipo de sangre:</strong> {{ $paciente->informacionMedica->tipo_sangre ?: 'No registrado' }}</p>
        <p><strong>Alergias:</strong> {{ $paciente->informacionMedica->alergias ?: 'No registrado' }}</p>
        <p><strong>Enfermedades crónicas:</strong> {{ $paciente->informacionMedica->enfermedades_cronicas ?: 'No registrado' }}</p>
        <p><strong>Medicamentos:</strong> {{ $paciente->informacionMedica->medicamentos ?: 'No registrado' }}</p>
        <p><strong>Antecedentes:</strong> {{ $paciente->informacionMedica->antecedentes ?: 'No registrado' }}</p>
    @else
        <p>No hay información médica registrada. Puedes completarla desde <a href="{{ route('portal.perfil') }}">Mi perfil</a>.</p>
    @endif
</section>
<section>
    <h2>Atenciones realizadas</h2>
    @if ($citasAtendidas->isEmpty())
        <p>No hay atenciones registradas.</p>
    @else
        <table><thead><tr><th>Fecha</th><th>Hora</th><th>Médico</th><th>Especialidad</th><th>Modalidad</th></tr></thead>
            <tbody>@foreach ($citasAtendidas as $cita)
                <tr><td>{{ $cita->fecha->format('d/m/Y') }}</td><td>{{ substr((string) $cita->hora_inicio, 0, 5) }}</td><td>{{ $cita->medico->nombres }} {{ $cita->medico->apellidos }}</td><td>{{ $cita->especialidad->nombre }}</td><td>{{ $cita->modalidad }}</td></tr>
            @endforeach</tbody>
        </table>
    @endif
</section>
@endsection
