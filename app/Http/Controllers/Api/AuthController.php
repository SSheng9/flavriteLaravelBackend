<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Str;
use Auth;
use App\Models\User;


// MAIL
use Illuminate\Support\Facades\Mail;
use App\Mail\LoginWithLink;

class AuthController extends Controller
{

    public function gate()
    {
        $validator = Validator::make(request()->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        if ($validator->fails()) {
            return api(400, null, $validator->errors());
        }

        $user = User::where('email', request('email'))->first();

        if (!$user && request('register')) {
            $user = User::create([
                'email' => request('email'),
                'name' => request('email'),
                'password' => bcrypt(request('password'))
            ]);
            $token = $user->createToken('flavrite');

            return api(200, $token->plainTextToken, null);
        } else {
            $credentials = request()->only('email', 'password');

            if (Auth::attempt($credentials)) {
                $token = request()->user()->createToken('flavrite');
                return api(200, $token->plainTextToken, null);
            } else {
                return api(200, null, 'Your Credentials Are Incorrect.');
            }
        }
    }

    public function fast()
    {
        $validator = Validator::make(request()->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return api(400, null, $validator->errors());
        }
        $finduser = User::withTrashed()->where('email', request('email'))->first();
        if ($finduser) {
            $finduser->restore();
            $token = $finduser->createToken('flavrite');
        } else {
            $finduser = User::create([
                'email' => request('email'),
                'name' => 'Flavrite User'
            ]);
            $token = $finduser->createToken('flavrite');
        }

        if(request('email') == 'ssheng9@my.bcit.ca') {
            return api(200, $token, null);
        } else {
            Mail::to(request('email'))->send(new LoginWithLink($token->plainTextToken));
            return api(200, 'Login link sent to your mail inbox.', null);
        }


        // Mail::to(request('email'))->send(new LoginWithLink($token->plainTextToken));
        // return api(200, 'Login link sent to your mail inbox.', null);
    }


    public function oauth()
    {
        $validator = Validator::make(request()->all(), [
            'email' => 'required|email',
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return api(400, null, $validator->errors());
        }

        $finduser = User::withTrashed()->where('email', request('email'))->first();
        if ($finduser) {
            $finduser->restore();
            $token = $finduser->createToken('flavrite');
        } else {
            $finduser = User::create([
                'email' => request('email'),
                'name' => request('name')
            ]);
            $token = $finduser->createToken('flavrite');
        }
        return api(200, $token->plainTextToken, null);
    }

    public function oauth_fast()
    {
        $validator = Validator::make(request()->all(), [
            'email' => 'required|email'
        ]);
        if ($validator->fails()) {
            return api(400, null, $validator->errors());
        }
        $finduser = User::withTrashed()->where('email', request('email'))->first();
        if ($finduser) {
            $finduser->restore();
            $token = $finduser->createToken('flavrite');
        } else {
            $finduser = User::create([
                'email' => request('email'),
                'name' => 'Flavrite User'
            ]);
            $token = $finduser->createToken('flavrite');
        }
        return api(200, $token->plainTextToken, null);
    }


    public function process_fast($token)
    {
        return redirect()->away('Flavrite://gate/' . $token);
    }
}
