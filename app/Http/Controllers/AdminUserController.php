<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class AdminUserController extends Controller
{
    //formulário de criação de admin/funcionário
    public function create()
    {
        return view('admin.users.create');
    }

    //guardar nome do membro na bd
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'user_type' => ['required', 'in:C,F,A'], // C = Cliente, F = Funcionário, A = Admin
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_type' => $request->user_type,
        ]);

        $user->markEmailAsVerified();

        return redirect()->route('admin.users.index')->with('status', 'Utilizador criado com sucesso!');
    }


    public function index(Request $request)
    {

        $query = User::query();

        // Filtro por nome ou email
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        // Filtro por Tipo de Utilizador (C = Cliente, F = Funcionário, A = Admin)
        if ($request->filled('type')) {
            $query->where('user_type', $request->type);
        }

        // Paginação de 10 em 10 utilizadores
        $users = $query->simplePaginate(10);

        return view('admin.users.index', compact('users'));
    }

    // Método para Bloquear/Desbloquear
    public function toggleBlock(User $user)
    {
        // Mudar o estado de bloqueado (se for 1 passa a 0, se for 0 passa a 1)
        $user->blocked = !$user->blocked;
        $user->save();

        $statusText = $user->blocked ? 'bloqueado' : 'desbloqueado';
        return redirect()->back()->with('status', "Utilizador {$user->name} foi {$statusText} com sucesso!");
    }
}
