<x-layouts::app :title="__('Fazer Upload de Imagem')">
    <div class="max-w-xl mx-auto space-y-6">
        <div>
            <flux:heading size="xl" class="font-bold tracking-tight text-zinc-900 dark:text-white">
                🖼️ Upload de Imagem Personalizada
            </flux:heading>
            <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400 mt-0.5">
                Envie o seu próprio design exclusivo para estampar nas suas t-shirts.
            </flux:text>
        </div>

        <flux:card class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800/80 p-6 rounded-xl">
            <form action="{{ route('customer.tshirt-images.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                
                <flux:field>
                    <flux:label>Nome do Design</flux:label>
                    <flux:input type="text" name="name" required placeholder="Ex: Logótipo da minha empresa" />
                </flux:field>

                <flux:field>
                    <flux:label>Descrição (Opcional)</flux:label>
                    <textarea name="description" rows="3" class="w-full text-sm rounded-lg border border-zinc-200 dark:border-zinc-700 bg-transparent p-2 text-zinc-800 dark:text-zinc-200 focus:outline-none focus:ring-2 focus:ring-zinc-400" placeholder="Breve nota sobre a estampa..."></textarea>
                </flux:field>

                <flux:field>
                    <flux:label>Ficheiro de Imagem (JPEG, PNG, WebP - Máx. 2MB)</flux:label>
                    <input type="file" name="image_file" required class="block w-full text-sm text-zinc-500 dark:text-zinc-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-zinc-100 file:text-zinc-700 dark:file:bg-zinc-850 dark:file:text-zinc-300 hover:file:opacity-80 transition cursor-pointer" />
                </flux:field>

                <div class="flex justify-end gap-2 pt-2">
                    <a href="{{ route('customer.tshirt-images.index') }}" wire:navigate class="inline-flex items-center justify-center px-4 py-2 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-200 text-sm font-medium rounded-lg transition cursor-pointer">
                        Cancelar
                    </a>
                    <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-zinc-900 hover:bg-zinc-800 dark:bg-zinc-100 dark:hover:bg-zinc-200 text-white dark:text-zinc-900 text-sm font-semibold rounded-lg shadow-sm transition cursor-pointer">
                        Enviar Imagem
                    </button>
                </div>
            </form>
        </flux:card>
    </div>
</x-layouts::app>