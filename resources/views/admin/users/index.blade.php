<x-layouts::app :title="__('Gestão de Utilizadores')">
    <div class="w-full space-y-6">
        
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <flux:heading size="xl" class="font-bold tracking-tight text-zinc-900 dark:text-white">
                    👥 Gestão de Utilizadores
                </flux:heading>
                <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400 mt-0.5">
                    Controle os acessos, bloqueie ou remova contas de utilizadores da plataforma (G1).
                </flux:text>
            </div>
            
            <a href="{{ route('admin.users.create') }}" wire:navigate class="inline-flex items-center justify-center px-4 py-2 bg-zinc-950 hover:bg-zinc-800 text-white dark:bg-white dark:hover:bg-zinc-100 dark:text-zinc-900 text-sm font-semibold rounded-lg shadow-xs transition cursor-pointer">
                + Criar Membro do Staff
            </a>
        </div>

        @if(session('status'))
            <flux:card class="p-3 border text-sm bg-emerald-50 dark:bg-emerald-950/30 border-emerald-200 text-emerald-700 dark:text-emerald-400">
                {{ session('status') }}
            </flux:card>
        @endif

        @if(session('error'))
            <flux:card class="p-3 border text-sm bg-red-50 dark:bg-red-950/30 border-red-200 text-red-700 dark:text-red-400">
                {{ session('error') }}
            </flux:card>
        @endif

        <flux:card class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800/80 p-4 rounded-xl">
            <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                <div class="md:col-span-5">
                    <flux:field>
                        <flux:label>Termo de Pesquisa</flux:label>
                        <flux:input type="text" name="search" value="{{ request('search') }}" placeholder="Pesquisar por nome ou e-mail..." />
                    </flux:field>
                </div>

                <div class="md:col-span-4">
                    <flux:field>
                        <flux:label>Perfil de Acesso</flux:label>
                        <flux:select name="type" placeholder="Todos os Perfis">
                            <option value="">Todos os Perfis</option>
                            <option value="C" {{ request('type') == 'C' ? 'selected' : '' }}>Cliente</option>
                            <option value="E" {{ request('type') == 'E' ? 'selected' : '' }}>Funcionário</option>
                            <option value="A" {{ request('type') == 'A' ? 'selected' : '' }}>Administrador</option>
                        </flux:select>
                    </flux:field>
                </div>

                <div class="md:col-span-3 flex gap-2">
                    <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-zinc-900 hover:bg-zinc-800 dark:bg-zinc-100 dark:hover:bg-zinc-200 text-white dark:text-zinc-900 text-sm font-semibold rounded-lg shadow-sm transition cursor-pointer">
                        Filtrar
                    </button>
                    <a href="{{ route('admin.users.index') }}" wire:navigate class="inline-flex items-center justify-center px-4 py-2 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-200 text-sm font-medium rounded-lg transition cursor-pointer">
                        Limpar
                    </a>
                </div>
            </form>
        </flux:card>

        <flux:card class="p-0 border border-zinc-200/60 dark:border-zinc-800/80 bg-white dark:bg-zinc-900 overflow-hidden">
            <div class="overflow-x-auto w-full">
                <table class="w-full text-left text-sm text-zinc-600 dark:text-zinc-400 table-auto min-w-[1000px]">
                    <thead class="bg-zinc-50 dark:bg-zinc-950 border-b border-zinc-200 dark:border-zinc-800 text-xs uppercase text-zinc-500 font-semibold">
                        <tr>
                            <th class="px-6 py-4 w-20">Avatar</th>
                            <th class="px-6 py-4 min-w-[220px]">Nome</th>
                            <th class="px-6 py-4 min-w-[200px]">E-mail</th>
                            <th class="px-6 py-4 w-32">Perfil</th>
                            <th class="px-6 py-4 w-32">Estado</th>
                            <th class="px-6 py-4 text-right w-64">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @forelse($users as $user)
                            <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-950/40 transition-colors">
                                
                                <td class="px-6 py-3.5 whitespace-nowrap">
                                    <div class="w-10 h-10 rounded-full bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 overflow-hidden flex items-center justify-center text-xs font-bold text-zinc-600 dark:text-zinc-300 shadow-2xs">
                                        
                                        @if($user->photo_url && \Illuminate\Support\Facades\Storage::disk('public')->exists('profiles/' . $user->photo_url))
                                            <img 
                                                src="{{ url('/img-profiles/' . $user->photo_url) }}?v={{ time() }}" 
                                                alt="Avatar de {{ $user->name }}" 
                                                class="w-full h-full object-cover"
                                            />
                                        @else
                                            {{ strtoupper(substr($user->name, 0, 2)) }}
                                        @endif

                                    </div>
                                </td>

                                <td class="px-6 py-4 font-semibold text-zinc-900 dark:text-white whitespace-nowrap">
                                    {{ $user->name }}
                                </td>

                                <td class="px-6 py-4 text-zinc-500 dark:text-zinc-400 font-mono text-xs whitespace-nowrap">
                                    {{ $user->email }}
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($user->user_type === 'A')
                                        <span class="px-2 py-0.5 text-[11px] font-bold rounded-md bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-400 border border-purple-200/60 dark:border-purple-900/50">Admin</span>
                                    @elseif($user->user_type === 'E' || $user->user_type === 'F')
                                        <span class="px-2 py-0.5 text-[11px] font-bold rounded-md bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-400 border border-blue-200/60 dark:border-blue-900/50">Staff</span>
                                    @else
                                        <span class="px-2 py-0.5 text-[11px] font-bold rounded-md bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700">Cliente</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($user->blocked)
                                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 text-xs font-semibold rounded-md bg-red-50 dark:bg-red-950/30 text-red-700 dark:text-red-400 border border-red-200 dark:border-red-900/50">
                                            <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span> Bloqueado
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 text-xs font-semibold rounded-md bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-900/50">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Ativo
                                        </span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-2 justify-end">
                                        
                                        <form method="POST" action="{{ route('admin.users.toggle-block', $user) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-semibold rounded-md border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-700 dark:text-zinc-200 hover:bg-zinc-50 dark:hover:bg-zinc-700 transition cursor-pointer">
                                                {{ $user->blocked ? 'Desbloquear' : 'Bloquear' }}
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline" onsubmit="return confirm('Tem a certeza que deseja remover permanentemente este utilizador? O histórico será preservado via soft delete (G1).');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-semibold rounded-md text-red-600 hover:bg-red-500/10 dark:hover:bg-red-500/20 transition cursor-pointer">
                                                Eliminar
                                            </button>
                                        </form>

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-zinc-400 dark:text-zinc-500">Nenhum utilizador encontrado com os filtros aplicados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </flux:card>

        @if($users instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="mt-4 py-2 border-t border-zinc-100 dark:border-zinc-800/60">
                {{ $users->appends(request()->query())->links() }}
            </div>
        @endif

    </div>
</x-layouts::app>