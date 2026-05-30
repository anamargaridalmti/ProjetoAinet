<x-layouts::app :title="__('Gestão de Categorias')">
    <div class="space-y-6">
        
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <flux:heading size="xl" class="font-bold">Categorias do Catálogo</flux:heading>
                <flux:text>Organize e gira os temas, coleções e imagens da FunShirt.</flux:text>
            </div>
            <flux:button :href="route('categories.create')" variant="filled" icon="plus" class="bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 cursor-pointer" wire:navigate>
                Nova Categoria
            </flux:button>
        </div>

        @if (session('success'))
            <flux:card class="p-3 bg-emerald-50 dark:bg-emerald-950/30 border-emerald-200 text-emerald-600 text-sm">
                {{ session('success') }}
            </flux:card>
        @endif

        <flux:card class="overflow-x-auto p-0">
            <table class="w-full text-left text-sm text-zinc-600 dark:text-zinc-400">
                <thead class="bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-800 text-xs uppercase text-zinc-500 font-semibold">
                    <tr>
                        <th class="px-6 py-4 w-20">Imagem</th>
                        <th class="px-6 py-4">Nome da Categoria</th>
                        <th class="px-6 py-4">Descrição</th>
                        <th class="px-6 py-4 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                    @forelse($categories as $category)
                        <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-900/30">
                            
                            <td class="px-6 py-3">
                                <div class="w-12 h-12 rounded-lg bg-zinc-100 dark:bg-zinc-800 overflow-hidden flex items-center justify-center p-1 border border-zinc-200 dark:border-zinc-700">
                                    <img 
                                        src="{{ $category->image_url ? url('/img-categories/' . $category->image_url) : url('/img-categories/default.png') }}" 
                                        alt="{{ $category->name }}" 
                                        class="w-full h-full object-contain"
                                    />
                                </div>
                            </td>

                            <td class="px-6 py-4 font-semibold text-zinc-900 dark:text-white">
                                {{ $category->name }}
                            </td>

                            <td class="px-6 py-4 max-w-xs truncate text-zinc-400">
                                {{ $category->description ?? 'Sem descrição atribuída.' }}
                            </td>

                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-2">
                                    <flux:button :href="route('categories.edit', $category)" variant="ghost" size="sm" icon="pencil" wire:navigate class="cursor-pointer">
                                        Editar
                                    </flux:button>

                                    <form method="POST" action="{{ route('categories.destroy', $category) }}" class="inline" onsubmit="return confirm('Tem a certeza que deseja eliminar a categoria \'{{ $category->name }}\'?');">
                                        @csrf
                                        @method('DELETE')
                                        <flux:button type="submit" variant="ghost" size="sm" class="text-red-600 hover:text-red-700 cursor-pointer">
                                            Eliminar
                                        </flux:button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-zinc-400">Nenhuma categoria encontrada no sistema.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </flux:card>

        <div class="mt-4">
            {{ $categories->links() }}
        </div>
    </div>
</x-layouts::app>