<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Product extends Model
{
    use Searchable, HasFactory;

    protected $guarded = [];

    public function searchableAs()
    {
        return 'products_index';
    }

    public function toSearchableArray()
    {
        $array = $this->toArray();
        $array['brand_name'] = $this->brand->name;
        unset($array['created_at']);
        unset($array['updated_at']);
        unset($array['total_likes']);
        unset($array['thumbnail']);
        unset($array['nickname']);
        unset($array['id']);
        return $array;
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function wished()
    {
        return $this->hasOne(Wishlist::class)->where('user_id', auth()->id());
    }

    public function wished_status()
    {
        return $this->hasOne(Wishlist::class)->where('user_id', auth()->id())->exists();
    }

    public function liked_status()
    {
        return $this->hasOne(Like::class)->where('user_id', auth()->id())->exists();
    }

    public function report()
    {
        return $this->belongsTo(User::class, 'reporter_id', 'id');
    }

    public function likers()
    {
        return $this->hasMany(Like::class)->where('user_id', '!=', auth()->id())->take(3);
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }

}
