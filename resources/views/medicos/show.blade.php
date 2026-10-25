<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Datos del médico</title>
</head>
<body>
    <h1>Datos del médico</h1>

    <p><strong>Código:</strong> {{ $medico->codigo }}</p>
    <p><strong>Nombre:</strong> {{ $medico->nombres }} {{ $medico->apellidos }}</p>
    <p><strong>Registro profesional:</strong> {{ $medico->registro_profesional }}</p>
    <p><strong>Teléfono:</strong> {{ $medico->telefono }}</p>
    <p><strong>Correo:</strong> {{ $medico->correo }}</p>
    <p><strong>Cuenta de usuario:</strong> {{ $medico->usuario?->nombre_completo ?? 'Sin cuenta vinculada' }}</p>
    <p><strong>Puede marcar citas como atendidas:</strong> {{ $medico->puede_marcar_atendida ? 'Sí' : 'No' }}</p>
    <p><strong>Estado:</strong> {{ $medico->estado ? 'Activo' : 'Inactivo' }}</p>

    <h2>Especialidades</h2>
    @if ($medico->especialidades->isEmpty())
        <p>Este médico no tiene especialidades registradas.</p>
    @else
        <ul>
            @foreach ($medico->especialidades as $especialidad)
                <li>{{ $especialidad->nombre }}</li>
            @endforeach
        </ul>
    @endif

    <p>
        <a href="{{ route('medicos.edit', $medico) }}">Editar médico</a>
        |
        <a href="{{ route('medicos.index') }}">Volver a la lista</a>
    </p>
</body>
</html>
