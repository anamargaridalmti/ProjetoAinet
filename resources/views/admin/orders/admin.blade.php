<x-layouts::app :title="__('Gestão de Encomendas')">

<div class="w-full max-w-7xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="border-b border-zinc-200 dark:border-zinc-800 pb-4">
        <flux:heading size="xl" class="font-bold tracking-tight text-zinc-900 dark:text-white">
            🗂️ Gestão de Encomendas
        </flux:heading>
        <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400 mt-0.5">
            Vista de Administrador — todas as encomendas, filtros completos e ações de gestão.
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

    {{-- Filters --}}
    <flux:card class="p-4 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800">
        <form method="GET" action="{{ route('admin.orders.index') }}" class="flex flex-wrap gap-3 items-end">

            {{-- Status --}}
            <div>
                <label class="block text-xs font-medium text-zinc-600 dark:text-zinc-400 mb-1">Estado</label>
                <select name="status" class="w-40 text-sm rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-2 text-zinc-800 dark:text-zinc-200 focus:outline-none focus:ring-2 focus:ring-zinc-400">
                    <option value="">Todos</option>
                    <option value="pending"  @selected(request('status') === 'pending')>Pendente</option>
                    <option value="closed"   @selected(request('status') === 'closed')>Fechada</option>
                    <option value="canceled" @selected(request('status') === 'canceled')>Cancelada</option>
                </select>
            </div>

            {{-- Customer --}}
            <div>
                <label class="block text-xs font-medium text-zinc-600 dark:text-zinc-400 mb-1">Cliente</label>
                <select name="customer_id" class="w-56 text-sm rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-2 text-zinc-800 dark:text-zinc-200 focus:outline-none focus:ring-2 focus:ring-zinc-400">
                    <option value="">Todos os clientes</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" @selected(request('customer_id') == $customer->id)>
                            {{ $customer->user?->name ?? '#'.$customer->id }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Date From --}}
            <div>
                <label class="block text-xs font-medium text-zinc-600 dark:text-zinc-400 mb-1">Data de</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                    class="w-40 text-sm rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-2 text-zinc-800 dark:text-zinc-200 focus:outline-none focus:ring-2 focus:ring-zinc-400">
            </div>

            {{-- Date To --}}
            <div>
                <label class="block text-xs font-medium text-zinc-600 dark:text-zinc-400 mb-1">Data até</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                    class="w-40 text-sm rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-2 text-zinc-800 dark:text-zinc-200 focus:outline-none focus:ring-2 focus:ring-zinc-400">
            </div>

            <div class="flex gap-2">
                <flux:button type="submit" variant="filled" size="sm" class="cursor-pointer">Filtrar</flux:button>
                <a href="{{ route('admin.orders.index') }}">
                    <flux:button as="span" variant="ghost" size="sm" class="cursor-pointer">Limpar</flux:button>
                </a>
            </div>
        </form>
    </flux:card>

    <flux:text size="sm" class="text-zinc-500">
        {{ $orders->total() }} encomenda(s) encontrada(s)
    </flux:text>

    @if($orders->isEmpty())
        <flux:card class="p-12 text-center border-2 border-dashed border-zinc-200 dark:border-zinc-800">
            <div class="text-4xl mb-3">📭</div>
            <flux:heading size="lg" class="font-bold">Nenhuma encomenda encontrada</flux:heading>
            <flux:text size="sm" class="text-zinc-400 mt-1">Tente ajustar os filtros de pesquisa.</flux:text>
        </flux:card>
    @else
        <div class="space-y-4">
            @foreach($orders as $order)
                @php
                    $statusColor = match($order->status) {
                        'pending'  => 'yellow',
                        'closed'   => 'green',
                        'canceled' => 'red',
                        default    => 'zinc',
                    };
                    $statusLabel = match($order->status) {
                        'pending'  => 'Pendente',
                        'closed'   => 'Fechada',
                        'canceled' => 'Cancelada',
                        default    => $order->status,
                    };
                @endphp

                <flux:card class="p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">
                    <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-4">

                        {{-- Order Info --}}
                        <div class="space-y-1 flex-1">
                            <div class="flex flex-wrap items-center gap-3">
                                <flux:heading size="md" class="font-bold">Encomenda #{{ $order->id }}</flux:heading>
                                <flux:badge color="{{ $statusColor }}" size="sm">{{ $statusLabel }}</flux:badge>
                            </div>

                            <flux:text size="sm" class="text-zinc-500">
                                📅 {{ $order->date->format('d/m/Y') }}
                                &bull; 👤 {{ $order->customer->user->name ?? '—' }}
                                &bull; 📧 {{ $order->customer->user->email ?? '—' }}
                            </flux:text>

                            <flux:text size="sm" class="text-zinc-500">
                                🏠 {{ $order->address }}
                                &bull; 💳 {{ $order->payment_type }}
                                @if($order->nif) &bull; NIF: {{ $order->nif }} @endif
                            </flux:text>

                            @if($order->reason_for_cancellation)
                                <p class="text-xs text-red-500 mt-1">
                                    ❌ Motivo: {{ $order->reason_for_cancellation }}
                                </p>
                            @endif

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

                        {{-- Actions --}}
                        <div class="flex flex-col items-end gap-3 shrink-0">
                            <span class="text-2xl font-black text-zinc-900 dark:text-white">
                                {{ number_format($order->total_price, 2, ',', '.') }}€
                            </span>

                            <div class="flex flex-col gap-2 w-full sm:w-auto">

                                @if($order->status === 'pending')
                                    <form action="{{ route('admin.orders.close', $order) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <flux:button type="submit" variant="filled" size="sm" icon="check"
                                            class="w-full bg-emerald-600 hover:bg-emerald-700 text-white cursor-pointer"
                                            onclick="return confirm('Fechar encomenda #{{ $order->id }} e enviar recibo?')">
                                            Fechar Encomenda
                                        </flux:button>
                                    </form>
                                @endif

                                @if(! in_array($order->status, ['closed', 'canceled']))
                                    <details class="w-full" id="cancel-details-{{ $order->id }}">
                                        <summary class="list-none">
                                            <button type="button"
                                                class="w-full text-sm text-red-500 hover:text-red-700 font-medium py-1.5 px-3 rounded-lg border border-red-200 dark:border-red-800 hover:bg-red-50 dark:hover:bg-red-950/30 transition cursor-pointer text-left">
                                                ✕ Cancelar Encomenda
                                            </button>
                                        </summary>
                                        <form action="{{ route('admin.orders.cancel', $order) }}" method="POST"
                                            class="mt-2 space-y-2 p-3 bg-red-50 dark:bg-red-950/20 rounded-lg border border-red-200 dark:border-red-900/30">
                                            @csrf
                                            @method('PATCH')
                                            <label class="block text-xs text-red-700 dark:text-red-400 font-medium">
                                                Motivo de cancelamento (opcional)
                                            </label>
                                            <textarea name="reason" rows="2"
                                                placeholder="Deixe em branco se não houver motivo específico..."
                                                class="w-full text-xs rounded-lg border border-red-200 dark:border-red-800 bg-white dark:bg-zinc-900 p-2 text-zinc-800 dark:text-zinc-200 focus:outline-none focus:ring-2 focus:ring-red-400 resize-none"></textarea>
                                            <button type="submit"
                                                class="w-full text-sm font-semibold py-2 px-3 rounded-lg bg-red-600 hover:bg-red-700 text-white transition cursor-pointer">
                                                Confirmar Cancelamento
                                            </button>
                                        </form>
                                    </details>
                                @endif

                                @if($order->status === 'closed' && $order->receipt_url)
                                    <a href="{{ route('orders.receipt.download', $order) }}">
                                        <flux:button as="span" variant="ghost" size="sm"
                                            class="w-full text-zinc-500 cursor-pointer">
                                            📄 Ver Recibo PDF
                                        </flux:button>
                                    </a>
                                @endif

                            </div>
                        </div>

                    </div>
                </flux:card>
            @endforeach
        </div>

        <div class="mt-4">{{ $orders->links() }}</div>
    @endif

</div>

</x-layouts::app>
