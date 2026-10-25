<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle del rol</title>
</head>
<body>
    <h1>Datos del rol</h1>

    <p><strong>Nombre:</strong> {{ $role->nombre }}</p>
    <p><strong>Descripción:</strong> {{ $role->descripcion ?? 'Sin descripción' }}</p>
    <p><strong>Estado:</strong> {{ $role->estado ? 'Activo' : 'Inactivo' }}</p>

    <h2>Permisos asignados</h2>

    @if ($role->permisos->isEmpty())
        <p>Este rol no tiene permisos asignados.</p>
    @else
        <ul>
            @foreach ($role->permisos as $permiso)
                <li>
                    <strong>{{ $permiso->codigo }}</strong>
                    — {{ $permiso->descripcion }}
                </li>
            @endforeach
        </ul>
    @endif

    <p>
        <a href="{{ route('roles.edit', $role->id_rol) }}">Editar rol</a>
        |
        <a href="{{ route('roles.index') }}">Volver a la lista</a>
    </p>
</body>
</html>