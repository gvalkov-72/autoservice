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
    /**
     * Списък на фактурите с търсене
     */
    public function index(Request $request)
    {
        $query = Invoice::with('customer');

        if ($request->filled('q')) {
            $q = $request->q;

            $query->where(function ($sub) use ($q) {
                $sub->where('invoice_number', 'like', $q . '%')
                    ->orWhere('status', 'like', $q . '%')
                    ->orWhere('payment_status', 'like', $q . '%')
                    ->orWhere('grand_total', 'like', $q . '%')
                    ->orWhereDate('invoice_date', 'like', $q . '%')
                    ->orWhereHas('customer', function ($c) use ($q) {
                        $c->where('name', 'like', $q . '%')
                          ->orWhere('eik', 'like', $q . '%');
                    });
            });
        }

        $invoices = $query->orderByDesc('invoice_date')->paginate(15);

        if ($request->ajax()) {
            return view('admin.invoices.index', compact('invoices'))->render();
        }

        return view('admin.invoices.index', compact('invoices'));
    }

    /**
     * Показване на формата за създаване
     */
    public function create(?WorkOrder $workOrder = null)
    {
        $workOrders = WorkOrder::doesntHave('invoice')->pluck('number', 'id');

        return view('admin.invoices.create', compact('workOrder', 'workOrders'));
    }

    /**
     * Съхраняване на нова фактура
     */
    public function store(Request $request)
    {
        $request->validate([
            'work_order_id' => 'nullable|exists:work_orders,id',
            'customer_id'   => 'required|exists:customers,id',
            'invoice_date'  => 'required|date',
            'due_date'      => 'nullable|date',
            'payment_method'=> 'nullable|string|max:50',
        ]);

        $invoice = DB::transaction(function () use ($request) {
            // Създаваме фактурата
            $inv = Invoice::create([
                'customer_id'   => $request->customer_id,
                'vehicle_id'    => optional($request->work_order_id ? WorkOrder::find($request->work_order_id) : null)->vehicle_id,
                'invoice_date'  => $request->invoice_date,
                'due_date'      => $request->due_date ?? now()->addDays(14),
                'payment_method'=> $request->payment_method ?? 'cash',
                'discount_amount'=> 0,
                // Други суми ще се преизчислят
            ]);

            // Ако има work_order_id — копираме редове
            if ($request->filled('work_order_id')) {
                $wo = WorkOrder::findOrFail($request->work_order_id);

                foreach ($wo->items as $item) {
                    $inv->items()->create([
                        'description'    => $item->description,
                        'quantity'       => $item->quantity,
                        'unit_price'     => $item->unit_price,
                        'vat_rate'       => 20,        // Ако искаш различен процент, настрой тук
                        'vat_amount'     => 0,         // Ще се изчисли по-долу
                        'total_price'    => 0,         // Ще се изчисли по-долу
                    ]);
                }
            }

            // Преизчисляване на суми (subtotal/tax/grand_total)
            $inv->recalculateTotals();

            return $inv;
        });

        return redirect()->route('admin.invoices.index')
                         ->with('success', 'Фактурата е създадена успешно.');
    }

    /**
     * Преглед на фактура
     */
    public function show(Invoice $invoice)
    {
        $invoice->load(['items', 'customer', 'payments', 'vehicle']);

        return view('admin.invoices.show', compact('invoice'));
    }

    /**
     * Редакция на фактура
     */
    public function edit(Invoice $invoice)
    {
        $invoice->load(['items', 'customer', 'vehicle']);

        return view('admin.invoices.edit', compact('invoice'));
    }

    /**
     * Обновяване на фактура
     */
    public function update(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'invoice_date'   => 'nullable|date',
            'due_date'       => 'nullable|date',
            'payment_method' => 'nullable|string|max:50',
            'status'         => 'required|in:draft,sent,paid,overdue,voided,cancelled',
            'payment_status' => 'nullable|string|max:50',
        ]);

        $invoice->update($validated);

        // Ако трябва — преизчисляваме суми
        $invoice->recalculateTotals();

        return redirect()->route('admin.invoices.index')
                         ->with('success', 'Фактурата е обновена успешно.');
    }

    /**
     * Изтриване на фактура
     */
    public function destroy(Invoice $invoice)
    {
        // Първо изтриваме редовете
        $invoice->items()->delete();

        // После самата фактура
        $invoice->delete();

        return redirect()->route('admin.invoices.index')
                         ->with('success', 'Фактурата е изтрита успешно.');
    }
}
