<div style="max-width:600px;margin:24px auto;padding:32px;border:1px solid #dcebe0;border-radius:16px;background:#ffffff;color:#18372b;font-family:Arial,sans-serif;line-height:1.6">
    <p style="margin:0 0 18px;color:#08744f;font-size:12px;font-weight:bold;letter-spacing:1px;text-transform:uppercase">Clínica Vida Salud</p>
    <h1 style="margin:0 0 12px;font-size:24px">Restablecer contraseña</h1>
    <p>Hola, {{ $usuario->nombre_completo }}. Recibimos una solicitud para cambiar la contraseña de tu cuenta.</p>
    <p style="margin:24px 0"><a href="{{ $url }}" style="display:inline-block;padding:12px 20px;border-radius:9px;background:#08744f;color:#ffffff;font-weight:bold;text-decoration:none">Crear nueva contraseña</a></p>
    <p>El enlace vence en 60 minutos y solo puede utilizarse una vez. Si no solicitaste este cambio, puedes ignorar este mensaje.</p>
    <p style="color:#698176;font-size:12px">Si el botón no funciona, copia este enlace en tu navegador:<br><a href="{{ $url }}" style="color:#08744f;word-break:break-all">{{ $url }}</a></p>
</div>
