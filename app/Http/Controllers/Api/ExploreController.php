<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// MODELS
use App\Models\{Like, Product, Tag};

class ExploreController extends Controller
{
    //
    public function index()
    {
        // $peoples = Like::where('product_id', $pr['id'])->where('user_id', '!=', auth()->id())
        //     ->with('user')->limit(5)->get();
        return api(
            200,
            Like::where('user_id', auth()->id())
                ->orderBy('sort_order')
                ->get()->each(function ($feed) {
                    $feed->load('product.likers', 'product.likers.user', 'product', 'product.brand', 'product.review');
                }),
            null
        );
    }

    // WITH CATEGORY CHOOSING
    public function index2()
    {
        $category = request()->category;
        // $peoples = Like::where('product_id', $pr['id'])->where('user_id', '!=', auth()->id())
        //     ->with('user')->limit(5)->get();
        return api(
            200,
            Like::where('user_id', auth()->id())
                ->orderBy('sort_order')
                ->whereHas('product', function ($q) use ($category) {
                    $q->where('category_id', $category);
                })
                ->get()->each(function ($feed) {
                    $feed->load('product.likers', 'product.likers.user', 'product', 'product.brand', 'product.review');
                }),
            null
        );
    }

    public function onboarding()
    {
        $items = Product::where('category_id', request('category') ?? 1)
            ->with('brand', 'category')
            ->where('promoted', 1)
            ->inRandomOrder()
            ->limit(5)
            ->get();
        foreach ($items as $item) {
            if($item['tags'])
            {
                $tags = Tag::whereIn('id', json_decode($item['tags'], true))->get();
            }
            $item['tags_item'] = $tags ?? [];
            $item['wished'] = $item->wished_status();
            $item['liked'] = $item->liked_status();
        }
        return api(200, $items, null);
    }
}
