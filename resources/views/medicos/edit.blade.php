<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar médico</title>
</head>
<body>
    <h1>Editar médico</h1>

    @if ($errors->any())
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    @php
        $especialidadesSeleccionadas = old(
            'especialidades',
            $medico->especialidades->pluck('id_especialidad')->all()
        );
        $especialidadesSeleccionadas = array_map('strval', $especialidadesSeleccionadas);
    @endphp

    <form action="{{ route('medicos.update', $medico) }}" method="POST">
        @csrf
        @method('PUT')

        <p>
            <label for="codigo">Código</label><br>
            <input id="codigo" name="codigo" type="text" maxlength="12" value="{{ old('codigo', $medico->codigo) }}" required>
        </p>

        <p>
            <label for="registro_profesional">Registro profesional</label><br>
            <input id="registro_profesional" name="registro_profesional" type="text" maxlength="30" value="{{ old('registro_profesional', $medico->registro_profesional) }}" required>
        </p>

        <p>
            <label for="nombres">Nombres</label><br>
            <input id="nombres" name="nombres" type="text" maxlength="80" value="{{ old('nombres', $medico->nombres) }}" required>
        </p>

        <p>
            <label for="apellidos">Apellidos</label><br>
            <input id="apellidos" name="apellidos" type="text" maxlength="80" value="{{ old('apellidos', $medico->apellidos) }}" required>
        </p>

        <p>
            <label for="telefono">Teléfono</label><br>
            <input id="telefono" name="telefono" type="text" maxlength="20" value="{{ old('telefono', $medico->telefono) }}" required>
        </p>

        <p>
            <label for="correo">Correo</label><br>
            <input id="correo" name="correo" type="email" maxlength="120" value="{{ old('correo', $medico->correo) }}" required>
        </p>

        <p>
            <label for="id_usuario">Cuenta de usuario (opcional)</label><br>
            <select id="id_usuario" name="id_usuario">
                <option value="">Sin cuenta vinculada</option>
                @foreach ($usuarios as $usuario)
                    <option value="{{ $usuario->id_usuario }}" @selected(old('id_usuario', $medico->id_usuario) == $usuario->id_usuario)>
                        {{ $usuario->nombre_completo }} ({{ $usuario->nombre_usuario }})
                    </option>
                @endforeach
            </select>
        </p>

        <h2>Especialidades</h2>
        @if ($especialidades->isEmpty())
            <p>No hay especialidades activas.</p>
        @else
            @foreach ($especialidades as $especialidad)
                <p>
                    <label>
                        <input
                            type="checkbox"
                            name="especialidades[]"
                            value="{{ $especialidad->id_especialidad }}"
                            @checked(in_array((string) $especialidad->id_especialidad, $especialidadesSeleccionadas, true))
                        >
                        {{ $especialidad->nombre }}
                    </label>
                </p>
            @endforeach
        @endif

        <p>
            <label for="puede_marcar_atendida">¿Puede marcar citas como atendidas?</label><br>
            <select id="puede_marcar_atendida" name="puede_marcar_atendida" required>
                <option value="1" @selected(old('puede_marcar_atendida', (string) (int) $medico->puede_marcar_atendida) == '1')>Sí</option>
                <option value="0" @selected(old('puede_marcar_atendida', (string) (int) $medico->puede_marcar_atendida) == '0')>No</option>
            </select>
        </p>

        <p>
            <label for="estado">Estado</label><br>
            <select id="estado" name="estado" required>
                <option value="1" @selected(old('estado', (string) (int) $medico->estado) == '1')>Activo</option>
                <option value="0" @selected(old('estado', (string) (int) $medico->estado) == '0')>Inactivo</option>
            </select>
        </p>

        <button type="submit">Guardar cambios</button>
        <a href="{{ route('medicos.index') }}">Cancelar</a>
    </form>
</body>
</html>
