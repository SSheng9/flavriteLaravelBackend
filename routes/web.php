<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{
    ProfileController,
    AuthController
};

// use Faker\Generator as Faker; 


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
})->name('login');

Route::get('/storage/{folder}/{filename}', function ($folder, $filename) {
    $path = storage_path() . '/app/public/' . $folder . '/' . $filename;
    if (!File::exists($path)) abort(404);

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);

    return $response;
});


Route::get('/g/redirect', function () {
    return Socialite::driver('google')->redirect();
});

Route::get('/f/redirect', function () {
    return Socialite::driver('facebook')->redirect();
});


Route::get('/callback/g', [ProfileController::class, 'callback_google']);
Route::get('/callback/f', [ProfileController::class, 'callback_facebook']);

Route::get('/fastpr/{token}', [AuthController::class, 'process_fast']);


// https://api.flavrite.com/notify/me/callback/apple
// https://api.flavrite.com/notify/me/callback/facebook

// Route::get('/fk', function(){
//     $users = range(1, 100);
//     $faker = \Faker\Factory::create();
//     return $users;
//     foreach($users as $user)
//     {
//         $tuser = \App\Models\User::create([
//             'name' => $faker->name(),
//             'email' => $faker->email(),
//             ''
//         ]); 
//         // LIKE

//     }
// });

Route::get('fixtempbrand', function () {
    $datas = \App\Models\TempDb::where('brand_name', '!=', '')->orWhere('brand_name2', '!=', '')->get();
    foreach ($datas as $data) {
        $brand = \App\Models\Brand::where('name', $data->brand_name)->first();
        if($data['brand_name2'] && !$brand){
            $brand = \App\Models\Brand::where('name', $data->brand_name2)->first();
        }
        if ($brand) {
            $data->update([
                'brand_id' => $brand->id
            ]);
        } else {
            $brand = \App\Models\Brand::create([
                'name' => $data->brand_name ?? $data->brand_name2,
            ]);
            $data->update([
                'brand_id' => $brand->id
            ]);
        }
    }
});

Route::get('fixnewproducts', function () {

    $datas = \App\Models\TempDb::where('amazon_id', '!=', '')->where('brand_id', '!=', '')->where('thumbnail', null)->get();
    foreach ($datas as $data) {
        $url = $data['img'];
        $contents = file_get_contents($url);
        $name = Str::uuid() . '.' . 'jpg';
        Storage::disk('flavas')->put($name, $contents);
        $data->update([
            'thumbnail' => $name
        ]);
    }
    return 'done';
});

Route::get('addtemotoproduct/{cat}', function ($cat) {
    $datas = \App\Models\TempDb::where('amazon_id', '!=', '')->where('brand_id', '!=', '')->where('thumbnail', '!=', null)->get();
    foreach ($datas as $data) {
        $product = \App\Models\Product::where('amazon_id', $data->amazon_id)->first();
        if (!$product) {
            $product = \App\Models\Product::create([
                'name' => $data->name,
                'amazon_id' => $data->amazon_id,
                'brand_id' => $data->brand_id,
                'thumbnail' => $data->thumbnail,
                'category_id' => $cat,
            ]);
            $data->update([
                'transfer' => 1
            ]);
        }
    }
    return 'done';
});