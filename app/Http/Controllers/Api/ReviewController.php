<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Validator;

use Illuminate\Http\Request;

// MODELS
use App\Models\{Review, Product, Tag, Like};

class ReviewController extends Controller
{

    public function index()
    {
        return api(200, Review::where('user_id', auth()->id())->where('category_id', request('category'))
            ->with('product', 'brand')
            ->latest()
            ->get(), null);
    }

    public function show($id)
    {
        $rv = Review::where('user_id', auth()->id())->where('product_id', $id)->first();
        if (!$rv) {
            return api(200, null, null);
        }
        $rv['tags'] = [];
        if ($rv['notes'] && count(json_decode($rv['notes'], true))) {
            $tags = Tag::whereIn('id', json_decode($rv['notes'], true))->get();
            $rv['tags'] = $tags;
        }

        return api(200, $rv, null);
    }

    public function store()
    {
        $validator = Validator::make(request()->all(), [
            'product' => 'required',
            'notes' => 'required',
            'rate' => 'required'
        ]);

        $product = Product::whereId(request('product'))->first();

        if (!$product) {
            return api(404, null, null);
        }

        $fc = Review::where('user_id', auth()->id())->where('product_id', $product['id'])->first();

        $this->addToLike($product);

        if (!$fc) {
            if (request('notes')) {
                $notes = request('notes');
                if ($notes && empty($notes)) {
                    $notes = null;
                }
            }
            Review::create([
                'user_id' => auth()->id(),
                'product_id' => $product['id'],
                'category_id' => $product['category_id'],
                'brand_id' => $product['brand_id'],
                'notes' => $notes ?? null,
                'rate' => request('rate') ?? null
            ]);
            return api(200, null, null);
        } else {
            $notes = null;
            if (request('notes')) {
                $notes = request('notes');
                if (!count($notes)) {
                    $notes = null;
                }
            }
            $fc->update([
                'notes' => $notes,
                'rate' => request('rate') ?? null
            ]);
            return api(200, null, null);
        }


        return api(200, null, null);
    }

    public function update($id)
    {
        $validator = Validator::make(request()->all(), [
            'product' => 'required',
            // 'note' => 'required',
            // 'rate' => 'required'
        ]);

        $product = Product::whereId(request('product'))->first();

        if (!$product) {
            return api(400, null, 'Bad Request I');
        }

        $fc = Review::whereId($id)->where('user_id', auth()->id())->where('product_id', $product['id'])->first();

        $this->addToLike($product);

        if ($fc) {
            $notes = null;
            if (request('notes')) {
                $notes = request('notes');
                if (!count($notes)) {
                    $notes = null;
                }
            }
            $fc->update([
                'notes' => $notes,
                'rate' => request('rate') ?? null
            ]);
            return api(200, null, null);
        }


        return api(400, null, 'Bad Request. II');
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

        $fc = Review::where('user_id', auth()->id())->where('product_id', $product['id'])->first();

        if ($fc) {
            $fc->delete();
            return api(200, null, null);
        } else {
            return api(200, null, 'Bad Request II');
        }
    }

    public function addToLike($product)
    {
        $fc = Like::where('user_id', auth()->id())->where('product_id', $product['id'])->first();


        if (!$fc) {
            $fc_pp = Product::whereId(request('product'))->with('category')->first();
            $product_category = $fc_pp->category->slug;
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
                'user_id' => $user['id'],
                'product_id' => $product['id']
            ]);
            return api(200, null, null);
        }
    }
}
