<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// MODELS
use App\Models\{Product, Search};
use Algolia\AlgoliaSearch\SearchIndex;

class SearchController extends Controller
{
    public function product()
    {
        $results = [];
        if (request('names') == "[]") {
            $items = [];
        }
        // elseif(str_starts_with(request('names'), '['))
        // {
        //     $items = Product::search('', function (SearchIndex $algolia, string $query, array $options) {
        //         $options['similarQuery'] = request('names');
        //         return $algolia->search($query, $options);
        //     })->get();
        //     $items->load('brand');
        // }
        // else 
        // {
        //     $items = Product::where('name', 'like', '%'.request('names').'%')->with('brand')->get();
        //     $items->load('brand');

        // }
        $cat = request('cat') ?? null;
        $items = Product::search('', function (SearchIndex $algolia, string $query, array $options) {
            $options['similarQuery'] = request('names');
            // $options['filters'] = 'category_id:'.request('cat') ?? '';
            return $algolia->search($query, $options);
        })->query(function ($query) use ($cat) {
            $query->where('category_id', $cat);
        })->get();
        $items->load('brand');

        Search::create([
            'user_id' => auth()->id(),
            'payload' => json_decode(request('names')),
            'response' => $items,
            'img' => request('img') == null ? null : request('img')
        ]);
        return api(200, $items ?? [], null);
    }
}
