<x-layouts::app :title="__('Nova Cor')">
    <div class="max-w-2xl mx-auto space-y-6">
        
        <div>
            <flux:heading size="xl" class="font-bold">Adicionar Nova Cor</flux:heading>
            <flux:text>Registe um novo tom de tecido e anexe a respetiva maquete crua de t-shirt (G2).</flux:text>
        </div>

        <flux:card>
            <form method="POST" action="{{ route('colors.store') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <flux:field>
                        <flux:label for="code">Código HEX (6 caracteres)</flux:label>
                        <flux:input 
                            type="text" 
                            name="code" 
                            id="code" 
                            maxlength="6"
                            placeholder="Ex: FF0000 (sem o #)" 
                            value="{{ old('code') }}"
                            required 
                        />
                        <flux:error name="code" />
                    </flux:field>

                    <flux:field>
                        <flux:label for="name">Nome da Cor</flux:label>
                        <flux:input 
                            type="text" 
                            name="name" 
                            id="name" 
                            placeholder="Ex: Vermelho Vivo, Verde Alface..." 
                            value="{{ old('name') }}"
                            required 
                        />
                        <flux:error name="name" />
                    </flux:field>
                </div>

                <flux:field>
                    <flux:label for="tshirt_image">Imagem da T-Shirt Base Vazia (.png)</flux:label>
                    <flux:input 
                        type="file" 
                        name="tshirt_image" 
                        id="tshirt_image" 
                        accept="image/png" 
                        required
                        class="file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-zinc-900 file:text-white dark:file:bg-zinc-100 dark:file:text-zinc-900 cursor-pointer"
                    />
                    <flux:error name="tshirt_image" />
                </flux:field>

                <div class="flex gap-3 justify-end pt-4 border-t border-zinc-100 dark:border-zinc-900">
                    <flux:button :href="route('colors.index')" variant="subtle" wire:navigate>
                        Cancelar
                    </flux:button>
                    <flux:button type="submit" variant="filled" class="bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 font-semibold cursor-pointer">
                        Gravar Cor
                    </flux:button>
                </div>
            </form>
        </flux:card>
    </div>
</x-layouts::app>