<x-layouts::app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            
            <div class="p-6 bg-white dark:bg-zinc-900 rounded-xl border border-neutral-200 dark:border-neutral-700 shadow-sm flex items-center gap-4">
                <div class="p-3 bg-blue-100 dark:bg-blue-950/50 rounded-lg text-blue-600 dark:text-blue-400">
                    <flux:icon.users class="size-6" />
                </div>
                <div>
                    <flux:text size="sm" class="uppercase tracking-wider font-semibold text-zinc-500">Clientes Totais</flux:text>
                    <flux:heading size="xl" class="font-bold">1,248</flux:heading>
                </div>
            </div>
            
            <div class="p-6 bg-white dark:bg-zinc-900 rounded-xl border border-neutral-200 dark:border-neutral-700 shadow-sm flex items-center gap-4">
                <div class="p-3 bg-amber-100 dark:bg-amber-950/50 rounded-lg text-amber-600 dark:text-amber-400">
                    <flux:icon.shopping-cart class="size-6" />
                </div>
                <div>
                    <flux:text size="sm" class="uppercase tracking-wider font-semibold text-zinc-500">Encomendas Novas</flux:text>
                    <flux:heading size="xl" class="font-bold">14</flux:heading>
                </div>
            </div>
            
            <div class="p-6 bg-white dark:bg-zinc-900 rounded-xl border border-neutral-200 dark:border-neutral-700 shadow-sm flex items-center gap-4">
                <div class="p-3 bg-emerald-100 dark:bg-emerald-950/50 rounded-lg text-emerald-600 dark:text-emerald-400">
                    <flux:icon.banknotes class="size-6" />
                </div>
                <div>
                    <flux:text size="sm" class="uppercase tracking-wider font-semibold text-zinc-500">Vendas do Mês</flux:text>
                    <flux:heading size="xl" class="font-bold">€3,450.00</flux:heading>
                </div>
            </div>

        </div>

        <div class="p-6 bg-white dark:bg-zinc-900 flex-1 rounded-xl border border-neutral-200 dark:border-neutral-700 shadow-sm">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <flux:heading size="lg" class="font-semibold">Atividade Recente</flux:heading>
                    <flux:text size="sm">Monitorização de vendas e novos registos de utilizadores.</flux:text>
                </div>
                <flux:button variant="subtle" size="sm" icon="arrow-path">Atualizar</flux:button>
            </div>
            
            <div class="relative h-64 border border-dashed border-zinc-300 dark:border-zinc-700 rounded-lg flex items-center justify-center bg-zinc-50/50 dark:bg-zinc-850/50">
                <flux:text class="text-zinc-400 dark:text-zinc-500">Gráfico de desempenho comercial estará disponível em breve.</flux:text>
            </div>
        </div>

    </div>
</x-layouts::app>