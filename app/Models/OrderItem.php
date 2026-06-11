<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'tshirt_image_id',
        'color_code',
        'size',
        'qty',
        'unit_price',
        'sub_total',
        'custom',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'sub_total'  => 'decimal:2',
        ];
    }

    /** @return BelongsTo<Order, $this> */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /** @return BelongsTo<TshirtImage, $this> */
    public function tshirtImage(): BelongsTo
    {
        return $this->belongsTo(TshirtImage::class);
    }
}
