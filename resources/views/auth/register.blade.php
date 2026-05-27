<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>FunShirt - Registo de Cliente</title>
    <style>
        body { background-color: #121212; color: white; font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .register-container { background-color: #1e1e1e; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.3); width: 100%; max-width: 400px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; color: #aaa; }
        input { width: 100%; padding: 10px; border: 1px solid #333; background-color: #2a2a2a; color: white; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background-color: #ff4757; border: none; color: white; font-weight: bold; border-radius: 4px; cursor: pointer; margin-top: 10px; }
        button:hover { background-color: #e84118; }
        .error { color: #ff4757; font-size: 13px; margin-top: 5px; }
    </style>
</head>
<body>

<div class="register-container">
    <h2>Criar Conta - FunShirt</h2>
    <p style="color: #888; font-size: 14px;">Registe-se como cliente para fazer as suas encomendas.</p>

    <form method="POST" action="/register">
        @csrf

        <div class="form-group">
            <label for="name">Nome Completo</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus>
            @error('name') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="email">E-mail</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required>
            @error('email') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="password">Palavra-passe (mínimo 8 caracteres)</label>
            <input id="password" type="password" name="password" required>
            @error('password') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="password_confirmation">Confirmar Palavra-passe</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required>
        </div>

        <button type="submit">Registar como Cliente</button>
    </form>
</div>

</body>
</html>