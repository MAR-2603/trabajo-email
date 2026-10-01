<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
    <style>
        body { font-family: sans-serif; margin: 40px; background: #f8fafc; color: #0f172a; }
        .card { max-width: 520px; background: #fff; padding: 28px; border-radius: 12px; box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08); }
        .datos { background: #eff6ff; border: 1px dashed #3b82f6; padding: 14px 16px; border-radius: 8px; margin-bottom: 22px; font-size: 14px; }
        .datos strong { display: block; margin-bottom: 6px; }
        label { font-weight: 600; font-size: 14px; }
        input { width: 100%; box-sizing: border-box; padding: 10px; margin-top: 6px; border: 1px solid #cbd5e1; border-radius: 6px; }
        button { background: #2563eb; color: #fff; border: 0; padding: 12px 18px; border-radius: 8px; cursor: pointer; font-weight: 600; }
        .error { color: #dc2626; font-size: 13px; }
        .success { color: #16a34a; font-weight: bold; }
        h2 { margin-top: 0; }
    </style>
</head>
<body>
    <div class="card">
        <div class="datos">
            <strong>Trabajo práctico — Programación IV</strong>
            Alumno: <u>;[Sanchez Sergio, Martin];</u><br>
            
        </div>

        <h2>Formulario de Registro</h2>

        @if(session('success'))
            <p class="success">{{ session('success') }}</p>
        @endif

        <form action="{{ route('register.store') }}" method="POST">
            @csrf
            <div>
                <label>Nombre Completo:</label><br>
                <input type="text" name="name" value="{{ old('name') }}">
                @error('name') <br><span class="error">{{ $message }}</span> @enderror
            </div><br>
            <div>
                <label>Correo Electrónico:</label><br>
                <input type="email" name="email" value="{{ old('email') }}">
                @error('email') <br><span class="error">{{ $message }}</span> @enderror
            </div><br>
            <div>
                <label>Contraseña:</label><br>
                <input type="password" name="password">
                @error('password') <br><span class="error">{{ $message }}</span> @enderror
            </div><br>
            <div>
                <label>Confirmar Contraseña:</label><br>
                <input type="password" name="password_confirmation">
            </div><br>
            <button type="submit">Registrarse</button>
        </form>
    </div>
</body>
</html>
