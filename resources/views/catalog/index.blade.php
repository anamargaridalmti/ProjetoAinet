<x-layouts::app :title="__('Catálogo')">
    <div class="space-y-8">
        
        <!-- Cabeçalho do Catálogo -->
        <div>
            <div class="flex items-center gap-2">
                <span class="text-2xl">👕</span>
                <flux:heading size="xl" class="font-black tracking-tight text-zinc-900 dark:text-white">
                    Catálogo FunShirt
                </flux:heading>
            </div>
            <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400 mt-1">
                Explore a nossa seleção de estampas exclusivas prontas para aplicar na sua t-shirt.
            </flux:text>
        </div>

        <!-- 🔍 Zona de Filtros: Fundo Branco Suave com Borda Discreta no Modo Claro -->
        <flux:card class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 p-5 rounded-xl shadow-xs">
            <form method="GET" action="{{ route('catalog.index') }}" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                
                <div class="md:col-span-5">
                    <flux:field>
                        <flux:label class="text-zinc-700 dark:text-zinc-300 font-medium mb-1">Termo de Pesquisa</flux:label>
                        <flux:input type="text" name="search" value="{{ request('search') }}" placeholder="Pesquisar por nome ou descrição..." class="bg-zinc-50 dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800" />
                    </flux:field>
                </div>

                <div class="md:col-span-4">
                    <flux:field>
                        <flux:label class="text-zinc-700 dark:text-zinc-300 font-medium mb-1">Categoria do Produto</flux:label>
                        <flux:select name="category" placeholder="Todas as Categorias" class="bg-zinc-50 dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800">
                            <option value="">Todas as Categorias</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </flux:select>
                    </flux:field>
                </div>

                <!-- 🔘 Botões Ajustados para Evitar Contrastes Gritantes -->
                <div class="md:col-span-3 flex gap-2">
                    <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-zinc-900 hover:bg-zinc-800 dark:bg-zinc-100 dark:hover:bg-zinc-200 text-white dark:text-zinc-900 text-sm font-semibold rounded-lg shadow-sm transition-colors cursor-pointer duration-150">
                        Filtrar Catálogo
                    </button>
                    
                    @if(request()->filled('search') || request()->filled('category'))
                        <a href="{{ route('catalog.index') }}" wire:navigate class="inline-flex items-center justify-center px-4 py-2.5 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-200 text-sm font-medium rounded-lg transition-colors cursor-pointer">
                            Limpar
                        </a>
                    @endif
                </div>
            </form>
        </flux:card>

        <!-- 🖼️ Grelha de Produtos: Cartões com Relevo em Fundo Off-White -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @forelse($tshirtImages as $image)
                <div class="group bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200/70 dark:border-zinc-800/80 overflow-hidden shadow-2xs hover:shadow-md transition-all duration-200 flex flex-col justify-between">
                    
                    <!-- Topo / Imagem -->
                    <div class="p-4 space-y-3">
                        <div class="relative w-full aspect-square bg-zinc-50 dark:bg-zinc-950/40 rounded-lg overflow-hidden border border-zinc-100 dark:border-zinc-800/50 p-4 flex items-center justify-center">
                            
                            <!-- Tag de Categoria -->
                            <span class="absolute top-2 left-2 z-10 px-2 py-0.5 text-[10px] font-bold tracking-wider uppercase rounded bg-zinc-900/80 dark:bg-zinc-800/90 text-white">
                                {{ $image->category->name ?? 'Sem Categoria' }}
                            </span>

                            <img 
                                src="{{ url('/images/catalog/' . $image->image_url) }}" 
                                alt="{{ $image->name }}" 
                                class="max-w-full max-h-full object-contain transform group-hover:scale-105 transition-transform duration-200"
                                onerror="this.src='https://placehold.co/300x300/f4f4f5/a1a1aa?text=FunShirt';"
                            />
                        </div>

                        <div>
                            <h3 class="font-bold text-zinc-900 dark:text-white text-base truncate">
                                {{ $image->name }}
                            </h3>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 line-clamp-2 mt-1 min-h-[32px]">
                                {{ $image->description ?? 'Design exclusivo oficial da FunShirt.' }}
                            </p>
                        </div>
                    </div>

                    {{-- Rodapé do Cartão / Ação --}}
                    <div class="px-4 pb-4 pt-2 border-t border-zinc-100 dark:border-zinc-800/60 bg-zinc-50/50 dark:bg-zinc-900/20">
                        @livewire('cart.add-to-cart', ['tshirtImageId' => $image->id], key('add-to-cart-' . $image->id))
                    </div>

                </div>
            @empty
                <div class="col-span-full py-12 text-center">
                    <p class="text-zinc-400 dark:text-zinc-500 font-medium">Nenhuma estampa corresponde aos filtros selecionados.</p>
                </div>
            @endforelse
        </div>

        <!-- Paginação -->
        <div class="mt-6">
            {{ $tshirtImages->links() }}
        </div>

    </div>
</x-layouts::app>