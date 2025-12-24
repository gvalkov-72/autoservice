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

class ProductController extends Controller
{
    /* ---------- CRUD ---------- */

    public function index(Request $request)
    {
        // Заявка с филтри
        $query = Product::query();

        // Филтриране по търсене
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->search($search);
        }

        // Филтриране по тип продукт
        if ($request->filled('product_type')) {
            $type = $request->input('product_type');
            if ($type == 'product') {
                $query->where('is_service', false);
            } elseif ($type == 'service') {
                $query->where('is_service', true);
            }
        }

        // Филтриране по активност
        if ($request->filled('status')) {
            $query->where('is_active', $request->input('status') == 'active');
        }

        // Филтриране по наличност
        if ($request->filled('stock_status')) {
            $status = $request->input('stock_status');
            if ($status == 'low') {
                $query->lowStock();
            } elseif ($status == 'out') {
                $query->outOfStock();
            } elseif ($status == 'in_stock') {
                $query->where('stock_quantity', '>', 0)
                      ->where('track_inventory', true);
            }
        }

        // Пагинация
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
            // Поля за миграция
            'old_id' => 'nullable|string|max:50|unique:products,old_id',
            'product_number' => 'nullable|string|max:50|unique:products,product_number',

            // Основни данни
            'sku' => 'required|string|max:100|unique:products,sku',
            'name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:100',
            'description' => 'nullable|string',

            // Мерни единици и количество
            'unit' => 'required|string|max:20',
            'uom_code' => 'nullable|string|max:20',
            'quantity' => 'nullable|integer|min:0',

            // Цени
            'price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'vat_percent' => 'nullable|numeric|min:0|max:100',

            // Складова информация
            'stock_quantity' => 'nullable|integer|min:0',
            'min_stock_level' => 'nullable|integer|min:0',
            'reorder_level' => 'nullable|integer|min:0',

            // Локации и кодове
            'location' => 'nullable|string|max:100',
            'barcode' => 'nullable|string|max:50|unique:products,barcode',
            'supplier_code' => 'nullable|string|max:50',

            // Флагове
            'is_active' => 'boolean',
            'is_service' => 'boolean',
            'track_inventory' => 'boolean',

            // Данни за закупки
            'lead_time_days' => 'nullable|integer|min:0',
            'last_purchase_price' => 'nullable|numeric|min:0',
            'last_purchase_date' => 'nullable|date',
        ]);

        // Автоматично генериране на баркод, ако не е предоставен
        if (empty($validated['barcode']) && !empty($validated['sku'])) {
            $validated['barcode'] = $validated['sku'];
        }

        // Автоматично генериране на product_number от SKU, ако не е предоставен
        if (empty($validated['product_number']) && !empty($validated['sku'])) {
            $validated['product_number'] = $validated['sku'];
        }

        // Копиране на quantity в stock_quantity, ако track_inventory е true
        if ($validated['track_inventory'] ?? true) {
            if (!empty($validated['quantity']) && empty($validated['stock_quantity'])) {
                $validated['stock_quantity'] = $validated['quantity'];
            }
        }

        // Създаване на продукта
        $product = Product::create($validated);

        // Генериране на баркод изображение
        $this->generateBarcodeImage($product);

        // Логване на действие
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

        // Статистики за този продукт
        $productStats = [
            'total_sold' => $product->invoiceItems()->sum('quantity'),
            'total_used' => $product->workOrderItems()->sum('quantity'),
            'total_stock_movements' => $product->stockMovements()->count(),
            'last_purchase' => $product->last_purchase_date,
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
            // Поля за миграция
            'old_id' => 'nullable|string|max:50|unique:products,old_id,' . $product->id,
            'product_number' => 'nullable|string|max:50|unique:products,product_number,' . $product->id,

            // Основни данни
            'sku' => 'required|string|max:100|unique:products,sku,' . $product->id,
            'name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:100',
            'description' => 'nullable|string',

            // Мерни единици и количество
            'unit' => 'required|string|max:20',
            'uom_code' => 'nullable|string|max:20',
            'quantity' => 'nullable|integer|min:0',

            // Цени
            'price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'vat_percent' => 'nullable|numeric|min:0|max:100',

            // Складова информация
            'stock_quantity' => 'nullable|integer|min:0',
            'min_stock_level' => 'nullable|integer|min:0',
            'reorder_level' => 'nullable|integer|min:0',

            // Локации и кодове
            'location' => 'nullable|string|max:100',
            'barcode' => 'nullable|string|max:50|unique:products,barcode,' . $product->id,
            'supplier_code' => 'nullable|string|max:50',

            // Флагове
            'is_active' => 'boolean',
            'is_service' => 'boolean',
            'track_inventory' => 'boolean',

            // Данни за закупки
            'lead_time_days' => 'nullable|integer|min:0',
            'last_purchase_price' => 'nullable|numeric|min:0',
            'last_purchase_date' => 'nullable|date',
        ]);

        // Запазване на старите данни за лог
        $oldData = $product->toArray();

        // Проверка за промяна на баркода
        $barcodeChanged = isset($validated['barcode']) && $validated['barcode'] != $product->barcode;

        // Актуализиране на продукта
        $product->update($validated);

        // Регенериране на баркод изображение, ако баркодът е променен
        if ($barcodeChanged && !empty($product->barcode)) {
            $this->generateBarcodeImage($product);
        }

        // Логване на промените
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

            // Логване на действието
            activity()
                ->causedBy(Auth::user())
                ->performedOn($product)
                ->log('Деактивиран продукт: ' . $productName);
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('admin.products.index')
                ->with('error', 'Не може да изтриете продукта, защото съществуват свързани записи (фактури, поръчки, движения).');
        }
        return redirect()->route('admin.products.index')
            ->with('success', 'Продуктът "' . $productName . '" е деактивиран успешно.');
    }

    public function restore($id)
    {
        $product = Product::withTrashed()->findOrFail($id);
        $product->restore();

        // Логване на действието
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

        // Проверка за свързани записи
        if (
            $product->workOrderItems()->count() > 0 ||
            $product->invoiceItems()->count() > 0 ||
            $product->stockMovements()->count() > 0
        ) {
            return redirect()->route('admin.products.index')
                ->with('error', 'Не може да изтриете продукта окончателно, защото има свързани записи.');
        }

        $product->forceDelete();

        // Логване на действието
        activity()
            ->causedBy(Auth::user())
            ->log('Изтрит окончателно продукт: ' . $productName);

        return redirect()->route('admin.products.index')
            ->with('success', 'Продуктът "' . $productName . '" е изтрит окончателно от системата.');
    }

    /* ---------- БАРКОД ФУНКЦИИ ---------- */

    /**
     * Генерира баркод изображение за продукта
     */
    private function generateBarcodeImage(Product $product)
    {
        if (empty($product->barcode)) {
            return;
        }

        try {
            // Генериране на баркод като SVG
            $barcodeSvg = DNS1D::getBarcodeSVG($product->barcode, 'C128', 2, 60);
            
            // Запазване на файла
            $filename = 'barcodes/product_' . $product->id . '_' . $product->barcode . '.svg';
            Storage::disk('public')->put($filename, $barcodeSvg);
            
            // Запазване на пътя в продукта (ако имаме отделно поле)
            // $product->update(['barcode_image' => $filename]);
            
        } catch (\Exception $e) {
            Log::error('Грешка при генериране на баркод за продукт ' . $product->id, [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Показва баркод изображение
     */
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

    /**
     * Страница за печат на баркод етикети
     */
    public function printBarcode(Product $product, Request $request)
    {
        $quantity = $request->input('quantity', 1);
        
        return view('admin.products.barcode', compact('product', 'quantity'));
    }

    /**
     * Групово генериране на баркодове
     */
    public function bulkGenerateBarcodes(Request $request)
    {
        $selectedIds = $request->input('selected_ids', []);
        
        if (empty($selectedIds)) {
            return back()->with('error', 'Не сте избрали продукти за генериране на баркодове.');
        }

        $products = Product::whereIn('id', $selectedIds)->get();
        $generatedCount = 0;

        foreach ($products as $product) {
            // Ако продуктът няма баркод, генерираме от SKU
            if (empty($product->barcode) && !empty($product->sku)) {
                $product->update(['barcode' => $product->sku]);
                $this->generateBarcodeImage($product);
                $generatedCount++;
            }
        }

        return back()->with('success', "Генерирани баркодове за {$generatedCount} продукта.");
    }

    /* ---------- ГРУПОВИ ДЕЙСТВИЯ ---------- */

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

            case 'mark_as_product':
                $products->each(function ($product) {
                    $product->update(['is_service' => false]);
                });
                $message = count($selectedIds) . ' продукта бяха маркирани като стоки.';
                break;

            case 'mark_as_service':
                $products->each(function ($product) {
                    $product->update(['is_service' => true]);
                });
                $message = count($selectedIds) . ' продукта бяха маркирани като услуги.';
                break;

            case 'enable_inventory':
                $products->each(function ($product) {
                    $product->update(['track_inventory' => true]);
                });
                $message = count($selectedIds) . ' продукта бяха включени в инвентара.';
                break;

            case 'disable_inventory':
                $products->each(function ($product) {
                    $product->update(['track_inventory' => false]);
                });
                $message = count($selectedIds) . ' продукта бяха изключени от инвентара.';
                break;

            case 'generate_barcodes':
                return $this->bulkGenerateBarcodes($request);

            case 'export':
                return $this->bulkExport($selectedIds);

            default:
                return back()->with('error', 'Невалидно действие.');
        }

        // Логване на групово действие
        activity()
            ->causedBy(Auth::user())
            ->withProperties(['action' => $action, 'count' => count($selectedIds)])
            ->log('Групово действие върху продукти: ' . $action);

        return back()->with('success', $message);
    }

    /* ---------- ЕКСПОРТ ---------- */

    public function exportPdf(Product $product)
    {
        $product->load(['stockMovements' => function ($query) {
            $query->orderBy('created_at', 'desc')->limit(10);
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

        // Прилагане на филтри от заявката
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

        // Създаване на експорт за множество продукти
        $export = new ProductExport($products);
        $fileName = 'products_export_' . now()->format('Ymd_His') . '.xlsx';

        // Логване на експорта
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

    /* ---------- СКЛАДОВИ ОПЕРАЦИИ ---------- */

    public function adjustStock(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer',
            'type' => 'required|in:adjustment,purchase,sale,return',
            'notes' => 'nullable|string|max:500',
        ]);

        $product->updateStock(
            $request->input('quantity'),
            $request->input('type'),
            $request->input('notes')
        );

        return back()->with('success', 'Наличността е актуализирана успешно.');
    }
}