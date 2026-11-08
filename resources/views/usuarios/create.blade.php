@extends('layouts.app')
@section('title', 'Crear cuenta de usuario')
@section('content')
@php
    $idRolPaciente = $roles->firstWhere('nombre', 'Paciente')?->id_rol;
    $idRolMedico = $roles->firstWhere('nombre', 'Medico')?->id_rol;
@endphp

<h1>Crear cuenta y perfil</h1>
<p>Registra los datos de acceso y, para pacientes o médicos, completa también su ficha en este mismo formulario.</p>

@if ($errors->any())
    <div role="alert" style="margin:16px 0;padding:14px;border:1px solid #efc8c3;border-radius:12px;background:#fff7f5;color:#8d3026">
        <strong>Revisa estos datos:</strong>
        <ul style="margin:8px 0 0;padding-left:22px">
            @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
@endif

<form action="{{ route('usuarios.store') }}" method="POST" id="form-crear-usuario">
    @csrf

    <section style="margin:18px 0;padding:20px;border:1px solid #dcebe0;border-radius:16px;background:#fff">
        <h2>1. Datos de la cuenta</h2>
        <p>Estos datos permiten iniciar sesión. El usuario sugerido se forma con la inicial del nombre y el apellido.</p>

        <p>
            <label for="id_rol">Tipo de usuario</label><br>
            <select id="id_rol" name="id_rol" required>
                <option value="">Selecciona un rol</option>
                @foreach ($roles as $rol)
                    <option value="{{ $rol->id_rol }}" data-role-name="{{ $rol->nombre }}" @selected(old('id_rol') == $rol->id_rol)>{{ $rol->nombre }}</option>
                @endforeach
            </select>
        </p>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:0 18px">
            <p><label for="nombres">Nombres</label><br><input id="nombres" name="nombres" maxlength="80" value="{{ old('nombres') }}" required></p>
            <p><label for="apellidos">Apellidos</label><br><input id="apellidos" name="apellidos" maxlength="80" value="{{ old('apellidos') }}" required></p>
            <p><label for="nombre_usuario">Usuario para iniciar sesión</label><br><input id="nombre_usuario" name="nombre_usuario" maxlength="40" value="{{ old('nombre_usuario') }}" required autocomplete="username"><small>Puedes modificar la sugerencia si ya está ocupada.</small></p>
            <p><label for="correo">Correo electrónico</label><br><input id="correo" name="correo" type="email" maxlength="120" value="{{ old('correo') }}" required autocomplete="email"></p>
            <p><label for="telefono">Teléfono</label><br><input id="telefono" name="telefono" maxlength="20" value="{{ old('telefono') }}" required></p>
            <p><label for="estado">Estado de la cuenta</label><br><select id="estado" name="estado" required><option value="1" @selected(old('estado', '1') == '1')>Activa</option><option value="0" @selected(old('estado') == '0')>Inactiva</option></select></p>
            <p><label for="contrasena">Contraseña</label><br><input id="contrasena" name="contrasena" type="password" minlength="8" required autocomplete="new-password"></p>
            <p><label for="contrasena_confirmation">Repetir contraseña</label><br><input id="contrasena_confirmation" name="contrasena_confirmation" type="password" minlength="8" required autocomplete="new-password"></p>
        </div>
    </section>

    <section data-role-section="Paciente" @if(old('id_rol') != $idRolPaciente) hidden @endif style="margin:18px 0;padding:20px;border:1px solid #dcebe0;border-radius:16px;background:#fff">
        <h2>2. Ficha del paciente</h2>
        <p>La cuenta se vinculará automáticamente con esta ficha. El CI será el identificador del paciente.</p>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:0 18px">
            <p><label for="paciente_codigo">Código de paciente</label><br><input id="paciente_codigo" name="codigo" maxlength="12" value="{{ old('codigo') }}" data-required="true"></p>
            <p><label for="ci">Carnet de identidad (CI)</label><br><input id="ci" name="ci" maxlength="20" value="{{ old('ci') }}" data-required="true"></p>
            <p><label for="fecha_nacimiento">Fecha de nacimiento</label><br><input id="fecha_nacimiento" name="fecha_nacimiento" type="date" max="{{ now()->toDateString() }}" value="{{ old('fecha_nacimiento') }}" data-required="true"></p>
            <p><label for="sexo">Sexo</label><br><select id="sexo" name="sexo"><option value="">Sin especificar</option>@foreach (['Femenino', 'Masculino', 'Otro', 'Prefiere no decir'] as $opcionSexo)<option value="{{ $opcionSexo }}" @selected(old('sexo') == $opcionSexo)>{{ $opcionSexo }}</option>@endforeach</select></p>
            <p><label for="direccion">Dirección</label><br><input id="direccion" name="direccion" maxlength="200" value="{{ old('direccion') }}"></p>
            <p><label for="acepta_notificaciones">¿Acepta notificaciones?</label><br><select id="acepta_notificaciones" name="acepta_notificaciones" data-required="true"><option value="1" @selected(old('acepta_notificaciones', '1') == '1')>Sí</option><option value="0" @selected(old('acepta_notificaciones') == '0')>No</option></select></p>
            <p><label for="acepta_privacidad">¿Acepta el aviso de privacidad?</label><br><select id="acepta_privacidad" name="acepta_privacidad" data-required="true"><option value="">Selecciona</option><option value="1" @selected(old('acepta_privacidad') === '1')>Sí</option><option value="0" @selected(old('acepta_privacidad') === '0')>No</option></select></p>
        </div>
        <h3>Información médica (opcional)</h3>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:0 18px">
            <p><label for="tipo_sangre">Tipo de sangre</label><br><input id="tipo_sangre" name="informacion_medica[tipo_sangre]" maxlength="5" value="{{ old('informacion_medica.tipo_sangre') }}"></p>
            <p><label for="alergias">Alergias</label><br><textarea id="alergias" name="informacion_medica[alergias]">{{ old('informacion_medica.alergias') }}</textarea></p>
            <p><label for="enfermedades_cronicas">Enfermedades crónicas</label><br><textarea id="enfermedades_cronicas" name="informacion_medica[enfermedades_cronicas]">{{ old('informacion_medica.enfermedades_cronicas') }}</textarea></p>
            <p><label for="medicamentos">Medicamentos</label><br><textarea id="medicamentos" name="informacion_medica[medicamentos]">{{ old('informacion_medica.medicamentos') }}</textarea></p>
            <p><label for="antecedentes">Antecedentes</label><br><textarea id="antecedentes" name="informacion_medica[antecedentes]">{{ old('informacion_medica.antecedentes') }}</textarea></p>
        </div>
        <h3>Contacto de emergencia (opcional)</h3>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:0 18px">
            <p><label for="contacto_nombre">Nombre</label><br><input id="contacto_nombre" name="contacto[nombre]" maxlength="120" value="{{ old('contacto.nombre') }}"></p>
            <p><label for="contacto_parentesco">Parentesco</label><br><input id="contacto_parentesco" name="contacto[parentesco]" maxlength="40" value="{{ old('contacto.parentesco') }}"></p>
            <p><label for="contacto_telefono">Teléfono</label><br><input id="contacto_telefono" name="contacto[telefono]" maxlength="20" value="{{ old('contacto.telefono') }}"></p>
        </div>
    </section>

    <section data-role-section="Medico" @if(old('id_rol') != $idRolMedico) hidden @endif style="margin:18px 0;padding:20px;border:1px solid #dcebe0;border-radius:16px;background:#fff">
        <h2>2. Ficha del médico</h2>
        <p>La cuenta se vinculará automáticamente con esta ficha. Selecciona al menos una especialidad.</p>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:0 18px">
            <p><label for="medico_codigo">Código de médico</label><br><input id="medico_codigo" name="codigo" maxlength="12" value="{{ old('codigo') }}" data-required="true"></p>
            <p><label for="registro_profesional">Registro profesional</label><br><input id="registro_profesional" name="registro_profesional" maxlength="30" value="{{ old('registro_profesional') }}" data-required="true"></p>
        </div>
        <fieldset style="margin:14px 0;padding:14px;border:1px solid #dcebe0;border-radius:12px">
            <legend>Especialidades</legend>
            @forelse ($especialidades as $especialidad)
                <label style="display:inline-flex;align-items:center;gap:8px;margin:6px 16px 6px 0">
                    <input type="checkbox" name="especialidades[]" value="{{ $especialidad->id_especialidad }}" @checked(in_array((string) $especialidad->id_especialidad, array_map('strval', old('especialidades', [])), true))>
                    {{ $especialidad->nombre }}
                </label>
            @empty
                <p>No hay especialidades activas. Primero registra una desde el menú de Especialidades.</p>
            @endforelse
        </fieldset>
        <p><label for="puede_marcar_atendida">¿Puede marcar citas como atendidas?</label><br><select id="puede_marcar_atendida" name="puede_marcar_atendida"><option value="0" @selected(old('puede_marcar_atendida', '0') == '0')>No</option><option value="1" @selected(old('puede_marcar_atendida') == '1')>Sí</option></select></p>
    </section>

    <p style="display:flex;flex-wrap:wrap;gap:10px;margin-top:20px"><button type="submit">Crear cuenta y guardar perfil</button><a href="{{ route('usuarios.index') }}">Cancelar</a></p>
</form>

<script>
(() => {
    const rol = document.getElementById('id_rol');
    const secciones = document.querySelectorAll('[data-role-section]');
    const nombres = document.getElementById('nombres');
    const apellidos = document.getElementById('apellidos');
    const usuario = document.getElementById('nombre_usuario');
    let usuarioEditado = usuario.value !== '';

    const generarUsuario = () => {
        if (usuarioEditado) return;
        const limpiar = (texto) => texto.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase().replace(/[^a-z0-9]/g, '');
        const nombre = limpiar(nombres.value.trim().split(/\s+/)[0] || '');
        const apellido = limpiar(apellidos.value.trim().split(/\s+/)[0] || '');
        usuario.value = nombre && apellido ? nombre[0] + apellido : '';
    };

    usuario.addEventListener('input', () => { usuarioEditado = true; });
    nombres.addEventListener('input', generarUsuario);
    apellidos.addEventListener('input', generarUsuario);

    const actualizarSecciones = () => {
        const rolSeleccionado = rol.options[rol.selectedIndex]?.dataset.roleName || '';
        secciones.forEach((seccion) => {
            const visible = seccion.dataset.roleSection === rolSeleccionado;
            seccion.hidden = !visible;
            seccion.querySelectorAll('input, select, textarea').forEach((campo) => { campo.disabled = !visible; });
            seccion.querySelectorAll('[data-required="true"]').forEach((campo) => {
                campo.required = visible;
            });
            const especialidades = seccion.querySelectorAll('input[name="especialidades[]"]');
            especialidades.forEach((campo, indice) => { campo.required = visible && indice === 0; });
        });
    };

    rol.addEventListener('change', actualizarSecciones);
    actualizarSecciones();
})();
</script>
@endsection