<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class AdminUserController extends Controller
{
    /**
     * Lista e filtra todos os utilizadores (G1)
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Filtro de pesquisa (Nome ou Email)
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        // Filtro por tipo de utilizador
        if ($request->filled('type')) {
            $query->where('user_type', $request->type);
        }

        $users = $query->latest()->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Formulário de criação de Staff (Funcionário / Administrador)
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Grava um novo colaborador na BD (G1)
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            // O Admin apenas cria Funcionários (F) ou outros Administradores (A)
            'user_type' => ['required', 'in:F,A'],
            'gender' => ['required', 'in:M,F'],
        ], [
            'email.unique' => 'Este endereço de e-mail já está registado na plataforma.',
            'user_type.in' => 'Através deste painel apenas é permitido criar Funcionários ou Administradores.',
        ]);

        // Captura o utilizador criado para podermos usar o método logo abaixo
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_type' => $request->user_type,
            'gender' => $request->gender,
            'blocked' => 0,
            'photo_url' => null,
            'custom' => null,
        ]);

        // Como a conta foi criada manualmente pelo Admin, marcamos o e-mail imediatamente como verificado
        $user->markEmailAsVerified();

        return redirect()->route('admin.users.index')->with('status', 'Colaborador criado com sucesso!');
    }

    /**
     * Método para Bloquear/Desbloquear um utilizador (G1)
     */
    public function toggleBlock(User $user)
    {
        // Impede que o admin se bloqueie a si próprio
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Não pode bloquear a sua própria conta!');
        }

        // Inverte o estado de bloqueado (se for 1 passa a 0, se for 0 passa a 1)
        $user->blocked = $user->blocked ? 0 : 1;
        $user->save();

        $statusText = $user->blocked ? 'bloqueado' : 'desbloqueado';
        return redirect()->back()->with('status', "O utilizador {$user->name} foi {$statusText} com sucesso!");
    }

    /**
     * Remove uma conta utilizando Soft Delete (G1)
     */
    public function destroy(User $user)
    {
        // Impede que o admin se elimine a si próprio
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Não pode eliminar a sua própria conta!');
        }

        // Executa o soft delete (garante que a trait SoftDeletes está ativa no teu Model User)
        $user->delete();

        return redirect()->back()->with('status', "O utilizador {$user->name} foi removido com sucesso (histórico preservado).");
    }
}
