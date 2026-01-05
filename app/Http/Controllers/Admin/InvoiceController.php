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

        /**
         * =========================
         * БЪРЗО ТЪРСЕНЕ
         * =========================
         * Търси по:
         * - номер на фактура
         * - клиент (име, телефон, имейл)
         */
        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($qc) use ($search) {
                      $qc->where('name', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        /**
         * =========================
         * ФИЛТЪР ПО СТАТУС
         * =========================
         * paid | unpaid | cancelled
         */
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        /**
         * =========================
         * ФИЛТЪР ПО ПЛАЩАНЕ
         * =========================
         * cash | card | bank
         */
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        /**
         * =========================
         * ПАГИНАЦИЯ
         * =========================
         * СЪЩАТА като Customers
         */
        $invoices = $query
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

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
