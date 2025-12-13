<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\WorkOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with(['customer', 'workOrder'])->latest()->paginate(25);
        return view('admin.invoices.index', compact('invoices'));
    }

    public function create(?WorkOrder $workOrder = null)
    {
        $workOrders = WorkOrder::doesntHave('invoices')->pluck('number', 'id');
        return view('admin.invoices.create', compact('workOrder', 'workOrders'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'work_order_id'  => 'nullable|exists:work_orders,id',
            'customer_id'    => 'required|exists:customers,id',
            'issue_date'     => 'required|date',
            'due_date'       => 'nullable|date',
            'payment_method' => 'nullable|string|max:50',
        ]);

        $invoice = DB::transaction(function () use ($request) {
            // ако има work_order_id – взимаме данните от него
            if ($request->filled('work_order_id')) {
                $wo = WorkOrder::findOrFail($request->work_order_id);
                $sub   = $wo->total_without_vat;
                $vat   = $wo->vat_amount;
                $total = $wo->total;
            } else {
                $sub = $vat = $total = 0; // празна фактура – ще се попълни после
            }

            $inv = Invoice::create([
                'number'        => 'INV-' . str_pad((Invoice::max('id') ?? 0) + 1, 6, '0', STR_PAD_LEFT),
                'work_order_id' => $request->work_order_id,
                'customer_id'   => $request->customer_id,
                'issue_date'    => $request->issue_date,
                'due_date'      => $request->due_date ?? now()->addDays(14),
                'payment_method'=> $request->payment_method ?? 'cash',
                'subtotal'      => $sub,
                'vat_total'     => $vat,
                'grand_total'   => $total,
                'status'        => 'draft',
                'created_by'    => Auth::check() ? Auth::user()->id : 0,
            ]);

            // ако има WorkOrder – копираме позициите
            if ($request->filled('work_order_id')) {
                foreach ($wo->items as $item) {
                    $inv->items()->create([
                        'product_id'  => $item->product_id,
                        'description' => $item->description,
                        'quantity'    => $item->quantity,
                        'unit_price'  => $item->unit_price,
                        'vat_percent' => $item->vat_percent,
                        'line_total'  => $item->line_total,
                    ]);
                }
            }

            return $inv;
        });

        return redirect()->route('admin.invoices.index')->with('success', 'Фактурата е създадена.');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['items.product', 'customer', 'workOrder']);
        return view('admin.invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        return view('admin.invoices.edit', compact('invoice'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'due_date'        => 'nullable|date',
            'payment_method'  => 'nullable|string|max:50',
            'status'          => 'required|in:draft,issued,paid,cancelled',
        ]);

        $invoice->update($validated);

        return redirect()->route('admin.invoices.index')->with('success', 'Фактурата е обновена.');
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->items()->delete();
        $invoice->delete();
        return redirect()->route('admin.invoices.index')->with('success', 'Фактурата е изтрита.');
    }
}