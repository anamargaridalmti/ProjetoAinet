<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\CategoryImageFileStorage;

class Category extends Model
{
    use SoftDeletes, CategoryImageFileStorage;
    public $timestamps = false;

    protected $fillable = ['name', 'image_url', 'custom'];

    public function getImageUrlFullAttribute()
    {
        if ($this->image_url) {
            // Aponta exatamente para a pasta do teu print: storage/categories/
            return asset('storage/categories/' . $this->image_url);
        }

        return asset('storage/categories/no_category.png');
    }
}
