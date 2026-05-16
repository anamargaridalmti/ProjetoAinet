<x-layouts::main-content :title="__('colors')"
                        heading="Detalhes da Cor"
                        subheading="Visualização dos dados da cor selecionada.">
    <div class="flex flex-col space-y-6 max-w-4xl">
        <div class="bg-white dark:bg-zinc-900 p-6 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-700">
            @include('colors.fields', ['mode' => 'show'])
        </div>

        <div class="flex justify-end">
            <flux:button variant="ghost" :href="route('colors.index')">Voltar à Lista</flux:button>
        </div>
    </div>
</x-layouts::main-content>