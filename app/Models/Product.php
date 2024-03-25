<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
             'name',
             'type_product',
             'quantity',
             'price_unit',
             'description',
             'user_id'
    ];

    public function field()
    {
        return $this->belongsTo(User::class);
    }
}
