<x-layouts::app :title="__('Editar Cor')">
    <div class="max-w-2xl mx-auto space-y-6">
        
        <div>
            <flux:heading size="xl" class="font-bold">Editar Detalhes da Cor</flux:heading>
            <flux:text>Modifique o nome ou atualize a imagem de maquete associada a este tecido.</flux:text>
        </div>

        <flux:card>
            <form method="POST" action="{{ route('colors.update', $color) }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <flux:field>
                        <flux:label>Código HEX (Imutável)</flux:label>
                        <div class="flex items-center gap-3 px-3 py-2 bg-zinc-100 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-lg text-zinc-500 font-mono text-sm">
                            <div class="w-4 h-4 rounded-full border border-zinc-300" style="background-color: #{{ $color->code }};"></div>
                            #{{ $color->code }}
                        </div>
                    </flux:field>

                    <flux:field>
                        <flux:label for="name">Nome da Cor</flux:label>
                        <flux:input 
                            type="text" 
                            name="name" 
                            id="name" 
                            value="{{ old('name', $color->name) }}"
                            required 
                        />
                        <flux:error name="name" />
                    </flux:field>
                </div>

                <flux:field>
                    <flux:label>Imagem Base Ativa</flux:label>
                    <div class="mb-2 flex items-center gap-4 p-3 bg-zinc-50 dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-800 w-fit">
                        <img 
                            src="{{ url('/img-tshirt-base/' . $color->code) }}" 
                            alt="Modelo atual" 
                            class="w-16 h-16 object-contain rounded bg-white p-1"
                            onerror="this.src='https://placehold.co/80x80/ffffff/a1a1aa?text=Crua';"
                        >
                        <div>
                            <flux:text size="sm" class="font-medium text-zinc-700 dark:text-zinc-300">Ficheiro no Servidor</flux:text>
                            <flux:text size="xs" class="font-mono text-zinc-400">{{ $color->code }}.png</flux:text>
                        </div>
                    </div>

                    <flux:label for="tshirt_image" class="mt-4">Substituir Imagem da T-Shirt Base (Opcional)</flux:label>
                    <flux:input 
                        type="file" 
                        name="tshirt_image" 
                        id="tshirt_image" 
                        accept="image/png" 
                        class="file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-zinc-900 file:text-white dark:file:bg-zinc-100 dark:file:text-zinc-900 cursor-pointer"
                    />
                    <flux:error name="tshirt_image" />
                </flux:field>

                <div class="flex gap-3 justify-end pt-4 border-t border-zinc-100 dark:border-zinc-900">
                    <flux:button :href="route('colors.index')" variant="subtle" wire:navigate>
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