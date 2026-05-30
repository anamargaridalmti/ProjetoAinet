<x-layouts::app :title="__('Gestão de Utilizadores')">
    <div class="space-y-6">
        
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <flux:heading size="xl" class="font-bold tracking-tight text-zinc-900 dark:text-white">
                    👥 Gestão de Utilizadores
                </flux:heading>
                <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400 mt-0.5">
                    Gire os acessos, bloqueie ou remova contas de utilizadores da plataforma.
                </flux:text>
            </div>
            
            <a href="{{ route('admin.users.create') }}" wire:navigate class="inline-flex items-center justify-center px-4 py-2 bg-zinc-950 hover:bg-zinc-800 text-white dark:bg-white dark:hover:bg-zinc-100 dark:text-zinc-900 text-sm font-semibold rounded-lg shadow-xs transition cursor-pointer">
                + Criar Membro do Staff
            </a>
        </div>

        @if(session('status'))
            <flux:card class="p-3 bg-emerald-50 dark:bg-emerald-950/30 border-emerald-200 text-emerald-600 dark:text-emerald-400 text-sm">
                {{ session('status') }}
            </flux:card>
        @endif

        <flux:card class="p-4 bg-zinc-50 dark:bg-zinc-900/50 border border-zinc-200/80 dark:border-zinc-800/80">
            <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
                
                <flux:field>
                    <flux:label for="search">Termo de Pesquisa</flux:label>
                    <flux:input 
                        type="text" 
                        name="search" 
                        id="search"
                        value="{{ request('search') }}" 
                        placeholder="Pesquisar por nome ou e-mail..." 
                        icon="magnifying-glass" 
                    />
                </flux:field>

                <div>
                    <label for="type" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Perfil de Acesso</label>
                    <select name="type" id="type" class="w-full border border-zinc-300 dark:border-zinc-700 rounded-lg px-3 py-2 text-sm bg-white dark:bg-zinc-900 text-zinc-800 dark:text-zinc-200 focus:outline-none focus:ring-2 focus:ring-zinc-500">
                        <option value="">Todos os Perfis</option>
                        <option value="C" {{ request('type') == 'C' ? 'selected' : '' }}>Clientes</option>
                        <option value="F" {{ request('type') == 'F' ? 'selected' : '' }}>Funcionários</option>
                        <option value="A" {{ request('type') == 'A' ? 'selected' : '' }}>Administradores</option>
                    </select>
                </div>

                <div class="flex gap-2">
                    <flux:button type="submit" variant="filled" class="w-full justify-center bg-zinc-900 hover:bg-zinc-800 text-white dark:bg-white dark:hover:bg-zinc-100 dark:text-zinc-900 font-semibold cursor-pointer">
                        Filtrar Lista
                    </flux:button>
                    <a href="{{ route('admin.users.index') }}" wire:navigate class="w-full inline-flex items-center justify-center px-4 py-2 bg-zinc-200/60 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 text-sm font-medium rounded-lg transition text-center">
                        Limpar
                    </a>
                </div>
            </form>
        </flux:card>

        <flux:card class="overflow-x-auto p-0 border border-zinc-200/60 dark:border-zinc-800/80">
            <table class="w-full text-left text-sm text-zinc-600 dark:text-zinc-400">
                <thead class="bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-800 text-xs uppercase text-zinc-500 font-semibold">
                    <tr>
                        <th class="px-6 py-4">Avatar</th>
                        <th class="px-6 py-4">Nome</th>
                        <th class="px-6 py-4">E-mail</th>
                        <th class="px-6 py-4">Perfil</th>
                        <th class="px-6 py-4">Estado</th>
                        <th class="px-6 py-4 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                    @forelse($users as $user)
                        <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-900/30 transition-colors">
                            
                            <td class="px-6 py-3 whitespace-nowrap">
                                <div class="w-9 h-9 rounded-full bg-zinc-100 dark:bg-zinc-800 overflow-hidden flex items-center justify-center border border-zinc-200 dark:border-zinc-700">
                                    @if($user->photo_url)
                                        <img src="{{ url('img-profiles/' . $user->photo_url) }}" class="w-full h-full object-cover" alt="Avatar">
                                    @else
                                        <span class="text-base">👤</span>
                                    @endif
                                </div>
                            </td>

                            <td class="px-6 py-4 font-semibold text-zinc-900 dark:text-white whitespace-nowrap">
                                {{ $user->name }}
                            </td>

                            <td class="px-6 py-4 text-zinc-500 dark:text-zinc-400 whitespace-nowrap">
                                {{ $user->email }}
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($user->user_type === 'A')
                                    <span class="px-2 py-0.5 text-xs font-bold rounded bg-amber-50 text-amber-600 dark:bg-amber-950/30 dark:text-amber-400">Admin</span>
                                @elseif($user->user_type === 'F')
                                    <span class="px-2 py-0.5 text-xs font-bold rounded bg-blue-50 text-blue-600 dark:bg-blue-950/30 dark:text-blue-400">Funcionário</span>
                                @else
                                    <span class="px-2 py-0.5 text-xs font-bold rounded bg-emerald-50 text-emerald-600 dark:bg-emerald-950/30 dark:text-emerald-400">Cliente</span>
                                @endif
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($user->blocked)
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 text-xs font-bold rounded bg-red-50 text-red-600 dark:bg-red-950/30 dark:text-red-400">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Bloqueado
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 text-xs font-bold rounded bg-zinc-100 text-zinc-600 dark:bg-zinc-800/50 dark:text-zinc-400">
                                        <span class="w-1.5 h-1.5 rounded-full bg-zinc-400 dark:bg-zinc-500"></span> Ativo
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-2 justify-end">
                                    
                                    <form method="POST" action="{{ route('admin.users.toggle-block', $user) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-semibold rounded-md border transition cursor-pointer w-24 shadow-2xs
                                            {{ $user->blocked 
                                                ? 'bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 border-emerald-500/30' 
                                                : 'bg-amber-500/10 hover:bg-amber-500/20 text-amber-600 dark:text-amber-400 border-amber-500/30' }}">
                                            {{ $user->blocked ? 'Desbloquear' : 'Bloquear' }}
                                        </button>
                                    </form>

                                    @if($user->id !== auth()->id())
                                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline"
                                            onsubmit="return confirm('Tem a certeza que deseja eliminar este utilizador?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-semibold rounded-md text-red-600 hover:bg-red-500/10 dark:hover:bg-red-500/20 transition cursor-pointer">
                                                Eliminar
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-xs text-zinc-400 dark:text-zinc-500 italic px-2">A sua conta</span>
                                    @endif

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-zinc-400">Nenhum utilizador encontrado com os filtros aplicados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </flux:card>

    </div>
</x-layouts::app>