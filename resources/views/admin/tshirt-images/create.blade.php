<x-layouts::app :title="__('Nova Estampa')">
    <div class="max-w-2xl mx-auto space-y-6">
        
        <div>
            <flux:heading size="xl" class="font-bold">Adicionar Nova Estampa</flux:heading>
            <flux:text>Insira uma nova arte no catálogo oficial da loja com upload obrigatório (G2).</flux:text>
        </div>

        <flux:card>
            <form method="POST" action="{{ route('admin.tshirt-images.store') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <flux:field>
                    <flux:label for="name">Nome do Modelo / Estampa</flux:label>
                    <flux:input type="text" name="name" id="name" placeholder="Ex: Caveira Mexicana, Vintage Surf..." value="{{ old('name') }}" required />
                    <flux:error name="name" />
                </flux:field>

                <flux:field>
                <flux:label for="category_id">Categoria Associada</flux:label>
                
                <flux:select name="category_id" id="category_id" placeholder="Nenhuma (Manter Geral / Sem Vínculo)">
                    <option value="" class="text-zinc-400">Nenhuma (Manter Geral / Sem Vínculo)</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }} class="bg-zinc-900 text-white">
                            {{ $category->name }}
                        </option>
                    @endforeach
                </flux:select>
                <flux:error name="category_id" />
            </flux:field>

                <flux:field>
                    <flux:label for="image_file">Ficheiro de Imagem da Arte (.png, .jpg)</flux:label>
                    <flux:input 
                        type="file" 
                        name="image_file" 
                        id="image_file" 
                        accept="image/*" 
                        required
                        class="file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-zinc-900 file:text-white dark:file:bg-zinc-100 dark:file:text-zinc-900 cursor-pointer"
                    />
                    <flux:error name="image_file" />
                </flux:field>

                <div class="flex gap-3 justify-end pt-4 border-t border-zinc-100 dark:border-zinc-900">
                    <flux:button :href="route('admin.tshirt-images.index')" variant="subtle" wire:navigate>
                        Cancelar
                    </flux:button>
                    <flux:button type="submit" variant="filled" class="bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 font-semibold cursor-pointer">
                        Gravar Estampa
                    </flux:button>
                </div>
            </form>
        </flux:card>
    </div>
</x-layouts::app>