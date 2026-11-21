<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MmoService extends Model
{
    protected $fillable = ['name', 'slug', 'service_type', 'description', 'is_active'];

    public function packages(): HasMany
    {
        return $this->hasMany(MmoServicePackage::class);
    }
}
