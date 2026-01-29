<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WorkOrder;
use App\Models\WorkOrderItem;
use App\Models\Vehicle;
use App\Models\Product;
use App\Models\Customer;
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
        // Проста валидация
        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'vehicle' => 'required|string|max:255',
            'plate_number' => 'required|string|max:20',
            'service_amount' => 'required|numeric|min:0',
            'created_by' => 'nullable|string|max:255',
        ]);

        // Задължително трябва да има customer_id от формата
        $customerId = $request->input('customer_id');
        if (!$customerId) {
            return back()->withErrors(['error' => 'Моля, изберете клиент от списъка.'])
                ->withInput();
        }

        // Обработка на автомобила
        $vehicleId = $request->input('vehicle_id');
        $isNewVehicle = $request->input('is_new_vehicle', 0) == '1';

        // Автоматично създаване на нов автомобил ако няма vehicle_id
        if (!$vehicleId && $request->vehicle && $request->plate_number) {
            $isNewVehicle = true;
        }

        if ($isNewVehicle) {
            // Създаване на нов автомобил
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
            // Ако не е нов автомобил и няма vehicle_id
            return back()->withErrors(['error' => 'Моля, изберете автомобил от списъка.'])
                ->withInput();
        }

        // Генериране на old_id
        $maxOldId = WorkOrder::max('old_id');
        $nextOldId = ($maxOldId ?: 0) + 1;

        // ВАЖНО: service_amount вече е в евро (от формата), не делиш на курс!
        $serviceAmountEur = $request->service_amount;

        // Подготовка на данните за поръчката
        $workOrderData = [
            'old_id' => $nextOldId,
            'customer_id' => $customerId,
            'client_name' => $request->client_name,
            'phone' => $request->phone ?? '',
            'vehicle_id' => $vehicleId,
            'order_date' => $request->order_date ?? now()->format('Y-m-d'),
            'mileage' => $request->mileage,
            'mechanic_code' => $request->mechanic_code,
            'service_amount' => $serviceAmountEur, // Вече в евро
            'note' => $request->note,
            'created_by' => $request->created_by ?? '',
            'vehicle' => $request->vehicle,
            'plate_number' => $request->plate_number,
            'chassis_number' => $request->chassis_number,
            'total' => 0, // Временно, ще се обнови след артикулите
        ];

        // Създаване на поръчката
        $workOrder = WorkOrder::create($workOrderData);

        // Добавяне на артикули
        $itemsTotalEur = 0;

        if ($request->has('items')) {
            foreach ($request->items as $index => $item) {
                $productId = $item['product_id'] ?? null;
                $isNewProduct = $item['is_new_product'] ?? 0;
                $itemName = $item['item_name'] ?? null;

                if (!empty($itemName)) {
                    $quantity = $item['quantity'] ?? 0;

                    // ВАЖНО: price_each вече е в евро (от формата), не делиш на курс!
                    $priceEachEur = $item['price_each'] ?? 0;

                    $rowTotalEur = $quantity * $priceEachEur;
                    $itemsTotalEur += $rowTotalEur;

                    // Създаваме артикула в поръчката
                    WorkOrderItem::create([
                        'work_order_old_id' => $workOrder->old_id,
                        'work_order_id' => $workOrder->id,
                        'row_number' => $index + 1,
                        'item_code' => $item['item_code'] ?? null,
                        'item_name' => $itemName,
                        'item_measure' => $item['item_measure'] ?? 'бр.',
                        'quantity' => $quantity,
                        'price_each' => $priceEachEur, // Вече в евро
                        'row_total' => $rowTotalEur,   // Вече в евро
                    ]);

                    // Ако е нов продукт, записваме го в таблицата products
                    if ($isNewProduct == 1 && !empty($itemName)) {
                        try {
                            $product = Product::create([
                                'old_id' => $item['item_code'] ?? null,
                                'name' => $itemName,
                                'uom' => $item['item_measure'] ?? 'бр.',
                                'price' => $priceEachEur, // Вече в евро
                                'quantity' => 0,
                                'is_active' => true,
                            ]);
                        } catch (\Exception $e) {
                            // Ако има грешка при създаване на продукта, продължаваме без него
                            logger('Грешка при създаване на продукт: ' . $e->getMessage());
                        }
                    }
                }
            }
        }

        // Преизчисляване на общата сума (в евро)
        $workOrder->update([
            'total' => $itemsTotalEur + $serviceAmountEur
        ]);

        return redirect()->route('admin.work-orders.show', $workOrder)
            ->with('success', 'Поръчката е създадена успешно.');
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
        // Валидация
        $validated = $request->validate([
            'client_name' => 'nullable|string|max:255',
            'vehicle' => 'nullable|string|max:255',
            'plate_number' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:50',
            'service_amount' => 'required|numeric|min:0',
        ]);

        // Обработка на автомобила - ВАЖНА ПРОМЯНА!
        $customerId = $request->input('customer_id');

        // Ако няма customer_id от формата, използвай съществуващия от поръчката
        if (!$customerId && $work_order->customer_id) {
            $customerId = $work_order->customer_id;
        } elseif (!$customerId) {
            // Ако няма customer_id нито от формата, нито от поръчката
            return back()->withErrors(['error' => 'Моля, изберете клиент.'])->withInput();
        }

        $vehicleId = $request->input('vehicle_id');
        $isNewVehicle = $request->input('is_new_vehicle', 0) == '1';

        // Ако няма vehicle_id, но има данни за автомобил, създаваме нов
        if (!$vehicleId && $request->vehicle && $request->plate_number) {
            $isNewVehicle = true;
        }

        if ($isNewVehicle) {
            // Създаване на нов автомобил - ГАРАНТИРАЙ, че customerId не е null!
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
            // Ако не е нов автомобил и няма vehicle_id, използвай съществуващия
            $vehicleId = $work_order->vehicle_id;
        }

        // Актуализиране на поръчката - ВАЖНО: задай customer_id!
        $work_order->update([
            'customer_id' => $customerId, // Това гарантира, че customer_id няма да бъде null
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

        // Изтриване на старите артикули
        $work_order->items()->delete();

        // Добавяне на новите артикули
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

                    // Ако е нов продукт, създаваме го
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

        // Преизчисляване на общата сума
        $work_order->update([
            'total' => $itemsTotalEur + $work_order->service_amount
        ]);

        return redirect()->route('admin.work-orders.show', $work_order)
            ->with('success', 'Поръчката е обновена успешно.');
    }

    public function destroy(WorkOrder $work_order)
    {
        $work_order->delete();

        return redirect()->route('admin.work-orders.index')
            ->with('success', 'Поръчката е изтрита успешно.');
    }
}
