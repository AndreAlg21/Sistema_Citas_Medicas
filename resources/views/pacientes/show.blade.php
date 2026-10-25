<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Datos del paciente</title>
</head>
<body>
    @if (session('success')) <p>{{ session('success') }}</p> @endif
    <h1>Datos del paciente</h1>
    <h2>Datos personales</h2>
    <p><strong>Código:</strong> {{ $paciente->codigo }}</p>
    <p><strong>Nombre:</strong> {{ $paciente->nombres }} {{ $paciente->apellidos }}</p>
    <p><strong>Cédula / CI:</strong> {{ $paciente->ci ?? 'No registrada' }}</p>
    <p><strong>Fecha de nacimiento:</strong> {{ optional($paciente->fecha_nacimiento)->format('d/m/Y') }}</p>
    <p><strong>Sexo:</strong> {{ $paciente->sexo ?? 'Sin especificar' }}</p>
    <p><strong>Teléfono:</strong> {{ $paciente->telefono }}</p>
    <p><strong>Correo:</strong> {{ $paciente->correo ?? 'No registrado' }}</p>
    <p><strong>Dirección:</strong> {{ $paciente->direccion ?? 'No registrada' }}</p>
    <p><strong>Cuenta de usuario:</strong> {{ $paciente->usuario?->nombre_completo ?? 'Sin cuenta vinculada' }}</p>
    <p><strong>Acepta notificaciones:</strong> {{ $paciente->acepta_notificaciones ? 'Sí' : 'No' }}</p>
    <p><strong>Acepta aviso de privacidad:</strong> {{ $paciente->acepta_privacidad ? 'Sí' : 'No' }}</p>
    <p><strong>Estado:</strong> {{ $paciente->estado ? 'Activo' : 'Inactivo' }}</p>

    <h2>Información médica</h2>
    @if (!$paciente->informacionMedica)
        <p>No hay información médica registrada.</p>
    @else
        <p><strong>Tipo de sangre:</strong> {{ $paciente->informacionMedica->tipo_sangre ?? 'No registrado' }}</p>
        <p><strong>Alergias:</strong> {{ $paciente->informacionMedica->alergias ?? 'No registradas' }}</p>
        <p><strong>Enfermedades crónicas:</strong> {{ $paciente->informacionMedica->enfermedades_cronicas ?? 'No registradas' }}</p>
        <p><strong>Medicamentos:</strong> {{ $paciente->informacionMedica->medicamentos ?? 'No registrados' }}</p>
        <p><strong>Antecedentes:</strong> {{ $paciente->informacionMedica->antecedentes ?? 'No registrados' }}</p>
    @endif

    <h2>Contactos de emergencia</h2>
    @if ($paciente->contactosEmergencia->isEmpty())
        <p>No hay contactos de emergencia registrados.</p>
    @else
        <ul>
            @foreach ($paciente->contactosEmergencia as $contacto)
                <li>{{ $contacto->nombre }} — {{ $contacto->parentesco }} — {{ $contacto->telefono }}</li>
            @endforeach
        </ul>
    @endif

    <p>
        <a href="{{ route('pacientes.edit', $paciente) }}">Editar paciente</a> |
        <a href="{{ route('pacientes.index') }}">Volver a la lista</a>
    </p>
</body>
</html>
