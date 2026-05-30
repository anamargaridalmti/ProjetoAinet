<x-layouts::app :title="__('Configuração de Preços')">
    <div class="max-w-4xl mx-auto space-y-6">
        
        <div>
            <flux:heading size="xl" class="font-bold">💰 Tabela de Preços e Descontos</flux:heading>
            <flux:text>Defina os valores base de venda e as regras de desconto por volume da FunShirt.</flux:text>
        </div>

        @if(session('alert-msg'))
            <flux:card class="p-3 border text-sm bg-emerald-50 dark:bg-emerald-950/30 border-emerald-200 text-emerald-700 dark:text-emerald-400">
                {!! session('alert-msg') !!}
            </flux:card>
        @endif

        <form method="POST" action="{{ route('admin.prices.update') }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <flux:card class="space-y-4">
                    <flux:heading size="lg" class="font-semibold text-zinc-900 dark:text-white border-b border-zinc-100 dark:border-zinc-800 pb-2">
                        Preços Base (Unitários)
                    </flux:heading>

                    <flux:field>
                        <flux:label for="unit_price_catalog">T-Shirt com Estampa do Catálogo (€)</flux:label>
                        <flux:input type="number" step="0.01" name="unit_price_catalog" id="unit_price_catalog" value="{{ old('unit_price_catalog', $price->unit_price_catalog) }}" required />
                        <flux:error name="unit_price_catalog" />
                    </flux:field>

                    <flux:field>
                        <flux:label for="unit_price_own">T-Shirt com Estampa Própria (€)</flux:label>
                        <flux:input type="number" step="0.01" name="unit_price_own" id="unit_price_own" value="{{ old('unit_price_own', $price->unit_price_own) }}" required />
                        <flux:error name="unit_price_own" />
                    </flux:field>
                </flux:card>

                <flux:card class="space-y-4">
                    <flux:heading size="lg" class="font-semibold text-zinc-900 dark:text-white border-b border-zinc-100 dark:border-zinc-800 pb-2">
                        Campanha de Desconto por Volume
                    </flux:heading>

                    <flux:field>
                        <flux:label for="qty_discount">Quantidade Mínima para Ativar Desconto</flux:label>
                        <flux:input type="number" name="qty_discount" id="qty_discount" value="{{ old('qty_discount', $price->qty_discount) }}" required />
                        <flux:error name="qty_discount" />
                    </flux:field>

                    <flux:field>
                        <flux:label for="unit_price_catalog_discount">Preço Catálogo com Desconto (€)</flux:label>
                        <flux:input type="number" step="0.01" name="unit_price_catalog_discount" id="unit_price_catalog_discount" value="{{ old('unit_price_catalog_discount', $price->unit_price_catalog_discount) }}" required />
                        <flux:error name="unit_price_catalog_discount" />
                    </flux:field>

                    <flux:field>
                        <flux:label for="unit_price_own_discount">Preço Própria com Desconto (€)</flux:label>
                        <flux:input type="number" step="0.01" name="unit_price_own_discount" id="unit_price_own_discount" value="{{ old('unit_price_own_discount', $price->unit_price_own_discount) }}" required />
                        <flux:error name="unit_price_own_discount" />
                    </flux:field>
                </flux:card>

            </div>

            <flux:card class="flex justify-end p-4">
                <flux:button type="submit" variant="filled" class="bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 font-semibold cursor-pointer">
                    Atualizar Tabela de Preços
                </flux:button>
            </flux:card>
        </form>

    </div>
</x-layouts::app>