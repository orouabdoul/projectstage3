<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Harvest extends Model
{
    use HasFactory;
    protected $fillable = [
        'weight_coton',
        'price_unit',
        'date',
        'observation',
        'field_id'
    ];

    public function field()
    {
        return $this->belongsTo(Field::class);
    }
}
