<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio | Clínica Vida Salud</title>
    <style>
        body { margin: 0; min-height: 100vh; background: #f1f5f9; color: #172033; font-family: Arial, sans-serif; }
        header { display: flex; justify-content: space-between; align-items: center; gap: 16px; padding: 18px 5%; background: #087e8b; color: white; }
        h1 { margin: 0; font-size: 20px; }
        main { max-width: 900px; margin: 48px auto; padding: 0 24px; }
        .panel { padding: 32px; border-radius: 14px; background: white; box-shadow: 0 12px 30px rgba(15, 23, 42, .08); }
        button { padding: 10px 16px; border: 1px solid white; border-radius: 7px; background: transparent; color: white; cursor: pointer; }
        p { color: #475569; line-height: 1.6; }
    </style>
</head>
<body>
    <header>
        <h1>Clínica Vida Salud</h1>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit">Cerrar sesión</button>
        </form>
    </header>
    <main>
        <section class="panel">
            <h2>Bienvenido, {{ auth()->user()->nombre_completo }}</h2>
            <p>Tu sesión está iniciada. Desde aquí se irán incorporando las funciones de gestión de citas médicas.</p>
        </section>
    </main>
</body>
</html>