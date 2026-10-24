<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión | Clínica Vida Salud</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; padding: 24px; background: #f1f5f9; color: #172033; font-family: Arial, sans-serif; }
        .card { width: min(100%, 420px); padding: 36px; border-radius: 16px; background: #fff; box-shadow: 0 16px 40px rgba(15, 23, 42, .10); }
        .brand { margin: 0 0 8px; color: #087e8b; font-size: 14px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; }
        h1 { margin: 0 0 8px; font-size: 28px; }
        .intro { margin: 0 0 28px; color: #64748b; line-height: 1.5; }
        label { display: block; margin: 18px 0 7px; font-size: 14px; font-weight: 700; }
        input { width: 100%; padding: 12px 13px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 15px; }
        input:focus { outline: 3px solid rgba(8, 126, 139, .15); border-color: #087e8b; }
        button { width: 100%; margin-top: 24px; padding: 13px; border: 0; border-radius: 8px; background: #087e8b; color: white; font-size: 15px; font-weight: 700; cursor: pointer; }
        button:hover { background: #066875; }
        .error { margin-top: 6px; color: #b42318; font-size: 13px; }
        .footer { margin: 24px 0 0; color: #64748b; font-size: 12px; text-align: center; }
    </style>
</head>
<body>
    <main class="card">
        <p class="brand">Clínica Vida Salud</p>
        <h1>Iniciar sesión</h1>
        <p class="intro">Ingresa con tu correo o nombre de usuario para continuar.</p>

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

        <p class="footer">Sistema de gestión de citas médicas</p>
    </main>
</body>
</html>