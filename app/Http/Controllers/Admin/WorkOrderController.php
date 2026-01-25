<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WorkOrder;
use App\Models\WorkOrderItem;
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

        // Пагинираме само ако има повече от 25 записа (по подразбиране)
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

        // Ако няма нищо за търсене
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

        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'vehicle' => 'nullable|string|max:255',
            'order_date' => 'nullable|date',
            'plate_number' => 'nullable|string|max:20',
            'chassis_number' => 'nullable|string|max:50',
            'mileage' => 'nullable|integer|min:0',
            'mechanic_code' => 'nullable|integer',
            'service_amount' => 'required|numeric|min:0',
            'note' => 'nullable|string',
            'created_by' => 'nullable|string|max:255',
            'items' => 'nullable|array',
            'items.*.item_code' => 'nullable|string|max:255',
            'items.*.item_name' => 'nullable|string|max:255',
            'items.*.item_measure' => 'nullable|string|max:50',
            'items.*.quantity' => 'nullable|numeric|min:0',
            'items.*.price_each' => 'nullable|numeric|min:0',
        ]);

        // Конвертиране на сумите от левове в евро
        $validated['service_amount'] = $validated['service_amount'] / $rate;

        // Генериране на old_id (максималното + 1)
        $maxOldId = WorkOrder::max('old_id');
        $validated['old_id'] = ($maxOldId ?: 0) + 1;

        // Създаване на поръчката
        $workOrder = WorkOrder::create($validated);

        // Създаване на артикулите (ако има)
        if (!empty($validated['items'])) {
            foreach ($validated['items'] as $itemData) {
                if (!empty($itemData['item_name'])) {
                    // Конвертиране на цените от левове в евро
                    $itemData['price_each'] = $itemData['price_each'] / $rate;
                    $itemData['row_total'] = $itemData['quantity'] * $itemData['price_each'];
                    $itemData['work_order_old_id'] = $workOrder->old_id;
                    $itemData['work_order_id'] = $workOrder->id;

                    WorkOrderItem::create($itemData);
                }
            }
        }

        return redirect()->route('admin.work-orders.show', $workOrder)
            ->with('success', 'Поръчката е създадена успешно.');
    }

    public function show(WorkOrder $work_order)
    {
        // ПРОВЕРКА: Дали връзката items() работи?
        // Ако не работи, зареждаме ръчно
        if (!$work_order->relationLoaded('items')) {
            $items = WorkOrderItem::where('work_order_old_id', $work_order->old_id)->get();
            $work_order->setRelation('items', $items);
        }

        // НЯМА customer връзка, само client_name като текст
        return view('admin.work-orders.show', compact('work_order'));
    }

    public function edit(WorkOrder $work_order)
    {
        // Зареждаме items, ако не са заредени
        if (!$work_order->relationLoaded('items')) {
            $items = WorkOrderItem::where('work_order_old_id', $work_order->old_id)->get();
            $work_order->setRelation('items', $items);
        }

        return view('admin.work-orders.edit', compact('work_order'));
    }

    public function update(Request $request, WorkOrder $work_order)
    {
        // Валидация - същата като при store
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
