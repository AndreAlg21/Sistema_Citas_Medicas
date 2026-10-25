<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear rol</title>
</head>
<body>
    <h1>Crear rol</h1>

    @if ($errors->any())
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form action="{{ route('roles.store') }}" method="POST">
        @csrf

        <p>
            <label for="nombre">Nombre del rol</label><br>
            <input
                type="text"
                id="nombre"
                name="nombre"
                value="{{ old('nombre') }}"
                maxlength="40"
                required
            >
        </p>

        <p>
            <label for="descripcion">Descripción</label><br>
            <textarea
                id="descripcion"
                name="descripcion"
                maxlength="150"
            >{{ old('descripcion') }}</textarea>
        </p>

        <p>
            <label for="estado">Estado</label><br>
            <select id="estado" name="estado" required>
                <option value="1" @selected(old('estado', '1') == '1')>Activo</option>
                <option value="0" @selected(old('estado') == '0')>Inactivo</option>
            </select>
        </p>

        <h2>Permisos del rol</h2>

        @if ($permisos->isEmpty())
            <p>No hay permisos registrados.</p>
        @else
            @foreach ($permisos as $permiso)
                <p>
                    <label>
                        <input
                            type="checkbox"
                            name="permisos[]"
                            value="{{ $permiso->id_permiso }}"
                            @checked(in_array($permiso->id_permiso, old('permisos', [])))
                        >
                        <strong>{{ $permiso->codigo }}</strong>
                        — {{ $permiso->descripcion }}
                    </label>
                </p>
            @endforeach
        @endif

        <button type="submit">Guardar rol</button>
        <a href="{{ route('roles.index') }}">Cancelar</a>
    </form>
</body>
</html>