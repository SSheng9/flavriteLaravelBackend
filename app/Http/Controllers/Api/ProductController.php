<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use Validator;
use Illuminate\Http\Request;

//MODELS
use App\Models\{Review, Product, Tag, Wishlist, Like};

class ProductController extends Controller
{

    public function show($id)
    {
        $pr = Product::whereId($id)->with('brand', 'category')->first();
        $product_category = $pr['category']['slug'];

        $rv = Review::where('user_id', auth()->id())->where('product_id', $id)->first();
        $liked = Like::where('user_id', auth()->id())->where('product_id', $id)->first();
        if ($pr['tags'] && count(json_decode($pr['tags'], true))) {
            $tags = Tag::whereIn('id', json_decode($pr['tags'], true))->get();
            $pr['tags'] = $tags;
        } else {
            $pr['tags'] = null;
        }
        $peoples = Like::where('product_id', $pr['id'])->where('user_id', '!=', auth()->id())
            ->with('user')->limit(5)->get();
        $pr['review'] = $rv;
        $pr['liked'] = $liked ? true : false;
        $pr['peoples'] = $peoples;
        $pr['wished'] = Wishlist::where('user_id', auth()->id())
            ->where('product_id', $pr['id'])
            ->first() ? true : false;
        $user = auth()->user();
        $pr['user_likes'] = $user[$product_category];
        return api(200, $pr, null);
    }

    // ADD PRODUCT
    public function store()
    {

        $validator = Validator::make(request()->all(), [
            'name' => 'required',
            'brand' => 'required',
            'category' => 'required',
            'thumb' => 'required'
        ]);

        if ($validator->fails()) {
            return api(400, null, $validator->errors());
        }

        // VALIDATE BRAND & CATEGORY
        $brand = request('brand');
        $brand_name = request('brand_name') ?? null;
        // if(request()->has(''))
        // {

        //     $brand = $new_brand = Brand::create([

        //     ]);
        // }

        $product = Product::create([
            'name' => request('name'),
            'brand_id' => $brand,
            'brand_name' => $brand_name,
            'category_id' => request('category'),
            'reporter_id' => auth()->id(),
            'thumbnail' => request('thumb'),
        ]);

        $product = Product::whereId($product['id'])->with('brand')->first();
        // SEND IT TO ALGOLIA 

        return api(200, $product, null);
    }

    // ADD RATE
    public function add_rate()
    {
    }

    // EDIT RATE
    public function edit_rate()
    {
    }

    // ADD TO WISHLIST
    public function add_to_wishlist()
    {
    }

    // REMOVE FROM WISHLIST
    public function remove_from_wishlist()
    {
    }

    // ADD TO FAVES
    public function add_to_faves()
    {
    }

    // REMOVE FROM FAVES
    public function remove_from_faves()
    {
    }

    // UPLOAD IMAGE
    public function upload(Request $request)
    {
        $path = $request->file('flava')->store('/', 'flavas');
        return api(200, $path, null);
    }
}
