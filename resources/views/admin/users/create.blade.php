<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>FunShirt - Criar Staff</title>
    <style>
        body { background-color: #121212; color: white; font-family: sans-serif; padding: 30px; }
        .container { max-width: 500px; margin: 0 auto; background-color: #1e1e1e; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.3); }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; color: #aaa; font-size: 14px; }
        input[type="text"], input[type="email"], input[type="password"], select { width: 100%; padding: 10px; background: #121212; border: 1px solid #444; color: white; border-radius: 4px; box-sizing: border-box; }
        .btn-submit { padding: 10px 20px; background-color: #2ed573; border: none; color: black; font-weight: bold; border-radius: 4px; cursor: pointer; width: 100%; font-size: 16px; }
        .btn-cancel { display: block; text-align: center; margin-top: 15px; color: #aaa; text-decoration: none; font-size: 14px; }
        .error-msg { color: #ff4757; font-size: 13px; margin-top: 5px; }
    </style>
</head>
<body>

<div class="container">
    <h2>Criar Novo Membro do Staff</h2>
    <p style="color: #888; font-size: 14px; margin-bottom: 25px;">Registe um novo Funcionário ou Administrador para a plataforma.</p>

    <form method="POST" action="{{ route('admin.users.store') }}">
        @csrf

        <div class="form-group">
            <label for="name">Nome Completo</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required>
            @error('name') <div class="error-msg">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="email">Endereço de E-mail</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required>
            @error('email') <div class="error-msg">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="user_type">Perfil de Acesso</label>
            <select id="user_type" name="user_type" required>
                <option value="F" {{ old('user_type') == 'F' ? 'selected' : '' }}>Funcionário (Staff)</option>
                <option value="A" {{ old('user_type') == 'A' ? 'selected' : '' }}>Administrador (Total)</option>
            </select>
            @error('user_type') <div class="error-msg">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="password">Palavra-passe</label>
            <input type="password" id="password" name="password" required>
            @error('password') <div class="error-msg">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="password_confirmation">Confirmar Palavra-passe</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required>
        </div>

        <button type="submit" class="btn-submit">Gravar Conta</button>
        <a href="{{ route('admin.users.index') }}" class="btn-cancel">Cancelar e Voltar</a>
    </form>
</div>

</body>
</html>