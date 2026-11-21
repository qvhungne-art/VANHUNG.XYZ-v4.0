<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MmoProduct extends Model
{
    protected $fillable = [
        'mmo_category_id', 'sku', 'title', 'platform',
        'country', 'format_spec', 'warranty_policy',
        'price', 'original_price', 'status',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(MmoCategory::class, 'mmo_category_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(MmoProductItem::class);
    }

    public function availableItems(): HasMany
    {
        return $this->hasMany(MmoProductItem::class)->where('status', 'available');
    }
}
