@php
    $disabled = $mode === 'show';
@endphp

<div class="flex flex-col space-y-4">
    <div>
        <flux:input 
            label="Código da Cor" 
            name="code" 
            value="{{ old('code', $color->code) }}" 
            :disabled="$disabled || $mode === 'edit'" 
            placeholder="Ex: black, white, red..."
        />
        @error('code')
            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
        @enderror
    </div>

    <div>
        <flux:input 
            label="Nome da Cor" 
            name="name" 
            value="{{ old('name', $color->name) }}" 
            :disabled="$disabled" 
            placeholder="Ex: Preto, Branco, Vermelho..."
        />
        @error('name')
            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
        @enderror
    </div>
</div>