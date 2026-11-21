<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MmoProductItem extends Model
{
    protected $fillable = [
        'mmo_product_id', 'mmo_purchase_id', 'credential_payload',
        'status', 'buyer_id', 'sold_at',
    ];

    protected $casts = ['sold_at' => 'datetime'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(MmoProduct::class, 'mmo_product_id');
    }

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(MmoPurchase::class, 'mmo_purchase_id');
    }
}
