<x-layouts::app :title="__('Gestão de Cores')">
    <div class="space-y-6">
        
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <flux:heading size="xl" class="font-bold tracking-tight text-zinc-900 dark:text-white">
                    🎨 Cores de T-Shirts
                </flux:heading>
                <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400 mt-0.5">
                    Gira a palete de cores disponíveis para venda e as suas respetivas maquetes base.
                </flux:text>
            </div>
            
            <a href="{{ route('colors.create') }}" wire:navigate class="inline-flex items-center justify-center px-4 py-2 bg-zinc-950 hover:bg-zinc-800 text-white dark:bg-white dark:hover:bg-zinc-100 dark:text-zinc-900 text-sm font-semibold rounded-lg shadow-xs transition cursor-pointer">
                + Nova Cor
            </a>
        </div>

        @if(session('alert-msg'))
            <flux:card class="p-3 border text-sm {{ session('alert-type') === 'success' ? 'bg-emerald-50 dark:bg-emerald-950/30 border-emerald-200 text-emerald-700 dark:text-emerald-400' : 'bg-red-50 dark:bg-red-950/30 border-red-200 text-red-700 dark:text-red-400' }}">
                {!! session('alert-msg') !!}
            </flux:card>
        @endif

        <flux:card class="overflow-x-auto p-0 border border-zinc-200/60 dark:border-zinc-800/80">
            <table class="w-full text-left text-sm text-zinc-600 dark:text-zinc-400">
                <thead class="bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-800 text-xs uppercase text-zinc-500 font-semibold">
                    <tr>
                        <th class="px-6 py-4 w-24">Amostra</th>
                        <th class="px-6 py-4 w-32">Código HEX</th>
                        <th class="px-6 py-4">Nome da Cor</th>
                        <th class="px-6 py-4 w-32">T-Shirt Base</th>
                        <th class="px-6 py-4 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                    @forelse($colors as $color)
                        <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-900/30 transition-colors">
                            
                            <td class="px-6 py-3 whitespace-nowrap">
                                <div class="w-7 h-7 rounded-full shadow-inner border border-zinc-300 dark:border-zinc-600" style="background-color: #{{ $color->code }};"></div>
                            </td>

                            <td class="px-6 py-4 font-mono text-xs font-bold text-zinc-900 dark:text-white whitespace-nowrap">
                                #{{ $color->code }}
                            </td>

                            <td class="px-6 py-4 font-semibold text-zinc-900 dark:text-white whitespace-nowrap">
                                {{ $color->name }}
                            </td>

                            <td class="px-6 py-2 whitespace-nowrap">
                                <div class="w-10 h-10 rounded bg-white border border-zinc-200 overflow-hidden p-0.5 flex items-center justify-center shadow-2xs">
                                    <img 
                                        src="{{ url('/img-tshirt-base/' . $color->code) }}" 
                                        alt="Base" 
                                        class="w-full h-full object-contain"
                                        onerror="this.src='https://placehold.co/80x80/ffffff/a1a1aa?text=Crua';"
                                    />
                                </div>
                            </td>

                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-2 justify-end">
                                    
                                    <a href="{{ route('colors.edit', $color) }}" wire:navigate class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-semibold rounded-md border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-700 dark:text-zinc-200 hover:bg-zinc-50 dark:hover:bg-zinc-700 transition cursor-pointer">
                                        Editar
                                    </a>

                                    <form method="POST" action="{{ route('colors.destroy', $color) }}" class="inline" onsubmit="return confirm('Tem a certeza que deseja eliminar a cor {{ $color->name }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-semibold rounded-md text-red-600 hover:bg-red-500/10 dark:hover:bg-red-500/20 transition cursor-pointer">
                                            Eliminar
                                        </button>
                                    </form>

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-zinc-400 dark:text-zinc-500">Nenhuma cor registada ou ativa no sistema.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </flux:card>

        <div class="mt-4 border-t border-zinc-100 dark:border-zinc-900 pt-4">
            {{ $colors->links() }}
        </div>
    </div>
</x-layouts::app>