<x-layouts::main-content :title="__('colors')"
                        heading="Edit Color"
                        subheading='Alter the information and click on "Save" button.'>
    <div class="flex flex-col space-y-6">
        <div class="max-full">
            <section>
                <form method="POST" action="{{ route('colors.update', ['color' => $color]) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="bg-white dark:bg-zinc-900 p-6 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-700">
                        @include('colors.fields', ['mode' => 'edit'])
                    </div>

                    <div class="mt-6 flex space-x-2 justify-end">
                        <flux:button variant="filled" type="submit">Guardar Alterações</flux:button>
                        <flux:button variant="ghost" :href="route('colors.index')">Cancelar</flux:button>
                    </div>
                </form>
            </section>
        </div>
    </div>
</x-layouts::main-content>