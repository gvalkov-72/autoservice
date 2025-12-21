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
            // Основна информация
            'old_system_id' => 'nullable|string|max:50',
            'type' => 'required|in:customer,supplier,both',
            'name' => 'required|string|max:255',
            'vat_number' => 'nullable|string|max:20',
            'bulstat' => 'nullable|string|max:13',
            'contact_person' => 'nullable|string|max:255',
            // Контакти
            'phone' => 'nullable|string|max:50',
            'fax' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            // Адрес
            'address' => 'nullable|string|max:500',
            'address_line1' => 'nullable|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            // Допълнителна информация
            'notes' => 'nullable|string',
            'court_registration' => 'nullable|string|max:100',
            'bulstat_letter' => 'nullable|string|max:1',
            // Флагове
            'is_active' => 'boolean',
            'include_in_reports' => 'boolean',
        ]);

        // Пълнене на комбинирания адрес, ако е празен
        if (empty($validated['address']) && (!empty($validated['address_line1']) || !empty($validated['address_line2']))) {
            $validated['address'] = trim($validated['address_line1'] . ', ' . $validated['address_line2'], ', ');
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

        // Статистики за този клиент
        $customerStats = [
            'total_vehicles' => $customer->vehicles()->count(),
            'total_work_orders' => $customer->workOrders()->count(),
            'total_invoices' => $customer->invoices()->count(),
            'total_spent' => $customer->workOrders()->sum('total'),
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
            // Основна информация
            'old_system_id' => 'nullable|string|max:50',
            'type' => 'required|in:customer,supplier,both',
            'name' => 'required|string|max:255',
            'vat_number' => 'nullable|string|max:20',
            'bulstat' => 'nullable|string|max:13',
            'contact_person' => 'nullable|string|max:255',
            // Контакти
            'phone' => 'nullable|string|max:50',
            'fax' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            // Адрес
            'address' => 'nullable|string|max:500',
            'address_line1' => 'nullable|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            // Допълнителна информация
            'notes' => 'nullable|string',
            'court_registration' => 'nullable|string|max:100',
            'bulstat_letter' => 'nullable|string|max:1',
            // Флагове
            'is_active' => 'boolean',
            'include_in_reports' => 'boolean',
        ]);

        // Пълнене на комбинирания адрес, ако е празен
        if (empty($validated['address']) && (!empty($validated['address_line1']) || !empty($validated['address_line2']))) {
            $validated['address'] = trim($validated['address_line1'] . ', ' . $validated['address_line2'], ', ');
        }

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
            return back()->with('error', 'Не сте избрали клиенти за действие.');
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

            case 'include_in_reports':
                $customers->each(function ($customer) {
                    $customer->update(['include_in_reports' => true]);
                });
                $message = count($selectedIds) . ' клиенти бяха включени в справки.';
                break;

            case 'exclude_from_reports':
                $customers->each(function ($customer) {
                    $customer->update(['include_in_reports' => false]);
                });
                $message = count($selectedIds) . ' клиенти бяха изключени от справки.';
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
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('phone', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
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
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('phone', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
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

        // Логване на експорта
        activity()
            ->causedBy(Auth::user())
            ->withProperties(['count' => $customers->count()])
            ->log('Експортирани всички клиенти в PDF');

        return $pdf->download('customers_list_' . now()->format('Ymd_His') . '.pdf');
    }

    protected function bulkExport(array $customerIds)
    {
        $customers = Customer::whereIn('id', $customerIds)->get();

        if ($customers->isEmpty()) {
            return back()->with('error', 'Няма клиенти за експорт.');
        }

        // Създаване на експорт за избрани клиенти
        $export = new CustomerExport($customers);
        $fileName = 'customers_selected_' . now()->format('Ymd_His') . '.xlsx';

        // Логване на експорта
        activity()
            ->causedBy(Auth::user())
            ->withProperties(['count' => $customers->count(), 'ids' => $customerIds])
            ->log('Експортирани избрани клиенти');

        return Excel::download($export, $fileName);
    }

    /* ---------- API / AJAX ---------- */

    public function search(Request $request)
    {
        $search = $request->input('q', '');

        $customers = Customer::where('name', 'LIKE', "%{$search}%")
            ->orWhere('phone', 'LIKE', "%{$search}%")
            ->orWhere('email', 'LIKE', "%{$search}%")
            ->orWhere('vat_number', 'LIKE', "%{$search}%")
            ->limit(10)
            ->get(['id', 'name', 'phone', 'email', 'type']);

        $results = $customers->map(function ($customer) {
            return [
                'id' => $customer->id,
                'text' => $customer->name .
                    ($customer->phone ? ' | Тел: ' . $customer->phone : '') .
                    ($customer->email ? ' | Email: ' . $customer->email : ''),
                'type' => $customer->type,
                'phone' => $customer->phone,
                'email' => $customer->email,
            ];
        });

        return response()->json(['results' => $results]);
    }

    public function quickView($id)
    {
        $customer = Customer::with(['vehicles' => function ($query) {
            $query->limit(3);
        }])->findOrFail($id);

        return response()->json([
            'success' => true,
            'html' => view('admin.customers.partials.quick-view', compact('customer'))->render()
        ]);
    }

    /* ---------- ДОПЪЛНИТЕЛНИ МЕТОДИ ---------- */

    public function deleted()
    {
        $customers = Customer::onlyTrashed()->paginate(20);

        return view('admin.customers.deleted', compact('customers'));
    }

    public function toggleStatus(Customer $customer)
    {
        $customer->update(['is_active' => !$customer->is_active]);

        $status = $customer->is_active ? 'активиран' : 'деактивиран';

        // Логване
        activity()
            ->causedBy(Auth::user())
            ->performedOn($customer)
            ->log('Променен статус на клиент: ' . $customer->name . ' (' . $status . ')');

        return back()->with('success', 'Статусът на клиент "' . $customer->name . '" е променен на ' . $status . '.');
    }

    public function duplicate(Customer $customer)
    {
        $newCustomer = $customer->replicate();
        $newCustomer->name = $customer->name . ' (Копие)';
        $newCustomer->created_at = now();
        $newCustomer->updated_at = now();
        $newCustomer->save();

        // Логване
        activity()
            ->causedBy(Auth::user())
            ->performedOn($newCustomer)
            ->log('Създадено копие на клиент: ' . $customer->name);

        return redirect()
            ->route('admin.customers.edit', $newCustomer)
            ->with('success', 'Копие на клиент "' . $customer->name . '" е създадено успешно. Можете да редактирате данните.');
    }
}
