<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TshirtImage extends Model
{
    use SoftDeletes;

    // Caso o Laravel não adivinhe o nome da tabela por causa do CamelCase:
    protected $table = 'tshirt_images';

    protected $fillable = ['customer_id', 'category_id', 'name', 'description', 'image_url', 'custom'];

    public function category()
    {
        // Define que o campo 'category_id' nesta tabela liga ao 'id' da tabela 'categories'
        return $this->belongsTo(Category::class, 'category_id');
    }
}
