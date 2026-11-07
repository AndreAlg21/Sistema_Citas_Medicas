<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Clínica Vida Salud')</title>
    <style>
        :root {
            color-scheme: light;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: #18372b;
            background: #eff7f1;
            font-synthesis: none;
            text-rendering: optimizeLegibility;
            --ink: #18372b;
            --muted: #698176;
            --line: #dcebe0;
            --brand: #0f9f6e;
            --brand-dark: #08744f;
            --surface: #fff;
        }
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; background: linear-gradient(135deg, #edf7ef 0%, #f8fcf9 54%, #eaf5ed 100%); }
        a { color: var(--brand-dark); text-decoration: none; }
        a:hover { color: #034f4b; }
        p > a, td > a { display: inline-flex; align-items: center; min-height: 32px; margin: 2px 4px 2px 0; padding: 6px 10px; border: 1px solid #cfe3d5; border-radius: 8px; background: #f5fbf6; color: #267550; font-size: 12px; font-weight: 650; }
        p > a:hover, td > a:hover { border-color: #a9d2b5; background: #e8f7eb; color: #075d43; }
        .app-shell { min-height: 100vh; }
        .sidebar { position: fixed; inset: 0 auto 0 0; z-index: 10; display: flex; flex-direction: column; width: 252px; height: 100vh; overflow: hidden; padding: 22px 14px 16px; border-right: 1px solid #ffffff20; background: linear-gradient(165deg, #08734f 0%, #075d43 52%, #064b3c 100%); box-shadow: 9px 0 28px #07573c14; transition: width .2s ease, transform .2s ease; }
        .brand-link { display: flex; align-items: center; gap: 11px; min-height: 46px; margin: 0 4px 28px; color: #f4fff7; white-space: nowrap; }
        .brand-mark { display: grid; flex: 0 0 38px; place-items: center; width: 38px; height: 38px; border-radius: 12px; background: #d9f7e5; color: #08744f; font-size: 24px; font-weight: 500; }
        .brand-name { font-size: 14px; font-weight: 800; letter-spacing: -.03em; }
        .sidebar-toggle { position: absolute; top: 25px; right: 12px; display: grid; place-items: center; width: 29px; min-height: 29px; margin: 0; padding: 0; border: 1px solid #dcebe0; border-radius: 50%; background: #fff; color: #08744f; box-shadow: 0 2px 6px #18374012; }
        .side-nav { display: grid; flex: 1 1 auto; min-height: 0; gap: 5px; overflow-y: auto; padding: 0 2px 14px 0; overscroll-behavior: contain; scrollbar-width: thin; scrollbar-color: #a9d7b5 transparent; }
        .side-nav a { display: flex; align-items: center; gap: 12px; min-height: 43px; padding: 9px 11px; border-radius: 10px; color: #e4f5e9; font-size: 13px; font-weight: 650; white-space: nowrap; transition: background .16s, color .16s; }
        .side-nav a:hover { color: #fff; background: #ffffff20; }
        .nav-icon { display: grid; flex: 0 0 22px; place-items: center; width: 22px; color: #a9ddbc; font-size: 15px; }
        .nav-group-title { margin: 14px 11px 5px; color: #abd5b8; font-size: 10px; font-weight: 800; letter-spacing: .1em; text-transform: uppercase; }
        .sidebar-bottom { flex: 0 0 auto; margin-top: auto; padding-top: 13px; border-top: 1px solid #ffffff35; }
        .user-summary { margin: 0 8px 12px; color: #b9ddc5; font-size: 11px; line-height: 1.5; }
        .user-summary strong { display: block; color: #fff; font-size: 12px; }
        .logout-form { margin: 0; }
        .logout-form button { width: 100%; justify-content: flex-start; border-color: #ffffff35; background: #ffffff14; color: #effbf2; }
        .logout-form button:hover { background: #ffffff26; color: #fff; box-shadow: none; }
        .main-area { min-height: 100vh; margin-left: 252px; transition: margin-left .2s ease; }
        .topbar { display: flex; align-items: center; justify-content: space-between; min-height: 62px; padding: 12px clamp(22px, 4vw, 54px); border-bottom: 1px solid #dcebe0; background: rgba(255,255,255,.84); color: #698176; font-size: 12px; }
        .topbar strong { color: var(--ink); font-size: 13px; }
        .mobile-menu { display: none; }
        body.sidebar-compact .sidebar { width: 76px; }
        body.sidebar-compact .main-area { margin-left: 76px; }
        body.sidebar-compact .brand-name, body.sidebar-compact .nav-label, body.sidebar-compact .nav-group-title, body.sidebar-compact .user-summary { display: none; }
        body.sidebar-compact .brand-link { justify-content: center; margin-left: 0; margin-right: 0; }
        body.sidebar-compact .side-nav a { justify-content: center; padding-inline: 5px; }
        body.sidebar-compact .logout-form button { justify-content: center; font-size: 0; }
        body.sidebar-compact .logout-form button::before { content: "↪"; font-size: 16px; }
        main { width: min(1200px, 100%); margin: 26px auto; padding: 0 clamp(18px, 3vw, 42px) 64px; }
        section, .panel { margin: 18px 0; padding: clamp(21px, 3vw, 32px); border: 1px solid #d9eade; border-radius: 18px; background: var(--surface); box-shadow: 0 10px 30px rgba(24, 95, 59, .065); }
        h1, h2, h3 { color: var(--ink); letter-spacing: -.035em; }
        h1 { margin: 0 0 12px; font-size: clamp(25px, 4vw, 34px); line-height: 1.18; }
        h2 { margin: 0 0 17px; font-size: 20px; }
        h3 { font-size: 16px; }
        p { line-height: 1.65; }
        label { display: block; max-width: 620px; margin: 14px 0 7px; color: #435861; font-size: 13px; font-weight: 650; }
        input, select, textarea {
            display: block; width: min(100%, 560px); min-height: 42px; max-width: 100%; margin-top: 6px; padding: 10px 12px; border: 1px solid #d5e0e1; border-radius: 9px;
            background: #fff; color: var(--ink); font: inherit; font-size: 14px; transition: border .18s, box-shadow .18s;
        }
        input:focus, select:focus, textarea:focus { outline: 0; border-color: #46b47c; box-shadow: 0 0 0 3px rgba(15,159,110,.15); }
        textarea { min-height: 90px; }
        button, .button {
            display: inline-flex; align-items: center; justify-content: center; gap: 7px; min-height: 40px; margin: 4px 3px 4px 0;
            padding: 9px 15px; border: 1px solid var(--brand); border-radius: 9px; background: var(--brand); color: #fff;
            font: inherit; font-size: 13px; font-weight: 700; cursor: pointer; text-decoration: none;
            transition: transform .15s, background .15s, box-shadow .15s;
        }
        button:hover, .button:hover { transform: translateY(-1px); background: var(--brand-dark); color: #fff; box-shadow: 0 6px 16px rgba(8,116,79,.22); }
        input[type="hidden"] { display: none; }
        input[type="checkbox"], input[type="radio"] { display: inline-block; width: auto; min-height: 0; margin: 0 7px 0 0; accent-color: var(--brand); }
        .stack-form { display: grid; justify-items: start; gap: 5px; }
        .stack-form > label { width: min(100%, 560px); }
        .page-heading { border: 0; padding: 8px 2px 5px; background: transparent; box-shadow: none; }
        .eyebrow { display: block; margin-bottom: 7px; color: #528266; font-size: 10px; font-weight: 800; letter-spacing: .11em; text-transform: uppercase; }
        .notice { margin: 16px 0; padding: 14px 16px; border: 1px solid #cce4d2; border-radius: 11px; background: #f0faf2; color: #3f6f50; font-size: 13px; line-height: 1.6; }
        .notice-error { border-color: #f1d6d2; background: #fff7f5; color: #9b4b42; }
        .patient-card { display: flex; align-items: center; gap: 14px; margin-top: 14px; padding: 16px; border: 1px solid #bfe1c8; border-radius: 13px; background: linear-gradient(110deg, #f1faf3, #fff); }
        .patient-card__icon { display: grid; flex: 0 0 44px; place-items: center; width: 44px; height: 44px; border-radius: 50%; background: #d6f2dc; color: var(--brand-dark); font-size: 13px; font-weight: 800; }
        .patient-card strong, .patient-card .muted { display: block; }
        .patient-card .muted { font-size: 12px; }
        .appointment-list { display: grid; gap: 11px; }
        .appointment-option { display: grid; grid-template-columns: 82px minmax(130px, 1fr) minmax(220px, 1.2fr); align-items: start; gap: 16px; padding: 16px; border: 1px solid var(--line); border-radius: 13px; }
        .appointment-option__time strong, .appointment-option__time span, .appointment-option__details strong, .appointment-option__details span { display: block; }
        .appointment-option__time strong { color: var(--brand-dark); font-size: 17px; }
        .appointment-option__time span, .appointment-option__details span { margin-top: 4px; color: var(--muted); font-size: 12px; }
        .appointment-option__form { gap: 0; }
        .booking-panel { overflow: hidden; border-top: 4px solid #0f9f6e; }
        .booking-panel__intro { display: flex; align-items: flex-start; gap: 14px; margin: -4px 0 22px; }
        .booking-panel__intro h2 { margin: 0 0 5px; }
        .booking-panel__intro p { margin: 0; color: var(--muted); font-size: 13px; line-height: 1.6; }
        .booking-panel__number { display: grid; flex: 0 0 40px; place-items: center; width: 40px; height: 40px; border: 1px solid #bfe4c8; border-radius: 12px; background: #e7f8eb; color: #08744f; font-size: 13px; font-weight: 850; }
        .booking-form { gap: 7px; }
        .booking-form label { width: min(100%, 640px); }
        .booking-submit { min-width: 240px; margin-top: 15px; background: linear-gradient(110deg, #10a66d, #08744f); }
        .section-heading { display: flex; align-items: center; justify-content: space-between; gap: 20px; }
        .section-heading h2 { margin-bottom: 0; }
        .date-chip { padding: 8px 12px; border: 1px solid #c5e5cb; border-radius: 99px; background: #effaf1; color: #267550; font-size: 12px; font-weight: 750; }
        select:disabled { background: #f3f7f4; color: #98a99b; cursor: not-allowed; }
        .form-actions { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; margin-top: 12px; }
        .button-secondary { border-color: #cfe3d5; background: #fff; color: #417253; }
        .button-secondary:hover { border-color: #a9d2b5; background: #eff9f1; color: var(--brand-dark); box-shadow: none; }
        .button-small { min-height: 32px; padding: 6px 10px; font-size: 11px; }
        .button-danger { border-color: #f1d6d2; background: #fff7f5; color: #a24d44; }
        .button-danger:hover { border-color: #ecc2bd; background: #fff0ed; color: #853a32; box-shadow: none; }
        .table-wrap { width: 100%; overflow-x: auto; }
        .table-actions { white-space: nowrap; }
        .status-pill { display: inline-block; padding: 5px 9px; border-radius: 99px; font-size: 11px; font-weight: 750; }
        .status-active { background: #e9f6ef; color: #297254; }
        .status-inactive { background: #f0f2f2; color: #748187; }
        .empty-state { padding: 42px 20px; text-align: center; }
        .empty-state > span { display: inline-grid; place-items: center; width: 50px; height: 50px; border-radius: 15px; background: #e1f5e6; color: var(--brand); font-size: 24px; }
        .detail-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); gap: 18px; }
        .detail-grid > div:not(.form-actions) { padding: 15px; border: 1px solid var(--line); border-radius: 12px; background: #fbfdfd; }
        .detail-grid strong { display: block; color: var(--ink); font-size: 14px; }
        .welcome-panel { position: relative; overflow: hidden; padding: 32px; background: linear-gradient(115deg, #fff 35%, #e8f8eb); }
        .welcome-panel::after { content: ""; position: absolute; right: -42px; top: -92px; width: 240px; height: 240px; border: 1px solid #c6e7ce; border-radius: 50%; box-shadow: 0 0 0 28px #effaf1, 0 0 0 56px #f7fcf8; opacity: .9; }
        .welcome-panel > * { position: relative; z-index: 1; }
        .dashboard-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 13px; }
        .action-card { display: grid; justify-items: start; gap: 7px; min-height: 140px; padding: 20px; border: 1px solid #d5e9da; border-radius: 15px; background: linear-gradient(145deg, #fff 55%, #f3fbf4); color: var(--ink); box-shadow: 0 6px 20px rgba(35,95,57,.055); transition: transform .18s, box-shadow .18s, border-color .18s; }
        .action-card:hover { transform: translateY(-2px); border-color: #a8d5b2; color: var(--ink); box-shadow: 0 12px 28px rgba(35,95,57,.12); }
        .action-card > span { display: grid; place-items: center; width: 36px; height: 36px; border-radius: 11px; background: #dff4e4; color: #08744f; font-size: 18px; }
        .action-card strong { font-size: 14px; }
        .action-card small { color: var(--muted); font-size: 12px; }
        table { width: 100%; margin: 17px 0; border-collapse: separate; border-spacing: 0; overflow: hidden; border: 1px solid var(--line); border-radius: 12px; background: #fff; font-size: 13px; }
        th, td { padding: 13px 14px; border-bottom: 1px solid var(--line); text-align: left; vertical-align: middle; }
        th { background: #eaf6ec; color: #3e6c4d; font-size: 11px; font-weight: 750; letter-spacing: .055em; text-transform: uppercase; }
        tr:last-child td { border-bottom: 0; }
        tbody tr:hover { background: #fbfdfd; }
        .error { color: #a23939; }
        section.error { border-color: #f2d1d1; background: #fff8f7; }
        .success { padding: 13px 16px; border: 1px solid #bce3c6; border-radius: 11px; background: #e9f8ed; color: #176342; }
        .muted { color: var(--muted); }
        .inline { display: inline-block; margin: 2px; }
        .inline label { display: inline-block; }
        .inline input, .inline select { display: inline-block; width: auto; }
        .side-nav a[aria-current="page"] { color: #075d43; background: #daf4e1; }
        @media (max-width: 760px) {
            .sidebar { width: 76px; padding-inline: 10px; }
            .brand-name, .nav-label, .nav-group-title, .user-summary { display: none; }
            .brand-link { justify-content: center; margin-inline: 0; }
            .side-nav a { justify-content: center; padding-inline: 5px; }
            .main-area { margin-left: 76px; }
            body.sidebar-open .sidebar { width: 240px; box-shadow: 12px 0 30px #18374018; }
            body.sidebar-open .main-area { margin-left: 76px; }
            body.sidebar-open .brand-name, body.sidebar-open .nav-label, body.sidebar-open .nav-group-title, body.sidebar-open .user-summary { display: initial; }
            body.sidebar-open .side-nav a { justify-content: flex-start; padding-inline: 11px; }
            .topbar { min-height: 55px; padding: 10px 15px; }
            .mobile-menu { display: inline-flex; margin: 0 10px 0 0; padding: 6px 10px; }
            main { margin: 20px auto; padding: 0 14px 40px; }
            section, .panel { padding: 20px 16px; border-radius: 14px; }
            table { display: block; overflow-x: auto; white-space: nowrap; }
            .appointment-option { grid-template-columns: 64px 1fr; }
            .appointment-option__form { grid-column: 1 / -1; }
        }
    </style>
</head>
<body>
    @php($rolActual = auth()->user()->role?->nombre)
    <div class="app-shell">
    <aside class="sidebar" aria-label="Menú principal">
        <a class="brand-link" href="{{ route('inicio') }}"><span class="brand-mark">+</span><span class="brand-name">Vida Salud</span></a>
        <button class="sidebar-toggle" id="sidebarToggle" type="button" aria-label="Contraer o desplegar menú" title="Contraer o desplegar menú">‹</button>
        <nav class="side-nav">
            <a href="{{ route('inicio') }}"><span class="nav-icon">⌂</span><span class="nav-label">Inicio</span></a>
            @if (in_array($rolActual, ['Recepcionista', 'Administrador'], true))
                <span class="nav-group-title">Atención</span>
                <a href="{{ route('citas.disponibilidad') }}"><span class="nav-icon">◷</span><span class="nav-label">Disponibilidad</span></a>
                <a href="{{ route('citas.create') }}"><span class="nav-icon">＋</span><span class="nav-label">Registrar cita</span></a>
                <a href="{{ route('citas.agenda') }}"><span class="nav-icon">▦</span><span class="nav-label">Agenda</span></a>
                <a href="{{ route('pacientes.index') }}"><span class="nav-icon">♙</span><span class="nav-label">Pacientes</span></a>
                <a href="{{ route('reportes.index') }}"><span class="nav-icon">▤</span><span class="nav-label">Reportes</span></a>
            @endif
            @if ($rolActual === 'Medico')
                <span class="nav-group-title">Mi espacio</span>
                <a href="{{ route('citas.agenda') }}"><span class="nav-icon">▦</span><span class="nav-label">Mi agenda</span></a>
            @endif
            @if ($rolActual === 'Paciente')
                <span class="nav-group-title">Mi atención</span>
                <a href="{{ route('citas.disponibilidad') }}"><span class="nav-icon">◷</span><span class="nav-label">Reservar cita</span></a>
                <a href="{{ route('portal.citas') }}"><span class="nav-icon">▦</span><span class="nav-label">Mis citas</span></a>
                <a href="{{ route('portal.perfil') }}"><span class="nav-icon">♙</span><span class="nav-label">Mi perfil</span></a>
                <a href="{{ route('portal.historial-medico') }}"><span class="nav-icon">▤</span><span class="nav-label">Historial médico</span></a>
            @endif
            @if ($rolActual === 'Administrador')
                <span class="nav-group-title">Administración</span>
                <a href="{{ route('usuarios.index') }}"><span class="nav-icon">♙</span><span class="nav-label">Usuarios</span></a>
                <a href="{{ route('medicos.index') }}"><span class="nav-icon">✚</span><span class="nav-label">Médicos</span></a>
                <a href="{{ route('especialidades.index') }}"><span class="nav-icon">✳</span><span class="nav-label">Especialidades</span></a>
                <a href="{{ route('horarios.index') }}"><span class="nav-icon">◷</span><span class="nav-label">Horarios</span></a>
                <a href="{{ route('roles.index') }}"><span class="nav-icon">⚙</span><span class="nav-label">Roles y permisos</span></a>
            @endif
        </nav>
        <div class="sidebar-bottom">
            <p class="user-summary"><strong>{{ auth()->user()->nombre_completo }}</strong>{{ $rolActual }}</p>
            <form method="POST" action="{{ route('logout') }}" class="logout-form">
                @csrf <button type="submit"><span class="nav-icon">↪</span><span class="nav-label">Cerrar sesión</span></button>
            </form>
        </div>
    </aside>
    <div class="main-area">
    <div class="topbar"><span><button class="mobile-menu" id="mobileMenu" type="button">Menú</button>Clínica Vida Salud</span><strong>{{ $rolActual }}</strong></div>
    <main>
        @if (session('success')) <p class="success">{{ session('success') }}</p> @endif
        @if ($errors->any())
            <section class="error"><strong>Revisa estos datos:</strong>
                <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </section>
        @endif
        @yield('content')
    </main>
    </div>
    </div>
    <script>
        (() => {
            const body = document.body;
            const toggle = document.getElementById('sidebarToggle');
            const mobile = document.getElementById('mobileMenu');
            toggle?.addEventListener('click', () => {
                if (window.matchMedia('(max-width: 760px)').matches) body.classList.toggle('sidebar-open');
                else body.classList.toggle('sidebar-compact');
            });
            mobile?.addEventListener('click', () => body.classList.toggle('sidebar-open'));
            document.querySelectorAll('input[data-weekdays-only]').forEach((input) => {
                const validarDia = () => {
                    if (!input.value) return;
                    const dia = new Date(`${input.value}T12:00:00`).getDay();
                    input.setCustomValidity(dia === 0 || dia === 6 ? 'La clínica no atiende sábados ni domingos.' : '');
                };
                input.addEventListener('input', validarDia);
                input.addEventListener('change', validarDia);
                validarDia();
            });
        })();
    </script>
</body>
</html>
