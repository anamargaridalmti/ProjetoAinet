<x-layouts::app :title="__('Encomenda em Processamento')">
    <div class="max-w-2xl mx-auto space-y-6 py-8">

        <div class="flex flex-col items-center text-center space-y-3">
            <div class="text-6xl">📦</div>
            <flux:heading size="xl" class="font-black text-zinc-900 dark:text-white">
                Encomenda #{{ $order->id }} recebida!
            </flux:heading>
            <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">
                Olá, <strong>{{ $order->customer->user->name ?? 'Cliente' }}</strong>!
            </flux:text>
        </div>

        <flux:card class="p-6 space-y-4">
            <flux:text>
                Recebemos a sua encomenda e estamos a tratá-la com todo o cuidado.
                Em breve a sua t-shirt personalizada estará a caminho!
            </flux:text>

            <div class="text-sm text-zinc-500 space-y-1">
                <p><strong>Nº Encomenda:</strong> #{{ $order->id }}</p>
                <p><strong>Data:</strong> {{ $order->date->format('d/m/Y') }}</p>
                <p><strong>Total:</strong> {{ number_format($order->total_price, 2, ',', '.') }}€</p>
                <p><strong>Pagamento:</strong> {{ $order->payment_type }}</p>
            </div>

            <flux:text size="sm" class="text-zinc-400 italic">
                Receberá uma confirmação no seu endereço de e-mail quando a encomenda for processada.
            </flux:text>
        </flux:card>

        <div class="text-center">
            <flux:button :href="route('orders.index')" wire:navigate variant="ghost">
                Ver histórico de encomendas
            </flux:button>
        </div>
    </div>
</x-layouts::app>
