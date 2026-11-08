<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar contraseña | Clínica Vida Salud</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; padding: 24px; background: radial-gradient(ellipse at 12% 8%, #d7f1de, transparent 35%), linear-gradient(135deg, #eff8f0, #e6f4e9); color: #18372b; font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; }
        .card { width: min(100%, 470px); padding: clamp(26px, 6vw, 42px); border: 1px solid #d4e9d9; border-radius: 22px; background: rgba(255,255,255,.97); box-shadow: 0 24px 70px rgba(21, 96, 54, .14); }
        .brand { display: flex; align-items: center; gap: 10px; margin: 0 0 30px; color: #08744f; font-size: 12px; font-weight: 800; letter-spacing: .09em; text-transform: uppercase; }
        .brand::before { content: "+"; display: grid; place-items: center; width: 37px; height: 37px; border-radius: 12px; background: linear-gradient(145deg, #13aa70, #08744f); color: #fff; font-size: 25px; font-weight: 400; }
        .eyebrow { margin: 0 0 8px; color: #528266; font-size: 10px; font-weight: 800; letter-spacing: .12em; text-transform: uppercase; }
        h1 { margin: 0 0 9px; font-size: 29px; letter-spacing: -.04em; }
        .intro { margin: 0 0 24px; color: #698176; line-height: 1.6; }
        label { display: block; margin: 17px 0 7px; color: #3f6550; font-size: 13px; font-weight: 700; }
        input { width: 100%; min-height: 46px; margin-top: 7px; padding: 12px 13px; border: 1px solid #cfe3d4; border-radius: 10px; background: #fff; color: #18372b; font: inherit; font-size: 14px; }
        input:focus { outline: 0; border-color: #46b47c; box-shadow: 0 0 0 3px rgba(15,159,110,.15); }
        button { width: 100%; min-height: 46px; margin-top: 22px; border: 0; border-radius: 10px; background: linear-gradient(110deg, #10a66d, #08744f); color: #fff; font: inherit; font-weight: 750; cursor: pointer; }
        button:hover { background: #06643f; }
        .status, .error { margin: 15px 0; padding: 12px 14px; border-radius: 10px; font-size: 13px; line-height: 1.5; }
        .status { border: 1px solid #cce4d2; background: #f0faf2; color: #3f6f50; }
        .error { border: 1px solid #f1d6d2; background: #fff7f5; color: #9b4b42; }
        .footer { margin: 22px 0 0; color: #698176; font-size: 13px; text-align: center; }
        .footer a { color: #08744f; font-weight: 700; text-decoration: none; }
        .footer a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <main class="card">
        <p class="brand">Clínica Vida Salud</p>
        <p class="eyebrow">Seguridad de la cuenta · CU03</p>
        <h1>Recuperar contraseña</h1>
        <p class="intro">Escribe el correo asociado a tu cuenta. Te enviaremos un enlace para crear una contraseña nueva.</p>
        @if (session('status'))<p class="status" role="status">{{ session('status') }}</p>@endif
        @if ($errors->any())<div class="error">{{ $errors->first() }}</div>@endif
        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <label for="correo">Correo electrónico
                <input id="correo" type="email" name="correo" value="{{ old('correo') }}" maxlength="120" autocomplete="email" required autofocus>
            </label>
            <button type="submit">Enviar enlace de recuperación</button>
        </form>
        <p class="footer"><a href="{{ route('login') }}">Volver al inicio de sesión</a></p>
    </main>
</body>
</html>
