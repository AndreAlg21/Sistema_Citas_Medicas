<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear especialidad</title>
</head>
<body>
    <h1>Crear especialidad</h1>

    @if ($errors->any())
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form action="{{ route('especialidades.store') }}" method="POST">
        @csrf

        <p>
            <label for="nombre">Nombre</label><br>
            <input
                type="text"
                id="nombre"
                name="nombre"
                value="{{ old('nombre') }}"
                maxlength="80"
                required
            >
        </p>

        <p>
            <label for="descripcion">Descripción</label><br>
            <textarea
                id="descripcion"
                name="descripcion"
                maxlength="200"
            >{{ old('descripcion') }}</textarea>
        </p>

        <p>
            <label for="estado">Estado</label><br>
            <select id="estado" name="estado" required>
                <option value="1" @selected(old('estado', '1') == '1')>Activa</option>
                <option value="0" @selected(old('estado') == '0')>Inactiva</option>
            </select>
        </p>

        <button type="submit">Guardar especialidad</button>
        <a href="{{ route('especialidades.index') }}">Cancelar</a>
    </form>
</body>
</html>
