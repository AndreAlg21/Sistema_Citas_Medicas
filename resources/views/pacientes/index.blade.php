@extends('layouts.app')
@section('title', 'Gestión de pacientes')
@section('content')
<h1>Gestión de pacientes</h1>
    @if (session('success')) <p>{{ session('success') }}</p> @endif
    @if ($errors->any())
        <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    @endif
    <p>
        <a href="{{ route('pacientes.create') }}">Registrar paciente</a> |
        <a href="{{ route('inicio') }}">Volver al inicio</a>
    </p>

    @if ($pacientes->isEmpty())
        <p>Todavía no hay pacientes registrados.</p>
    @else
        <table border="1" cellpadding="8">
            <thead>
                <tr><th>Código</th><th>Paciente</th><th>Cédula</th><th>Teléfono</th><th>Correo</th><th>Estado</th><th>Acciones</th></tr>
            </thead>
            <tbody>
                @foreach ($pacientes as $paciente)
                    <tr>
                        <td>{{ $paciente->codigo }}</td>
                        <td>{{ $paciente->nombres }} {{ $paciente->apellidos }}</td>
                        <td>{{ $paciente->ci ?? 'No registrada' }}</td>
                        <td>{{ $paciente->telefono }}</td>
                        <td>{{ $paciente->correo ?? 'No registrado' }}</td>
                        <td>{{ $paciente->estado ? 'Activo' : 'Inactivo' }}</td>
                        <td>
                            <a href="{{ route('pacientes.show', $paciente) }}">Ver</a>
                            <a href="{{ route('citas.historial-paciente', $paciente) }}">Historial de citas</a>
                            <a href="{{ route('pacientes.edit', $paciente) }}">Editar</a>
                            @if ($paciente->estado)
                                <form action="{{ route('pacientes.destroy', $paciente) }}" method="POST" style="display:inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('¿Deseas desactivar este paciente?')">Desactivar</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $pacientes->links() }}
    @endif
@endsection
