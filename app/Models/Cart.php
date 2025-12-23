<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
    ];

    // Relasi: Cart punya banyak CartItem
    public function items()
    {
        return $this->hasMany(CartItem::class);
    }
}
