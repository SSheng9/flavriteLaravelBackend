<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Wishlist extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];
    protected $table = "wishlist";

    public function brand()
    {
        return $this->belongsTo(Brand::Class);
    }

    public function category()
    {
        return $this->belongsTo(Category::Class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
