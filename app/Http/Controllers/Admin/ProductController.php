<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Exports\ProductExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Milon\Barcode\Facades\DNS1DFacade as DNS1D;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    /* ---------- CRUD ---------- */

    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->search($search);
        }

        if ($request->filled('product_type')) {
            $type = $request->input('product_type');
            if ($type == 'product') {
                $query->where('is_service', false);
            } elseif ($type == 'service') {
                $query->where('is_service', true);
            }
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->input('status') == 'active');
        }

        if ($request->filled('stock_status')) {
            $status = $request->input('stock_status');
            if ($status == 'low') {
                $query->lowStock();
            } elseif ($status == 'out') {
                $query->outOfStock();
            } elseif ($status == 'in_stock') {
                $query->where('quantity', '>', 0)
                      ->where('track_stock', true);
            }
        }

        $products = $query->orderBy('name')->paginate(20);

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        return view('admin.products.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'old_id' => 'nullable|string|max:50|unique:products,old_id',
            'plu' => 'nullable|string|max:50|unique:products,plu',
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:100|unique:products,code',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'quantity' => 'nullable|numeric|min:0',
            'unit_of_measure' => 'required|string|max:20',
            'location' => 'nullable|string|max:100',
            'min_stock' => 'nullable|integer|min:0',
            'max_stock' => 'nullable|integer|min:0',
            'barcode' => 'nullable|string|max:50|unique:products,barcode',
            'vendor_code' => 'nullable|string|max:50',
            'manufacturer' => 'nullable|string|max:100',
            'vat_rate' => 'nullable|string|max:10',
            'accounting_code' => 'nullable|string|max:50',
            'is_active' => 'boolean',
            'is_service' => 'boolean',
            'track_stock' => 'boolean',
            'is_taxable' => 'boolean',
        ]);

        // Автоматично попълване
        if (empty($validated['code']) && !empty($validated['plu'])) {
            $validated['code'] = $validated['plu'];
        }

        if (empty($validated['barcode']) && !empty($validated['code'])) {
            $validated['barcode'] = $validated['code'];
        }

        if (empty($validated['plu']) && !empty($validated['old_id'])) {
            $validated['plu'] = $validated['old_id'];
        }

        // Стойности по подразбиране
        if (!isset($validated['is_active'])) $validated['is_active'] = true;
        if (!isset($validated['track_stock'])) $validated['track_stock'] = true;
        if (!isset($validated['is_taxable'])) $validated['is_taxable'] = true;
        if (!isset($validated['is_service'])) $validated['is_service'] = false;
        if (empty($validated['vat_rate'])) $validated['vat_rate'] = '20%';
        if (empty($validated['unit_of_measure'])) $validated['unit_of_measure'] = 'бр.';

        $product = Product::create($validated);

        if (!empty($product->barcode)) {
            $this->generateBarcodeImage($product);
        }

        activity()
            ->causedBy(Auth::user())
            ->performedOn($product)
            ->log('Създаден нов продукт: ' . $product->name);

        return redirect()
            ->route('admin.products.show', $product)
            ->with('success', 'Продуктът "' . $product->name . '" е създаден успешно.');
    }

    public function show(Product $product)
    {
        $product->load(['stockMovements' => function ($query) {
            $query->orderBy('created_at', 'desc')->limit(10);
        }, 'invoiceItems' => function ($query) {
            $query->orderBy('created_at', 'desc')->limit(5);
        }, 'workOrderItems' => function ($query) {
            $query->orderBy('created_at', 'desc')->limit(5);
        }]);

        $productStats = [
            'total_sold' => $product->invoiceItems()->sum('quantity'),
            'total_used' => $product->workOrderItems()->sum('quantity'),
            'total_stock_movements' => $product->stockMovements()->count(),
            'total_value' => $product->total_value,
            'profit_margin' => $product->profit_margin,
        ];

        return view('admin.products.show', compact('product', 'productStats'));
    }

    public function edit(Product $product)
    {
        return view('admin.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'old_id' => 'nullable|string|max:50|unique:products,old_id,' . $product->id,
            'plu' => 'nullable|string|max:50|unique:products,plu,' . $product->id,
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:100|unique:products,code,' . $product->id,
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'quantity' => 'nullable|numeric|min:0',
            'unit_of_measure' => 'required|string|max:20',
            'location' => 'nullable|string|max:100',
            'min_stock' => 'nullable|integer|min:0',
            'max_stock' => 'nullable|integer|min:0',
            'barcode' => 'nullable|string|max:50|unique:products,barcode,' . $product->id,
            'vendor_code' => 'nullable|string|max:50',
            'manufacturer' => 'nullable|string|max:100',
            'vat_rate' => 'nullable|string|max:10',
            'accounting_code' => 'nullable|string|max:50',
            'is_active' => 'boolean',
            'is_service' => 'boolean',
            'track_stock' => 'boolean',
            'is_taxable' => 'boolean',
        ]);

        $oldData = $product->toArray();
        $barcodeChanged = isset($validated['barcode']) && $validated['barcode'] != $product->barcode;

        $product->update($validated);

        if ($barcodeChanged && !empty($product->barcode)) {
            $this->generateBarcodeImage($product);
        }

        $changes = [];
        foreach ($validated as $key => $value) {
            if (isset($oldData[$key]) && $oldData[$key] != $value) {
                $changes[$key] = [
                    'old' => $oldData[$key],
                    'new' => $value
                ];
            }
        }

        if (!empty($changes)) {
            activity()
                ->causedBy(Auth::user())
                ->performedOn($product)
                ->withProperties(['changes' => $changes])
                ->log('Актуализиран продукт: ' . $product->name);
        }

        return redirect()
            ->route('admin.products.show', $product)
            ->with('success', 'Данните на продукт "' . $product->name . '" са актуализирани успешно.');
    }

    public function destroy(Product $product)
    {
        try {
            $productName = $product->name;
            $product->delete();

            activity()
                ->causedBy(Auth::user())
                ->performedOn($product)
                ->log('Деактивиран продукт: ' . $productName);
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('admin.products.index')
                ->with('error', 'Не може да изтриете продукта, защото съществуват свързани записи.');
        }
        return redirect()->route('admin.products.index')
            ->with('success', 'Продуктът "' . $productName . '" е деактивиран успешно.');
    }

    public function restore($id)
    {
        $product = Product::withTrashed()->findOrFail($id);
        $product->restore();

        activity()
            ->causedBy(Auth::user())
            ->performedOn($product)
            ->log('Възстановен продукт: ' . $product->name);

        return redirect()->route('admin.products.index')
            ->with('success', 'Продуктът "' . $product->name . '" е възстановен успешно.');
    }

    public function forceDelete($id)
    {
        $product = Product::withTrashed()->findOrFail($id);
        $productName = $product->name;

        if (
            $product->workOrderItems()->count() > 0 ||
            $product->invoiceItems()->count() > 0 ||
            $product->stockMovements()->count() > 0
        ) {
            return redirect()->route('admin.products.index')
                ->with('error', 'Не може да изтриете продукта окончателно, защото има свързани записи.');
        }

        $product->forceDelete();

        activity()
            ->causedBy(Auth::user())
            ->log('Изтрит окончателно продукт: ' . $productName);

        return redirect()->route('admin.products.index')
            ->with('success', 'Продуктът "' . $productName . '" е изтрит окончателно от системата.');
    }

    public function bulkAction(Request $request)
    {
        $action = $request->input('action');
        $selectedIds = $request->input('selected_ids', []);

        if (empty($selectedIds)) {
            return back()->with('error', 'Не сте избрали продукти за действие.');
        }

        $products = Product::whereIn('id', $selectedIds)->get();

        switch ($action) {
            case 'activate':
                $products->each(function ($product) {
                    $product->update(['is_active' => true]);
                });
                $message = count($selectedIds) . ' продукта бяха активирани.';
                break;

            case 'deactivate':
                $products->each(function ($product) {
                    $product->update(['is_active' => false]);
                });
                $message = count($selectedIds) . ' продукта бяха деактивирани.';
                break;

            case 'enable_tracking':
                $products->each(function ($product) {
                    $product->update(['track_stock' => true]);
                });
                $message = count($selectedIds) . ' продукта бяха включени в проследяване.';
                break;

            case 'disable_tracking':
                $products->each(function ($product) {
                    $product->update(['track_stock' => false]);
                });
                $message = count($selectedIds) . ' продукта бяха изключени от проследяване.';
                break;

            case 'mark_as_service':
                $products->each(function ($product) {
                    $product->update(['is_service' => true]);
                });
                $message = count($selectedIds) . ' продукта бяха маркирани като услуги.';
                break;

            case 'mark_as_product':
                $products->each(function ($product) {
                    $product->update(['is_service' => false]);
                });
                $message = count($selectedIds) . ' продукта бяха маркирани като продукти.';
                break;

            case 'export':
                return $this->bulkExport($selectedIds);

            default:
                return back()->with('error', 'Невалидно действие.');
        }

        activity()
            ->causedBy(Auth::user())
            ->withProperties(['action' => $action, 'count' => count($selectedIds)])
            ->log('Групово действие върху продукти: ' . $action);

        return back()->with('success', $message);
    }

    /* ---------- БАРКОД ФУНКЦИИ ---------- */

    private function generateBarcodeImage(Product $product)
    {
        if (empty($product->barcode)) {
            return;
        }

        try {
            $barcodeSvg = DNS1D::getBarcodeSVG($product->barcode, 'C128', 2, 60);
            $filename = 'barcodes/product_' . $product->id . '_' . $product->barcode . '.svg';
            Storage::disk('public')->put($filename, $barcodeSvg);
        } catch (\Exception $e) {
            Log::error('Грешка при генериране на баркод за продукт ' . $product->id, [
                'error' => $e->getMessage()
            ]);
        }
    }

    public function showBarcode(Product $product)
    {
        if (empty($product->barcode)) {
            abort(404, 'Няма баркод за този продукт');
        }

        try {
            $barcodeSvg = DNS1D::getBarcodeSVG($product->barcode, 'C128', 2, 60);
            return response($barcodeSvg, 200)
                ->header('Content-Type', 'image/svg+xml');
        } catch (\Exception $e) {
            abort(500, 'Грешка при генериране на баркод');
        }
    }

    public function printBarcode(Product $product, Request $request)
    {
        $quantity = $request->input('quantity', 1);
        return view('admin.products.barcode', compact('product', 'quantity'));
    }

    public function bulkGenerateBarcodes(Request $request)
    {
        $selectedIds = $request->input('selected_ids', []);

        if (empty($selectedIds)) {
            return back()->with('error', 'Не сте избрали продукти за генериране на баркодове.');
        }

        $products = Product::whereIn('id', $selectedIds)->get();
        $generated = 0;

        foreach ($products as $product) {
            if (empty($product->barcode)) {
                $product->update(['barcode' => 'PRD' . str_pad($product->id, 6, '0', STR_PAD_LEFT)]);
            }
            $this->generateBarcodeImage($product);
            $generated++;
        }

        activity()
            ->causedBy(Auth::user())
            ->withProperties(['count' => $generated])
            ->log('Групово генериране на баркодове за продукти');

        return back()->with('success', 'Генерирани баркодове за ' . $generated . ' продукта.');
    }

    /* ---------- ЕКСПОРТ ---------- */

    public function exportPdf(Product $product)
    {
        $product->load(['stockMovements' => function ($query) {
            $query->orderBy('created_at', 'desc')->limit(20);
        }]);

        return Pdf::loadView('admin.products.export-pdf', compact('product'))
            ->setPaper('a4', 'portrait')
            ->stream('product_' . $product->id . '.pdf');
    }

    public function exportExcel(Product $product)
    {
        return Excel::download(
            new ProductExport($product),
            'product_' . $product->id . '.xlsx'
        );
    }

    public function exportCsv(Product $product)
    {
        return Excel::download(
            new ProductExport($product),
            'product_' . $product->id . '.csv',
            \Maatwebsite\Excel\Excel::CSV
        );
    }

    public function exportAll(Request $request)
    {
        $query = Product::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->search($search);
        }

        if ($request->filled('product_type')) {
            $type = $request->input('product_type');
            if ($type == 'product') {
                $query->where('is_service', false);
            } elseif ($type == 'service') {
                $query->where('is_service', true);
            }
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->input('status') == 'active');
        }

        $products = $query->get();

        if ($products->isEmpty()) {
            return back()->with('error', 'Няма продукти за експорт с текущите филтри.');
        }

        $export = new ProductExport($products);
        $fileName = 'products_export_' . now()->format('Ymd_His') . '.xlsx';

        activity()
            ->causedBy(Auth::user())
            ->withProperties(['count' => $products->count(), 'filters' => $request->except('_token')])
            ->log('Експортирани всички продукти');

        return Excel::download($export, $fileName);
    }

    /* ---------- ДОПЪЛНИТЕЛНИ МЕТОДИ ---------- */

    private function bulkExport($selectedIds)
    {
        $products = Product::whereIn('id', $selectedIds)->get();

        if ($products->isEmpty()) {
            return back()->with('error', 'Няма избрани продукти за експорт.');
        }

        $export = new ProductExport($products);
        $fileName = 'products_selected_' . now()->format('Ymd_His') . '.xlsx';

        activity()
            ->causedBy(Auth::user())
            ->withProperties(['count' => $products->count()])
            ->log('Експортирани избрани продукти');

        return Excel::download($export, $fileName);
    }
}