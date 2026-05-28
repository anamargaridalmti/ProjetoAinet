@props(['categories', 'showView' => true, 'showEdit' => true, 'showDelete' => true])

<div {{ $attributes }}>
    <table class="table-auto border-collapse w-full">
        <thead>
            <tr class="border-b-2 border-b-gray-400 dark:border-b-gray-500 bg-gray-100 dark:bg-gray-800">
                <th class="px-2 py-2 text-left w-20">Imagem</th>
                <th class="px-2 py-2 text-left">Nome da Categoria</th>
                @if($showView) <th></th> @endif
                @if($showEdit) <th></th> @endif
                @if($showDelete) <th></th> @endif
            </tr>
        </thead>
        <tbody>
        @foreach ($categories as $cat)
            <tr class="border-b border-b-gray-400 dark:border-b-gray-500 hover:bg-gray-50 dark:hover:bg-zinc-700/50">
                <td class="px-2 py-2">
                    @if($cat->image_url && \Illuminate\Support\Facades\Storage::disk('public')->exists('categories/' . $cat->image_url))
                        <img src="{{ url('img-categories/' . $cat->image_url) }}" style="width: 45px; height: 45px; object-fit: cover; border-radius: 6px; border: 1px solid #444;" alt="{{ $cat->name }}">
                    @else
                        <div style="width: 45px; height: 45px; background: #333; display: flex; align-items: center; justify-content: center; border-radius: 6px; font-size: 18px;">📁</div>
                    @endif
                </td>
                
                <td class="px-2 py-2 font-medium">
                    {{ $cat->name }}
                </td>
                
                @if($showView)
                    <td class="ps-2 px-0.5 w-10">
                        <a href="{{ route('categories.show', ['category' => $cat]) }}">
                            <flux:icon.eye class="size-5 hover:text-green-600" />
                        </a>
                    </td>
                @endif
                @if($showEdit)
                    <td class="px-0.5 w-10">
                        <a href="{{ route('categories.edit', ['category' => $cat]) }}">
                            <flux:icon.pencil-square class="size-5 hover:text-blue-600" />
                        </a>
                    </td>
                @endif
                @if($showDelete)
                    <td class="px-0.5 w-10">
                        <form method="POST" action="{{ route('categories.destroy', ['category' => $cat]) }}" class="flex items-center">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Tem a certeza que pretende eliminar esta categoria?')">
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