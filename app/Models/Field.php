<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    use HasFactory;
    protected $fillable = [
           'name',
             'location',
             'surface',
             'observation',
             'user_id'
    ];



    public function harvests()
    {
        return $this->hasMany(Harvest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
