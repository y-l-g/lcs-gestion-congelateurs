<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Produit extends Model
{
    use HasFactory;

    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class);
    }

    protected $fillable = [
        'nom'
    ];
}
