<div>
    @if($addedSuccessfully)
        <div class="flex flex-col items-center gap-2 p-3 bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 rounded-lg text-emerald-700 dark:text-emerald-400 text-xs font-semibold text-center">
            <svg class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            Adicionado ao carrinho!
            <a href="{{ route('cart.show') }}" wire:navigate
                class="underline hover:text-emerald-600 dark:hover:text-emerald-300 transition">
                Ver Carrinho →
            </a>
        </div>
    @else
        <div class="space-y-2">

            {{-- Color & Size row --}}
            <div class="flex gap-2">
                <div class="flex-1">
                    <label for="color-add-{{ $tshirtImageId }}"
                        class="block text-[10px] font-semibold text-zinc-400 uppercase tracking-wider mb-1">
                        Cor
                    </label>
                    <select wire:model.live="colorCode"
                        id="color-add-{{ $tshirtImageId }}"
                        class="w-full text-xs rounded-lg border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 p-2 text-zinc-800 dark:text-zinc-200 focus:outline-none focus:ring-2 focus:ring-zinc-400">
                        @foreach($colors as $color)
                            <option value="{{ $color->code }}">{{ $color->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="w-20">
                    <label for="size-add-{{ $tshirtImageId }}"
                        class="block text-[10px] font-semibold text-zinc-400 uppercase tracking-wider mb-1">
                        Tamanho
                    </label>
                    <select wire:model.live="size"
                        id="size-add-{{ $tshirtImageId }}"
                        class="w-full text-xs rounded-lg border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 p-2 text-zinc-800 dark:text-zinc-200 focus:outline-none focus:ring-2 focus:ring-zinc-400">
                        @foreach(['XS','S','M','L','XL'] as $sz)
                            <option value="{{ $sz }}">{{ $sz }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Quantity --}}
            <div>
                <label for="qty-add-{{ $tshirtImageId }}"
                    class="block text-[10px] font-semibold text-zinc-400 uppercase tracking-wider mb-1">
                    Quantidade
                </label>
                <input type="number"
                    wire:model.live="qty"
                    id="qty-add-{{ $tshirtImageId }}"
                    min="1" max="999"
                    class="w-full text-center text-xs rounded-lg border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 p-2 text-zinc-800 dark:text-zinc-200 font-bold focus:outline-none focus:ring-2 focus:ring-zinc-400" />
            </div>

            {{-- Price preview --}}
            @if($prices)
                <div class="text-[10px] text-zinc-400 text-right">
                    @php
                        $isCatalog = is_null($image?->customer_id);
                        $applyDiscount = $qty >= $prices->qty_discount;
                        if ($isCatalog) {
                            $previewPrice = $applyDiscount ? $prices->unit_price_catalog_discount : $prices->unit_price_catalog;
                        } else {
                            $previewPrice = $applyDiscount ? $prices->unit_price_own_discount : $prices->unit_price_own;
                        }
                    @endphp
                    {{ number_format($previewPrice, 2, ',', '.') }}€ / un
                    @if($applyDiscount)
                        <span class="text-emerald-500 font-semibold">💚 Desc. aplicado</span>
                    @endif
                </div>
            @endif

            {{-- Add to cart button --}}
            <flux:button wire:click="addToCart"
                wire:loading.attr="disabled"
                variant="filled"
                class="w-full cursor-pointer">
                <span wire:loading.remove wire:target="addToCart">
                    🛒 Adicionar ao Carrinho
                </span>
                <span wire:loading wire:target="addToCart">
                    A adicionar...
                </span>
            </flux:button>

            @error('colorCode') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            @error('size') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            @error('qty') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
    @endif
</div>
