@extends('layouts.app')
@section('title', 'Detalle de especialidad')
@section('content')
<h1>Datos de la especialidad</h1>

    <p><strong>ID:</strong> {{ $especialidad->id_especialidad }}</p>
    <p><strong>Nombre:</strong> {{ $especialidad->nombre }}</p>
    <p><strong>Descripción:</strong> {{ $especialidad->descripcion ?? 'Sin descripción' }}</p>
    <p><strong>Estado:</strong> {{ $especialidad->estado ? 'Activa' : 'Inactiva' }}</p>

    <p>
        <a href="{{ route('especialidades.edit', $especialidad->id_especialidad) }}">Editar especialidad</a>
        |
        <a href="{{ route('especialidades.index') }}">Volver a la lista</a>
    </p>
@endsection
