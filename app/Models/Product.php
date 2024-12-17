<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'stock'
    ];

    public function scopeSearch($query, string $search)
    {
        return $query->whereRaw(
            "MATCH(name, description) AGAINST(? IN BOOLEAN MODE)",
            [$search]
        );
    }
}