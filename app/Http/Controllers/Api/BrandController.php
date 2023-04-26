<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// MODELS
use App\Models\Brand;

class BrandController extends Controller
{
    //
    public function index()
    {
        return api(200, Brand::get(), null);
    }
}
