<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// MODELS
use App\Models\Category;

class CategoryController extends Controller
{

    public function list()
    {
        $items = Category::latest()->get();

        return api( 200, $items, null);
    }
}
