<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Customer;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{

/**
 * Списък с фактури
 */
public function index(Request $request)
{
    // Основна заявка
    $query = Invoice::query()
        ->with('customer')
        ->whereNull('deleted_at');

    // ТЪРСЕНЕ (започва с)
    if ($request->filled('search')) {
        $search = trim($request->search);

        $query->where(function ($q) use ($search) {
            $q->where('invoice_number', 'like', "{$search}%")
              ->orWhereHas('customer', function ($qc) use ($search) {
                  $qc->where('name', 'like', "{$search}%")
                     ->orWhere('phone', 'like', "{$search}%")
                     ->orWhere('email', 'like', "{$search}%");
              });
        });
    }

    // ФИЛТЪР ПО АКТИВНОСТ (is_active) - ФИКСИРАНО!
    if ($request->filled('is_active_filter')) {
        if ($request->is_active_filter == 'active') {
            $query->whereNotIn('status', ['voided', 'cancelled']);
        } elseif ($request->is_active_filter == 'inactive') {
            $query->whereIn('status', ['voided', 'cancelled']);
        }
    }

    // ФИЛТЪР ПО ПЛАЩАНЕ (payment_status) - ФИКСИРАНО!
    if ($request->filled('payment_filter')) {
        if ($request->payment_filter == 'paid') {
            // Платени: само 'paid'
            $query->where('payment_status', 'paid');
        } elseif ($request->payment_filter == 'unpaid') {
            // Неплатени: всичко РАЗЛИЧНО от 'paid'
            $query->where(function($q) {
                $q->where('payment_status', '!=', 'paid')
                  ->orWhereNull('payment_status');
            });
        }
    }

    // Стари филтри за съвместимост
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    if ($request->filled('payment_status')) {
        $query->where('payment_status', $request->payment_status);
    }

    $invoices = $query
        ->orderByDesc('created_at')
        ->paginate(20);

    // AJAX заявка
    if ($request->ajax() || $request->has('ajax')) {
        return view('admin.invoices.partials.table', compact('invoices'));
    }

    return view('admin.invoices.index', compact('invoices'));
}

    /**
     * Създаване на фактура
     */
    public function create()
    {
        $customers = Customer::orderBy('name')->get();

        return view('admin.invoices.create', compact('customers'));
    }

    /**
     * Запис на фактура
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id'     => 'required|exists:customers,id',
            'invoice_number'  => 'required|string|max:50',
            'status'          => 'required|string',
            'payment_status'  => 'required|string',
            'total'           => 'required|numeric|min:0',
            'notes'           => 'nullable|string',
        ]);

        Invoice::create($data);

        return redirect()
            ->route('admin.invoices.index')
            ->with('success', 'Фактурата беше създадена успешно.');
    }

    /**
     * Преглед на фактура
     */
    public function show(Invoice $invoice)
    {
        $invoice->load('customer');

        return view('admin.invoices.show', compact('invoice'));
    }

    /**
     * Редакция на фактура
     */
    public function edit(Invoice $invoice)
    {
        $customers = Customer::orderBy('name')->get();

        return view('admin.invoices.edit', compact('invoice', 'customers'));
    }

    /**
     * Обновяване на фактура
     */
    public function update(Request $request, Invoice $invoice)
    {
        $data = $request->validate([
            'customer_id'     => 'required|exists:customers,id',
            'invoice_number'  => 'required|string|max:50',
            'status'          => 'required|string',
            'payment_status'  => 'required|string',
            'total'           => 'required|numeric|min:0',
            'notes'           => 'nullable|string',
        ]);

        $invoice->update($data);

        return redirect()
            ->route('admin.invoices.index')
            ->with('success', 'Фактурата беше обновена успешно.');
    }

    /**
     * Изтриване (soft delete)
     */
    public function destroy(Invoice $invoice)
    {
        $invoice->delete();

        return redirect()
            ->route('admin.invoices.index')
            ->with('success', 'Фактурата беше деактивирана.');
    }

    /**
     * =========================
     * ЕКСПОРТ В PDF
     * =========================
     * Използва InvoicePdfController
     */
    public function pdf(Invoice $invoice)
    {
        return app(InvoicePdfController::class)->show($invoice);
    }
}
