<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = "product_categories";

    protected static function boot()
    {
        parent::boot();

//        static::addGlobalScope('visible', function (Builder $builder) {
//            $builder->where('visible', 1);
//        });
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function scopeWithNotVisible(Builder $query)
    {
        $query->whereIn('visible', [0,1]);
    }
    public function scopeVisible(Builder $query)
    {
        $query->where('visible', 1);
    }
}
