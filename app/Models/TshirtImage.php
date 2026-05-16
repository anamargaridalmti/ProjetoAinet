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
}
