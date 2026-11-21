<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MmoServicePackage extends Model
{
    protected $fillable = ['mmo_service_id', 'name', 'unit_price', 'unit_label', 'min_qty', 'description'];

    public function service(): BelongsTo
    {
        return $this->belongsTo(MmoService::class, 'mmo_service_id');
    }
}
