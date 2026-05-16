<x-layouts::main-content>
    <div class="flex flex-col space-y-6">
        <div class="flex justify-between items-center">
            <flux:heading size="xl">Gestão de Categorias</flux:heading>
            <flux:button variant="filled" icon="plus" :href="route('categories.create')">
                Criar Categoria
            </flux:button>
        </div>

        <div class="bg-white dark:bg-zinc-900 p-6 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-700">
            <x-categories.table :categories="$categories" :showView="true" :showEdit="true" :showDelete="true" />
            
            <div class="mt-4">
                {{ $categories->links() }}
            </div>
        </div>
    </div>
</x-layouts.app>
