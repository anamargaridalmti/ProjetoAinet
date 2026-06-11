<div class="w-full max-w-7xl mx-auto space-y-6">

    {{-- Header --}}
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
            <flux:button wire:click="clearCart" wire:confirm="Deseja limpar todos os itens do carrinho?"
                variant="subtle" icon="trash"
                class="text-red-600 hover:text-red-700 cursor-pointer">
                Limpar Carrinho
            </flux:button>
        @endif
    </div>

    @if(count($cart) > 0)
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            {{-- Cart Lines --}}
            <div class="lg:col-span-8 space-y-4">
                @foreach($cart as $key => $item)
                    <flux:card wire:key="{{ $key }}"
                        class="p-4 bg-white dark:bg-zinc-900 border border-zinc-200/60 dark:border-zinc-800/80">

                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">

                            {{-- Image & Name --}}
                            <div class="flex items-center gap-4 shrink-0">
                                <div class="w-16 h-16 rounded-lg bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 flex items-center justify-center p-1 overflow-hidden">
                                    @if($item['image_type'] === 'own')
                                        <img src="{{ url('/img-profiles/' . $item['image_url']) }}"
                                            alt="{{ $item['name'] }}"
                                            class="w-full h-full object-contain">
                                    @else
                                        <img src="{{ url('/images/catalog/' . $item['image_url']) }}"
                                            alt="{{ $item['name'] }}"
                                            class="w-full h-full object-contain">
                                    @endif
                                </div>
                                <div>
                                    <flux:heading size="md" class="font-bold text-zinc-900 dark:text-white">
                                        {{ $item['name'] }}
                                    </flux:heading>
                                    <flux:text size="xs" class="text-zinc-500 font-mono">
                                        {{ $item['image_type'] === 'own' ? '✨ Imagem Própria' : '📁 Catálogo' }}
                                    </flux:text>
                                </div>
                            </div>

                            {{-- Inline Controls --}}
                            <div class="flex flex-wrap items-center gap-3 sm:gap-4 flex-1 justify-end w-full sm:w-auto">

                                {{-- Colour selector --}}
                                <div class="w-32">
                                    <select wire:model.live="items.{{ $key }}.color_code"
                                        wire:change="updateItem('{{ $key }}')"
                                        id="color-{{ $key }}"
                                        class="w-full text-xs rounded-lg border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 p-2 text-zinc-800 dark:text-zinc-200 focus:outline-none focus:ring-2 focus:ring-zinc-400">
                                        @foreach($colors as $color)
                                            <option value="{{ $color->code }}">{{ $color->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Size selector --}}
                                <div class="w-20">
                                    <select wire:model.live="items.{{ $key }}.size"
                                        wire:change="updateItem('{{ $key }}')"
                                        id="size-{{ $key }}"
                                        class="w-full text-xs rounded-lg border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 p-2 text-zinc-800 dark:text-zinc-200 focus:outline-none focus:ring-2 focus:ring-zinc-400">
                                        @foreach(['XS','S','M','L','XL'] as $sz)
                                            <option value="{{ $sz }}">{{ $sz }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Quantity input --}}
                                <div class="w-20">
                                    <input type="number"
                                        wire:model="items.{{ $key }}.qty"
                                        wire:change="updateItem('{{ $key }}')"
                                        id="qty-{{ $key }}"
                                        min="0" max="9999"
                                        class="w-full text-center text-xs rounded-lg border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 p-2 text-zinc-800 dark:text-zinc-200 font-bold focus:outline-none focus:ring-2 focus:ring-zinc-400" />
                                </div>

                                {{-- Subtotal & Unit Price --}}
                                <div class="text-right min-w-[90px]">
                                    <div class="text-sm font-black text-zinc-900 dark:text-white">
                                        {{ number_format($item['subtotal'], 2, ',', '.') }}€
                                    </div>
                                    <div class="text-[10px] text-zinc-400">
                                        {{ number_format($item['unit_price'], 2, ',', '.') }}€ / un
                                        @if($prices && $item['qty'] >= $prices->qty_discount)
                                            <span class="text-emerald-500 font-semibold ml-1">💚 Desc.</span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Remove button --}}
                                <flux:button wire:click="removeItem('{{ $key }}')"
                                    wire:confirm="Remover este item do carrinho?"
                                    variant="ghost" icon="trash"
                                    class="p-2 text-zinc-400 hover:text-red-500 transition cursor-pointer" />
                            </div>

                        </div>

                        {{-- Loading overlay --}}
                        <div wire:loading.class="opacity-50 pointer-events-none" wire:target="updateItem('{{ $key }}'),removeItem('{{ $key }}')">
                        </div>

                    </flux:card>
                @endforeach
            </div>

            {{-- Order Summary --}}
            <div class="lg:col-span-4">
                <flux:card class="p-6 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 space-y-4 sticky top-24">
                    <flux:heading size="lg" class="font-bold">Resumo do Pedido</flux:heading>

                    <div class="space-y-2 text-sm border-b border-zinc-200 dark:border-zinc-800 pb-4">
                        <div class="flex justify-between text-zinc-500">
                            <span>Artigos no carrinho:</span>
                            <span class="font-bold text-zinc-800 dark:text-zinc-200">{{ count($cart) }}</span>
                        </div>
                        <div class="flex justify-between text-zinc-500">
                            <span>Total de Unidades:</span>
                            <span class="font-bold text-zinc-800 dark:text-zinc-200">
                                {{ array_sum(array_column($cart, 'qty')) }}
                            </span>
                        </div>
                        @if($prices)
                            <div class="flex justify-between text-zinc-500">
                                <span>Desconto de Volume:</span>
                                <span class="text-xs text-zinc-400">≥ {{ $prices->qty_discount }} un / linha</span>
                            </div>
                        @endif
                    </div>

                    <div class="flex justify-between items-baseline py-2">
                        <span class="text-zinc-600 dark:text-zinc-400 font-medium">Total Global:</span>
                        <span class="text-3xl font-black text-zinc-900 dark:text-white">
                            {{ number_format($total, 2, ',', '.') }}€
                        </span>
                    </div>

                    <div class="pt-2 space-y-2">
                        <a href="{{ route('catalog.index') }}" wire:navigate
                            class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-200 text-sm font-semibold rounded-xl transition text-center cursor-pointer">
                            ← Continuar a Comprar
                        </a>
                        <a href="{{ route('cart.checkout') }}"
                            class="w-full inline-flex items-center justify-center px-4 py-3 bg-zinc-950 hover:bg-zinc-800 text-white dark:bg-white dark:hover:bg-zinc-100 dark:text-zinc-900 font-bold rounded-xl shadow-sm transition text-center text-sm cursor-pointer">
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
            <flux:text size="sm" class="text-zinc-400 mt-1 mb-6">
                Explore as nossas estampas e encontre o seu estilo.
            </flux:text>
            <flux:button :href="route('catalog.index')" wire:navigate variant="filled" class="cursor-pointer">
                Ver Catálogo de Imagens
            </flux:button>
        </flux:card>
    @endif

</div>
