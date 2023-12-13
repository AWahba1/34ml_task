<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::query();
        if ($request->has('average_rating')) {
            $products->where('average_rating', $request->average_rating);
        }

        if ($request->has('options')) {
            $filterOptions = explode(',', $request->input('options'));
            foreach ($filterOptions as $optionValue) {
                $products->whereHas('options', function ($query) use ($optionValue) {
                    $query->whereJsonContains('values', $optionValue);
                });
            }
        }


        if ($request->has('max_price')) {
            $products->whereHas('variants', function ($query) use ($request) {
                $query->where('price', '<=', $request->input('max_price'));
            });
        }
    
        return $products->get();
    }
}
