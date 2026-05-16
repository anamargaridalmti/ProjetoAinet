<x-layouts::main-content :title="__('categories')"
                        heading="Criar Categoria"
                        subheading="Preencha o nome e escolha uma imagem para a nova categoria.">
    <div class="flex flex-col space-y-6 max-w-4xl">
        <form method="POST" action="{{ route('categories.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="bg-white dark:bg-zinc-900 p-6 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-700">
                @include('categories.fields', ['mode' => 'create'])
            </div>

            <div class="flex space-x-2 justify-end">
                <flux:button variant="filled" type="submit">Criar Categoria</flux:button>
                <flux:button variant="ghost" :href="route('categories.index')">Cancelar</flux:button>
            </div>
        </form>
    </div>
</x-layouts::main-content>