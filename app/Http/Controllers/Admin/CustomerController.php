<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Vehicle;
use App\Models\WorkOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class CustomerController extends Controller
{
    /**
     * ТЪРСЕНЕ НА КЛИЕНТИ – ИЗПОЛЗВА СЕ ОТ РАБОТНИ ПОРЪЧКИ (Select2)
     * ⚠️ НЕ ПРОМЕНЯЙ! ⚠️
     */
    public function search(Request $request)
    {
        try {
            $query = trim($request->get('q'));

            if (strlen($query) < 2) {
                return response()->json([]);
            }

            $customers = Customer::query()
                ->where(function ($qry) use ($query) {
                    $qry->where('name', 'like', "{$query}%")
                        ->orWhere('phone', 'like', "{$query}%")
                        ->orWhere('customer_number', 'like', "{$query}%")
                        ->orWhere('name', 'like', "%{$query}%");
                })
                ->orderByRaw("CASE WHEN name LIKE '{$query}%' THEN 0 ELSE 1 END")
                ->orderBy('name', 'asc')
                ->orderBy('id', 'asc')
                ->limit(50)
                ->get(['id', 'old_id', 'name', 'phone', 'customer_number']);

            return response()->json($customers);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /* ------------------------------------------------------------------
       CRUD ОПЕРАЦИИ ЗА КЛИЕНТИ
    ------------------------------------------------------------------ */

    /**
     * СПИСЪК НА КЛИЕНТИ – нормално зареждане (paginate)
     */
    public function index(Request $request)
    {
        $customers = Customer::with('vehicles')
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('customer_number', 'like', "%{$search}%")
                        ->orWhere('bulstat', 'like', "%{$search}%")
                        ->orWhere('tax_number', 'like', "%{$search}%");
                });
            })
            ->when(isset($request->is_active), function ($query) use ($request) {
                $query->where('is_active', $request->is_active);
            })
            ->when($request->type, function ($query, $type) {
                if ($type === 'customer') {
                    $query->where('is_customer', true);
                } elseif ($type === 'supplier') {
                    $query->where('is_supplier', true);
                }
            })
            ->orderBy('name', 'asc')
            ->paginate(15)
            ->withQueryString();

        return view('admin.customers.index', [
            'customers'  => $customers,
            'search'     => $request->search,
            'is_active'  => $request->is_active,
            'type'       => $request->type,
        ]);
    }

    /**
     * LIVE ТЪРСЕНЕ – връща само partials/rows за AJAX заявки
     */
    public function liveSearch(Request $request)
    {
        $customers = Customer::with('vehicles')
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('customer_number', 'like', "%{$search}%")
                        ->orWhere('bulstat', 'like', "%{$search}%")
                        ->orWhere('tax_number', 'like', "%{$search}%");
                });
            })
            ->when(isset($request->is_active), function ($query) use ($request) {
                $query->where('is_active', $request->is_active);
            })
            ->when($request->type, function ($query, $type) {
                if ($type === 'customer') {
                    $query->where('is_customer', true);
                } elseif ($type === 'supplier') {
                    $query->where('is_supplier', true);
                }
            })
            ->orderBy('name', 'asc')
            ->paginate(15);

        $html = view('admin.customers.partials.rows', compact('customers'))->render();

        return response()->json([
            'html'  => $html,
            'total' => $customers->total()
        ]);
    }

    /**
     * ФОРМА ЗА НОВ КЛИЕНТ
     */
    public function create()
    {
        return view('admin.customers.create');
    }

    /**
     * ЗАПИС НА НОВ КЛИЕНТ + АВТОМОБИЛИ
     */
    public function store(Request $request)
    {
        // ⚡ ЗАДАВАМЕ СТОЙНОСТИ ПО ПОДРАЗБИРАНЕ ЗА БУЛЕВИТЕ ПОЛЕТА
        $request->merge([
            'include_in_mailing' => $request->has('include_in_mailing') ? 1 : 0,
            'is_active'          => $request->has('is_active') ? 1 : 0,
            'is_customer'        => $request->has('is_customer') ? 1 : 0,
            'is_supplier'        => $request->has('is_supplier') ? 1 : 0,
        ]);

        $validated = $request->validate([
            'customer_number'  => 'nullable|string|max:255',
            'name'            => 'required|string|max:255',
            'email'           => 'nullable|email|max:255',
            'phone'           => 'nullable|string|max:255',
            'fax'             => 'nullable|string|max:255',
            'address'         => 'nullable|string|max:255',
            'address_2'       => 'nullable|string|max:255',
            'res_address_1'   => 'nullable|string|max:255',
            'res_address_2'   => 'nullable|string|max:255',
            'mol'             => 'nullable|string|max:255',
            'contact_person'  => 'nullable|string|max:255',
            'tax_number'      => 'nullable|string|max:255',
            'bulstat'         => 'nullable|string|max:255',
            'bulstat_letter'  => 'nullable|string|max:255',
            'doc_type'        => 'nullable|string|max:255',
            'receiver'        => 'nullable|string|max:255',
            'receiver_details' => 'nullable|string',
            'eidate'          => 'nullable|date',
            'partida'         => 'nullable|string|max:255',
            'notes'           => 'nullable|string',
            'include_in_mailing' => 'required|boolean',
            'is_active'          => 'required|boolean',
            'is_customer'        => 'required|boolean',
            'is_supplier'        => 'required|boolean',
            'vehicles'        => 'nullable|array',
            'vehicles.*.vehicle'        => 'nullable|string|max:255',
            'vehicles.*.plate_number'   => 'nullable|string|max:255',
            'vehicles.*.chassis_number' => 'nullable|string|max:255',
            'vehicles.*.last_mileage'   => 'nullable|integer',
            'vehicles.*.notes'          => 'nullable|string|max:255',
            'vehicles.*.is_active'      => 'sometimes|boolean',
        ]);

        // ⚡ ВЕЧЕ НЕ НУЖДАЕМ FROM $request->has() – стойностите са във $validated
        DB::beginTransaction();
        try {
            $customer = Customer::create($validated);

            if ($request->has('vehicles')) {
                $this->syncVehicles($customer, $request->vehicles);
            }

            DB::commit();
            return redirect()
                ->route('admin.customers.show', $customer->id)
                ->with('success', 'Клиентът е създаден успешно.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Грешка при запис: ' . $e->getMessage());
        }
    }

    /**
     * ДЕТАЙЛЕН ПРЕГЛЕД НА КЛИЕНТ (с автомобили и работни поръчки)
     */
    public function show(Customer $customer)
    {
        $customer->load('vehicles');

        $workOrders = WorkOrder::where('customer_id', $customer->id)
            ->orderBy('order_date', 'desc')
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();

        return view('admin.customers.show', compact('customer', 'workOrders'));
    }

    /**
     * ФОРМА ЗА РЕДАКТИРАНЕ
     */
    public function edit(Customer $customer)
    {
        $customer->load('vehicles');
        return view('admin.customers.edit', compact('customer'));
    }

    /**
     * ОБНОВЯВАНЕ НА КЛИЕНТ + АВТОМОБИЛИ
     */
    public function update(Request $request, Customer $customer)
    {
        // ⚡ ЗАДАВАМЕ СТОЙНОСТИ ПО ПОДРАЗБИРАНЕ ЗА БУЛЕВИТЕ ПОЛЕТА
        $request->merge([
            'include_in_mailing' => $request->has('include_in_mailing') ? 1 : 0,
            'is_active'          => $request->has('is_active') ? 1 : 0,
            'is_customer'        => $request->has('is_customer') ? 1 : 0,
            'is_supplier'        => $request->has('is_supplier') ? 1 : 0,
        ]);

        $validated = $request->validate([
            'old_id'           => 'nullable|string|max:255',
            'customer_number'  => 'nullable|string|max:255',
            'name'            => 'required|string|max:255',
            'email'           => 'nullable|email|max:255',
            'phone'           => 'nullable|string|max:255',
            'fax'             => 'nullable|string|max:255',
            'address'         => 'nullable|string|max:255',
            'address_2'       => 'nullable|string|max:255',
            'res_address_1'   => 'nullable|string|max:255',
            'res_address_2'   => 'nullable|string|max:255',
            'mol'             => 'nullable|string|max:255',
            'contact_person'  => 'nullable|string|max:255',
            'tax_number'      => 'nullable|string|max:255',
            'bulstat'         => 'nullable|string|max:255',
            'bulstat_letter'  => 'nullable|string|max:255',
            'doc_type'        => 'nullable|string|max:255',
            'receiver'        => 'nullable|string|max:255',
            'receiver_details' => 'nullable|string',
            'eidate'          => 'nullable|date',
            'partida'         => 'nullable|string|max:255',
            'notes'           => 'nullable|string',
            'include_in_mailing' => 'required|boolean',
            'is_active'          => 'required|boolean',
            'is_customer'        => 'required|boolean',
            'is_supplier'        => 'required|boolean',
            'vehicles'        => 'nullable|array',
            'vehicles.*.id'              => 'nullable|integer|exists:vehicles,id',
            'vehicles.*.vehicle'         => 'nullable|string|max:255',
            'vehicles.*.plate_number'    => 'nullable|string|max:255',
            'vehicles.*.chassis_number'  => 'nullable|string|max:255',
            'vehicles.*.last_mileage'    => 'nullable|integer',
            'vehicles.*.notes'           => 'nullable|string|max:255',
            'vehicles.*.is_active'       => 'sometimes|boolean',
            'vehicles.*._delete'         => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $customer->update($validated);

            if ($request->has('vehicles')) {
                $this->syncVehicles($customer, $request->vehicles);
            }

            DB::commit();
            return redirect()
                ->route('admin.customers.show', $customer->id)
                ->with('success', 'Клиентът е обновен успешно.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Грешка при обновяване: ' . $e->getMessage());
        }
    }


    /**
     * ИЗТРИВАНЕ НА КЛИЕНТ
     */
    public function destroy(Customer $customer)
    {
        try {
            DB::beginTransaction();
            $customer->vehicles()->delete();
            $customer->delete();
            DB::commit();

            return redirect()
                ->route('admin.customers.index')
                ->with('success', 'Клиентът е изтрит успешно.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->with('error', 'Грешка при изтриване: ' . $e->getMessage());
        }
    }

    /**
     * ПЕЧАТ НА КЛИЕНТ
     */
    public function print(Customer $customer)
    {
        $customer->load('vehicles');

        $workOrders = WorkOrder::where('customer_id', $customer->id)
            ->orderBy('order_date', 'desc')
            ->orderBy('id', 'desc')
            ->limit(20)
            ->get();

        return view('admin.customers.print', compact('customer', 'workOrders'));
    }


    /**
     * ГЕНЕРИРАНЕ НА PDF ЗА КЛИЕНТ – ПОКАЗВАНЕ В БРАУЗЪРА
     */
    public function pdf(Customer $customer)
    {
        $customer->load('vehicles');

        $workOrders = WorkOrder::where('customer_id', $customer->id)
            ->orderBy('order_date', 'desc')
            ->orderBy('id', 'desc')
            ->limit(20)
            ->get();

        $pdf = Pdf::loadView('admin.customers.pdf', compact('customer', 'workOrders'));

        // ⚡ СЪЩОТО КАТО ПРИ РАБОТНИТЕ ПОРЪЧКИ – просто и сигурно
        return $pdf->stream('client-' . $customer->id . '.pdf');
    }

    /* ------------------------------------------------------------------
       ПОМОЩНИ МЕТОДИ
    ------------------------------------------------------------------ */

    private function syncVehicles(Customer $customer, array $vehicleRows)
    {
        $submittedIds = [];

        foreach ($vehicleRows as $row) {
            if (empty($row['vehicle']) && empty($row['plate_number']) && empty($row['chassis_number'])) {
                continue;
            }

            $data = [
                'vehicle'        => $row['vehicle'] ?? null,
                'plate_number'   => $row['plate_number'] ?? null,
                'chassis_number' => $row['chassis_number'] ?? null,
                'last_mileage'   => $row['last_mileage'] ?? null,
                'notes'          => $row['notes'] ?? null,
                'is_active'      => isset($row['is_active']) ? (bool)$row['is_active'] : true,
            ];

            if (!empty($row['id'])) {
                $vehicle = Vehicle::where('id', $row['id'])
                    ->where('customer_id', $customer->id)
                    ->first();

                if ($vehicle) {
                    if (!empty($row['_delete'])) {
                        $vehicle->delete();
                        continue;
                    }
                    $vehicle->update($data);
                    $submittedIds[] = $vehicle->id;
                }
            } else {
                if (empty($row['_delete'])) {
                    $vehicle = $customer->vehicles()->create($data);
                    $submittedIds[] = $vehicle->id;
                }
            }
        }
    }
}
