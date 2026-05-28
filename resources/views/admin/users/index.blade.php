<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>FunShirt - Gestão de Utilizadores</title>
    <style>
        body { background-color: #121212; color: white; font-family: sans-serif; padding: 30px; }
        .container { max-width: 1000px; margin: 0 auto; background-color: #1e1e1e; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.3); }
        .filter-bar { display: flex; gap: 15px; margin-bottom: 20px; background: #2a2a2a; padding: 15px; border-radius: 6px; }
        input[type="text"], select { padding: 8px; background: #121212; border: 1px solid #444; color: white; border-radius: 4px; }
        button { padding: 8px 15px; background-color: #ff4757; border: none; color: white; font-weight: bold; border-radius: 4px; cursor: pointer; }
        button.btn-secondary { background-color: #555; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #333; }
        th { background-color: #252525; color: #aaa; }
        .avatar-mini { width: 35px; height: 35px; border-radius: 50%; object-fit: cover; vertical-align: middle; }
        .badge { padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; }
        .badge-client { background-color: #2ed573; color: black; }
        .badge-employee { background-color: #1e90ff; color: white; }
        .badge-admin { background-color: #ffa500; color: black; }
        .badge-blocked { background-color: #ff4757; color: white; }
        .alert-success { color: #2ed573; margin-bottom: 15px; font-weight: bold; }
        
        /* Classe utilitária para alinhar os botões */
        .actions-cell { display: flex; gap: 10px; align-items: center; }
        .btn-block-toggle { width: 110px; text-align: center;}
    </style>
</head>
<body>

<div class="container">
    <h2>Painel Administrativo - Gestão de Utilizadores</h2>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <p style="color: #aaa; margin: 0;">Gira os acessos, bloqueie ou remova contas de utilizadores.</p>
        <a href="{{ route('admin.users.create') }}">
            <button type="button" style="background-color: #2ed573; color: black; padding: 10px 20px; font-size: 14px;">
                + Criar Membro do Staff
            </button>
        </a>
    </div>

    @if(session('status'))
        <div class="alert-success">{{ session('status') }}</div>
    @endif

    <form method="GET" action="{{ route('admin.users.index') }}" class="filter-bar">
        <input type="text" name="search" placeholder="Pesquisar por nome ou e-mail..." value="{{ request('search') }}" style="flex: 1;">
        
        <select name="type">
            <option value="">Todos os Perfis</option>
            <option value="C" {{ request('type') == 'C' ? 'selected' : '' }}>Clientes</option>
            <option value="F" {{ request('type') == 'F' ? 'selected' : '' }}>Funcionários</option>
            <option value="A" {{ request('type') == 'A' ? 'selected' : '' }}>Administradores</option>
        </select>

        <button type="submit">Filtrar</button>
        <a href="{{ route('admin.users.index') }}"><button type="button" class="btn-secondary">Limpar</button></a>
    </form>

    <table>
        <thead>
            <tr>
                <th>Avatar</th>
                <th>Nome</th>
                <th>E-mail</th>
                <th>Perfil</th>
                <th>Estado</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr>
                    <td>
                        @if($user->photo_url && \Illuminate\Support\Facades\Storage::disk('public')->exists('profiles/' . $user->photo_url))
                            <img src="{{ url('img-profiles/' . $user->photo_url) }}" class="avatar-mini" alt="Avatar">
                        @else
                            <div style="width: 35px; height: 35px; border-radius: 50%; background: #333; display: flex; align-items: center; justify-content: center; font-size: 16px; margin: 0;">👤</div>
                        @endif
                    </td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @if($user->user_type === 'A')
                            <span class="badge badge-admin">Admin</span>
                        @elseif($user->user_type === 'F')
                            <span class="badge badge-employee">Funcionário</span>
                        @else
                            <span class="badge badge-client">Cliente</span>
                        @endif
                    </td>
                    <td>
                        @if($user->blocked)
                            <span class="badge badge-blocked">Bloqueado</span>
                        @else
                            <span class="badge" style="background:#444;">Ativo</span>
                        @endif
                    </td>
                   <td>
                        <div class="actions-cell">
                            <form method="POST" action="{{ route('admin.users.toggle-block', $user) }}" style="margin: 0; padding: 0;">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn-block-toggle" style="background-color: {{ $user->blocked ? '#2ed573' : '#ffa500' }}; color: black; font-size: 12px; padding: 6px 0; border-radius: 4px; font-weight: bold; cursor: pointer; white-space: nowrap; display: block;">
                                    {{ $user->blocked ? 'Desbloquear' : 'Bloquear' }}
                                </button>
                            </form>

                            @if($user->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" style="margin: 0; padding: 0;" onsubmit="return confirm('Tem a certeza que deseja eliminar o utilizador {{ $user->name }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background-color: #ff4757; color: white; font-size: 12px; padding: 6px 15px; border-radius: 4px; font-weight: bold; cursor: pointer; white-space: nowrap;">
                                        Eliminar
                                    </button>
                                </form>
                            @else
                                <div style="width: 75px;"></div>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 20px;">
        {{ $users->links() }}
    </div>
</div>

</body>
</html>