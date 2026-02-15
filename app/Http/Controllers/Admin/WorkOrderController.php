<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WorkOrder;
use App\Models\WorkOrderItem;
use App\Models\Vehicle;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class WorkOrderController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        $query = WorkOrder::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('client_name', 'like', "%{$search}%")
                        ->orWhere('vehicle', 'like', "%{$search}%")
                        ->orWhere('plate_number', 'like', "%{$search}%")
                        ->orWhere('chassis_number', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('old_id', 'like', "%{$search}%");
                });
            })
            ->when($dateFrom, function ($query) use ($dateFrom) {
                $query->whereDate('order_date', '>=', $dateFrom);
            })
            ->when($dateTo, function ($query) use ($dateTo) {
                $query->whereDate('order_date', '<=', $dateTo);
            })
            ->orderByDesc('id');

        $perPage = 25;
        $total = $query->count();

        if ($total > $perPage) {
            $workOrders = $query->paginate($perPage)->withQueryString();
            $needsPagination = true;
        } else {
            $workOrders = $query->limit($perPage)->get();
            $needsPagination = false;
        }

        return view('admin.work-orders.index', compact('workOrders', 'search', 'dateFrom', 'dateTo', 'needsPagination'));
    }

    public function search(Request $request)
    {
        $q = trim($request->get('q'));
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        if (mb_strlen($q) < 1 && !$dateFrom && !$dateTo) {
            return response()->json([]);
        }

        $orders = WorkOrder::query()
            ->when($q, function ($query) use ($q) {
                $query->where(function ($qry) use ($q) {
                    $qry->where('client_name', 'like', "{$q}%")
                        ->orWhere('vehicle', 'like', "{$q}%")
                        ->orWhere('plate_number', 'like', "{$q}%")
                        ->orWhere('chassis_number', 'like', "{$q}%")
                        ->orWhere('phone', 'like', "{$q}%")
                        ->orWhere('old_id', 'like', "{$q}%");
                });
            })
            ->when($dateFrom, function ($query) use ($dateFrom) {
                $query->whereDate('order_date', '>=', $dateFrom);
            })
            ->when($dateTo, function ($query) use ($dateTo) {
                $query->whereDate('order_date', '<=', $dateTo);
            })
            ->orderByDesc('id')
            ->limit(50)
            ->get();

        $totalResults = $orders->count();

        return response()->json([
            'html' => view('admin.work-orders.partials.rows', compact('orders'))->render(),
            'total' => $totalResults
        ]);
    }

    public function create()
    {
        return view('admin.work-orders.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'vehicle' => 'required|string|max:255',
            'plate_number' => 'required|string|max:20',
            'service_amount' => 'required|numeric|min:0',
            'created_by' => 'nullable|string|max:255',
        ]);

        $customerId = $request->input('customer_id');
        $customerMode = $request->input('customer_mode', 'search');

        if ($customerMode === 'new' || !$customerId) {
            $newCustomer = Customer::create([
                'name' => $request->input('client_name'),
                'phone' => $request->input('phone'),
                'email' => $request->input('new_customer_email'),
                'is_active' => true,
                'is_customer' => true,
                'include_in_mailing' => true,
                'customer_number' => $this->generateCustomerNumber(),
            ]);

            $customerId = $newCustomer->id;
        }

        if (!$customerId) {
            return back()->withErrors(['error' => 'Грешка при обработка на клиента. Моля, опитайте отново.'])
                ->withInput();
        }

        $vehicleId = $request->input('vehicle_id');
        $isNewVehicle = $request->input('is_new_vehicle', 0) == '1';

        if (!$vehicleId && $request->vehicle && $request->plate_number) {
            $isNewVehicle = true;
        }

        if ($isNewVehicle) {
            $newVehicle = Vehicle::create([
                'customer_id' => $customerId,
                'vehicle' => $request->vehicle,
                'plate_number' => $request->plate_number,
                'chassis_number' => $request->chassis_number,
                'last_mileage' => $request->mileage,
                'notes' => 'Добавен чрез поръчка',
                'is_active' => true,
            ]);
            $vehicleId = $newVehicle->id;
        } elseif (!$vehicleId) {
            return back()->withErrors(['error' => 'Моля, изберете автомобил от списъка.'])
                ->withInput();
        }

        $maxOldId = WorkOrder::max('old_id');
        $nextOldId = ($maxOldId ?: 0) + 1;
        $serviceAmountEur = $request->service_amount;

        $workOrderData = [
            'old_id' => $nextOldId,
            'customer_id' => $customerId,
            'client_name' => $request->client_name,
            'phone' => $request->phone ?? '',
            'vehicle_id' => $vehicleId,
            'order_date' => $request->order_date ?? now()->format('Y-m-d'),
            'mileage' => $request->mileage,
            'mechanic_code' => $request->mechanic_code,
            'service_amount' => $serviceAmountEur,
            'note' => $request->note,
            'created_by' => $request->created_by ?? '',
            'vehicle' => $request->vehicle,
            'plate_number' => $request->plate_number,
            'chassis_number' => $request->chassis_number,
            'total' => 0,
        ];

        $workOrder = WorkOrder::create($workOrderData);
        $itemsTotalEur = 0;

        if ($request->has('items')) {
            foreach ($request->items as $index => $item) {
                $productId = $item['product_id'] ?? null;
                $isNewProduct = $item['is_new_product'] ?? 0;
                $itemName = $item['item_name'] ?? null;

                if (!empty($itemName)) {
                    $quantity = $item['quantity'] ?? 0;
                    $priceEachEur = $item['price_each'] ?? 0;
                    $rowTotalEur = $quantity * $priceEachEur;
                    $itemsTotalEur += $rowTotalEur;

                    WorkOrderItem::create([
                        'work_order_old_id' => $workOrder->old_id,
                        'work_order_id' => $workOrder->id,
                        'row_number' => $index + 1,
                        'item_code' => $item['item_code'] ?? null,
                        'item_name' => $itemName,
                        'item_measure' => $item['item_measure'] ?? 'бр.',
                        'quantity' => $quantity,
                        'price_each' => $priceEachEur,
                        'row_total' => $rowTotalEur,
                    ]);

                    if ($isNewProduct == 1 && !empty($itemName)) {
                        try {
                            Product::create([
                                'old_id' => $item['item_code'] ?? null,
                                'name' => $itemName,
                                'uom' => $item['item_measure'] ?? 'бр.',
                                'price' => $priceEachEur,
                                'quantity' => 0,
                                'is_active' => true,
                            ]);
                        } catch (\Exception $e) {
                            logger('Грешка при създаване на продукт: ' . $e->getMessage());
                        }
                    }
                }
            }
        }

        $workOrder->update([
            'total' => $itemsTotalEur + $serviceAmountEur
        ]);

        return redirect()->route('admin.work-orders.show', $workOrder)
            ->with('success', 'Поръчката е създадена успешно.');
    }

    private function generateCustomerNumber()
    {
        $lastCustomer = Customer::orderBy('id', 'desc')->first();
        if ($lastCustomer && $lastCustomer->customer_number) {
            if (preg_match('/CUST-(\d+)/', $lastCustomer->customer_number, $matches)) {
                $nextNumber = str_pad((int)$matches[1] + 1, 3, '0', STR_PAD_LEFT);
                return 'CUST-' . $nextNumber;
            }
        }
        return 'CUST-001';
    }

    public function show(WorkOrder $work_order)
    {
        if (!$work_order->relationLoaded('items')) {
            $items = WorkOrderItem::where('work_order_old_id', $work_order->old_id)->get();
            $work_order->setRelation('items', $items);
        }

        return view('admin.work-orders.show', compact('work_order'));
    }

    public function edit(WorkOrder $work_order)
    {
        if (!$work_order->relationLoaded('items')) {
            $items = WorkOrderItem::where('work_order_old_id', $work_order->old_id)->get();
            $work_order->setRelation('items', $items);
        }

        return view('admin.work-orders.edit', compact('work_order'));
    }

    public function update(Request $request, WorkOrder $work_order)
    {
        $validated = $request->validate([
            'client_name' => 'nullable|string|max:255',
            'vehicle' => 'nullable|string|max:255',
            'plate_number' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:50',
            'service_amount' => 'required|numeric|min:0',
        ]);

        $customerId = $request->input('customer_id');

        if (!$customerId && $work_order->customer_id) {
            $customerId = $work_order->customer_id;
        } elseif (!$customerId) {
            return back()->withErrors(['error' => 'Моля, изберете клиент.'])->withInput();
        }

        $vehicleId = $request->input('vehicle_id');
        $isNewVehicle = $request->input('is_new_vehicle', 0) == '1';

        if (!$vehicleId && $request->vehicle && $request->plate_number) {
            $isNewVehicle = true;
        }

        if ($isNewVehicle) {
            if (!$customerId) {
                return back()->withErrors(['error' => 'Не може да се създаде автомобил без клиент.'])->withInput();
            }

            $newVehicle = Vehicle::create([
                'customer_id' => $customerId,
                'vehicle' => $request->vehicle,
                'plate_number' => $request->plate_number,
                'chassis_number' => $request->chassis_number,
                'last_mileage' => $request->mileage,
                'notes' => 'Добавен чрез поръчка',
                'is_active' => true,
            ]);
            $vehicleId = $newVehicle->id;
        } elseif (!$vehicleId) {
            $vehicleId = $work_order->vehicle_id;
        }

        $work_order->update([
            'customer_id' => $customerId,
            'client_name' => $request->client_name ?? $work_order->client_name,
            'phone' => $request->phone ?? $work_order->phone,
            'vehicle_id' => $vehicleId,
            'order_date' => $request->order_date ?? $work_order->order_date,
            'mileage' => $request->mileage ?? $work_order->mileage,
            'mechanic_code' => $request->mechanic_code ?? $work_order->mechanic_code,
            'service_amount' => $request->service_amount,
            'note' => $request->note ?? $work_order->note,
            'created_by' => $request->created_by ?? $work_order->created_by,
            'vehicle' => $request->vehicle ?? $work_order->vehicle,
            'plate_number' => $request->plate_number ?? $work_order->plate_number,
            'chassis_number' => $request->chassis_number ?? $work_order->chassis_number,
        ]);

        $work_order->items()->delete();
        $itemsTotalEur = 0;

        if ($request->has('items')) {
            foreach ($request->items as $index => $item) {
                $productId = $item['product_id'] ?? null;
                $isNewProduct = $item['is_new_product'] ?? 0;
                $itemName = $item['item_name'] ?? null;

                if (!empty($itemName)) {
                    $quantity = $item['quantity'] ?? 0;
                    $priceEachEur = $item['price_each'] ?? 0;
                    $rowTotalEur = $quantity * $priceEachEur;
                    $itemsTotalEur += $rowTotalEur;

                    WorkOrderItem::create([
                        'work_order_old_id' => $work_order->old_id,
                        'work_order_id' => $work_order->id,
                        'row_number' => $index + 1,
                        'item_code' => $item['item_code'] ?? null,
                        'item_name' => $itemName,
                        'item_measure' => $item['item_measure'] ?? 'бр.',
                        'quantity' => $quantity,
                        'price_each' => $priceEachEur,
                        'row_total' => $rowTotalEur,
                    ]);

                    if ($isNewProduct == 1 && !empty($itemName)) {
                        try {
                            Product::create([
                                'old_id' => $item['item_code'] ?? null,
                                'name' => $itemName,
                                'uom' => $item['item_measure'] ?? 'бр.',
                                'price' => $priceEachEur,
                                'quantity' => 0,
                                'is_active' => true,
                            ]);
                        } catch (\Exception $e) {
                            logger('Грешка при създаване на продукт: ' . $e->getMessage());
                        }
                    }
                }
            }
        }

        $work_order->update([
            'total' => $itemsTotalEur + $work_order->service_amount
        ]);

        return redirect()->route('admin.work-orders.show', $work_order)
            ->with('success', 'Поръчката е обновена успешно.');
    }

    public function createInvoice(WorkOrder $work_order)
    {
        // Задължително зареждаме клиента
        $work_order->load('customer');

        // Проверка дали поръчката има клиент
        if (!$work_order->customer_id) {
            return back()->with('error', 'Работната поръчка няма обвързан клиент.');
        }

        // Проверка дали клиентът съществува (не е изтрит)
        if (!$work_order->customer) {
            return back()->with('error', 'Клиентът не съществува (вероятно е изтрит).');
        }

        // ⚡ НОВА ПРОВЕРКА: Дали вече има фактура за тази поръчка?
        if ($work_order->invoices()->exists()) {
            return back()->with('error', 'За тази работна поръчка вече има създадена фактура.');
        }

        // Генериране на следващ номер на фактура
        $nextOldId = Invoice::max('old_id') + 1;
        $createdBy = $work_order->created_by ?? 'Система';

        // Подготовка на данни за фактурата
        $invoiceData = [
            'old_id'                 => $nextOldId,
            'customer_id'            => $work_order->customer_id,
            'customer_old_id'        => $work_order->customer->old_id,
            'work_order_id'          => $work_order->id,           // ⚡ добавяме това
            'invoice_type'           => 1,
            'invoice_date'           => now()->toDateString(),
            'date_due'               => now()->addDays(14)->toDateString(),
            'invoice_created_by'     => $createdBy,
            'note'                   => 'Създадена от работна поръчка №' . $work_order->old_id,
            'payment_cash'           => false,
            'is_void'                => false,
            'printed'                => false,
            'paid'                   => false,
            'tipsdelka'              => 0,
            'sale_type'              => 0,
            'pay_method'             => 0,
        ];

        DB::beginTransaction();
        try {
            $invoice = Invoice::create($invoiceData);

            // Пренасяне на артикулите
            $items = WorkOrderItem::where('work_order_old_id', $work_order->old_id)->get();
            foreach ($items as $index => $item) {
                InvoiceItem::create([
                    'invoice_old_id' => $invoice->old_id,
                    'row_number'     => $index + 1,
                    'item_code'      => $item->item_code,
                    'item_name'      => $item->item_name,
                    'item_measure'   => $item->item_measure,
                    'quantity'       => $item->quantity,
                    'price_each'     => $item->price_each,
                    'row_total'      => $item->row_total,
                ]);
            }

            // Труд
            if ($work_order->service_amount > 0) {
                InvoiceItem::create([
                    'invoice_old_id' => $invoice->old_id,
                    'row_number'     => $items->count() + 1,
                    'item_code'      => 'SERVICE',
                    'item_name'      => 'Труд (ремонтни дейности)',
                    'item_measure'   => 'усл.',
                    'quantity'       => 1,
                    'price_each'     => $work_order->service_amount,
                    'row_total'      => $work_order->service_amount,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('admin.invoices.edit', $invoice->id)
                ->with('success', 'Фактура №' . $invoice->old_id . ' е създадена.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Грешка: ' . $e->getMessage());
        }
    }

    public function print(WorkOrder $work_order)
    {
        if (!$work_order->relationLoaded('items')) {
            $items = WorkOrderItem::where('work_order_old_id', $work_order->old_id)->get();
            $work_order->setRelation('items', $items);
        }

        return view('admin.work-orders.print', compact('work_order'));
    }

    public function pdf(WorkOrder $work_order)
    {
        if (!$work_order->relationLoaded('items')) {
            $items = WorkOrderItem::where('work_order_old_id', $work_order->old_id)->get();
            $work_order->setRelation('items', $items);
        }

        $pdf = Pdf::loadView('admin.work-orders.pdf', compact('work_order'));
        return $pdf->stream('work-order-' . $work_order->id . '.pdf');
    }

    public function destroy(WorkOrder $work_order)
    {
        $work_order->delete();

        return redirect()->route('admin.work-orders.index')
            ->with('success', 'Поръчката е изтрита успешно.');
    }
}
