<p>Hola {{ $cita->paciente->nombres }},</p>
<p>Tu cita fue registrada correctamente.</p>
<ul><li>Código: {{ $cita->codigo }}</li>
<li>Fecha: {{ $cita->fecha->format('d/m/Y') }}</li>
<li>Hora: {{ substr((string) $cita->hora_inicio, 0, 5) }}</li>
<li>Médico: {{ $cita->medico->nombres }} {{ $cita->medico->apellidos }}</li>
<li>Especialidad: {{ $cita->especialidad->nombre }}</li></ul>
<p>Clínica Vida Salud</p>
