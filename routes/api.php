<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\{
    SearchController,
    AuthController,
    CategoryController,
    ProfileController,
    ProductController,
    FavesController,
    ReviewController,
    WishlistController,
    BrandController,
    LikeController,
    ExploreController
};

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('V1')->group(function () {


    Route::post('/gate', [AuthController::class, 'gate']);

    Route::post('/fastlogin', [AuthController::class, 'fast']);

    // OAUTH
    Route::post('/oauth', [AuthController::class, 'oauth']);
    Route::post('/oauth/fast', [AuthController::class, 'oauth_fast']);


    Route::middleware('auth:sanctum')->group(function () {

        Route::get('/explore', [ExploreController::class, 'index']);
        Route::get('/explorecat', [ExploreController::class, 'index2']);

        Route::get('/profile', [ProfileController::class, 'show_profile']);
        Route::post('/profile', [ProfileController::class, 'update_profile']);
        Route::post('/profile/image-upload', [ProfileController::class, 'upload']);
        Route::post('/product/image-upload-search', [ProfileController::class, 'product']);
        Route::post('/profile/delete', [ProfileController::class, 'delete']);

        Route::get('/search/product', [SearchController::class, 'product']);

        Route::get('/categories', [CategoryController::class, 'list']);

        // ADDING FLAVA
        Route::post('/product/store', [ProductController::class, 'store']);
        Route::post('/product/image-upload', [ProductController::class, 'upload']);

        // SHOW FLAVA
        Route::get('/product/{id}', [ProductController::class, 'show']);
        Route::get('/product/peoples/{id}', [ProductController::class, 'peoples']);

        // BRANS
        Route::get('/brands', [BrandController::class, 'index']);

        // ADD TO WISHLIST
        Route::get('/wishlist', [WishlistController::class, 'index']);
        Route::post('/wishlist/store', [WishlistController::class, 'store']);
        Route::post('/wishlist/delete', [WishlistController::class, 'delete']);

        // REVIEW
        // ADD REVIEW
        Route::get('/reviews', [ReviewController::class, 'index']);
        Route::post('/review/store', [ReviewController::class, 'store']);
        Route::get('/review/{id}', [ReviewController::class, 'show']);
        Route::post('/review/update/{id}', [ReviewController::class, 'update']);
        Route::post('/review/delete', [ReviewController::class, 'delete']);

        // LIKE
        Route::post('/like', [LikeController::class, 'store']);
        Route::post('/dislike', [LikeController::class, 'delete']);


        // EDIT REVIEW

        // GET PRODUCTS BY CATEGORY
        Route::get('/product/category/{id}', [ProfileController::class, 'user_reviews_category_products']);

        // RECOMMENDATION
        Route::get('/recommendation', [ProfileController::class, 'recommendation']);

        // ONBOARDING
        Route::get('/onboarding', [ExploreController::class, 'onboarding']);

        // PROFILE
        Route::get('/user/{id}', [ProfileController::class, 'show']);
        Route::get('/user/likes/{id}', [ProfileController::class, 'user_likes']);


        // RE ORDERING
        Route::post('/reordering', [LikeController::class, 'reordering']);
    });
});
