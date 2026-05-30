<div class="space-y-6">
    <flux:field>
        <flux:label for="name">Nome da Categoria</flux:label>
        <flux:input 
            type="text" 
            name="name" 
            id="name" 
            value="{{ old('name', $category->name) }}" 
            placeholder="Ex: Desporto, Geek, Frases..."
            required 
        />
        <flux:error name="name" />
    </flux:field>

    <flux:field>
        <flux:label for="description">Descrição</flux:label>
        <flux:textarea 
            name="description" 
            id="description" 
            placeholder="Breve descrição sobre o tema desta coleção..." 
            rows="4"
        >{{ old('description', $category->description) }}</flux:textarea>
        <flux:error name="description" />
    </flux:field>

    <flux:field>
        <flux:label for="image_file">Imagem da Categoria (Opcional)</flux:label>
        
        @if($category->image_url)
            <div class="mb-4 flex items-center gap-4 p-3 bg-zinc-50 dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-800 w-fit">
                <img 
                    src="{{ url('/img-categories/' . $category->image_url) }}" 
                    alt="Imagem atual" 
                    class="w-16 h-16 object-contain rounded bg-white p-1"
                >
                <div>
                    <flux:text size="sm" class="font-medium text-zinc-700 dark:text-zinc-300">Imagem ativa</flux:text>
                    <flux:text size="xs" class="text-zinc-400">{{ $category->image_url }}</flux:text>
                </div>
            </div>
        @endif

        <flux:input 
            type="file" 
            name="image_file" 
            id="image_file" 
            accept="image/*" 
            class="file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-zinc-900 file:text-white dark:file:bg-zinc-100 dark:file:text-zinc-900 cursor-pointer"
        />
        <flux:error name="image_file" />
    </flux:field>
</div>