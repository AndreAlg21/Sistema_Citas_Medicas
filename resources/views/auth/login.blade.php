<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión | Clínica Vida Salud</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; padding: 24px; background: radial-gradient(ellipse at 12% 8%, #d7f1de, transparent 35%), linear-gradient(135deg, #eff8f0, #e6f4e9); color: #18372b; font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; }
        .card { width: min(100%, 440px); padding: 42px; border: 1px solid #d4e9d9; border-radius: 22px; background: rgba(255,255,255,.97); box-shadow: 0 24px 70px rgba(21, 96, 54, .14); }
        .brand { display: flex; align-items: center; gap: 10px; margin: 0 0 30px; color: #08744f; font-size: 12px; font-weight: 800; letter-spacing: .09em; text-transform: uppercase; }
        .brand::before { content: "+"; display: grid; place-items: center; width: 37px; height: 37px; border-radius: 12px; background: linear-gradient(145deg, #13aa70, #08744f); color: #fff; font-size: 25px; font-weight: 400; }
        h1 { margin: 0 0 9px; color: #18372b; font-size: 30px; letter-spacing: -.04em; }
        .intro { margin: 0 0 27px; color: #698176; line-height: 1.6; }
        label { display: block; margin: 17px 0 7px; color: #3f6550; font-size: 13px; font-weight: 700; }
        input { width: 100%; min-height: 46px; padding: 12px 13px; border: 1px solid #cfe3d4; border-radius: 10px; background: #fff; color: #18372b; font: inherit; font-size: 14px; }
        input:focus { outline: 0; border-color: #46b47c; box-shadow: 0 0 0 3px rgba(15,159,110,.15); }
        button { width: 100%; min-height: 46px; margin-top: 23px; padding: 12px; border: 0; border-radius: 10px; background: linear-gradient(110deg, #10a66d, #08744f); color: white; font: inherit; font-size: 14px; font-weight: 750; cursor: pointer; transition: background .18s, transform .18s; }
        button:hover { transform: translateY(-1px); background: #06643f; }
        .error { margin-top: 6px; color: #a23939; font-size: 13px; }
        .footer { margin: 23px 0 0; color: #698176; font-size: 12px; text-align: center; }
        .footer a { color: #08744f; font-weight: 700; text-decoration: none; }
        .footer a:hover { text-decoration: underline; }
        @media (max-width: 500px) { .card { padding: 30px 23px; border-radius: 17px; } }
    </style>
</head>
<body>
    <main class="card">
        <p class="brand">Clínica Vida Salud</p>
        <h1>Iniciar sesión</h1>
        <p class="intro">Ingresa con tu correo o nombre de usuario para continuar.</p>

        @if (session('success'))
            <p class="footer">{{ session('success') }}</p>
        @endif

        <form method="POST" action="{{ route('login.store') }}">
            @csrf

            <label for="identificador">Correo o nombre de usuario</label>
            <input id="identificador" name="identificador" type="text" value="{{ old('identificador') }}" autocomplete="username" required autofocus>
            @error('identificador')
                <div class="error">{{ $message }}</div>
            @enderror

            <label for="contrasena">Contraseña</label>
            <input id="contrasena" name="contrasena" type="password" autocomplete="current-password" required>
            @error('contrasena')
                <div class="error">{{ $message }}</div>
            @enderror

            <button type="submit">Entrar</button>
        </form>

        <p class="footer"><a href="{{ route('pacientes.registro') }}">Crear cuenta de paciente</a></p>
        <p class="footer">Sistema de gestión de citas médicas</p>
    </main>
</body>
</html>
