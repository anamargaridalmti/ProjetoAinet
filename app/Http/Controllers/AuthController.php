<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;

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
            // Redireciona para o painel de controlo principal (Dashboard)
            return redirect()->intended('dashboard');
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

        // Cria o utilizador garantindo todos os campos obrigatórios do seeder [cite: 322, 323, 324]
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_type' => 'C', // C: Cliente [cite: 322]
            'gender' => 'M',    // M: Masculino [cite: 323]
            'blocked' => 0,   // Não bloqueado [cite: 324]
            'photo_url' => null,
            'custom' => null,
        ]);

        // Dispara o e-mail de verificação oficial do Laravel para o Mailtrap 
        event(new Registered($user));

        // Faz o login imediato do utilizador recém-criado
        Auth::login($user);

        // Redireciona para o aviso de que precisa de ir ao e-mail ativar a conta [cite: 61]
        return redirect()->route('verification.notice');
    }

    // --- RECUPERAÇÃO DE PASSWORD (G1) ---

    /**
     * Envia o e-mail com o link de recuperação de password para o Mailtrap 
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ], [
            'email.required' => 'O campo e-mail é obrigatório.',
            'email.email' => 'Introduza um endereço de e-mail válido.',
        ]);

        // Dispara o link via broker nativo do Laravel 
        $status = Password::broker()->sendResetLink(
            $request->only('email')
        );

        // Se o link foi enviado com sucesso, devolve mensagem amigável 
        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', 'Enviámos o link de recuperação para o seu e-mail!');
        }

        // Se o e-mail não existir na base de dados, devolve o erro
        return back()->withErrors(['email' => 'Não encontrámos nenhum utilizador com esse endereço de e-mail.']);
    }

    /**
     * Processa a gravação da nova password na Base de Dados 
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:3|confirmed', // Mínimo de 3 caracteres conforme os requisitos de password simples [cite: 309]
        ], [
            'password.required' => 'O campo da nova palavra-passe é obrigatório.',
            'password.min' => 'A palavra-passe deve ter pelo menos 3 caracteres.',
            'password.confirmed' => 'As palavras-passe introduzidas não coincidem.',
        ]);

        // Executa a alteração através do broker 
        $status = Password::broker()->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        // Se a password foi redefinida com sucesso, volta para o login 
        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('status', 'A sua palavra-passe foi atualizada com sucesso!');
        }

        return back()->withErrors(['email' => 'Este código de recuperação é inválido ou já expirou.']);
    }
}
