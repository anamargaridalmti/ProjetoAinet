<x-layouts::app :title="__('Estatísticas do Negócio')">
    <div class="w-full space-y-6">
        
        <div>
            <flux:heading size="xl" class="font-bold tracking-tight text-zinc-900 dark:text-white">
                📊 Painel Estatístico do Negócio
            </flux:heading>
            <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400 mt-0.5">
                Extração eficiente e agregada de dados comerciais da FunShirt (G8).
            </flux:text>
        </div>

        <div class="grid auto-rows-min gap-4 md:grid-cols-2">
            
            <div class="p-6 bg-white dark:bg-zinc-900 rounded-xl border border-neutral-200 dark:border-neutral-700 shadow-sm flex items-center gap-4">
                <div class="p-3 bg-emerald-100 dark:bg-emerald-950/50 rounded-lg text-emerald-600 dark:text-emerald-400">
                    <flux:icon.banknotes class="size-6" />
                </div>
                <div>
                    <flux:text size="sm" class="uppercase tracking-wider font-semibold text-zinc-500">Volume de Negócio Total</flux:text>
                    <flux:heading size="xl" class="font-bold">€ {{ number_format($totalFaturado, 2, ',', '.') }}</flux:heading>
                </div>
            </div>
            
            <div class="p-6 bg-white dark:bg-zinc-900 rounded-xl border border-neutral-200 dark:border-neutral-700 shadow-sm flex items-center gap-4">
                <div class="p-3 bg-blue-100 dark:bg-blue-950/50 rounded-lg text-blue-600 dark:text-blue-400">
                    <flux:icon.shopping-cart class="size-6" />
                </div>
                <div>
                    <flux:text size="sm" class="uppercase tracking-wider font-semibold text-zinc-500">T-shirts Vendidas</flux:text>
                    <flux:heading size="xl" class="font-bold">{{ $totalTshirtsVendidas ?? 0 }} unidades</flux:heading>
                </div>
            </div>

        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <flux:card class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800/80 p-5 rounded-xl">
                <flux:heading size="lg" class="font-semibold mb-4 text-zinc-900 dark:text-white">📦 Vendas por Categoria</flux:heading>
                <div class="overflow-x-auto w-full">
                    <table class="w-full text-left text-sm text-zinc-600 dark:text-zinc-400">
                        <thead class="bg-zinc-50 dark:bg-zinc-950 text-xs uppercase text-zinc-500 font-semibold border-b border-zinc-200 dark:border-zinc-800">
                            <tr>
                                <th class="py-3 px-4">Categoria</th>
                                <th class="py-3 px-4 text-center">Unidades</th>
                                <th class="py-3 px-4 text-right">Faturação</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                            @foreach($vendasPorCategoria as $cat)
                                <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-950/40">
                                    <td class="py-3 px-4 font-semibold text-zinc-900 dark:text-white">{{ $cat->name }}</td>
                                    <td class="py-3 px-4 text-center">{{ $cat->total_qty }}</td>
                                    <td class="py-3 px-4 text-right font-mono text-emerald-600 font-semibold">€ {{ number_format($cat->total_revenue, 2, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </flux:card>

            <flux:card class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800/80 p-5 rounded-xl">
                <flux:heading size="lg" class="font-semibold mb-4 text-zinc-900 dark:text-white">🏆 Top 5 Clientes (Maior Faturamento)</flux:heading>
                <div class="overflow-x-auto w-full">
                    <table class="w-full text-left text-sm text-zinc-600 dark:text-zinc-400">
                        <thead class="bg-zinc-50 dark:bg-zinc-950 text-xs uppercase text-zinc-500 font-semibold border-b border-zinc-200 dark:border-zinc-800">
                            <tr>
                                <th class="py-3 px-4">Cliente / Email</th>
                                <th class="py-3 px-4 text-right">Total Contribuído</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                            @foreach($topClientes as $cliente)
                                <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-950/40">
                                    <td class="py-3 px-4">
                                        <span class="font-semibold text-zinc-900 dark:text-white block">{{ $cliente->name }}</span>
                                        <span class="text-xs text-zinc-400 font-mono">{{ $cliente->email }}</span>
                                    </td>
                                    <td class="py-3 px-4 text-right font-mono text-zinc-900 dark:text-white font-semibold">€ {{ number_format($cliente->gasto_total, 2, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </flux:card>

        </div>
    </div>
</x-layouts::app>