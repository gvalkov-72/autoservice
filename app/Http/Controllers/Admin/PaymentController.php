<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with(['invoice.customer', 'creator'])->latest()->paginate(25);
        return view('admin.payments.index', compact('payments'));
    }

    public function create(?Invoice $invoice = null)
    {
        $invoices = Invoice::doesntHave('payments')->pluck('number', 'id');
        return view('admin.payments.create', compact('invoice', 'invoices'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'amount'     => 'required|numeric|min:0.01',
            'method'     => 'required|in:cash,card,bank',
            'paid_at'    => 'required|date',
            'reference'  => 'nullable|string|max:50',
        ]);

        $payment = Payment::create([
            'invoice_id'  => $request->invoice_id,
            'amount'      => $request->amount,
            'method'      => $request->method,
            'paid_at'     => $request->paid_at,
            'reference'   => $request->reference,
            'created_by'  => Auth::check() ? Auth::user()->id : 0,
        ]);

        $invoice = $payment->invoice;
        if ($invoice->payments()->sum('amount') >= $invoice->grand_total) {
            $invoice->update(['status' => 'paid']);
        }

        return redirect()->route('admin.payments.index')->with('success', 'Плащането е записано.');
    }

    public function pdf(Payment $payment)
    {
        $copy = request()->get('copy', 0);
        return Pdf::loadView('admin.payments.receipt', compact('payment', 'copy'))
                  ->stream('receipt_'.$payment->id.'.pdf');
    }
}