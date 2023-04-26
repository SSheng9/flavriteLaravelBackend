<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Validator;
// MODELS
use App\Models\{Wishlist, Product};

class WishlistController extends Controller
{
    //
    public function index()
    {
        return api(200, Wishlist::where('user_id', auth()->id())->where('category_id', request('category'))
            ->latest()
            ->with('product', 'product.report', 'product.wished', 'category', 'brand')
            ->latest()
            ->get(), null);
    }

    public function store()
    {
        $validator = Validator::make(request()->all(), [
            'product' => 'required',
        ]);

        $product = Product::whereId(request('product'))->first();

        if (!$product) {
            return api(404, null, null);
        }

        $fc = Wishlist::where('user_id', auth()->id())->where('product_id', $product['id'])->first();

        if (!$fc) {
            Wishlist::create([
                'user_id' => auth()->id(),
                'product_id' => $product['id'],
                'category_id' => $product['category_id'],
                'brand_id' => $product['brand_id']
            ]);
        }

        return api(200, null, null);
    }

    public function delete()
    {
        $validator = Validator::make(request()->all(), [
            'product' => 'required',
        ]);

        $product = Product::whereId(request('product'))->first();

        if (!$product) {
            return api(200, null, 'Bad Request I');
        }

        $fc = Wishlist::where('user_id', auth()->id())->where('product_id', $product['id'])->first();

        if ($fc) {
            $fc->delete();
            return api(200, null, null);
        } else {
            return api(200, null, 'Bad Request II');
        }
    }
}
