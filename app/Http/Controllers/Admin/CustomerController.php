<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Exports\CustomerExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /* ---------- CRUD ---------- */

    public function index()
    {
        $customers = Customer::paginate(25);
        return view('admin.customers.index', compact('customers'));
    }

    public function create()
    {
        return view('admin.customers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type'           => 'required|in:company,individual',
            'name'           => 'required|string|max:255',
            'vat_number'     => 'nullable|string|max:20',
            'contact_person' => 'nullable|string|max:255',
            'phone'          => 'nullable|string|max:50',
            'email'          => 'nullable|email|max:255',
            'address'        => 'nullable|string|max:500',
            'city'           => 'nullable|string|max:255',
            'notes'          => 'nullable|string',
        ]);

        Customer::create($validated);

        return redirect()->route('admin.customers.index')->with('success', 'Клиентът е добавен.');
    }

    public function show(Customer $customer)
    {
        $customer->load(['vehicles', 'workOrders']);
        return view('admin.customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        return view('admin.customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'type'           => 'required|in:company,individual',
            'name'           => 'required|string|max:255',
            'vat_number'     => 'nullable|string|max:20',
            'contact_person' => 'nullable|string|max:255',
            'phone'          => 'nullable|string|max:50',
            'email'          => 'nullable|email|max:255',
            'address'        => 'nullable|string|max:500',
            'city'           => 'nullable|string|max:255',
            'notes'          => 'nullable|string',
        ]);

        $customer->update($validated);

        return redirect()->route('admin.customers.index')->with('success', 'Клиентът е обновен.');
    }

    public function destroy(Customer $customer)
    {
        try {
            $customer->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('admin.customers.index')
                             ->with('error','Не може да изтриете клиента, защото съществуват свързани записи.');
        }
        return redirect()->route('admin.customers.index')
                         ->with('success','Клиентът е деактивиран.');
    }

    /* ---------- EXPORT ---------- */

    public function exportPdf(Customer $customer)
    {
        $customer->load(['vehicles', 'workOrders']);
        $copy = request()->get('copy', 0);
        return Pdf::loadView('admin.customers.export-pdf', compact('customer', 'copy'))
                  ->stream('customer_'.$customer->id.'.pdf');
    }

    public function exportExcel(Customer $customer)
    {
        return Excel::download(new CustomerExport($customer), 'customer_'.$customer->id.'.xlsx');
    }

    public function exportCsv(Customer $customer)
    {
        return Excel::download(new CustomerExport($customer), 'customer_'.$customer->id.'.csv', \Maatwebsite\Excel\Excel::CSV);
    }
}