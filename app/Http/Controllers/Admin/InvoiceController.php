<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Customer;
use App\Models\Doctype;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    /**
     * Генериране на следващ номер на фактура (old_id)
     */
    private function getNextOldId()
    {
        $max = Invoice::max('old_id');
        return $max ? $max + 1 : 1;
    }

    /* ------------------------------------------------------------------
       LIST & LIVE SEARCH
    ------------------------------------------------------------------ */

    public function index(Request $request)
    {
        $invoices = Invoice::with(['customer', 'doctype', 'items'])
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('old_id', 'like', "%{$search}%")
                        ->orWhereHas('customer', function ($c) use ($search) {
                            $c->where('name', 'like', "%{$search}%")
                                ->orWhere('phone', 'like', "%{$search}%")
                                ->orWhere('customer_number', 'like', "%{$search}%");
                        });
                });
            })
            ->when($request->date_from, function ($query, $date) {
                $query->whereDate('invoice_date', '>=', $date);
            })
            ->when($request->date_to, function ($query, $date) {
                $query->whereDate('invoice_date', '<=', $date);
            })
            ->when(isset($request->paid), function ($query) use ($request) {
                $query->where('paid', $request->paid);
            })
            ->when(isset($request->is_void), function ($query) use ($request) {
                $query->where('is_void', $request->is_void);
            })
            ->orderBy('invoice_date', 'desc')
            ->orderBy('old_id', 'desc')
            ->paginate(15)
            ->withQueryString();

        if ($request->wantsJson()) {
            $html = view('admin.invoices.partials.rows', compact('invoices'))->render();
            return response()->json([
                'html' => $html,
                'total' => $invoices->total()
            ]);
        }

        $doctypes = Doctype::where('is_active', true)->orderBy('name')->pluck('name', 'type');

        return view('admin.invoices.index', [
            'invoices' => $invoices,
            'search' => $request->search,
            'date_from' => $request->date_from,
            'date_to' => $request->date_to,
            'paid' => $request->paid,
            'is_void' => $request->is_void,
            'doctypes' => $doctypes,
        ]);
    }

    public function liveSearch(Request $request)
    {
        $invoices = Invoice::with(['customer', 'doctype'])
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('old_id', 'like', "%{$search}%")
                        ->orWhereHas('customer', function ($c) use ($search) {
                            $c->where('name', 'like', "%{$search}%")
                                ->orWhere('phone', 'like', "%{$search}%");
                        });
                });
            })
            ->when($request->date_from, function ($query, $date) {
                $query->whereDate('invoice_date', '>=', $date);
            })
            ->when($request->date_to, function ($query, $date) {
                $query->whereDate('invoice_date', '<=', $date);
            })
            ->when(isset($request->paid), function ($query) use ($request) {
                $query->where('paid', $request->paid);
            })
            ->when(isset($request->is_void), function ($query) use ($request) {
                $query->where('is_void', $request->is_void);
            })
            ->orderBy('invoice_date', 'desc')
            ->orderBy('old_id', 'desc')
            ->paginate(15);

        $html = view('admin.invoices.partials.rows', compact('invoices'))->render();

        return response()->json([
            'html' => $html,
            'total' => $invoices->total()
        ]);
    }

    /* ------------------------------------------------------------------
       CREATE & STORE
    ------------------------------------------------------------------ */

    public function create()
    {
        $nextOldId = $this->getNextOldId();
        $doctypes = Doctype::where('is_active', true)->orderBy('name')->get();
        $paymentMethods = [0 => 'Банков превод', 1 => 'В брой', 2 => 'Карта']; // според нуждите

        return view('admin.invoices.create', compact('nextOldId', 'doctypes', 'paymentMethods'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'old_id' => 'required|integer|unique:invoices,old_id',
            'customer_old_id' => 'required|integer|exists:customers,old_id',
            'invoice_type' => 'required|integer|exists:doctypes,type',
            'invoice_date' => 'nullable|date',
            'date_due' => 'nullable|date|after_or_equal:invoice_date',
            'invoice_received_date' => 'nullable|date',
            'invoice_received_person' => 'nullable|string|max:255',
            'invoice_created_by' => 'nullable|string|max:255',
            'invoice_rec_responsible' => 'nullable|string|max:255',
            'invoice_cre_responsible' => 'nullable|string|max:255',
            'note' => 'nullable|string',
            'zeroexplain' => 'nullable|string',
            'payment_cash' => 'sometimes|boolean',
            'is_void' => 'sometimes|boolean',
            'printed' => 'sometimes|boolean',
            'paid' => 'sometimes|boolean',
            'tipsdelka' => 'nullable|integer|min:0|max:255',
            'sale_type' => 'nullable|integer|min:0|max:255',
            'pay_method' => 'nullable|integer|min:0|max:255',
            'items' => 'nullable|array',
            'items.*.item_code' => 'nullable|string|max:255',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.item_measure' => 'nullable|string|max:50',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.price_each' => 'required|numeric|min:0',
        ]);

        // Булеви полета
        $validated['payment_cash'] = $request->has('payment_cash');
        $validated['is_void'] = $request->has('is_void');
        $validated['printed'] = $request->has('printed');
        $validated['paid'] = $request->has('paid');

        DB::beginTransaction();
        try {
            $invoice = Invoice::create($validated);

            if ($request->has('items')) {
                $this->syncItems($invoice, $request->items);
            }

            DB::commit();
            return redirect()
                ->route('admin.invoices.show', $invoice->id)
                ->with('success', 'Фактура №' . $invoice->old_id . ' е създадена.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Грешка: ' . $e->getMessage());
        }
    }

    /* ------------------------------------------------------------------
       SHOW, EDIT, UPDATE, DESTROY
    ------------------------------------------------------------------ */

    public function show(Invoice $invoice)
    {
        $invoice->load(['customer', 'doctype', 'items']);
        return view('admin.invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        $invoice->load('items');
        $doctypes = Doctype::where('is_active', true)->orderBy('name')->get();
        $paymentMethods = [0 => 'Банков превод', 1 => 'В брой', 2 => 'Карта'];

        return view('admin.invoices.edit', compact('invoice', 'doctypes', 'paymentMethods'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'old_id' => 'required|integer|unique:invoices,old_id,' . $invoice->id,
            'customer_old_id' => 'required|integer|exists:customers,old_id',
            'invoice_type' => 'required|integer|exists:doctypes,type',
            'invoice_date' => 'nullable|date',
            'date_due' => 'nullable|date|after_or_equal:invoice_date',
            'invoice_received_date' => 'nullable|date',
            'invoice_received_person' => 'nullable|string|max:255',
            'invoice_created_by' => 'nullable|string|max:255',
            'invoice_rec_responsible' => 'nullable|string|max:255',
            'invoice_cre_responsible' => 'nullable|string|max:255',
            'note' => 'nullable|string',
            'zeroexplain' => 'nullable|string',
            'payment_cash' => 'sometimes|boolean',
            'is_void' => 'sometimes|boolean',
            'printed' => 'sometimes|boolean',
            'paid' => 'sometimes|boolean',
            'tipsdelka' => 'nullable|integer|min:0|max:255',
            'sale_type' => 'nullable|integer|min:0|max:255',
            'pay_method' => 'nullable|integer|min:0|max:255',
            'items' => 'nullable|array',
            'items.*.id' => 'nullable|integer|exists:invoice_items,id',
            'items.*.item_code' => 'nullable|string|max:255',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.item_measure' => 'nullable|string|max:50',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.price_each' => 'required|numeric|min:0',
            'items.*._delete' => 'nullable|string',
        ]);

        $validated['payment_cash'] = $request->has('payment_cash');
        $validated['is_void'] = $request->has('is_void');
        $validated['printed'] = $request->has('printed');
        $validated['paid'] = $request->has('paid');

        DB::beginTransaction();
        try {
            $invoice->update($validated);

            if ($request->has('items')) {
                $this->syncItems($invoice, $request->items);
            }

            DB::commit();
            return redirect()
                ->route('admin.invoices.show', $invoice->id)
                ->with('success', 'Фактура №' . $invoice->old_id . ' е обновена.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Грешка: ' . $e->getMessage());
        }
    }

    public function destroy(Invoice $invoice)
    {
        try {
            DB::beginTransaction();
            $invoice->items()->delete();
            $invoice->delete();
            DB::commit();

            return redirect()
                ->route('admin.invoices.index')
                ->with('success', 'Фактура №' . $invoice->old_id . ' е изтрита.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Грешка: ' . $e->getMessage());
        }
    }

    /* ------------------------------------------------------------------
       PRINT & PDF
    ------------------------------------------------------------------ */

    public function print(Invoice $invoice)
    {
        $invoice->load(['customer', 'doctype', 'items']);
        return view('admin.invoices.print', compact('invoice'));
    }

    public function pdf(Invoice $invoice)
    {
        $invoice->load(['customer', 'doctype', 'items']);
        $pdf = Pdf::loadView('admin.invoices.pdf', compact('invoice'));
        return $pdf->stream('factura-' . $invoice->old_id . '.pdf');
    }

    /* ------------------------------------------------------------------
       ПОМОЩНИ МЕТОДИ
    ------------------------------------------------------------------ */

    private function syncItems(Invoice $invoice, array $itemRows)
    {
        $submittedIds = [];

        foreach ($itemRows as $row) {
            if (empty($row['item_name'])) {
                continue;
            }

            $data = [
                'invoice_old_id' => $invoice->old_id,
                'row_number' => $row['row_number'] ?? null,
                'item_code' => $row['item_code'] ?? null,
                'item_name' => $row['item_name'],
                'item_measure' => $row['item_measure'] ?? null,
                'quantity' => $row['quantity'],
                'price_each' => $row['price_each'],
                'row_total' => $row['quantity'] * $row['price_each'],
            ];

            if (!empty($row['id'])) {
                $item = InvoiceItem::where('id', $row['id'])
                    ->where('invoice_old_id', $invoice->old_id)
                    ->first();

                if ($item) {
                    if (!empty($row['_delete'])) {
                        $item->delete();
                        continue;
                    }
                    $item->update($data);
                    $submittedIds[] = $item->id;
                }
            } else {
                if (empty($row['_delete'])) {
                    $item = $invoice->items()->create($data);
                    $submittedIds[] = $item->id;
                }
            }
        }

        // Изтриване на неподадени (маркирани чрез липса)
        if (!empty($submittedIds)) {
            $invoice->items()->whereNotIn('id', $submittedIds)->delete();
        }
    }
}