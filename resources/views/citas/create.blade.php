@extends('layouts.app')
@section('title', 'Registrar cita')
@section('content')
<section class="page-heading">
    <span class="eyebrow">Recepción · Nueva reserva</span>
    <h1>Registrar cita</h1>
    <p class="muted">Ingresa el carnet para identificar al paciente y completar los datos de la cita.</p>
</section>

<section class="booking-panel">
    <div class="booking-panel__intro">
        <span class="booking-panel__number">01</span>
        <div><h2>Datos de la cita</h2><p>La clínica atiende de lunes a viernes, de 07:00 a 12:00 y de 14:00 a 18:00, con turnos cada 15 minutos. Puedes reservar dentro de los próximos 14 días.</p></div>
    </div>
    <form method="GET" action="{{ route('citas.create') }}" class="stack-form booking-form">
        <label for="ci">Carnet de identidad del paciente
            <input id="ci" name="ci" type="text" maxlength="20" value="{{ $ci }}" placeholder="Escribe el carnet" autocomplete="off" required>
        </label>
        <div id="patientFeedback" aria-live="polite">
            @if ($pacienteNoEncontrado)
                <div class="notice notice-error">No encontramos una ficha activa con ese carnet. Verifica el número o registra al paciente primero.</div>
            @elseif ($pacienteSeleccionado)
                <div class="patient-card">
                    <span class="patient-card__icon">{{ mb_substr($pacienteSeleccionado->nombres, 0, 1) }}{{ mb_substr($pacienteSeleccionado->apellidos, 0, 1) }}</span>
                    <div><span class="eyebrow">Paciente identificado</span><strong>{{ $pacienteSeleccionado->nombres }} {{ $pacienteSeleccionado->apellidos }}</strong><span class="muted">Carnet: {{ $pacienteSeleccionado->ci }} · Código: {{ $pacienteSeleccionado->codigo }}</span></div>
                </div>
            @else
                <div class="notice">Escribe el carnet para consultar la ficha del paciente.</div>
            @endif
        </div>

        <label for="id_especialidad">Especialidad
            <select id="id_especialidad" name="id_especialidad" @disabled(!$pacienteSeleccionado) required>
                <option value="">Selecciona una especialidad</option>
                @foreach ($especialidades as $especialidad)
                    <option value="{{ $especialidad->id_especialidad }}" @selected(($filtros['id_especialidad'] ?? request('id_especialidad')) == $especialidad->id_especialidad)>{{ $especialidad->nombre }}</option>
                @endforeach
            </select>
        </label>
        <label for="fecha">Fecha de la cita
            <input type="date" id="fecha" name="fecha" data-weekdays-only min="{{ today()->toDateString() }}" max="{{ today()->addDays(13)->toDateString() }}" value="{{ $filtros['fecha'] ?? request('fecha') }}" required>
        </label>
        <button type="submit" class="button booking-submit">Buscar horarios disponibles <span aria-hidden="true">→</span></button>
    </form>
</section>

@if (($filtros !== []) && !$errors->any())
<section>
    <div class="section-heading"><div><span class="eyebrow">Siguiente paso</span><h2>Selecciona un turno</h2></div><span class="date-chip">{{ \Illuminate\Support\Carbon::parse($filtros['fecha'])->format('d/m/Y') }}</span></div>
    @if (!$pacienteSeleccionado)
        <div class="notice notice-error">Primero ingresa un carnet válido para mostrar y confirmar la ficha del paciente.</div>
    @elseif (empty($opciones))
        <div class="notice">No encontramos turnos libres con esos datos. Prueba otra fecha dentro de las próximas dos semanas.</div>
    @else
        <div class="appointment-list">
        @foreach ($opciones as $opcion)
            <article class="appointment-option">
                <div class="appointment-option__time"><strong>{{ $opcion['hora_inicio'] }}</strong><span>{{ $opcion['hora_fin'] }}</span></div>
                <div class="appointment-option__details"><strong>{{ $opcion['medico'] }}</strong><span>{{ $opcion['consultorio'] }}</span></div>
                <form method="POST" action="{{ route('citas.store') }}" class="stack-form appointment-option__form">
                    @csrf
                    <input type="hidden" name="ci_paciente" value="{{ $pacienteSeleccionado->ci }}">
                    <input type="hidden" name="id_especialidad" value="{{ $opcion['id_especialidad'] }}">
                    <input type="hidden" name="id_medico" value="{{ $opcion['id_medico'] }}">
                    <input type="hidden" name="fecha" value="{{ $opcion['fecha'] }}">
                    <input type="hidden" name="hora_inicio" value="{{ $opcion['hora_inicio'] }}">
                    <label>Modalidad<select name="modalidad" required><option value="Presencial">Presencial</option><option value="Virtual">Virtual</option></select></label>
                    <label>Motivo (opcional)<input name="motivo" maxlength="200" value="{{ old('motivo') }}"></label>
                    <button type="submit" class="button">Generar ficha</button>
                </form>
            </article>
        @endforeach
        </div>
    @endif
</section>
@endif

<script>
    (() => {
        const feedback = document.getElementById('patientFeedback');
        const ciInput = document.getElementById('ci');
        const endpoint = @json(route('pacientes.buscar-ci'));
        const specialty = document.getElementById('id_especialidad');
        let lookupTimer;

        function showMessage(text, error = false) {
            feedback.replaceChildren();
            const message = document.createElement('div');
            message.className = `notice${error ? ' notice-error' : ''}`;
            message.textContent = text;
            feedback.append(message);
        }

        function showPatient(patient) {
            feedback.replaceChildren();
            const card = document.createElement('div');
            card.className = 'patient-card';
            const initials = document.createElement('span');
            initials.className = 'patient-card__icon';
            initials.textContent = `${patient.nombres.slice(0, 1)}${patient.apellidos.slice(0, 1)}`;
            const details = document.createElement('div');
            const eyebrow = document.createElement('span');
            eyebrow.className = 'eyebrow';
            eyebrow.textContent = 'Paciente identificado';
            const name = document.createElement('strong');
            name.textContent = `${patient.nombres} ${patient.apellidos}`;
            const meta = document.createElement('span');
            meta.className = 'muted';
            meta.textContent = `Carnet: ${patient.ci} · Código: ${patient.codigo}`;
            details.append(eyebrow, name, meta);
            card.append(initials, details);
            feedback.append(card);
            specialty.disabled = false;
        }

        ciInput?.addEventListener('input', () => {
            clearTimeout(lookupTimer);
            const ci = ciInput.value.trim();
            specialty.disabled = true;
            specialty.value = '';
            if (ci.length < 4) {
                showMessage('Escribe el carnet para consultar la ficha del paciente.');
                return;
            }
            lookupTimer = setTimeout(async () => {
                showMessage('Buscando al paciente…');
                try {
                    const response = await fetch(`${endpoint}?ci=${encodeURIComponent(ci)}`, { headers: { Accept: 'application/json' } });
                    const result = await response.json();
                    if (!response.ok) showMessage(result.mensaje || 'No encontramos una ficha con ese carnet.', true);
                    else showPatient(result.paciente);
                } catch {
                    showMessage('No se pudo consultar el paciente. Intenta nuevamente.', true);
                }
            }, 300);
        });

    })();
</script>
@endsection
