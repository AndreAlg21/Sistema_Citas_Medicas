@extends('layouts.app')
@section('title', 'Gestión de médicos')
@section('content')
<h1>Gestión de médicos</h1>

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
        <a href="{{ route('medicos.create') }}">Registrar médico</a>
        |
        <a href="{{ route('inicio') }}">Volver al inicio</a>
    </p>

    @if ($medicos->isEmpty())
        <p>Todavía no hay médicos registrados.</p>
    @else
        <table border="1" cellpadding="8">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Médico</th>
                    <th>Registro profesional</th>
                    <th>Especialidades</th>
                    <th>Cuenta de usuario</th>
                    <th>Teléfono</th>
                    <th>Correo</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($medicos as $medico)
                    <tr>
                        <td>{{ $medico->codigo }}</td>
                        <td>{{ $medico->nombres }} {{ $medico->apellidos }}</td>
                        <td>{{ $medico->registro_profesional }}</td>
                        <td>{{ $medico->especialidades->pluck('nombre')->join(', ') ?: 'Sin especialidad' }}</td>
                        <td>{{ $medico->usuario?->nombre_completo ?? 'Sin cuenta vinculada' }}</td>
                        <td>{{ $medico->telefono }}</td>
                        <td>{{ $medico->correo }}</td>
                        <td>{{ $medico->estado ? 'Activo' : 'Inactivo' }}</td>
                        <td>
                            <a href="{{ route('medicos.show', $medico) }}">Ver</a>
                            <a href="{{ route('medicos.edit', $medico) }}">Editar</a>

                            @if ($medico->estado)
                                <form action="{{ route('medicos.destroy', $medico) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('¿Deseas desactivar este médico?')">
                                        Desactivar
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $medicos->links() }}
    @endif
@endsection
