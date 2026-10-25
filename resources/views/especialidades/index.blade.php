<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de especialidades</title>
</head>
<body>
    <h1>Especialidades médicas</h1>

    @if (session('success'))
        <p>{{ session('success') }}</p>
    @endif

    @if ($errors->any())
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <p>
        <a href="{{ route('especialidades.create') }}">Crear especialidad</a>
        |
        <a href="{{ route('inicio') }}">Volver al inicio</a>
    </p>

    @if ($especialidades->isEmpty())
        <p>Todavía no hay especialidades registradas.</p>
    @else
        <table border="1" cellpadding="8">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($especialidades as $especialidad)
                    <tr>
                        <td>{{ $especialidad->id_especialidad }}</td>
                        <td>{{ $especialidad->nombre }}</td>
                        <td>{{ $especialidad->descripcion ?? 'Sin descripción' }}</td>
                        <td>{{ $especialidad->estado ? 'Activa' : 'Inactiva' }}</td>
                        <td>
                            <a href="{{ route('especialidades.show', $especialidad->id_especialidad) }}">Ver</a>
                            <a href="{{ route('especialidades.edit', $especialidad->id_especialidad) }}">Editar</a>

                            @if ($especialidad->estado)
                                <form action="{{ route('especialidades.destroy', $especialidad->id_especialidad) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('¿Deseas desactivar esta especialidad?')">
                                        Desactivar
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>
</html>
