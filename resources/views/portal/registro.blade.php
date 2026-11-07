<!DOCTYPE html>
<html lang="es"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Crear cuenta</title></head>
<body>
    <h1>Crear cuenta de paciente</h1>
    @if ($errors->any())<ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>@endif
    <form method="POST" action="{{ route('pacientes.registro.store') }}">
        @csrf
        <label>Nombres <input name="nombres" value="{{ old('nombres') }}" maxlength="80" required></label><br>
        <label>Apellidos <input name="apellidos" value="{{ old('apellidos') }}" maxlength="80" required></label><br>
        <label>Documento de identidad (CI) <input name="ci" value="{{ old('ci') }}" maxlength="20" required></label><br>
        <label>Fecha de nacimiento <input type="date" name="fecha_nacimiento" value="{{ old('fecha_nacimiento') }}" required></label><br>
        <label>Teléfono <input name="telefono" value="{{ old('telefono') }}" maxlength="20" required></label><br>
        <label>Correo <input type="email" name="correo" value="{{ old('correo') }}" maxlength="120" required></label><br>
        <label>Contraseña <input type="password" name="contrasena" minlength="8" required></label><br>
        <label>Repetir contraseña <input type="password" name="contrasena_confirmation" minlength="8" required></label><br>
        <label><input type="checkbox" name="acepta_privacidad" value="1" required> Acepto el tratamiento de mis datos para gestionar mi atención.</label><br>
        <button type="submit">Crear cuenta</button>
    </form>
    <p><a href="{{ route('login') }}">Volver al inicio de sesión</a></p>
</body></html>
