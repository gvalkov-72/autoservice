<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Exports\CustomerExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    /* ---------- CRUD ---------- */

    public function index(Request $request)
    {
        // Заявка с филтри
        $query = Customer::query();

        // Филтриране по търсене
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->search($search);
        }

        // Филтриране по тип клиент
        if ($request->filled('customer_type')) {
            $type = $request->input('customer_type');
            if ($type == 'customer') {
                $query->where('is_customer', true);
            } elseif ($type == 'supplier') {
                $query->where('is_supplier', true);
            }
        }

        // Филтриране по активност
        if ($request->filled('status')) {
            $query->where('is_active', $request->input('status') == 'active');
        }

        // Пагинация
        $customers = $query->orderBy('name')->paginate(20);

        return view('admin.customers.index', compact('customers'));
    }

    public function create()
    {
        return view('admin.customers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // Поля за миграция
            'old_id' => 'nullable|string|max:50|unique:customers,old_id',
            'customer_number' => 'nullable|string|max:50|unique:customers,customer_number',

            // Основни данни
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'mol' => 'nullable|string|max:255',
            'tax_number' => 'nullable|string|max:20',
            'bulstat' => 'nullable|string|max:13',
            'doc_type' => 'nullable|string|max:50',

            // Контакти
            'phone' => 'nullable|string|max:50',
            'fax' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',

            // Адреси
            'address' => 'nullable|string|max:500',
            'address_2' => 'nullable|string|max:500',
            'res_address_1' => 'nullable|string|max:255',
            'res_address_2' => 'nullable|string|max:255',

            // Получател
            'receiver' => 'nullable|string|max:255',
            'receiver_details' => 'nullable|string',

            // Допълнителни полета от Access
            'eidale' => 'nullable|string|max:50',
            'partida' => 'nullable|string|max:50',
            'bulsial_letter' => 'nullable|string|max:10',

            // Флагове
            'is_active' => 'boolean',
            'is_customer' => 'boolean',
            'is_supplier' => 'boolean',
            'include_in_mailing' => 'boolean',

            // Бележки
            'notes' => 'nullable|string',
        ]);

        // Автоматично генериране на customer_number, ако не е предоставен
        if (empty($validated['customer_number']) && !empty($validated['old_id'])) {
            $validated['customer_number'] = $validated['old_id'];
        }

        // Създаване на клиента
        $customer = Customer::create($validated);

        // Логване на действие
        activity()
            ->causedBy(Auth::user())
            ->performedOn($customer)
            ->log('Създаден нов клиент: ' . $customer->name);

        return redirect()
            ->route('admin.customers.show', $customer)
            ->with('success', 'Клиентът "' . $customer->name . '" е създаден успешно.');
    }

    public function show(Customer $customer)
    {
        $customer->load(['vehicles', 'workOrders' => function ($query) {
            $query->orderBy('received_at', 'desc')->limit(10);
        }, 'invoices' => function ($query) {
            $query->orderBy('created_at', 'desc')->limit(10);
        }]);

        // Статистики за този клиент (актуализирани за новите отношения)
        $customerStats = [
            'total_vehicles' => $customer->vehicles()->count(),
            'total_work_orders' => $customer->workOrders()->count(),
            'total_invoices' => $customer->invoices()->count(),
            'total_spent' => $customer->invoices()->sum('total_amount') + $customer->workOrders()->sum('total_amount'),
            'last_service' => $customer->workOrders()->latest()->first()?->received_at,
            'active_vehicles' => $customer->vehicles()->where('is_active', true)->count(),
        ];

        return view('admin.customers.show', compact('customer', 'customerStats'));
    }

    public function edit(Customer $customer)
    {
        return view('admin.customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            // Поля за миграция
            'old_id' => 'nullable|string|max:50|unique:customers,old_id,' . $customer->id,
            'customer_number' => 'nullable|string|max:50|unique:customers,customer_number,' . $customer->id,

            // Основни данни
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'mol' => 'nullable|string|max:255',
            'tax_number' => 'nullable|string|max:20',
            'bulstat' => 'nullable|string|max:13',
            'doc_type' => 'nullable|string|max:50',

            // Контакти
            'phone' => 'nullable|string|max:50',
            'fax' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',

            // Адреси
            'address' => 'nullable|string|max:500',
            'address_2' => 'nullable|string|max:500',
            'res_address_1' => 'nullable|string|max:255',
            'res_address_2' => 'nullable|string|max:255',

            // Получател
            'receiver' => 'nullable|string|max:255',
            'receiver_details' => 'nullable|string',

            // Допълнителни полета от Access
            'eidale' => 'nullable|string|max:50',
            'partida' => 'nullable|string|max:50',
            'bulsial_letter' => 'nullable|string|max:10',

            // Флагове
            'is_active' => 'boolean',
            'is_customer' => 'boolean',
            'is_supplier' => 'boolean',
            'include_in_mailing' => 'boolean',

            // Бележки
            'notes' => 'nullable|string',
        ]);

        // Запазване на старите данни за лог
        $oldData = $customer->toArray();

        // Актуализиране на клиента
        $customer->update($validated);

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
                ->performedOn($customer)
                ->withProperties(['changes' => $changes])
                ->log('Актуализиран клиент: ' . $customer->name);
        }

        return redirect()
            ->route('admin.customers.show', $customer)
            ->with('success', 'Данните на клиент "' . $customer->name . '" са актуализирани успешно.');
    }

    public function destroy(Customer $customer)
    {
        try {
            $customerName = $customer->name;
            $customer->delete();

            // Логване на действието
            activity()
                ->causedBy(Auth::user())
                ->performedOn($customer)
                ->log('Деактивиран клиент: ' . $customerName);
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('admin.customers.index')
                ->with('error', 'Не може да изтриете клиента, защото съществуват свързани записи (автомобили, поръчки, фактури).');
        }
        return redirect()->route('admin.customers.index')
            ->with('success', 'Клиентът "' . $customerName . '" е деактивиран успешно.');
    }

    public function restore($id)
    {
        $customer = Customer::withTrashed()->findOrFail($id);
        $customer->restore();

        // Логване на действието
        activity()
            ->causedBy(Auth::user())
            ->performedOn($customer)
            ->log('Възстановен клиент: ' . $customer->name);

        return redirect()->route('admin.customers.index')
            ->with('success', 'Клиентът "' . $customer->name . '" е възстановен успешно.');
    }

    public function forceDelete($id)
    {
        $customer = Customer::withTrashed()->findOrFail($id);
        $customerName = $customer->name;

        // Проверка за свързани записи
        if (
            $customer->vehicles()->count() > 0 ||
            $customer->workOrders()->count() > 0 ||
            $customer->invoices()->count() > 0
        ) {
            return redirect()->route('admin.customers.index')
                ->with('error', 'Не може да изтриете клиента окончателно, защото има свързани записи.');
        }

        $customer->forceDelete();

        // Логване на действието
        activity()
            ->causedBy(Auth::user())
            ->log('Изтрит окончателно клиент: ' . $customerName);

        return redirect()->route('admin.customers.index')
            ->with('success', 'Клиентът "' . $customerName . '" е изтрит окончателно от системата.');
    }

    public function bulkAction(Request $request)
    {
        $action = $request->input('action');
        $selectedIds = $request->input('selected_ids', []);

        if (empty($selectedIds)) {
            return back()->with('error', 'Не сте избрани клиенти за действие.');
        }

        $customers = Customer::whereIn('id', $selectedIds)->get();

        switch ($action) {
            case 'activate':
                $customers->each(function ($customer) {
                    $customer->update(['is_active' => true]);
                });
                $message = count($selectedIds) . ' клиенти бяха активирани.';
                break;

            case 'deactivate':
                $customers->each(function ($customer) {
                    $customer->update(['is_active' => false]);
                });
                $message = count($selectedIds) . ' клиенти бяха деактивирани.';
                break;

            case 'make_customer':
                $customers->each(function ($customer) {
                    $customer->update(['is_customer' => true]);
                });
                $message = count($selectedIds) . ' клиенти бяха маркирани като клиенти.';
                break;

            case 'make_supplier':
                $customers->each(function ($customer) {
                    $customer->update(['is_supplier' => true]);
                });
                $message = count($selectedIds) . ' клиенти бяха маркирани като доставчици.';
                break;

            case 'include_in_mailing':
                $customers->each(function ($customer) {
                    $customer->update(['include_in_mailing' => true]);
                });
                $message = count($selectedIds) . ' клиенти бяха включени в бюлетин.';
                break;

            case 'exclude_from_mailing':
                $customers->each(function ($customer) {
                    $customer->update(['include_in_mailing' => false]);
                });
                $message = count($selectedIds) . ' клиенти бяха изключени от бюлетин.';
                break;

            case 'export':
                // Препратка към експорт метод
                return $this->bulkExport($selectedIds);

            default:
                return back()->with('error', 'Невалидно действие.');
        }

        // Логване на групово действие
        activity()
            ->causedBy(Auth::user())
            ->withProperties(['action' => $action, 'count' => count($selectedIds)])
            ->log('Групово действие върху клиенти: ' . $action);

        return back()->with('success', $message);
    }

    /* ---------- EXPORT ---------- */

    public function exportPdf(Customer $customer)
    {
        $customer->load(['vehicles', 'workOrders' => function ($query) {
            $query->orderBy('received_at', 'desc')->limit(20);
        }]);

        $copy = request()->get('copy', 0);

        return Pdf::loadView('admin.customers.export-pdf', compact('customer', 'copy'))
            ->setPaper('a4', 'portrait')
            ->stream('customer_' . $customer->id . '.pdf');
    }

    public function exportExcel(Customer $customer)
    {
        return Excel::download(
            new CustomerExport($customer),
            'customer_' . $customer->id . '.xlsx'
        );
    }

    public function exportCsv(Customer $customer)
    {
        return Excel::download(
            new CustomerExport($customer),
            'customer_' . $customer->id . '.csv',
            \Maatwebsite\Excel\Excel::CSV
        );
    }

    public function exportAll(Request $request)
    {
        $query = Customer::query();

        // Прилагане на филтри от заявката
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->search($search);
        }

        if ($request->filled('customer_type')) {
            $type = $request->input('customer_type');
            if ($type == 'customer') {
                $query->where('is_customer', true);
            } elseif ($type == 'supplier') {
                $query->where('is_supplier', true);
            }
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->input('status') == 'active');
        }

        $customers = $query->get();

        if ($customers->isEmpty()) {
            return back()->with('error', 'Няма клиенти за експорт с текущите филтри.');
        }

        // Създаване на експорт за множество клиенти
        $export = new CustomerExport($customers);
        $fileName = 'customers_export_' . now()->format('Ymd_His') . '.xlsx';

        // Логване на експорта
        activity()
            ->causedBy(Auth::user())
            ->withProperties(['count' => $customers->count(), 'filters' => $request->except('_token')])
            ->log('Експортирани всички клиенти');

        return Excel::download($export, $fileName);
    }

    public function exportAllPdf(Request $request)
    {
        $query = Customer::query();

        // Прилагане на филтри
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->search($search);
        }

        if ($request->filled('customer_type')) {
            $type = $request->input('customer_type');
            if ($type == 'customer') {
                $query->where('is_customer', true);
            } elseif ($type == 'supplier') {
                $query->where('is_supplier', true);
            }
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->input('status') == 'active');
        }

        $customers = $query->get();

        if ($customers->isEmpty()) {
            return back()->with('error', 'Няма клиенти за експорт с текущите филтри.');
        }

        $pdf = Pdf::loadView('admin.customers.export-all-pdf', compact('customers'))
            ->setPaper('a4', 'landscape')
            ->setOption('margin-top', 10)
            ->setOption('margin-bottom', 10)
            ->setOption('margin-left', 10)
            ->setOption('margin-right', 10);

        return $pdf->stream('customers_export_' . now()->format('Ymd_His') . '.pdf');
    }

    /* ---------- ДОПЪЛНИТЕЛНИ МЕТОДИ ---------- */

    private function bulkExport($selectedIds)
    {
        $customers = Customer::whereIn('id', $selectedIds)->get();
        
        if ($customers->isEmpty()) {
            return back()->with('error', 'Няма избрани клиенти за експорт.');
        }

        $export = new CustomerExport($customers);
        $fileName = 'customers_selected_' . now()->format('Ymd_His') . '.xlsx';

        activity()
            ->causedBy(Auth::user())
            ->withProperties(['count' => $customers->count()])
            ->log('Експортирани избрани клиенти');

        return Excel::download($export, $fileName);
    }
}