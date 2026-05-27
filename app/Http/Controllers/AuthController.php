<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // --- LOGIN & LOGOUT ---

    // Mostra a página do formulário de Login
    public function showLogin()
    {
        return view('login');
    }

    // Processa a tentativa de login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('categories');
        }

        return back()->withErrors([
            'email' => 'As credenciais introduzidas não correspondem aos nossos registos.',
        ])->onlyInput('email');
    }

    // Processa o Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    // --- REGISTO DE CLIENTES (G1) ---

    // Mostra o formulário de registo
    public function showRegister()
    {
        return view('auth.register');
    }

    // Processa o registo do cliente na Base de Dados
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Cria o utilizador garantindo todos os campos obrigatórios do seeder
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_type' => 'C',
            'gender' => 'M',
            'blocked' => 0,
            'photo_url' => null,
            'custom' => null,
        ]);

        // Dispara o e-mail de verificação oficial do Laravel para o Mailtrap
        event(new Registered($user));

        // Faz o login imediato do utilizador recém-criado
        Auth::login($user);

        // Redireciona para o aviso de que precisa de ir ao e-mail ativar a conta
        return redirect()->route('verification.notice');
    }
}
