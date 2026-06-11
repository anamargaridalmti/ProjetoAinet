<?php

namespace App\Livewire\Cart;

use App\Models\Color;
use App\Models\Price;
use App\Models\TshirtImage;
use App\Services\CartService;
use Livewire\Component;

class AddToCart extends Component
{
    public int $tshirtImageId;

    public string $colorCode = '';

    public string $size = 'M';

    public int $qty = 1;

    public bool $addedSuccessfully = false;

    /** @var array<int, object> */
    public array $colors = [];

    public ?object $prices = null;

    public ?object $image = null;

    public function mount(int $tshirtImageId): void
    {
        $this->tshirtImageId = $tshirtImageId;
        $this->image         = TshirtImage::find($tshirtImageId);
        $this->colors        = Color::orderBy('name')->get()->all();
        $this->prices        = Price::first();

        // Pre-select first available color
        if (! empty($this->colors)) {
            $this->colorCode = $this->colors[0]->code;
        }
    }

    public function addToCart(CartService $cartService): void
    {
        $this->validate([
            'tshirtImageId' => 'required|exists:tshirt_images,id',
            'colorCode'     => 'required|exists:colors,code',
            'size'          => 'required|in:XS,S,M,L,XL',
            'qty'           => 'required|integer|min:1|max:999',
        ]);

        $cartService->addItem(
            tshirtImageId: $this->tshirtImageId,
            colorCode: $this->colorCode,
            size: $this->size,
            qty: $this->qty,
        );

        $this->addedSuccessfully = true;

        // Reset the flash state after a short delay
        $this->dispatch('cart-updated');
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.cart.add-to-cart');
    }
}
