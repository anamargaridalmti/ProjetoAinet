<x-layouts::app :title="__('Editar Estampa')">
    <div class="max-w-2xl mx-auto space-y-6">
        
        <div>
            <flux:heading size="xl" class="font-bold">Modificar Estampa</flux:heading>
            <flux:text>Atualize os metadados da imagem ou substitua o ficheiro de design guardado.</flux:text>
        </div>

        <flux:card>
            <form method="POST" action="{{ route('admin.tshirt-images.update', $tshirtImage) }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <flux:field>
                    <flux:label for="name">Nome da Estampa</flux:label>
                    <flux:input type="text" name="name" id="name" value="{{ old('name', $tshirtImage->name) }}" required />
                    <flux:error name="name" />
                </flux:field>

                <flux:field>
                    <flux:label for="description">Descrição</flux:label>
                    <flux:textarea name="description" id="description" rows="3">{{ old('description', $tshirtImage->description) }}</flux:textarea>
                    <flux:error name="description" />
                </flux:field>

                <flux:field>
                    <flux:label for="category_id">Categoria</flux:label>
                    <flux:select name="category_id" id="category_id" placeholder="Nenhuma (Geral)">
                        @foreach($categories as $category)
                            <flux:select.option value="{{ $category->id }}" {{ old('category_id', $tshirtImage->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="category_id" />
                </flux:field>

                <flux:field>
                    <flux:label>Ficheiro de Design Atual</flux:label>
                    <div class="mb-4 flex items-center gap-4 p-3 bg-zinc-50 dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-800 w-fit">
                        <img 
                            src="{{ url('/images/catalog/' . $tshirtImage->image_url) }}" 
                            alt="Design atual" 
                            class="w-16 h-16 object-contain rounded bg-zinc-100 p-1"
                            onerror="this.src='https://placehold.co/80x80?text=Error';"
                        >
                        <div>
                            <flux:text size="sm" class="font-medium">Imagem no Servidor</flux:text>
                            <flux:text size="xs" class="font-mono text-zinc-400 max-w-[200px] truncate">{{ $tshirtImage->image_url }}</flux:text>
                        </div>
                    </div>

                    <flux:label for="image_file">Substituir Arte do Catálogo (Opcional)</flux:label>
                    <flux:input 
                        type="file" 
                        name="image_file" 
                        id="image_file" 
                        accept="image/*" 
                        class="file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-zinc-900 file:text-white dark:file:bg-zinc-100 dark:file:text-zinc-900 cursor-pointer"
                    />
                    <flux:error name="image_file" />
                </flux:field>

                <div class="flex gap-3 justify-end pt-4 border-t border-zinc-100 dark:border-zinc-900">
                    <flux:button :href="route('admin.tshirt-images.index')" variant="subtle" wire:navigate>
                        Cancelar
                    </flux:button>
                    <flux:button type="submit" variant="filled" class="bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 font-semibold cursor-pointer">
                        Atualizar Dados
                    </flux:button>
                </div>
            </form>
        </flux:card>
    </div>
</x-layouts::app>