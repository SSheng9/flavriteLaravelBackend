<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Socialite;

use App\Models\{Product, Review, User};

class ProfileController extends Controller
{
    //
    public function show_profile()
    {
        return api(200, auth()->user(), null);
    }

    public function update_profile()
    {

        $user = auth()->user();
        $user->update([
            'name' => request('name'),
            'instagram' => request('instagram'),
            'twitter' => request('twitter'),
            'about' => request('about')
        ]);
        return api(200, null, null);
    }

    public function recommendation()
    {
        return api(200, Product::where('category_id', request('category'))->inRandomOrder()->with('brand', 'category', 'wished')->limit(5)->get(), null);
    }

    // SHOW ALL PRODUCTS FOR CATEGORY 
    public function user_reviews_category_products($id)
    {
        // GET USER REVIEW PRODUCTS BY CATEGORY
        return api(200, Review::where('category_id', $id)->where('user_id', auth()->id())->with('product', 'brand')->orderBy('rate', 'DESC')->latest()->get(), null);
    }

    // UPLOAD IMAGE
    public function upload(Request $request)
    {
        $path = $request->file('avatar')->store('/', 'users');
        auth()->user()->update([
            'avatar' => $path
        ]);
        return api(200, $path, null);
    }

    public function product(Request $request)
    {
        $path = $request->file('avatar')->store('/', 'products');
        return api(200, $path, null);
    }

    public function show($id)
    {
        $user = User::where('id', $id)->first();

        return api(200, [
            'user' => $user,
            'me' => auth()->user(),
            'people' => User::inRandomOrder()->limit(5)->get()
        ], null);
    }

    public function user_likes($id)
    {

        $user = User::where('id', $id)->whereHas('likes.product', function ($q) {
            $q->where('category_id', request('category'));
        })->with('likes.product', 'likes.product.brand')->first();
        return api(200, $user['likes'] ?? [], null);
    }

    public function callback_google()
    {

        try {

            $user = Socialite::driver('google')->stateless()->user();
            $finduser = User::where('google_id', $user->id)->first();

            if ($finduser) {

                $token = $finduser->createToken('flavrite');
                return redirect()->away('Flavrite://gate/' . $token->plainTextToken);
            } else {
                $finduser = User::where('email', $user->email)->first();
                if ($finduser) {
                    $finduser->update([
                        'google_id' => $user->id
                    ]);
                } else {
                    $finduser = User::create([
                        'name' => $user->name,
                        'email' => $user->email,
                        'google_id' => $user->id
                    ]);
                }

                $token = $finduser->createToken('flavrite');
                return redirect()->away('Flavrite://gate/' . $token->plainTextToken);
            }
        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }

    public function callback_facebook()
    {

        try {

            $user = Socialite::driver('facebook')->stateless()->user();
            $finduser = User::where('facebook_id', $user->id)->first();

            if ($finduser) {

                $token = $finduser->createToken('flavrite');
                return redirect()->away('Flavrite://gate/' . $token->plainTextToken);
            } else {
                $finduser = User::where('email', $user->email)->first();
                if ($finduser) {
                    $finduser->update([
                        'facebook_id' => $user->id
                    ]);
                } else {
                    $finduser = User::create([
                        'name' => $user->name,
                        'email' => $user->email,
                        'facebook_id' => $user->id
                    ]);
                }

                $token = $finduser->createToken('flavrite');
                return redirect()->away('Flavrite://gate/' . $token->plainTextToken);
            }
        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }

    public function delete()
    {
        User::where('id', auth()->id())->delete();
        return api(200, null, null);
    }
}
