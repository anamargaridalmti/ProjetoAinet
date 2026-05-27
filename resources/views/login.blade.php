<form method="POST" action="{{ route('login.post') }}">
    @csrf

    <div>
        <label for="email">Email</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
        @error('email') <span style="color: red;">{{ $message }}</span> @enderror
    </div>

    <div style="margin-top: 15px;">
        <label for="password">Palavra-passe</label>
        <input id="password" type="password" name="password" required>
        @error('password') <span style="color: red;">{{ $message }}</span> @enderror
    </div>

    <button type="submit" style="margin-top: 20px;">Entrar</button>
</form>