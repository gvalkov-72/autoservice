<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WorkOrder;
use App\Models\WorkOrderItem;
use App\Models\Vehicle;
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
        $rate = 1.95583;

        // Проста валидация - само най-необходимите полета
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
        $isNewVehicle = $request->has('is_new_vehicle') && $request->input('is_new_vehicle') == '1';

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
            return back()->withErrors(['error' => 'Моля, изберете автомобил от списъка.'])
                         ->withInput();
        }

        // Подготовка на данните за поръчката
        $workOrderData = [
            'client_name' => $request->client_name,
            'phone' => $request->phone ?? '',
            'vehicle_id' => $vehicleId,
            'order_date' => $request->order_date ?? now()->format('Y-m-d'),
            'mileage' => $request->mileage,
            'mechanic_code' => $request->mechanic_code,
            'service_amount' => $request->service_amount / $rate,
            'note' => $request->note,
            'created_by' => $request->created_by ?? '',
            'vehicle' => $request->vehicle,
            'plate_number' => $request->plate_number,
            'chassis_number' => $request->chassis_number,
        ];

        // Генериране на old_id
        $maxOldId = WorkOrder::max('old_id');
        $workOrderData['old_id'] = ($maxOldId ?: 0) + 1;

        // Създаване на поръчката
        $workOrder = WorkOrder::create($workOrderData);

        // Добавяне на артикули (ако има)
        if ($request->has('items')) {
            foreach ($request->items as $index => $item) {
                if (!empty($item['item_name'])) {
                    $priceEach = ($item['price_each'] ?? 0) / $rate;
                    $quantity = $item['quantity'] ?? 0;
                    
                    WorkOrderItem::create([
                        'work_order_old_id' => $workOrder->old_id,
                        'work_order_id' => $workOrder->id,
                        'row_number' => $index + 1,
                        'item_code' => $item['item_code'] ?? null,
                        'item_name' => $item['item_name'],
                        'item_measure' => $item['item_measure'] ?? 'бр.',
                        'quantity' => $quantity,
                        'price_each' => $priceEach,
                        'row_total' => $quantity * $priceEach,
                    ]);
                }
            }
        }

        // Преизчисляване на общата сума
        $totalItems = $workOrder->items()->sum('row_total');
        $workOrder->update([
            'total' => $totalItems + $workOrder->service_amount
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
        $validated = $request->validate([
            'client_name' => 'nullable|string|max:255',
            'order_date' => 'nullable|date',
            'vehicle' => 'nullable|string|max:255',
            'plate_number' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:50',
        ]);

        $work_order->update($validated);

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