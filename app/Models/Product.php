<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $with = ['options', 'variants'];
    protected $appends = ['default_variant'];

    public function options()
    {
        return $this->hasMany(Option::class);
    }

    public function variants()
    {
        return $this->hasMany(Variant::class);
    }

    public function getDefaultVariantAttribute()
    {
        return $this->variants()->orderBy('price')->first();
    }


}
