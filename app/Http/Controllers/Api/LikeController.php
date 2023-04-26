<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Validator;
use Illuminate\Http\Request;

use App\Models\{Review, Product, Tag, Like};

class LikeController extends Controller
{
    public function store()
    {
        $validator = Validator::make(request()->all(), [
            'product' => 'required',
        ]);

        $product = Product::whereId(request('product'))->with('category')->first();
        $product_category = $product['category']['slug'];
        if (!$product) {
            return api(404, null, null);
        }

        $fc = Like::where('user_id', auth()->id())->where('product_id', $product['id'])->first();

        if (!$fc) {
            $user = auth()->user();
            $user_coffess = $user[$product_category];
            if ($user_coffess) {
                $user_coffess = json_decode($user_coffess);
                array_push($user_coffess, $product['id']);
                $user->update([
                    $product_category => $user_coffess
                ]);
            } else {
                $user->update([
                    $product_category => [$product['id']]
                ]);
            }
            Like::create([
                'user_id' => auth()->id(),
                'product_id' => $product['id']
            ]);
            return api(200, null, null);
        }

        return api(200, null, null);
    }

    public function delete()
    {
        $validator = Validator::make(request()->all(), [
            'product' => 'required',
        ]);

        $product = Product::whereId(request('product'))->with('category')->first();
        $product_category = $product['category']['slug'];

        if (!$product) {
            return api(200, null, 'Bad Request I');
        }

        $fc = Like::where('user_id', auth()->id())->where('product_id', $product['id'])->first();

        if ($fc) {
            $user = auth()->user();
            $user_coffess = $user[$product_category];
            $user_coffess = json_decode($user_coffess);
            $pos = array_search($product['id'], $user_coffess);
            // Remove from array
            unset($user_coffess[$pos]);
            $user_coffess = array_values($user_coffess);
            $user->update([
                $product_category => $user_coffess
            ]);
            $fc->delete();
            return api(200, null, null);
        } else {
            return api(200, null, 'Bad Request II');
        }
    }

    public function reordering()
    {

        $validator = Validator::make(request()->all(), [
            'products' => 'required',
        ]);

        foreach (request('products') as $key => $product) {
            Like::where('user_id', auth()->id())
                ->where('product_id', $product)
                ->update([
                    'sort_order' => $key
                ]);
        }
        return api(200, null, null);
    }
}
