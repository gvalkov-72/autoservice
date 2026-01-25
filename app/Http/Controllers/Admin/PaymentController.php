<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;

class PaymentController extends Controller
{
    /**
     * Запис на плащане към фактура
     */
    public function store(Request $request, Invoice $invoice)
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'paid_at' => ['required', 'date'],
            'payment_method_id' => ['required', 'exists:payment_methods,id'],
            'bank_id' => ['nullable', 'exists:banks,id'],
            'reference' => ['nullable', 'string', 'max:255'],
        ]);

        // Защита от надплащане
        if ($data['amount'] > $invoice->remaining_amount) {
            return back()->withErrors([
                'amount' => 'Сумата надвишава оставащата за плащане по фактурата.',
            ]);
        }

        Payment::create([
            'invoice_id' => $invoice->id,
            'amount' => $data['amount'],
            'paid_at' => $data['paid_at'],
            'payment_method_id' => $data['payment_method_id'],
            'bank_id' => $data['bank_id'] ?? null,
            'reference' => $data['reference'] ?? null,
            'created_by' => Auth::id(),
        ]);

        // ❗ НЯМА update на invoice тук
        // Това става автоматично в Payment model

        return back()->with('success', 'Плащането е добавено успешно.');
    }

    /**
     * Изтриване на плащане
     */
    public function destroy(Payment $payment): RedirectResponse
    {
        $invoice = $payment->invoice;

        $payment->delete(); // 🔥 refreshPaymentStatus() се вика автоматично

        return redirect()
            ->route('admin.invoices.show', $invoice)
            ->with('success', 'Плащането беше изтрито успешно.');
    }
}
