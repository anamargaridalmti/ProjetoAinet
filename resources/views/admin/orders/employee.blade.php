<x-layouts::app :title="__('Encomendas Pendentes')">

<div class="w-full max-w-6xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="border-b border-zinc-200 dark:border-zinc-800 pb-4">
        <flux:heading size="xl" class="font-bold tracking-tight text-zinc-900 dark:text-white">
            📋 Encomendas Pendentes
        </flux:heading>
        <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400 mt-0.5">
            Lista de encomendas por processar. Feche cada encomenda quando estiver pronta para envio.
        </flux:text>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="p-4 bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 rounded-lg text-sm text-emerald-700 dark:text-emerald-400">
            ✅ {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-800 rounded-lg text-sm text-red-700 dark:text-red-400">
            ❌ {{ session('error') }}
        </div>
    @endif

    @if($orders->isEmpty())
        <flux:card class="p-12 text-center border-2 border-dashed border-zinc-200 dark:border-zinc-800">
            <div class="text-4xl mb-3">✅</div>
            <flux:heading size="lg" class="font-bold">Sem encomendas pendentes</flux:heading>
            <flux:text size="sm" class="text-zinc-400 mt-1">Todas as encomendas foram processadas.</flux:text>
        </flux:card>
    @else
        <div class="space-y-4">
            @foreach($orders as $order)
                <flux:card class="p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">
                    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">

                        {{-- Order Info --}}
                        <div class="space-y-1 flex-1">
                            <div class="flex items-center gap-3">
                                <flux:heading size="md" class="font-bold">
                                    Encomenda #{{ $order->id }}
                                </flux:heading>
                                <flux:badge color="yellow" size="sm">Pendente</flux:badge>
                            </div>
                            <flux:text size="sm" class="text-zinc-500">
                                📅 {{ $order->date->format('d/m/Y') }}
                                &bull; 👤 {{ $order->customer->user->name ?? '—' }}
                                &bull; 📧 {{ $order->customer->user->email ?? '—' }}
                            </flux:text>
                            <flux:text size="sm" class="text-zinc-500">
                                🏠 {{ $order->address }}
                                &bull; 💳 {{ $order->payment_type }}
                            </flux:text>
                            <div class="pt-2 border-t border-zinc-100 dark:border-zinc-800 mt-2 space-y-0.5">
                                @foreach($order->items as $item)
                                    <flux:text size="xs" class="text-zinc-400">
                                        {{ $item->qty }}× img#{{ $item->tshirt_image_id }}
                                        ({{ $item->color_code }} · {{ $item->size }})
                                        — {{ number_format($item->sub_total, 2, ',', '.') }}€
                                    </flux:text>
                                @endforeach
                            </div>
                        </div>

                        {{-- Total + Action --}}
                        <div class="flex flex-col items-end gap-3 shrink-0">
                            <span class="text-2xl font-black text-zinc-900 dark:text-white">
                                {{ number_format($order->total_price, 2, ',', '.') }}€
                            </span>

                            <form action="{{ route('admin.orders.close', $order) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <flux:button type="submit" variant="filled" size="sm" icon="check"
                                    class="bg-emerald-600 hover:bg-emerald-700 text-white cursor-pointer"
                                    onclick="return confirm('Fechar encomenda #{{ $order->id }} e enviar recibo ao cliente?')">
                                    Fechar Encomenda
                                </flux:button>
                            </form>
                        </div>

                    </div>
                </flux:card>
            @endforeach
        </div>

        <div class="mt-4">{{ $orders->links() }}</div>
    @endif

</div>

</x-layouts::app>

