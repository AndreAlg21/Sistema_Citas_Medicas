@extends('layouts.app')
@section('title', 'Detalle del usuario')
@section('content')
<h1>Datos del usuario</h1>

    <p><strong>Nombre:</strong> {{ $usuario->nombre_completo }}</p>
    <p><strong>Nombre de usuario:</strong> {{ $usuario->nombre_usuario }}</p>
    <p><strong>Correo:</strong> {{ $usuario->correo }}</p>
    <p><strong>Teléfono:</strong> {{ $usuario->telefono ?? 'No registrado' }}</p>
    <p><strong>Rol:</strong> {{ $usuario->role?->nombre ?? 'Sin rol' }}</p>
    <p><strong>Estado:</strong> {{ $usuario->estado ? 'Activo' : 'Inactivo' }}</p>

    <p>
        <strong>Último acceso:</strong>
        {{ $usuario->ultimo_acceso?->format('d/m/Y H:i') ?? 'Todavía no ha iniciado sesión' }}
    </p>

    <p>
        <strong>Cuenta creada:</strong>
        {{ $usuario->created_at?->format('d/m/Y H:i') ?? 'Sin fecha registrada' }}
    </p>

    <p>
        <a href="{{ route('usuarios.edit', $usuario->id_usuario) }}">Editar usuario</a>
        |
        <a href="{{ route('usuarios.index') }}">Volver a la lista</a>
    </p>
@endsection
