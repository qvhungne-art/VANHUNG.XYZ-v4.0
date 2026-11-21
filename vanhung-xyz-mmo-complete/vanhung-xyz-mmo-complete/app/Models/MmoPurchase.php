<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MmoPurchase extends Model
{
    protected $fillable = [
        'order_code', 'user_id', 'mmo_product_id', 'quantity',
        'unit_price', 'discount_amount', 'total_amount', 'discount_code',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(MmoProduct::class, 'mmo_product_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(MmoProductItem::class, 'mmo_purchase_id');
    }
}
