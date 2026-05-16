<x-layouts::main-content :title="__('categories')"
                        heading="Editar Categoria"
                        subheading="Altere os dados ou a imagem da categoria e clique em Guardar.">
    <div class="flex flex-col space-y-6 max-w-4xl">
        <form method="POST" action="{{ route('categories.update', ['category' => $category]) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="bg-white dark:bg-zinc-900 p-6 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-700">
                @include('categories.fields', ['mode' => 'edit'])
            </div>

            <div class="flex space-x-2 justify-end">
                <flux:button variant="filled" type="submit">Guardar Alterações</flux:button>
                <flux:button variant="ghost" :href="route('categories.index')">Cancelar</flux:button>
            </div>
        </form>

        <form id="form_to_delete_category_image" method="POST" action="{{ route('categories.image.destroy', ['category' => $category]) }}">
            @csrf
            @method('DELETE')
        </form>
    </div>
</x-layouts::main-content>