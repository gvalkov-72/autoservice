<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function search(Request $request)
    {
        $q = trim($request->get('q'));

        if (mb_strlen($q) < 2) {
            return response()->json([]);
        }

        $products = Product::where('is_active', true)
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('old_id', 'like', "%{$q}%");
            })
            ->orderBy('name')
            ->limit(20)
            ->get(['id', 'old_id as code', 'name', 'uom', 'price']);

        // ВАЖНО: НЕ умножавай по курса! Връщай цената в евро
        // $rate = 1.95583;
        // $products->transform(function ($product) use ($rate) {
        //     $product->price = number_format($product->price * $rate, 2, '.', '');
        //     return $product;
        // });

        return response()->json($products);
    }
}
