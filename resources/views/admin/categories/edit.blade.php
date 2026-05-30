<x-layouts::app :title="__('Editar Categoria')">
    <div class="max-w-2xl mx-auto space-y-6">
        
        <div class="flex justify-between items-start">
            <div>
                <flux:heading size="xl" class="font-bold">Editar Categoria</flux:heading>
                <flux:text>Modifique os dados ou atualize a imagem de identificação desta coleção.</flux:text>
            </div>

            @if($category->image_url)
                <form method="POST" action="{{ route('categories.image.destroy', $category) }}" onsubmit="return confirm('Tem a certeza que deseja remover apenas a imagem desta categoria?');">
                    @csrf
                    @method('DELETE')
                    <flux:button type="submit" variant="subtle" size="sm" class="text-red-600 border-red-200 hover:bg-red-50 dark:hover:bg-red-950/20 cursor-pointer">
                        Remover Imagem
                    </flux:button>
                </form>
            @endif
        </div>

        <flux:card>
            <form method="POST" action="{{ route('categories.update', $category) }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                @include('admin.categories.fields')

                <div class="flex gap-3 justify-end pt-4 border-t border-zinc-100 dark:border-zinc-900">
                    <flux:button :href="route('categories.index')" variant="subtle" wire:navigate>
                        Cancelar
                    </flux:button>
                    <flux:button type="submit" variant="filled" class="bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 font-semibold cursor-pointer">
                        Guardar Alterações
                    </flux:button>
                </div>
            </form>
        </flux:card>
    </div>
</x-layouts::app>