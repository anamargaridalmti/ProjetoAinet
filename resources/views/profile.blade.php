<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>FunShirt - Meu Perfil</title>
    <style>
        body { background-color: #121212; color: white; font-family: sans-serif; padding: 30px; }
        .container { max-width: 600px; margin: 0 auto; background-color: #1e1e1e; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.3); }
        .section { margin-bottom: 30px; border-bottom: 1px solid #333; padding-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; color: #aaa; }
        input[type="text"], input[type="email"], input[type="password"] { width: 100%; padding: 10px; border: 1px solid #333; background-color: #2a2a2a; color: white; border-radius: 4px; box-sizing: border-box; }
        button { padding: 10px 20px; background-color: #ff4757; border: none; color: white; font-weight: bold; border-radius: 4px; cursor: pointer; }
        button:hover { background-color: #e84118; }
        .alert-success { color: #2ed573; margin-bottom: 15px; }
        .error { color: #ff4757; font-size: 13px; margin-top: 5px; }
    </style>
</head>
<body>

<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h2>O Meu Perfil (Cliente)</h2>
        <form method="POST" action="/logout">
            @csrf
            <button type="submit" style="background-color: #333;">Logout</button>
        </form>
    </div>

    @if(session('status'))
        <div class="alert-success">{{ session('status') }}</div>
    @endif

    <div class="section">
        <h3>Alterar Dados Pessoais e Avatar</h3>
        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="name">Nome</label>
                <input id="name" type="text" name="name" value="{{ old('name', Auth::user()->name) }}" required>
                @error('name') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="email">E-mail</label>
                <input id="email" type="email" name="email" value="{{ old('email', Auth::user()->email) }}" required>
                @error('email') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="photo">Fotografia / Avatar</label>
                @if(Auth::user()->photo_url)
                    <img src="{{ url('img-profiles/' . Auth::user()->photo_url) }}?v={{ time() }}" alt="Avatar" style="width: 100px; height: 100px; border-radius: 50%; margin-bottom: 15px; display: block; object-fit: cover; border: 2px solid #ff4757;">
                @else
                    <div style="width: 100px; height: 100px; border-radius: 50%; background-color: #333; display: flex; align-items: center; justify-content: center; font-size: 40px; margin-bottom: 15px; border: 2px dashed #555;">👤</div>
                @endif
                <input id="photo" type="file" name="photo" accept="image/*" style="background: #2a2a2a; padding: 8px; border-radius: 4px; display: block; width: 100%; box-sizing: border-box;">
                @error('photo') <div class="error">{{ $message }}</div> @enderror
            </div>

            <button type="submit">Guardar Dados</button>
        </form>
    </div>

    <div class="section" style="border: none; padding: 0; margin: 0;">
        <h3>Alterar Palavra-passe</h3>
        <form method="POST" action="/user/password">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="current_password">Palavra-passe Atual</label>
                <input id="current_password" type="password" name="current_password" required>
                @error('current_password', 'updatePassword') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="password">Nova Palavra-passe</label>
                <input id="password" type="password" name="password" required>
                @error('password', 'updatePassword') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirmar Nova Palavra-passe</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required>
            </div>

            <button type="submit" style="background-color: #2ed573;">Atualizar Senha</button>
        </form>
    </div>
</div>

</body>
</html>