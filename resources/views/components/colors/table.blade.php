@props(['colors', 'showView' => true, 'showEdit' => true, 'showDelete' => true])

<div {{ $attributes }}>
    <table class="table-auto border-collapse w-full">
        <thead>
            <tr class="border-b-2 border-b-gray-400 dark:border-b-gray-500 bg-gray-100 dark:bg-gray-800">
                <th class="px-2 py-2 text-left w-24">Código</th>
                <th class="px-2 py-2 text-left">Nome da Cor</th>
                @if($showView) <th></th> @endif
                @if($showEdit) <th></th> @endif
                @if($showDelete) <th></th> @endif
            </tr>
        </thead>
        <tbody>
        @foreach ($colors as $color)
            <tr class="border-b border-b-gray-400 dark:border-b-gray-500 hover:bg-gray-50 dark:hover:bg-zinc-700/50">
                <td class="px-2 py-2 font-mono text-xs flex items-center gap-2">
                    <div class="w-4 h-4 rounded-full border border-gray-300" style="background-color: {{ $color->code }};"></div>
                    {{ $color->code }}
                </td>
                
                <td class="px-2 py-2 text-left font-medium">{{ $color->name }}</td>
                
                @if($showView)
                    <td class="ps-2 px-0.5 w-10">
                        <a href="{{ route('colors.show', ['color' => $color]) }}">
                            <flux:icon.eye class="size-5 hover:text-green-600" />
                        </a>
                    </td>
                @endif

                @if($showEdit)
                    <td class="px-0.5 w-10">
                        <a href="{{ route('colors.edit', ['color' => $color]) }}">
                            <flux:icon.pencil-square class="size-5 hover:text-blue-600" />
                        </a>
                    </td>
                @endif

                @if($showDelete)
                    <td class="px-0.5 w-10">
                        <form method="POST" action="{{ route('colors.destroy', ['color' => $color]) }}" class="flex items-center">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Tem a certeza que pretende eliminar esta cor?')">
                                <flux:icon.trash class="size-5 hover:text-red-600" />
                            </button>
                        </form>
                    </td>
                @endif
            </tr>
        @endforeach
        </tbody>
    </table>
</div>