<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Exports\ProductExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    /* ---------- CRUD ---------- */

public function index(Request $request)
{
    $query = Product::query();

    /* --- търсене --- */
    if ($request->filled('search')) {
        $search = $request->input('search');
        $query->search($search);
    }

    /* --- статус --- */
    if ($request->filled('status')) {
        $query->where('is_active', $request->input('status') === 'active');
    }

    /* --- складов статус --- */
    if ($request->filled('stock_status')) {
        switch ($request->input('stock_status')) {
            case 'low':
                $query->lowStock();
                break;
            case 'out':
                $query->outOfStock();
                break;
            case 'normal':
                $query->where('quantity', '>', 0)
                      ->whereColumn('quantity', '>', 'min_stock');
                break;
        }
    }

    /* --- тип продукт / услуга --- */
    if ($request->filled('type')) {
        $query->where('is_service', $request->input('type') === 'service');
    }

    /* БРОЙ НА НАМЕРЕНИТЕ – преди пагинация */
    $foundCount = $query->count();

    /* ако са ≤ 200 – вземаме всички; ако са повече – само 200 (за да не гърми PDF) */
    $exportLimit = $foundCount <= 200 ? $foundCount : 200;
    $exportIds   = $query->limit($exportLimit)->pluck('id')->toArray();

    session([
        'filtered_products' => $exportIds,
        'filtered_count'    => $foundCount
    ]);

    /* ПАГИНАЦИЯТА остава само за таблицата */
    $products = $query->orderBy('name')->paginate(20);

    activity()
        ->causedBy(Auth::user())
        ->log('Прегледан списък с продукти');

    return view('admin.products.index', compact('products'));
}

    public function create()
    {
        return view('admin.products.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                => 'required|string|max:255',
            'code'                => 'nullable|string|max:50|unique:products,code',
            'plu'                 => 'nullable|string|max:50|unique:products,plu',
            'description'         => 'nullable|string',
            'price'               => 'required|numeric|min:0',
            'cost_price'          => 'nullable|numeric|min:0',
            'quantity'            => 'required|numeric|min:0',
            'unit_of_measure'     => 'required|string|max:20',
            'location'            => 'nullable|string|max:100',
            'min_stock'           => 'nullable|integer|min:0',
            'max_stock'           => 'nullable|integer|min:0',
            'barcode'             => 'nullable|string|max:100|unique:products,barcode',
            'vendor_code'         => 'nullable|string|max:100',
            'manufacturer'        => 'nullable|string|max:255',
            'vat_rate'            => 'nullable|string|max:10',
            'accounting_code'     => 'nullable|string|max:50',
            'is_active'           => 'boolean',
            'is_taxable'          => 'boolean',
            'track_stock'         => 'boolean',
            'is_service'          => 'boolean',
        ]);

        $product = Product::create($validated);

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
        $product->load(['stockMovements' => fn($q) => $q->latest()->limit(10)]);

        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        return view('admin.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name'                => 'required|string|max:255',
            'code'                => 'nullable|string|max:50|unique:products,code,' . $product->id,
            'plu'                 => 'nullable|string|max:50|unique:products,plu,' . $product->id,
            'description'         => 'nullable|string',
            'price'               => 'required|numeric|min:0',
            'cost_price'          => 'nullable|numeric|min:0',
            'quantity'            => 'required|numeric|min:0',
            'unit_of_measure'     => 'required|string|max:20',
            'location'            => 'nullable|string|max:100',
            'min_stock'           => 'nullable|integer|min:0',
            'max_stock'           => 'nullable|integer|min:0',
            'barcode'             => 'nullable|string|max:100|unique:products,barcode,' . $product->id,
            'vendor_code'         => 'nullable|string|max:100',
            'manufacturer'        => 'nullable|string|max:255',
            'vat_rate'            => 'nullable|string|max:10',
            'accounting_code'     => 'nullable|string|max:50',
            'is_active'           => 'boolean',
            'is_taxable'          => 'boolean',
            'track_stock'         => 'boolean',
            'is_service'          => 'boolean',
        ]);

        $oldData = $product->toArray();
        $product->update($validated);

        $changes = [];
        foreach ($validated as $key => $value) {
            if (isset($oldData[$key]) && $oldData[$key] != $value) {
                $changes[$key] = ['old' => $oldData[$key], 'new' => $value];
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
            ->with('success', 'Продуктът "' . $product->name . '" е актуализиран успешно.');
    }

    public function destroy(Product $product)
    {
        if ($product->invoiceItems()->exists() || $product->workOrderItems()->exists()) {
            return redirect()->route('admin.products.index')
                ->with('error', 'Продуктът не може да бъде изтрит, защото се използва във фактури или работни поръчки.');
        }

        $productName = $product->name;
        $product->delete();

        activity()
            ->causedBy(Auth::user())
            ->log('Изтрит продукт: ' . $productName);

        return redirect()->route('admin.products.index')
            ->with('success', 'Продуктът "' . $productName . '" е изтрит успешно.');
    }

    /* ---------- EXPORT ---------- */

    public function exportAll(Request $request)
    {
        /* вземаме вече филтрираните ID-та от сесията (запазени в index()) */
        $ids = session('filtered_products', []);
        $count = session('filtered_count', 0);

        if (empty($ids)) {
            return back()->with('error', 'Няма продукти за експорт с текущите филтри.');
        }

        $format = $request->get('format', 'excel');   // pdf | excel

        /* вземаме само нужните колони, за да пестим памет */
        $products = Product::whereIn('id', $ids)
            ->orderBy('name')
            ->get([
                'id','name','code','plu','barcode','description',
                'quantity','unit_of_measure','min_stock','price','cost_price',
                'is_service','is_active','location'
            ]);

        activity()
            ->causedBy(Auth::user())
            ->withProperties(['filters' => $request->except('_token'), 'count' => $count])
            ->log('Експортирани всички продукти');

        if ($format === 'pdf') {
            return Pdf::loadView('admin.products.export-pdf', compact('products'))
                      ->setPaper('a4', 'landscape')
                      ->stream('products_export_' . now()->format('Ymd_His') . '.pdf');
        }

        /* подразбиране: excel */
        return Excel::download(new ProductExport($products), 'products_export_' . now()->format('Ymd_His') . '.xlsx');
    }

    public function exportSelected(Request $request)
    {
        $ids = $request->get('ids', '');
        if (empty($ids)) {
            return back()->with('error', 'Не са избрани продукти за експорт.');
        }

        $products = Product::whereIn('id', explode(',', $ids))->orderBy('name')->get();

        activity()
            ->causedBy(Auth::user())
            ->withProperties(['count' => $products->count()])
            ->log('Експортирани избрани продукти');

        return Excel::download(new ProductExport($products), 'products_selected_' . now()->format('Ymd_His') . '.xlsx');
    }

    /* ---------- BULK ACTIONS ---------- */

    public function bulkActions(Request $request)
    {
        $action = $request->input('action');
        $selectedIds = $request->input('selected_ids', []);

        if (empty($selectedIds)) {
            return back()->with('error', 'Не са избрани продукти за действие.');
        }

        $products = Product::whereIn('id', $selectedIds)->get();

        switch ($action) {
            case 'activate':
                $products->each(fn($p) => $p->update(['is_active' => true]));
                $message = count($selectedIds) . ' продукта бяха активирани.';
                break;

            case 'deactivate':
                $products->each(fn($p) => $p->update(['is_active' => false]));
                $message = count($selectedIds) . ' продукта бяха деактивирани.';
                break;

            case 'update_stock':
                return back()->with('info', 'Функцията „Обнови наличност“ ще бъде добавена скоро.');

            case 'export':
                return $this->bulkExport($selectedIds);

            case 'delete':
                $count = 0;
                foreach ($products as $product) {
                    if (!$product->invoiceItems()->exists() && !$product->workOrderItems()->exists()) {
                        $product->delete();
                        $count++;
                    }
                }
                $message = "Изтрити са {$count} продукта (само без свързани фактури/поръчки).";
                break;

            default:
                return back()->with('error', 'Невалидно действие.');
        }

        activity()
            ->causedBy(Auth::user())
            ->withProperties(['action' => $action, 'count' => count($selectedIds)])
            ->log('Групово действие върху продукти: ' . $action);

        return back()->with('success', $message);
    }

    /* ---------- BARCODE ---------- */

    public function barcode(Product $product)
    {
        return view('admin.products.barcode', compact('product'));
    }

    /* ---------- PRIVATE ---------- */

    private function bulkExport($selectedIds)
    {
        $products = Product::whereIn('id', $selectedIds)->orderBy('name')->get();

        if ($products->isEmpty()) {
            return back()->with('error', 'Няма избрани продукти за експорт.');
        }

        activity()
            ->causedBy(Auth::user())
            ->withProperties(['count' => $products->count()])
            ->log('Експортирани избрани продукти');

        return Excel::download(new ProductExport($products), 'products_selected_' . now()->format('Ymd_His') . '.xlsx');
    }
}