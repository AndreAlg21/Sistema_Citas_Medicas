<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios</title>
</head>
<body>
    <h1>Gestión de usuarios</h1>

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
        <a href="{{ route('usuarios.create') }}">Registrar usuario</a>
        |
        <a href="{{ route('inicio') }}">Volver al inicio</a>
    </p>

    @if ($usuarios->isEmpty())
        <p>Todavía no hay usuarios registrados.</p>
    @else
        <table border="1" cellpadding="8">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Usuario</th>
                    <th>Correo</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($usuarios as $usuario)
                    <tr>
                        <td>{{ $usuario->nombre_completo }}</td>
                        <td>{{ $usuario->nombre_usuario }}</td>
                        <td>{{ $usuario->correo }}</td>
                        <td>{{ $usuario->role?->nombre ?? 'Sin rol' }}</td>
                        <td>{{ $usuario->estado ? 'Activo' : 'Inactivo' }}</td>
                        <td>
                            <a href="{{ route('usuarios.show', $usuario->id_usuario) }}">
                                Ver
                            </a>

                            <a href="{{ route('usuarios.edit', $usuario->id_usuario) }}">
                                Editar
                            </a>

                            @if ($usuario->estado)
                                <form
                                    action="{{ route('usuarios.destroy', $usuario->id_usuario) }}"
                                    method="POST"
                                    style="display: inline;"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit">Desactivar</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $usuarios->links() }}
    @endif
</body>
</html>