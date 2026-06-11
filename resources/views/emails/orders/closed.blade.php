<x-layouts::app :title="__('Encomenda Concluída')">
    <div class="max-w-2xl mx-auto space-y-6 py-8">

        <div class="flex flex-col items-center text-center space-y-3">
            <div class="text-6xl">🎉</div>
            <flux:heading size="xl" class="font-black text-zinc-900 dark:text-white">
                Encomenda #{{ $order->id }} concluída!
            </flux:heading>
            <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">
                Olá, <strong>{{ $order->customer->user->name ?? 'Cliente' }}</strong>!<br>
                A sua t-shirt foi estampada e enviada. Obrigado por escolher a FunShirt!
            </flux:text>
        </div>

        <flux:card class="p-6 space-y-4">
            <flux:text>
                A sua encomenda foi processada com sucesso e está a caminho do endereço indicado.
                Em anexo encontra o recibo em PDF desta encomenda.
            </flux:text>

            <div class="text-sm text-zinc-500 space-y-1">
                <p><strong>Nº Encomenda:</strong> #{{ $order->id }}</p>
                <p><strong>Data:</strong> {{ $order->date->format('d/m/Y') }}</p>
                <p><strong>Total pago:</strong> {{ number_format($order->total_price, 2, ',', '.') }}€</p>
                <p><strong>Endereço de entrega:</strong> {{ $order->address }}</p>
            </div>

            <flux:callout variant="success" icon="check-circle">
                O recibo da sua encomenda está disponível em anexo neste e-mail.
            </flux:callout>
        </flux:card>

        <div class="text-center">
            <flux:button :href="route('orders.index')" wire:navigate variant="ghost">
                Ver histórico de encomendas
            </flux:button>
        </div>
    </div>
</x-layouts::app>
