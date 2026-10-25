<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar usuario</title>
</head>
<body>
    <h1>Editar usuario</h1>

    @if ($errors->any())
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form
        action="{{ route('usuarios.update', $usuario->id_usuario) }}"
        method="POST"
    >
        @csrf
        @method('PUT')

        <p>
            <label for="nombre_completo">Nombre completo</label><br>
            <input
                type="text"
                id="nombre_completo"
                name="nombre_completo"
                value="{{ old('nombre_completo', $usuario->nombre_completo) }}"
                maxlength="120"
                required
            >
        </p>

        <p>
            <label for="nombre_usuario">Nombre de usuario</label><br>
            <input
                type="text"
                id="nombre_usuario"
                name="nombre_usuario"
                value="{{ old('nombre_usuario', $usuario->nombre_usuario) }}"
                maxlength="40"
                required
            >
        </p>

        <p>
            <label for="correo">Correo electrónico</label><br>
            <input
                type="email"
                id="correo"
                name="correo"
                value="{{ old('correo', $usuario->correo) }}"
                maxlength="120"
                required
            >
        </p>

        <p>
            <label for="telefono">Teléfono</label><br>
            <input
                type="text"
                id="telefono"
                name="telefono"
                value="{{ old('telefono', $usuario->telefono) }}"
                maxlength="20"
            >
        </p>

        <p>
            <label for="id_rol">Rol</label><br>
            <select id="id_rol" name="id_rol" required>
                @foreach ($roles as $rol)
                    <option
                        value="{{ $rol->id_rol }}"
                        @selected(old('id_rol', $usuario->id_rol) == $rol->id_rol)
                    >
                        {{ $rol->nombre }}
                    </option>
                @endforeach
            </select>
        </p>

        <p>
            <label for="estado">Estado</label><br>
            <select id="estado" name="estado" required>
                <option
                    value="1"
                    @selected(old('estado', (string) (int) $usuario->estado) == '1')
                >
                    Activo
                </option>
                <option
                    value="0"
                    @selected(old('estado', (string) (int) $usuario->estado) == '0')
                >
                    Inactivo
                </option>
            </select>
        </p>

        <p>
            <label for="contrasena">Nueva contraseña</label><br>
            <input
                type="password"
                id="contrasena"
                name="contrasena"
                minlength="8"
                autocomplete="new-password"
            >
            <br>
            <small>Déjala vacía si no deseas cambiarla.</small>
        </p>

        <p>
            <label for="contrasena_confirmation">Repetir nueva contraseña</label><br>
            <input
                type="password"
                id="contrasena_confirmation"
                name="contrasena_confirmation"
                minlength="8"
                autocomplete="new-password"
            >
        </p>

        <button type="submit">Guardar cambios</button>
        <a href="{{ route('usuarios.index') }}">Cancelar</a>
    </form>
</body>
</html>