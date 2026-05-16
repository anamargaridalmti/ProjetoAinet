<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;

    public $incrementing = false; // Como indicado pela professora
    public $timestamps = false;   // Não tem created_at/updated_at

    protected $fillable = ['id', 'nif', 'address', 'default_payment_type', 'default_payment_ref', 'custom'];
}
