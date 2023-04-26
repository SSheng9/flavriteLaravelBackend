<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Validator;
use Illuminate\Http\Request;

// MODELS
use App\Models\{Fave, Product};

class FavesController extends Controller
{
    //
    public function index()
    {
        return api(200, Fave::where('user_id', auth()->id())->where('category_id', request('category'))
            ->latest()
            ->with('product', 'category', 'brand')
            ->get(), null);
    }

    public function store()
    {
        $validator = Validator::make(request()->all(), [
            'product' => 'required',
        ]);

        $product = Product::whereId(request('product'))->first();

        if(!$product)
        {
            return api(404, null, null);
        }

        $fc = Fave::where('user_id', auth()->id())->where('product_id', $product['id'])->first();

        if(!$fc)
        {
            Fave::create([
                'user_id' => auth()->id(),
                'product_id' => request('product'),
                'category_id' => $product['category_id'],
                'brand_id' => $product['brand_id']
            ]);
        }

        return api(200, null, null);
    }

    public function delete()
    {

    }
}
