<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear cuenta | Clínica Vida Salud</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; padding: 34px 18px; background: radial-gradient(ellipse at 12% 8%, #d7f1de, transparent 35%), linear-gradient(135deg, #eff8f0, #e6f4e9); color: #18372b; font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; }
        .page { width: min(100%, 760px); margin: auto; }
        .brand { display: flex; align-items: center; gap: 11px; margin: 0 0 18px 5px; color: #08744f; font-size: 12px; font-weight: 800; letter-spacing: .09em; text-transform: uppercase; }
        .brand-mark { display: grid; place-items: center; width: 38px; height: 38px; border-radius: 12px; background: linear-gradient(145deg, #13aa70, #08744f); color: #fff; font-size: 25px; font-weight: 400; }
        .card { padding: clamp(24px, 5vw, 44px); border: 1px solid #d4e9d9; border-radius: 22px; background: rgba(255,255,255,.97); box-shadow: 0 24px 70px rgba(21, 96, 54, .12); }
        .eyebrow { margin: 0 0 8px; color: #528266; font-size: 10px; font-weight: 800; letter-spacing: .12em; text-transform: uppercase; }
        h1 { margin: 0 0 9px; color: #18372b; font-size: clamp(26px, 5vw, 34px); letter-spacing: -.04em; }
        .intro { margin: 0 0 26px; color: #698176; line-height: 1.6; }
        .form-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 0 16px; }
        label { display: block; margin: 14px 0 7px; color: #3f6550; font-size: 13px; font-weight: 700; }
        input { width: 100%; min-height: 45px; margin-top: 7px; padding: 11px 13px; border: 1px solid #cfe3d4; border-radius: 10px; background: #fff; color: #18372b; font: inherit; font-size: 14px; }
        input:focus { outline: 0; border-color: #46b47c; box-shadow: 0 0 0 3px rgba(15,159,110,.15); }
        .full { grid-column: 1 / -1; }
        .privacy { display: flex; align-items: flex-start; gap: 10px; margin: 23px 0 8px; color: #536b5c; font-size: 12px; font-weight: 500; line-height: 1.55; }
        .privacy input { flex: 0 0 auto; width: 16px; min-height: 16px; margin: 2px 0 0; accent-color: #0f9f6e; }
        button { width: 100%; min-height: 47px; margin-top: 18px; padding: 12px; border: 0; border-radius: 10px; background: linear-gradient(110deg, #10a66d, #08744f); color: #fff; font: inherit; font-size: 14px; font-weight: 750; cursor: pointer; transition: background .18s, transform .18s; }
        button:hover { transform: translateY(-1px); background: #06643f; }
        .error-list { margin: 0 0 20px; padding: 12px 16px 12px 34px; border: 1px solid #f1d6d2; border-radius: 10px; background: #fff7f5; color: #9b4b42; font-size: 13px; line-height: 1.6; }
        .error { display: block; margin-top: 5px; color: #a23939; font-size: 12px; font-weight: 500; }
        .footer { margin: 21px 0 0; color: #698176; font-size: 13px; text-align: center; }
        .footer a { color: #08744f; font-weight: 750; text-decoration: none; }
        .footer a:hover { text-decoration: underline; }
        @media (max-width: 560px) { .form-grid { grid-template-columns: 1fr; } .full { grid-column: auto; } .card { border-radius: 17px; } }
    </style>
</head>
<body>
    <main class="page">
        <p class="brand"><span class="brand-mark">+</span> Clínica Vida Salud</p>
        <section class="card">
            <p class="eyebrow">Portal del paciente · CU18</p>
            <h1>Crear cuenta</h1>
            <p class="intro">Registra tus datos para reservar citas y consultar tu información desde el portal.</p>

            @if ($errors->any())
                <ul class="error-list">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            @endif

            <form method="POST" action="{{ route('pacientes.registro.store') }}">
                @csrf
                <div class="form-grid">
                    <label>Nombres
                        <input name="nombres" value="{{ old('nombres') }}" maxlength="80" autocomplete="given-name" required>
                        @error('nombres')<span class="error">{{ $message }}</span>@enderror
                    </label>
                    <label>Apellidos
                        <input name="apellidos" value="{{ old('apellidos') }}" maxlength="80" autocomplete="family-name" required>
                        @error('apellidos')<span class="error">{{ $message }}</span>@enderror
                    </label>
                    <label>Documento de identidad (CI)
                        <input name="ci" value="{{ old('ci') }}" maxlength="20" inputmode="numeric" autocomplete="off" required>
                        @error('ci')<span class="error">{{ $message }}</span>@enderror
                    </label>
                    <label>Fecha de nacimiento
                        <input type="date" name="fecha_nacimiento" value="{{ old('fecha_nacimiento') }}" required>
                        @error('fecha_nacimiento')<span class="error">{{ $message }}</span>@enderror
                    </label>
                    <label>Teléfono
                        <input name="telefono" value="{{ old('telefono') }}" maxlength="20" autocomplete="tel" required>
                        @error('telefono')<span class="error">{{ $message }}</span>@enderror
                    </label>
                    <label>Correo electrónico
                        <input type="email" name="correo" value="{{ old('correo') }}" maxlength="120" autocomplete="email" required>
                        @error('correo')<span class="error">{{ $message }}</span>@enderror
                    </label>
                    <label>Contraseña
                        <input type="password" name="contrasena" minlength="8" autocomplete="new-password" required>
                        @error('contrasena')<span class="error">{{ $message }}</span>@enderror
                    </label>
                    <label>Confirmar contraseña
                        <input type="password" name="contrasena_confirmation" minlength="8" autocomplete="new-password" required>
                    </label>
                </div>
                <label class="privacy"><input type="checkbox" name="acepta_privacidad" value="1" required {{ old('acepta_privacidad') ? 'checked' : '' }}><span>Acepto el tratamiento de mis datos personales para gestionar mi atención médica.</span></label>
                @error('acepta_privacidad')<span class="error">{{ $message }}</span>@enderror
                <button type="submit">Crear mi cuenta</button>
            </form>
            <p class="footer">¿Ya tienes cuenta? <a href="{{ route('login') }}">Volver a iniciar sesión</a></p>
        </section>
    </main>
</body>
</html>
