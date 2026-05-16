<x-layouts::main-content :title="__('colors')"
                        heading="Create a Color"
                        subheading='Click on "Save" button to store the information.'>
    <div class="flex flex-col space-y-6">
        <div class="max-full">
            <section>
                <form method="POST" action="{{ route('colors.store') }}">
                    @csrf
                    
                    <div class="bg-white dark:bg-zinc-900 p-6 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-700">
                        @include('colors.fields', ['mode' => 'create'])
                    </div>

                    <div class="mt-6 flex space-x-2 justify-end">
                        <flux:button variant="filled" type="submit">Guardar</flux:button>
                        <flux:button variant="ghost" :href="route('colors.index')">Cancelar</flux:button>
                    </div>
                </form>
            </section>
        </div>
    </div>
</x-layouts::main-content>