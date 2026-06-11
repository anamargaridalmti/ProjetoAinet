@props(['colorCode', 'imageUrl', 'customerId' => null])

<div class="tshirt-preview-container" style="position: relative; width: 100px; height: 100px; display: inline-block; background-color: transparent; overflow: visible;">
    <img src="{{ route('img-tshirt-base', ['code' => $colorCode]) }}" 
         alt="Base" 
         style="width: 100%; height: 100%; object-fit: contain; position: absolute; top: 0; left: 0; z-index: 1; background-color: transparent;">
    
    <img src="{{ $customerId ? route('images.private', ['filename' => $imageUrl]) : route('images.catalog', ['filename' => $imageUrl]) }}" 
         alt="Estampa" 
         style="position: absolute; top: 26%; left: 26%; width: 48%; height: 48%; object-fit: contain; z-index: 2; background-color: transparent;">
</div>