<x-layouts::app :title="__('O Meu Carrinho')">
    <div class="w-full max-w-7xl mx-auto space-y-6">
        
        <div class="flex justify-between items-center border-b border-zinc-200 dark:border-zinc-800 pb-4">
            <div>
                <flux:heading size="xl" class="font-bold tracking-tight text-zinc-900 dark:text-white">
                    🛒 O Meu Carrinho de Compras
                </flux:heading>
                <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400 mt-0.5">
                    Reveja os seus itens, ajuste as características e quantidades antes do checkout.
                </flux:text>
            </div>
            
            @if(count($cart) > 0)
                <form method="POST" action="{{ route('cart.clear') }}" onsubmit="return confirm('Deseja limpar todos os itens do carrinho?');">
                    @csrf
                    @method('DELETE')
                    <flux:button type="submit" variant="subtle" icon="trash" class="text-red-600 hover:text-red-700 cursor-pointer">
                        Limpar Carrinho
                    </flux:button>
                </form>
            @endif
        </div>

        @if(session('status'))
            <flux:card class="p-3 border text-sm bg-emerald-50 dark:bg-emerald-950/30 border-emerald-200 text-emerald-700 dark:text-emerald-400">
                {{ session('status') }}
            </flux:card>
        @endif

        @if(count($cart) > 0)
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                
                <div class="lg:col-span-8 space-y-4">
                    @foreach($cart as $key => $item)
                        <flux:card class="p-4 bg-white dark:bg-zinc-900 border border-zinc-200/60 dark:border-zinc-800/80">
                            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                
                                <div class="flex items-center gap-4">
                                    <div class="w-16 h-16 rounded-lg bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 flex items-center justify-center p-1 overflow-hidden shrink-0">
                                        @if($item['customer_id'])
                                            <img src="{{ url('/img-profiles/' . $item['image_url']) }}" alt="Personalizada" class="w-full h-full object-contain">
                                        @else
                                            <img src="{{ url('/images/catalog/' . $item['image_url']) }}" alt="Catálogo" class="w-full h-full object-contain">
                                        @endif
                                    </div>
                                    <div>
                                        <flux:heading size="md" class="font-bold text-zinc-900 dark:text-white">{{ $item['name'] }}</flux:heading>
                                        <flux:text size="xs" class="text-zinc-500 font-mono">
                                            {{ $item['customer_id'] ? '✨ Imagem Própria' : '📁 Catálogo' }}
                                        </flux:text>
                                    </div>
                                </div>

                                <form method="POST" action="{{ route('cart.update', $key) }}" class="flex flex-wrap items-center gap-3 sm:gap-4 flex-1 justify-end w-full sm:w-auto">
                                    @csrf
                                    @method('PATCH')

                                    <div class="w-32">
                                        <select name="color_code" onchange="this.form.submit()" class="w-full text-xs rounded-lg border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 p-2 text-zinc-800 dark:text-zinc-200">
                                            @foreach(\App\Models\Color::all() as $color)
                                                <option value="{{ $color->code }}" {{ $item['color_code'] == $color->code ? 'selected' : '' }}>
                                                    {{ $color->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="w-20">
                                        <select name="size" onchange="this.form.submit()" class="w-full text-xs rounded-lg border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 p-2 text-zinc-800 dark:text-zinc-200">
                                            @foreach(['XS','S','M','L','XL'] as $size)
                                                <option value="{{ $size }}" {{ $item['size'] == $size ? 'selected' : '' }}>{{ $size }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="w-20">
                                        <input type="number" name="qty" value="{{ $item['qty'] }}" min="0" onchange="this.form.submit()" class="w-full text-center text-xs rounded-lg border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 p-2 text-zinc-800 dark:text-zinc-200 font-bold" />
                                    </div>

                                    <div class="text-right min-w-[90px]">
                                        <div class="text-sm font-black text-zinc-900 dark:text-white">
                                            {{ number_format($item['subtotal'], 2) }}€
                                        </div>
                                        <div class="text-[10px] text-zinc-400">
                                            {{ number_format($item['unit_price'], 2) }}€ / un
                                        </div>
                                    </div>
                                </form>

                                <form method="POST" action="{{ route('cart.remove', $key) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-zinc-400 hover:text-red-500 transition cursor-pointer">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>

                            </div>
                        </flux:card>
                    @endforeach
                </div>

                <div class="lg:col-span-4">
                    <flux:card class="p-6 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 space-y-4 sticky top-24">
                        <flux:heading size="lg" class="font-bold">Resumo do Pedido</flux:heading>
                        
                        <div class="space-y-2 text-sm border-b border-zinc-200 dark:border-zinc-800 pb-4">
                            <div class="flex justify-between text-zinc-500">
                                <span>Total de Itens:</span>
                                <span class="font-bold text-zinc-800 dark:text-zinc-200">{{ array_sum(array_column($cart, 'qty')) }}</span>
                            </div>
                            <div class="flex justify-between text-zinc-500">
                                <span>Volume de Desconto:</span>
                                <span class="text-xs text-zinc-400">A partir de {{ $prices->qty_discount }} un / linha</span>
                            </div>
                        </div>

                        <div class="flex justify-between items-baseline py-2">
                            <span class="text-zinc-600 dark:text-zinc-400 font-medium">Total Global:</span>
                            <span class="text-3xl font-black text-zinc-900 dark:text-white">
                                {{ number_format($total, 2) }}€
                            </span>
                        </div>

                        <div class="pt-2">
                            <a href="#" class="w-full inline-flex items-center justify-center px-4 py-3 bg-zinc-950 hover:bg-zinc-800 text-white dark:bg-white dark:hover:bg-zinc-100 dark:text-zinc-900 font-bold rounded-xl shadow-sm transition text-center text-sm cursor-pointer">
                                Avançar para o Checkout →
                            </a>
                        </div>
                    </flux:card>
                </div>

            </div>
        @else
            <flux:card class="p-12 text-center border-2 border-dashed border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900">
                <div class="text-4xl mb-3">🛒</div>
                <flux:heading size="lg" class="font-bold">O seu carrinho está vazio</flux:heading>
                <flux:text size="sm" class="text-zinc-400 mt-1 mb-6">Explore as nossas estampas e encontre o seu estilo.</flux:text>
                <flux:button :href="route('catalog.index')" wire:navigate variant="filled" class="cursor-pointer">
                    Ver Catálogo de Imagens
                </flux:button>
            </flux:card>
        @endif

    </div>
</x-layouts::app>