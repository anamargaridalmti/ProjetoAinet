@php
    $mode = $mode ?? 'edit';
    $readonly = $mode == 'show';
@endphp

<div class="flex flex-col sm:flex-row sm:justify-between space-x-8">
    <div class="grow mt-6 space-y-4">
        <flux:input name="name" label="Nome da Categoria" value="{{ old('name', $category->name) }}" :disabled="$readonly" />
        <flux:error name="name" />
    </div>

    <div>
        <x-field.image
            name="image_file"
            label="Imagem de Capa"
            width="md"
            :readonly="$readonly"
            deleteTitle="Eliminar Imagem"
            :deleteAllow="($mode == 'edit') && ($category->image_url)"
            deleteForm="form_to_delete_category_image"
            :imageUrl="$category->imageUrlFull"
            class="sm:-mt-[1.5rem] w-full sm:w-64"/>
    </div>
</div>