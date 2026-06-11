<x-layouts::app :title="__('As Minhas Encomendas')">
    <div class="w-full max-w-5xl mx-auto space-y-6">

        {{-- Header --}}
        <div class="border-b border-zinc-200 dark:border-zinc-800 pb-4">
            <flux:heading size="xl" class="font-bold tracking-tight text-zinc-900 dark:text-white">
                📦 As Minhas Encomendas
            </flux:heading>
            <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400 mt-0.5">
                Histórico completo das suas encomendas na FunShirt.
            </flux:text>
        </div>

        {{-- Success flash --}}
        @if(session('success'))
            <flux:callout variant="success" icon="check-circle">
                {{ session('success') }}
            </flux:callout>
        @endif

        @if($orders->isEmpty())
            <flux:card class="p-12 text-center border-2 border-dashed border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900">
                <div class="text-4xl mb-3">📦</div>
                <flux:heading size="lg" class="font-bold">Ainda não fez nenhuma encomenda</flux:heading>
                <flux:text size="sm" class="text-zinc-400 mt-1 mb-6">
                    Explore o nosso catálogo e encontre a sua t-shirt favorita.
                </flux:text>
                <flux:button :href="route('catalog.index')" wire:navigate variant="filled" class="cursor-pointer">
                    Ver Catálogo
                </flux:button>
            </flux:card>
        @else
            <div class="space-y-4">
                @foreach($orders as $order)
                    <flux:card class="p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">
                        {{-- Order header --}}
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                            <div>
                                <flux:heading size="md" class="font-bold">
                                    Encomenda #{{ $order->id }}
                                </flux:heading>
                                <flux:text size="xs" class="text-zinc-400">
                                    {{ $order->date->format('d/m/Y') }} &bull; {{ $order->payment_type }}
                                </flux:text>
                            </div>
                            <div class="flex items-center gap-3">
                                <flux:badge
                                    color="{{ match($order->status) {
                                        'pending'  => 'yellow',
                                        'closed'   => 'green',
                                        'canceled' => 'red',
                                        default    => 'zinc',
                                    } }}"
                                    size="sm">
                                    {{ match($order->status) {
                                        'pending'  => 'Pendente',
                                        'closed'   => 'Concluída',
                                        'canceled' => 'Cancelada',
                                        default    => $order->status,
                                    } }}
                                </flux:badge>
                                <span class="text-lg font-black text-zinc-900 dark:text-white">
                                    {{ number_format($order->total_price, 2, ',', '.') }}€
                                </span>
                            </div>
                        </div>

                        {{-- Items summary --}}
                        <div class="border-t border-zinc-100 dark:border-zinc-800 pt-3 space-y-1.5">
                            @foreach($order->items as $item)
                                <div class="flex justify-between text-sm">
                                    <span class="text-zinc-600 dark:text-zinc-400">
                                        {{ $item->qty }}× #{{ $item->tshirt_image_id }}
                                        <span class="text-zinc-400">({{ $item->color_code }} · {{ $item->size }})</span>
                                    </span>
                                    <span class="font-semibold text-zinc-900 dark:text-white">
                                        {{ number_format($item->sub_total, 2, ',', '.') }}€
                                    </span>
                                </div>
                            @endforeach
                        </div>

                        @if($order->notes)
                            <div class="mt-3 pt-3 border-t border-zinc-100 dark:border-zinc-800">
                                <flux:text size="xs" class="text-zinc-400">
                                    📝 {{ $order->notes }}
                                </flux:text>
                            </div>
                        @endif
                    </flux:card>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-4">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</x-layouts::app>
