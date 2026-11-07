@extends('layouts.app')
@section('title', 'Gestión de roles')
@section('content')
<h1>Gestión de roles y permisos</h1>

    @if (session('success'))
        <p>{{ session('success') }}</p>
    @endif

    @if ($errors->any())
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <p>
        <a href="{{ route('roles.create') }}">Crear rol</a>
        |
        <a href="{{ route('inicio') }}">Volver al inicio</a>
    </p>

    @if ($roles->isEmpty())
        <p>Todavía no hay roles registrados.</p>
    @else
        <table border="1" cellpadding="8">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Usuarios</th>
                    <th>Permisos</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($roles as $role)
                    <tr>
                        <td>{{ $role->nombre }}</td>
                        <td>{{ $role->descripcion ?? 'Sin descripción' }}</td>
                        <td>{{ $role->usuarios_count }}</td>
                        <td>{{ $role->permisos_count }}</td>
                        <td>{{ $role->estado ? 'Activo' : 'Inactivo' }}</td>
                        <td>
                            <a href="{{ route('roles.show', $role->id_rol) }}">Ver</a>
                            <a href="{{ route('roles.edit', $role->id_rol) }}">Editar</a>

                            @if ($role->estado)
                                <form
                                    action="{{ route('roles.destroy', $role->id_rol) }}"
                                    method="POST"
                                    style="display: inline;"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit">Desactivar</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $roles->links() }}
    @endif
@endsection
