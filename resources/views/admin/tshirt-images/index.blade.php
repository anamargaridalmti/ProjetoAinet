<x-layouts::app :title="__('Gestão do Catálogo')">
    <div class="space-y-6">
        
        <!-- Cabeçalho Administrativo -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <flux:heading size="xl" class="font-bold tracking-tight text-zinc-900 dark:text-white">
                    🖼️ Catálogo de Estampagem
                </flux:heading>
                <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400 mt-0.5">
                    Gira as artes e imagens disponíveis para aplicação nas t-shirts da loja.
                </flux:text>
            </div>
            
            <a href="{{ route('admin.tshirt-images.create') }}" wire:navigate class="inline-flex items-center justify-center px-4 py-2 bg-zinc-950 hover:bg-zinc-800 text-white dark:bg-white dark:hover:bg-zinc-100 dark:text-zinc-900 text-sm font-semibold rounded-lg shadow-xs transition cursor-pointer">
                + Nova Estampa
            </a>
        </div>

        <!-- Alertas de Feedback Integrados -->
        @if(session('alert-msg'))
            <flux:card class="p-3 border text-sm {{ session('alert-type') === 'success' ? 'bg-emerald-50 dark:bg-emerald-950/30 border-emerald-200 text-emerald-700 dark:text-emerald-400' : 'bg-red-50 dark:bg-red-950/30 border-red-200 text-red-700 dark:text-red-400' }}">
                {!! session('alert-msg') !!}
            </flux:card>
        @endif

        <!-- Tabela Administrativa do Flux UI -->
        <flux:card class="overflow-x-auto p-0 border border-zinc-200/60 dark:border-zinc-800/80">
            <table class="w-full text-left text-sm text-zinc-600 dark:text-zinc-400">
                <thead class="bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-800 text-xs uppercase text-zinc-500 font-semibold">
                    <tr>
                        <th class="px-6 py-4 w-24">Arte</th>
                        <th class="px-6 py-4">Nome da Estampa</th>
                        <th class="px-6 py-4">Descrição</th>
                        <th class="px-6 py-4 w-40">Categoria</th>
                        <th class="px-6 py-4 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                    @forelse($tshirtImages as $image)
                        <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-900/30 transition-colors">
                            
                            <!-- Miniatura Física da Estampa -->
                            <td class="px-6 py-2 whitespace-nowrap">
                                <div class="w-12 h-12 rounded bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 overflow-hidden p-1 flex items-center justify-center shadow-2xs">
                                    <img 
                                        src="{{ url('/images/catalog/' . $image->image_url) }}" 
                                        alt="Estampa" 
                                        class="w-full h-full object-contain"
                                        onerror="this.src='https://placehold.co/100x100/e4e4e7/a1a1aa?text=No+Img';"
                                    />
                                </div>
                            </td>

                            <!-- Nome -->
                            <td class="px-6 py-4 font-semibold text-zinc-900 dark:text-white whitespace-nowrap">
                                {{ $image->name }}
                            </td>

                            <!-- Descrição Curta com limite de caracteres para não quebrar a tabela -->
                            <td class="px-6 py-4 text-zinc-500 dark:text-zinc-400 max-w-xs truncate">
                                {{ $image->description ?? 'Sem descrição fornecida.' }}
                            </td>

                            <!-- Vínculo com Categoria (Enunciado preve elementos sem categoria) -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($image->category)
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-md bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700">
                                        {{ $image->category->name }}
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 text-xs font-medium italic rounded-md bg-zinc-50 dark:bg-zinc-900/50 text-zinc-400">
                                        Sem Categoria
                                    </span>
                                @endif
                            </td>

                            <!-- Painel de Operações -->
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-2 justify-end">
                                    
                                    <a href="{{ route('admin.tshirt-images.edit', $image) }}" wire:navigate class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-semibold rounded-md border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-700 dark:text-zinc-200 hover:bg-zinc-50 dark:hover:bg-zinc-700 transition cursor-pointer">
                                        Editar
                                    </a>

                                    <form method="POST" action="{{ route('admin.tshirt-images.destroy', $image) }}" class="inline" onsubmit="return confirm('Tem a certeza que deseja remover esta estampa do catálogo?');">
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
                            <td colspan="5" class="px-6 py-8 text-center text-zinc-400 dark:text-zinc-500">Nenhuma estampa registada no catálogo de administração.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </flux:card>

        <!-- Paginação -->
        <div class="mt-4">
            {{ $tshirtImages->links() }}
        </div>
    </div>
</x-layouts::app>