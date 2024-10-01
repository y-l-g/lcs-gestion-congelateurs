<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stock extends Model
{
    use HasFactory;

    use SoftDeletes;

    public function produit(): BelongsTo
    {
        return $this->belongsTo(Produit::class);
    }
    protected $fillable = [
        'produit_id',
        'congelateur',
        'poids',
        'etage',
        'date_entree',
        'date_sortie'
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime:d/m/Y',
        ];
    }
}
