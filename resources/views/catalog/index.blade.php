<x-layouts::app :title="__('Catálogo de T-Shirts')">
    <div class="space-y-8">
        
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <flux:heading size="xl" class="font-bold tracking-tight text-zinc-900 dark:text-white">
                    👕 Catálogo FunShirt
                </flux:heading>
                <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400 mt-0.5">
                    Explore a nossa seleção de estampas exclusivas prontas para aplicar na sua t-shirt.
                </flux:text>
            </div>
            
            <div class="text-sm">
                @guest
                    <flux:text size="sm" class="text-zinc-400">
                        Deseja personalizar? <a href="{{ route('login') }}" class="font-semibold text-zinc-900 dark:text-zinc-100 underline underline-offset-4 hover:text-zinc-700">Faça Login</a>
                    </flux:text>
                @endguest
            </div>
        </div>

        <flux:card class="p-4 bg-zinc-50 dark:bg-zinc-900/50 border border-zinc-200/80 dark:border-zinc-800/80">
            <form method="GET" action="{{ route('catalog.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
                
                <flux:field>
                    <flux:label for="search">Termo de Pesquisa</flux:label>
                    <flux:input 
                        type="text" 
                        name="search" 
                        id="search"
                        value="{{ request('search') }}" 
                        placeholder="Pesquisar por nome ou descrição..." 
                        icon="magnifying-glass" 
                    />
                </flux:field>

                <flux:field>
                    <flux:label for="category_id">Categoria do Produto</flux:label>
                    <flux:select name="category_id" id="category_id" placeholder="Todas as Categorias">
                        @foreach($categories as $cat)
                            <flux:select.option value="{{ $cat->id }}">
                                {{ $cat->name }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                </flux:field>

                <div class="flex gap-2">
                    <flux:button type="submit" variant="filled" class="w-full justify-center bg-zinc-900 hover:bg-zinc-800 text-white dark:bg-white dark:hover:bg-zinc-100 dark:text-zinc-900 font-semibold cursor-pointer">
                        Filtrar Catálogo
                    </flux:button>
                    
                    <flux:button href="{{ route('catalog.index') }}" variant="subtle" class="w-full justify-center" wire:navigate>
                        Limpar
                    </flux:button>
                </div>
            </form>
        </flux:card>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @forelse($tshirts as $tshirt)
                <flux:card class="flex flex-col justify-between overflow-hidden p-0 group border border-zinc-200/60 dark:border-zinc-800/80 bg-white dark:bg-zinc-950 shadow-sm hover:shadow-md transition">
                    
                    <div class="aspect-square bg-zinc-50 dark:bg-zinc-900/40 flex items-center justify-center overflow-hidden border-b border-zinc-100 dark:border-zinc-900 relative p-4">
                        <img 
                            src="{{ $tshirt->image_url ? url('/images/catalog/' . $tshirt->image_url) : url('/img-categories/default.png') }}" 
                            alt="{{ $tshirt->name }}"
                            class="max-w-full max-h-full object-contain group-hover:scale-105 transition duration-300"
                            loading="lazy"
                        />
                        
                        <span class="absolute top-3 left-3 px-2 py-0.5 rounded text-[10px] font-bold tracking-wide uppercase shadow-xs backdrop-blur-xs bg-zinc-900/80 text-white dark:bg-zinc-100/90 dark:text-zinc-900">
                            📁 {{ $tshirt->category->name ?? 'Sem Categoria' }}
                        </span>
                    </div>

                    <div class="p-4 space-y-3 flex-grow flex flex-col justify-between">
                        <div>
                            <div class="font-bold text-zinc-900 dark:text-white truncate text-base">
                                {{ $tshirt->name }}
                            </div>
                            <p class="text-xs text-zinc-400 dark:text-zinc-500 line-clamp-2 mt-1 h-8 leading-relaxed">
                                {{ $tshirt->description ?? 'Sem descrição detalhada configurada para esta estampa.' }}
                            </p>
                        </div>

                        <div class="pt-1 border-t border-zinc-100 dark:border-zinc-900">
                            <flux:button variant="subtle" size="sm" class="w-full justify-center gap-2 cursor-not-allowed opacity-75">
                                <flux:icon icon="shopping-bag" class="w-3.5 h-3.5" />
                                Ver Opções
                            </flux:button>
                        </div>
                    </div>
                </flux:card>
            @empty
                <div class="col-span-full py-16 text-center space-y-3 border border-dashed border-zinc-200 dark:border-zinc-800 rounded-xl">
                    <flux:icon icon="photo" class="w-10 h-10 mx-auto text-zinc-300 dark:text-zinc-700" />
                    <flux:heading size="lg" class="text-zinc-400 font-medium">Nenhuma estampa registada</flux:heading>
                    <flux:text size="sm" class="text-zinc-400 max-w-sm mx-auto px-4">
                        Não encontrámos t-shirts que correspondam aos filtros introduzidos.
                    </flux:text>
                </div>
            @endforelse
        </div>

        <div class="mt-8 border-t border-zinc-100 dark:border-zinc-900 pt-4">
            {{ $tshirts->links() }}
        </div>
    </div>
</x-layouts::app>