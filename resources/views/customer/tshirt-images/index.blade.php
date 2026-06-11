<x-layouts::app :title="__('As Minhas Imagens')">
    <div class="w-full space-y-6">
        
        <div class="flex justify-between items-center border-b border-zinc-200 dark:border-zinc-800 pb-4">
            <div>
                <flux:heading size="xl" class="font-bold tracking-tight text-zinc-900 dark:text-white">
                    🖼️ A Minha Biblioteca Privada
                </flux:heading>
                <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400 mt-0.5">
                    Faça o upload e gira as suas imagens personalizadas para estampagem (G5).
                </flux:text>
            </div>
            
            <a href="{{ route('customer.tshirt-images.create') }}" wire:navigate class="inline-flex items-center justify-center px-4 py-2 bg-zinc-950 hover:bg-zinc-800 text-white dark:bg-white dark:hover:bg-zinc-100 dark:text-zinc-900 text-sm font-semibold rounded-lg shadow-xs transition cursor-pointer">
                + Upload de Nova Imagem
            </a>
        </div>

        @if(session('alert-msg'))
            <flux:card class="p-3 border text-sm bg-emerald-50 dark:bg-emerald-950/30 border-emerald-200 text-emerald-700 dark:text-emerald-400">
                {{ session('alert-msg') }}
            </flux:card>
        @endif

        @if($tshirtImages->isEmpty())
            <flux:card class="p-12 text-center border-2 border-dashed border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900">
                <flux:text size="sm" class="text-zinc-400 mt-1">Ainda não adicionou nenhuma imagem personalizada.</flux:text>
            </flux:card>
        @else
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($tshirtImages as $image)
                    <flux:card class="p-4 bg-white dark:bg-zinc-900 border border-zinc-200/60 dark:border-zinc-800/80 flex flex-col justify-between hover:shadow-md transition">
                        <div>
                            <div class="text-center mb-4 flex justify-center">
                                <x-tshirt-preview colorCode="white" :imageUrl="$image->image_url" :customerId="$image->customer_id" />
                            </div>
                            <flux:heading size="md" class="font-bold text-zinc-900 dark:text-white truncate">
                                {{ $image->name }}
                            </flux:heading>
                            <flux:text size="xs" class="text-zinc-500 mt-1 h-8 overflow-hidden">
                                {{ $image->description ?? 'Sem descrição fornecida.' }}
                            </flux:text>
                        </div>
                        
                        <div class="mt-4 border-t border-zinc-100 dark:border-zinc-800/60 pt-3 flex justify-between items-center">
                            <form action="{{ route('cart.add') }}" method="POST" class="inline-flex gap-1">
                                @csrf
                                <input type="hidden" name="tshirt_image_id" value="{{ $image->id }}">
                                <input type="hidden" name="qty" value="1">
                                <input type="hidden" name="size" value="M">
                                <input type="hidden" name="color_code" value="white">
                                <button type="submit" class="inline-flex items-center justify-center px-3 py-1.5 bg-zinc-900 hover:bg-zinc-800 text-white dark:bg-white dark:hover:bg-zinc-100 dark:text-zinc-900 text-xs font-semibold rounded-lg shadow-sm transition cursor-pointer">
                                    Comprar
                                </button>
                            </form>

                            <form action="{{ route('customer.tshirt-images.destroy', $image) }}" method="POST" onsubmit="return confirm('Pretende apagar este design privado?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs text-red-600 hover:underline cursor-pointer">
                                    Remover
                                </button>
                            </form>
                        </div>
                    </flux:card>
                @endforeach
            </div>
            
            <div class="mt-4">
                {{ $tshirtImages->links() }}
            </div>
        @endif
    </div>
</x-layouts::app>