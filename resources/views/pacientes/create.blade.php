<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar paciente</title>
</head>
<body>
    <h1>Registrar paciente</h1>
    @if ($errors->any())
        <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    @endif

    <form action="{{ route('pacientes.store') }}" method="POST">
        @csrf
        <h2>Datos personales</h2>
        <p><label for="codigo">Código</label><br><input id="codigo" name="codigo" maxlength="12" value="{{ old('codigo') }}" required></p>
        <p><label for="ci">Cédula / CI (opcional)</label><br><input id="ci" name="ci" maxlength="20" value="{{ old('ci') }}"></p>
        <p><label for="nombres">Nombres</label><br><input id="nombres" name="nombres" maxlength="80" value="{{ old('nombres') }}" required></p>
        <p><label for="apellidos">Apellidos</label><br><input id="apellidos" name="apellidos" maxlength="80" value="{{ old('apellidos') }}" required></p>
        <p><label for="fecha_nacimiento">Fecha de nacimiento</label><br><input id="fecha_nacimiento" name="fecha_nacimiento" type="date" max="{{ now()->toDateString() }}" value="{{ old('fecha_nacimiento') }}" required></p>
        <p>
            <label for="sexo">Sexo</label><br>
            <select id="sexo" name="sexo">
                <option value="">Sin especificar</option>
                @foreach (['Femenino', 'Masculino', 'Otro', 'Prefiere no decir'] as $opcionSexo)
                    <option value="{{ $opcionSexo }}" @selected(old('sexo') == $opcionSexo)>{{ $opcionSexo }}</option>
                @endforeach
            </select>
        </p>
        <p><label for="telefono">Teléfono</label><br><input id="telefono" name="telefono" maxlength="20" value="{{ old('telefono') }}" required></p>
        <p><label for="correo">Correo (opcional)</label><br><input id="correo" name="correo" type="email" maxlength="120" value="{{ old('correo') }}"></p>
        <p><label for="direccion">Dirección (opcional)</label><br><textarea id="direccion" name="direccion" maxlength="200">{{ old('direccion') }}</textarea></p>
        <p>
            <label for="id_usuario">Cuenta de usuario (opcional)</label><br>
            <select id="id_usuario" name="id_usuario">
                <option value="">Sin cuenta vinculada</option>
                @foreach ($usuarios as $usuario)
                    <option value="{{ $usuario->id_usuario }}" @selected(old('id_usuario') == $usuario->id_usuario)>{{ $usuario->nombre_completo }} ({{ $usuario->nombre_usuario }})</option>
                @endforeach
            </select>
        </p>
        <p><label for="acepta_notificaciones">¿Acepta notificaciones?</label><br><select id="acepta_notificaciones" name="acepta_notificaciones" required><option value="1" @selected(old('acepta_notificaciones', '1') == '1')>Sí</option><option value="0" @selected(old('acepta_notificaciones') == '0')>No</option></select></p>
        <p><label for="acepta_privacidad">¿Acepta el aviso de privacidad?</label><br><select id="acepta_privacidad" name="acepta_privacidad" required><option value="0" @selected(old('acepta_privacidad', '0') == '0')>No</option><option value="1" @selected(old('acepta_privacidad') == '1')>Sí</option></select></p>
        <p><label for="estado">Estado</label><br><select id="estado" name="estado" required><option value="1" @selected(old('estado', '1') == '1')>Activo</option><option value="0" @selected(old('estado') == '0')>Inactivo</option></select></p>

        <h2>Información médica (opcional)</h2>
        <p><label for="tipo_sangre">Tipo de sangre</label><br><input id="tipo_sangre" name="informacion_medica[tipo_sangre]" maxlength="5" value="{{ old('informacion_medica.tipo_sangre') }}"></p>
        <p><label for="alergias">Alergias</label><br><textarea id="alergias" name="informacion_medica[alergias]">{{ old('informacion_medica.alergias') }}</textarea></p>
        <p><label for="enfermedades_cronicas">Enfermedades crónicas</label><br><textarea id="enfermedades_cronicas" name="informacion_medica[enfermedades_cronicas]">{{ old('informacion_medica.enfermedades_cronicas') }}</textarea></p>
        <p><label for="medicamentos">Medicamentos</label><br><textarea id="medicamentos" name="informacion_medica[medicamentos]">{{ old('informacion_medica.medicamentos') }}</textarea></p>
        <p><label for="antecedentes">Antecedentes</label><br><textarea id="antecedentes" name="informacion_medica[antecedentes]">{{ old('informacion_medica.antecedentes') }}</textarea></p>

        <h2>Contactos de emergencia (opcional)</h2>
        <div id="contactos">
            <fieldset>
                <legend>Contacto</legend>
                <label>Nombre<br><input name="contactos[0][nombre]" maxlength="120" value="{{ old('contactos.0.nombre') }}"></label><br>
                <label>Parentesco<br><input name="contactos[0][parentesco]" maxlength="40" value="{{ old('contactos.0.parentesco') }}"></label><br>
                <label>Teléfono<br><input name="contactos[0][telefono]" maxlength="20" value="{{ old('contactos.0.telefono') }}"></label>
            </fieldset>
        </div>
        <button type="button" id="agregar-contacto">Agregar otro contacto</button>

        <p><button type="submit">Guardar paciente</button> <a href="{{ route('pacientes.index') }}">Cancelar</a></p>
    </form>
    <script>
        let siguienteContacto = 1;
        document.getElementById('agregar-contacto').addEventListener('click', function () {
            const contenedor = document.getElementById('contactos');
            const fieldset = document.createElement('fieldset');
            fieldset.innerHTML =
                '<legend>Contacto</legend>' +
                '<label>Nombre<br><input name="contactos[' + siguienteContacto + '][nombre]" maxlength="120"></label><br>' +
                '<label>Parentesco<br><input name="contactos[' + siguienteContacto + '][parentesco]" maxlength="40"></label><br>' +
                '<label>Teléfono<br><input name="contactos[' + siguienteContacto + '][telefono]" maxlength="20"></label> ' +
                '<button type="button" class="quitar-contacto">Quitar</button>';
            contenedor.appendChild(fieldset);
            siguienteContacto++;
            fieldset.querySelector('.quitar-contacto').addEventListener('click', function () {
                fieldset.remove();
            });
        });
    </script>
</body>
</html>
