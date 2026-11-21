<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MmoServiceOrder extends Model
{
    protected $fillable = [
        'order_code', 'user_id', 'mmo_service_package_id', 'target_link',
        'quantity', 'total_amount', 'status', 'user_note', 'admin_note',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(MmoServicePackage::class, 'mmo_service_package_id');
    }
}
