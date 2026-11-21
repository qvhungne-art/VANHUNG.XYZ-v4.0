<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MmoCategory extends Model
{
    protected $fillable = ['name', 'slug', 'thumbnail', 'description', 'is_active'];

    public function products(): HasMany
    {
        return $this->hasMany(MmoProduct::class);
    }
}
