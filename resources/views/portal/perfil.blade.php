@extends('layouts.app')
@section('title', 'Mi perfil')
@section('content')
@php($info = $paciente->informacionMedica)
@php($contacto = $paciente->contactosEmergencia->first())
<section>
    <h1>Mi perfil</h1>
    <p>El CI no se puede modificar desde el portal. Para corregirlo, comunícate con recepción.</p>
    <form method="POST" action="{{ route('portal.perfil.actualizar') }}">
        @csrf @method('PUT')
        <h2>Datos personales y contacto</h2>
        <label>Nombres <input name="nombres" value="{{ old('nombres', $paciente->nombres) }}" maxlength="80" required></label>
        <label>Apellidos <input name="apellidos" value="{{ old('apellidos', $paciente->apellidos) }}" maxlength="80" required></label>
        <label>CI <input value="{{ $paciente->ci }}" disabled></label>
        <label>Fecha de nacimiento <input type="date" name="fecha_nacimiento" value="{{ old('fecha_nacimiento', $paciente->fecha_nacimiento->format('Y-m-d')) }}" required></label>
        <label>Sexo <input name="sexo" value="{{ old('sexo', $paciente->sexo) }}" maxlength="15"></label>
        <label>Teléfono <input name="telefono" value="{{ old('telefono', $paciente->telefono) }}" maxlength="20" required></label>
        <label>Correo <input type="email" name="correo" value="{{ old('correo', auth()->user()->correo) }}" maxlength="120" required></label>
        <label>Dirección <input name="direccion" value="{{ old('direccion', $paciente->direccion) }}" maxlength="200"></label>
        <h2>Información médica básica</h2>
        <label>Tipo de sangre <input name="informacion_medica[tipo_sangre]" value="{{ old('informacion_medica.tipo_sangre', $info?->tipo_sangre) }}" maxlength="5"></label>
        <label>Alergias <textarea name="informacion_medica[alergias]">{{ old('informacion_medica.alergias', $info?->alergias) }}</textarea></label>
        <label>Enfermedades crónicas <textarea name="informacion_medica[enfermedades_cronicas]">{{ old('informacion_medica.enfermedades_cronicas', $info?->enfermedades_cronicas) }}</textarea></label>
        <label>Medicamentos <textarea name="informacion_medica[medicamentos]">{{ old('informacion_medica.medicamentos', $info?->medicamentos) }}</textarea></label>
        <label>Antecedentes relevantes <textarea name="informacion_medica[antecedentes]">{{ old('informacion_medica.antecedentes', $info?->antecedentes) }}</textarea></label>
        <h2>Contacto de emergencia</h2>
        <label>Nombre <input name="contacto[nombre]" value="{{ old('contacto.nombre', $contacto?->nombre) }}" maxlength="120"></label>
        <label>Parentesco <input name="contacto[parentesco]" value="{{ old('contacto.parentesco', $contacto?->parentesco) }}" maxlength="40"></label>
        <label>Teléfono <input name="contacto[telefono]" value="{{ old('contacto.telefono', $contacto?->telefono) }}" maxlength="20"></label>
        <h2>Preferencias</h2>
        <label>Recibir notificaciones <select name="acepta_notificaciones" required>
            <option value="1" {{ old('acepta_notificaciones', $paciente->acepta_notificaciones ? '1' : '0') === '1' ? 'selected' : '' }}>Sí</option>
            <option value="0" {{ old('acepta_notificaciones', $paciente->acepta_notificaciones ? '1' : '0') === '0' ? 'selected' : '' }}>No</option>
        </select></label>
        <label>Privacidad aceptada <select name="acepta_privacidad" required>
            <option value="1" {{ old('acepta_privacidad', $paciente->acepta_privacidad ? '1' : '0') === '1' ? 'selected' : '' }}>Sí</option>
            <option value="0" {{ old('acepta_privacidad', $paciente->acepta_privacidad ? '1' : '0') === '0' ? 'selected' : '' }}>No</option>
        </select></label>
        <h2>Cambiar contraseña (opcional)</h2>
        <label>Contraseña actual <input type="password" name="contrasena_actual" autocomplete="current-password"></label>
        <label>Nueva contraseña <input type="password" name="nueva_contrasena" minlength="8" autocomplete="new-password"></label>
        <label>Repetir nueva contraseña <input type="password" name="nueva_contrasena_confirmation" minlength="8" autocomplete="new-password"></label>
        <p><button type="submit">Guardar perfil</button></p>
    </form>
</section>
@endsection
