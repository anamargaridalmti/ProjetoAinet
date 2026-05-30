<x-layouts::app :title="__('Nova Categoria')">
    <div class="max-w-2xl mx-auto space-y-6">
        
        <div>
            <flux:heading size="xl" class="font-bold">Criar Nova Categoria</flux:heading>
            <flux:text>Adicione um novo tema ou coleção para agrupar as estampas no catálogo.</flux:text>
        </div>

        <flux:card>
            <form method="POST" action="{{ route('categories.store') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf

                @include('admin.categories.fields')

                <div class="flex gap-3 justify-end pt-4 border-t border-zinc-100 dark:border-zinc-900">
                    <flux:button :href="route('categories.index')" variant="subtle" wire:navigate>
                        Cancelar
                    </flux:button>
                    <flux:button type="submit" variant="filled" class="bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 font-semibold cursor-pointer">
                        Gravar Categoria
                    </flux:button>
                </div>
            </form>
        </flux:card>
    </div>
</x-layouts::app>