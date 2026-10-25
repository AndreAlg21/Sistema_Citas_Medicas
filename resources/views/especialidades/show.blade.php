<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de especialidad</title>
</head>
<body>
    <h1>Datos de la especialidad</h1>

    <p><strong>ID:</strong> {{ $especialidad->id_especialidad }}</p>
    <p><strong>Nombre:</strong> {{ $especialidad->nombre }}</p>
    <p><strong>Descripción:</strong> {{ $especialidad->descripcion ?? 'Sin descripción' }}</p>
    <p><strong>Estado:</strong> {{ $especialidad->estado ? 'Activa' : 'Inactiva' }}</p>

    <p>
        <a href="{{ route('especialidades.edit', $especialidad->id_especialidad) }}">Editar especialidad</a>
        |
        <a href="{{ route('especialidades.index') }}">Volver a la lista</a>
    </p>
</body>
</html>
