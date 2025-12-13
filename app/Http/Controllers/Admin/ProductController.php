<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Support\BarcodeHelper;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::paginate(25);
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        return view('admin.products.create');
    }

    public function store(Request $request)
    {
        // Normalize decimals
        $request->merge([
            'price'       => str_replace(',', '.', $request->price),
            'cost_price'  => str_replace(',', '.', $request->cost_price),
            'vat_percent' => str_replace(',', '.', $request->vat_percent),
        ]);

        $validated = $request->validate([
            'sku'              => 'required|unique:products,sku',
            'name'             => 'required|string|max:255',
            'brand'            => 'nullable|string|max:255',
            'description'      => 'nullable|string',
            'unit'             => 'required|string|max:20',
            'price'            => 'required|numeric|min:0',
            'cost_price'       => 'nullable|numeric|min:0',
            'vat_percent'      => 'required|numeric|min:0|max:100',
            'stock_quantity'   => 'integer|min:0',
            'min_stock_level'  => 'integer|min:0',
            'location'         => 'nullable|string|max:50',
        ]);

        $validated['cost_price'] = $request->filled('cost_price') ? $validated['cost_price'] : null;

        Product::create($validated);

        return redirect()->route('admin.products.index')->with('success', 'Артикулът е добавен.');
    }

    public function show(Product $product)
    {
        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        return view('admin.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $request->merge([
            'price'       => str_replace(',', '.', $request->price),
            'cost_price'  => str_replace(',', '.', $request->cost_price),
            'vat_percent' => str_replace(',', '.', $request->vat_percent),
        ]);

        $validated = $request->validate([
            'sku'              => 'required|unique:products,sku,' . $product->id,
            'name'             => 'required|string|max:255',
            'brand'            => 'nullable|string|max:255',
            'description'      => 'nullable|string',
            'unit'             => 'required|string|max:20',
            'price'            => 'required|numeric|min:0',
            'cost_price'       => 'nullable|numeric|min:0',
            'vat_percent'      => 'required|numeric|min:0|max:100',
            'stock_quantity'   => 'integer|min:0',
            'min_stock_level'  => 'integer|min:0',
            'location'         => 'nullable|string|max:50',
        ]);

        $validated['cost_price'] = $request->filled('cost_price') ? $validated['cost_price'] : null;

        $product->update($validated);

        return redirect()->route('admin.products.index')->with('success', 'Артикулът е обновен.');
    }

    public function destroy(Product $product)
    {
        try {
            $product->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('admin.products.index')
                             ->with('error','Не може да изтриете арикула, защото съществуват свързани записи.');
        }
        return redirect()->route('admin.products.index')
                         ->with('success','Артикулът е деактивиран.');
    }

    public function barcode(Product $product)
    {
        return view('admin.products.barcode', compact('product'));
    }
}