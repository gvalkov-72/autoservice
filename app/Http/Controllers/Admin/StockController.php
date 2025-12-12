<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StockController extends Controller
{
    public function index()
    {
        $lowStock = Product::whereRaw('stock_quantity <= min_stock_level')->get();
        $movements = StockMovement::with('product', 'creator')
                                  ->latest()
                                  ->paginate(25);

        return view('admin.stock.index', compact('lowStock', 'movements'));
    }

    public function createPurchase()
    {
        $products = Product::pluck('name', 'id');
        return view('admin.stock.create-purchase', compact('products'));
    }

    public function storePurchase(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|numeric|min:1',
            'cost_price' => 'nullable|numeric|min:0',
            'notes'      => 'nullable|string',
        ]);

        DB::transaction(function () use ($request) {
            $userId = Auth::check() ? Auth::user()->id : 0;

            StockMovement::create([
                'product_id'     => $request->product_id,
                'change'         => $request->quantity,
                'type'           => 'purchase',
                'reference_id'   => null,
                'reference_type' => null,
                'notes'          => $request->notes,
                'created_by'     => $userId,
            ]);

            $product = Product::find($request->product_id);
            $product->increment('stock_quantity', $request->quantity);
            if ($request->filled('cost_price')) {
                $product->update(['cost_price' => $request->cost_price]);
            }
        });

        return redirect()->route('admin.stock.index')->with('success', 'Доставката е записана.');
    }

    public function createAdjustment()
    {
        $products = Product::pluck('name', 'id');
        return view('admin.stock.create-adjustment', compact('products'));
    }

    public function storeAdjustment(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|numeric',
            'notes'      => 'required|string|max:500',
        ]);

        DB::transaction(function () use ($request) {
            $userId = Auth::check() ? Auth::user()->id : 0;

            $product = Product::find($request->product_id);

            StockMovement::create([
                'product_id'     => $request->product_id,
                'change'         => $request->quantity,
                'type'           => 'adjustment',
                'reference_id'   => null,
                'reference_type' => null,
                'notes'          => $request->notes,
                'created_by'     => $userId,
            ]);

            $product->increment('stock_quantity', $request->quantity);
        });

        return redirect()->route('admin.stock.index')->with('success', 'Корекцията е записана.');
    }
}