<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\WorkOrder;
use App\Models\WorkOrderItem;
use App\Models\StockMovement;
use App\Models\Product;
use App\Models\Service;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class WorkOrderController extends Controller
{
    public function index()
    {
        $workOrders = WorkOrder::with(['customer', 'vehicle', 'mechanic'])->paginate(25);
        return view('admin.work-orders.index', compact('workOrders'));
    }

    public function create()
    {
        $customers = Customer::pluck('name', 'id');
        $vehicles = collect();
        $mechanics = User::role('mechanic')->pluck('name', 'id');
        $products = Product::select('id', 'sku', 'name', 'price', 'vat_percent')
            ->orderBy('name')
            ->get();
        $services = Service::active()->select('id', 'code', 'name', 'price', 'vat_percent', 'duration_minutes')
            ->orderBy('name')
            ->get();
        
        // АКТУАЛИЗИРАНО: Използваме правилното име на колоната 'mileage'
        $vehiclesForMileage = Vehicle::select('id', 'mileage')->get();
        
        return view('admin.work-orders.create', compact('customers', 'vehicles', 'mechanics', 'products', 'services', 'vehiclesForMileage'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id'   => 'required|exists:customers,id',
            'vehicle_id'    => 'nullable|exists:vehicles,id',
            'status'        => 'required|in:draft,open,in_progress,completed,invoiced,closed,cancelled',
            'received_at'   => 'nullable|date',
            'km_on_receive' => 'nullable|integer|min:0',
            'assigned_to'   => 'nullable|exists:users,id',
            'notes'         => 'nullable|string',
            'items'         => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity'    => 'required|numeric|min:0.01',
            'items.*.unit_price'  => 'required|numeric|min:0',
            'items.*.vat_percent' => 'required|numeric|min:0|max:100',
            'items.*.item_type'   => 'required|in:product,service',
        ]);

        $order = DB::transaction(function () use ($validated, $request) {
            $userId = Auth::check() ? Auth::user()->id : 0;

            $order = WorkOrder::create([
                'number'        => 'WO-' . str_pad(WorkOrder::count() + 1, 6, '0', STR_PAD_LEFT),
                'customer_id'   => $validated['customer_id'],
                'vehicle_id'    => $validated['vehicle_id'] ?? null,
                'status'        => $validated['status'],
                'received_at'   => $validated['received_at'] ?? now(),
                'km_on_receive' => $validated['km_on_receive'],
                'assigned_to'   => $validated['assigned_to'],
                'notes'         => $validated['notes'],
                'created_by'    => $userId,
            ]);

            $totalWithout = 0;
            foreach ($request->items as $row) {
                $qty   = $row['quantity'];
                $price = $row['unit_price'];
                $vat   = $row['vat_percent'];
                $line  = $qty * $price;
                $vatAm = $line * $vat / 100;

                $itemData = [
                    'work_order_id'           => $order->id,
                    'description'             => $row['description'],
                    'quantity'                => $qty,
                    'unit_price'              => $price,
                    'vat_percent'             => $vat,
                    'line_total_without_vat'  => $line,
                    'line_vat_amount'         => $vatAm,
                    'line_total'              => $line + $vatAm,
                ];

                // Разпознаване на типа на артикула (продукт или услуга)
                if (isset($row['product_id']) && $row['product_id']) {
                    $productId = $row['product_id'];
                    if (strpos($productId, 'product_') === 0) {
                        // Това е продукт
                        $itemData['product_id'] = str_replace('product_', '', $productId);
                        $itemData['service_id'] = null;
                        
                        // Складова наличност само за продукти
                        StockMovement::create([
                            'product_id'     => $itemData['product_id'],
                            'change'         => -$qty,
                            'type'           => 'reservation',
                            'reference_id'   => $order->id,
                            'reference_type' => WorkOrder::class,
                            'created_by'     => $userId,
                        ]);
                    } elseif (strpos($productId, 'service_') === 0) {
                        // Това е услуга
                        $itemData['service_id'] = str_replace('service_', '', $productId);
                        $itemData['product_id'] = null;
                    }
                }

                WorkOrderItem::create($itemData);
                $totalWithout += $line;
            }

            $order->update([
                'total_without_vat' => $totalWithout,
                'vat_amount'        => $order->items()->sum('line_vat_amount'),
                'total'             => $order->items()->sum('line_total'),
            ]);

            return $order;
        });

        return redirect()->route('admin.work-orders.show', $order)->with('success', 'Поръчката е създадена.');
    }

    public function show(WorkOrder $workOrder)
    {
        // ПОПРАВЕНО: Добавен 'items.service'
        $workOrder->load(['items.product', 'items.service', 'customer', 'vehicle', 'mechanic']);
        return view('admin.work-orders.show', compact('workOrder'));
    }

        /**
     * Експортира ремонтна поръчка в различни формати (PDF, Excel, CSV).
     */
    public function export(WorkOrder $workOrder, string $type)
    {
        try {
            if ($type === 'pdf') {
                // Вече имаме маршрут 'admin.work-orders.pdf', затова пренасочваме към него
                return redirect()->route('admin.work-orders.pdf', $workOrder);
            } 
            elseif (in_array($type, ['excel', 'csv'])) {
                // Засега само демонстрация - в бъще може да се имплементира
                return redirect()->back()->with('warning', 'Експортът в ' . strtoupper($type) . ' формат ще бъде достъпен скоро.');
            }
            
            return redirect()->back()->with('error', 'Невалиден тип за експорт.');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Грешка при експорт: ' . $e->getMessage());
        }
    }

    public function edit(WorkOrder $workOrder)
    {
        $customers = Customer::pluck('name', 'id');
        $vehicles  = Vehicle::where('customer_id', $workOrder->customer_id)->pluck('plate', 'id');
        $mechanics = User::role('mechanic')->pluck('name', 'id');
        $products = Product::select('id', 'sku', 'name', 'price', 'vat_percent')
            ->orderBy('name')
            ->get();
        $services = Service::active()->select('id', 'code', 'name', 'price', 'vat_percent', 'duration_minutes')
            ->orderBy('name')
            ->get();
        
        // АКТУАЛИЗИРАНО: Използваме правилното име на колоната 'mileage'
        $vehiclesForMileage = Vehicle::select('id', 'mileage')->get();
            
        return view('admin.work-orders.edit', compact('workOrder', 'customers', 'vehicles', 'mechanics', 'products', 'services', 'vehiclesForMileage'));
    }

    public function update(Request $request, WorkOrder $workOrder)
    {
        $validated = $request->validate([
            'customer_id'   => 'required|exists:customers,id',
            'vehicle_id'    => 'nullable|exists:vehicles,id',
            'status'        => 'required|in:draft,open,in_progress,completed,invoiced,closed,cancelled',
            'received_at'   => 'nullable|date',
            'km_on_receive' => 'nullable|integer|min:0',
            'assigned_to'   => 'nullable|exists:users,id',
            'notes'         => 'nullable|string',
            'items'         => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity'    => 'required|numeric|min:0.01',
            'items.*.unit_price'  => 'required|numeric|min:0',
            'items.*.vat_percent' => 'required|numeric|min:0|max:100',
            'items.*.item_type'   => 'required|in:product,service',
        ]);

        DB::transaction(function () use ($validated, $request, $workOrder) {
            $userId = Auth::check() ? Auth::user()->id : 0;
            
            // Актуализирай основната информация за поръчката
            $workOrder->update([
                'customer_id'   => $validated['customer_id'],
                'vehicle_id'    => $validated['vehicle_id'] ?? null,
                'status'        => $validated['status'],
                'received_at'   => $validated['received_at'],
                'km_on_receive' => $validated['km_on_receive'],
                'assigned_to'   => $validated['assigned_to'],
                'notes'         => $validated['notes'],
            ]);

            // ИЗТРИЙ старите items и добави новите
            $workOrder->items()->delete();
            
            $totalWithout = 0;
            foreach ($request->items as $row) {
                $qty   = $row['quantity'];
                $price = $row['unit_price'];
                $vat   = $row['vat_percent'];
                $line  = $qty * $price;
                $vatAm = $line * $vat / 100;

                $itemData = [
                    'work_order_id'           => $workOrder->id,
                    'description'             => $row['description'],
                    'quantity'                => $qty,
                    'unit_price'              => $price,
                    'vat_percent'             => $vat,
                    'line_total_without_vat'  => $line,
                    'line_vat_amount'         => $vatAm,
                    'line_total'              => $line + $vatAm,
                ];

                // Разпознаване на типа на артикула (продукт или услуга)
                if (isset($row['product_id']) && $row['product_id']) {
                    $productId = $row['product_id'];
                    if (strpos($productId, 'product_') === 0) {
                        // Това е продукт
                        $itemData['product_id'] = str_replace('product_', '', $productId);
                        $itemData['service_id'] = null;
                        
                        // Складова наличност само за продукти
                        StockMovement::create([
                            'product_id'     => $itemData['product_id'],
                            'change'         => -$qty,
                            'type'           => 'reservation',
                            'reference_id'   => $workOrder->id,
                            'reference_type' => WorkOrder::class,
                            'created_by'     => $userId,
                        ]);
                    } elseif (strpos($productId, 'service_') === 0) {
                        // Това е услуга
                        $itemData['service_id'] = str_replace('service_', '', $productId);
                        $itemData['product_id'] = null;
                    }
                }

                WorkOrderItem::create($itemData);
                $totalWithout += $line;
            }

            // Актуализирай общите суми на поръчката
            $workOrder->update([
                'total_without_vat' => $totalWithout,
                'vat_amount'        => $workOrder->items()->sum('line_vat_amount'),
                'total'             => $workOrder->items()->sum('line_total'),
            ]);
        });

        return redirect()->route('admin.work-orders.show', $workOrder)->with('success', 'Поръчката е обновена.');
    }

    public function destroy(WorkOrder $workOrder)
    {
        $workOrder->items()->delete();
        $workOrder->delete();
        return redirect()->route('admin.work-orders.index')->with('success', 'Поръчката е изтрита.');
    }

    public function pdf(\App\Models\WorkOrder $workOrder, Request $request)
    {
        $invoice = $workOrder->invoices()->first();
        if (! $invoice) {
            $userId = Auth::check() ? Auth::user()->id : 0;

            $invoice = \App\Models\Invoice::create([
                'number'        => 'INV-' . str_pad(\App\Models\Invoice::count() + 1, 6, '0', STR_PAD_LEFT),
                'work_order_id' => $workOrder->id,
                'customer_id'   => $workOrder->customer_id,
                'issue_date'    => now(),
                'due_date'      => now()->addDays(14),
                'subtotal'      => $workOrder->total_without_vat,
                'vat_total'     => $workOrder->vat_amount,
                'grand_total'   => $workOrder->total,
                'status'        => 'draft',
                'created_by'    => $userId,
            ]);

            foreach ($workOrder->items as $item) {
                $invoice->items()->create([
                    'product_id'  => $item->product_id,
                    'service_id'  => $item->service_id,
                    'description' => $item->description,
                    'quantity'    => $item->quantity,
                    'unit_price'  => $item->unit_price,
                    'vat_percent' => $item->vat_percent,
                    'line_total'  => $item->line_total,
                ]);
            }
        }

        $copy = $request->get('copy', 0);
        return Pdf::loadView('admin.invoices.pdf', compact('invoice', 'copy'))
                  ->stream('invoice_' . $invoice->number . '.pdf');
    }

    /**
     * Търсене на клиенти и автомобили за autocomplete
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        
        if (!$query || strlen($query) < 2) {
            return response()->json([
                'customers' => [],
                'vehicles' => []
            ]);
        }

        $results = [
            'customers' => Customer::where('name', 'like', "%{$query}%")
                ->orWhere('phone', 'like', "%{$query}%")
                ->orWhere('email', 'like', "%{$query}%")
                ->withCount('vehicles')
                ->limit(10)
                ->get()
                ->map(function ($customer) {
                    return [
                        'id' => $customer->id,
                        'name' => $customer->name,
                        'phone' => $customer->phone,
                        'email' => $customer->email,
                        'vehicles_count' => $customer->vehicles_count,
                        'address' => $customer->address,
                    ];
                }),
                
            'vehicles' => Vehicle::where('plate', 'like', "%{$query}%")
                ->orWhere('vin', 'like', "%{$query}%")
                ->orWhere('make', 'like', "%{$query}%")
                ->orWhere('model', 'like', "%{$query}%")
                ->with('customer')
                ->limit(10)
                ->get()
                ->map(function ($vehicle) {
                    return [
                        'id' => $vehicle->id,
                        'plate' => $vehicle->plate,
                        'make' => $vehicle->make,
                        'model' => $vehicle->model,
                        'year' => $vehicle->year,
                        'engine' => $vehicle->engine,
                        'vin' => $vehicle->vin,
                        'mileage' => $vehicle->mileage, // АКТУАЛИЗИРАНО: от current_mileage на mileage
                        'customer_id' => $vehicle->customer_id,
                        'customer' => $vehicle->customer ? [
                            'id' => $vehicle->customer->id,
                            'name' => $vehicle->customer->name,
                            'phone' => $vehicle->customer->phone,
                            'email' => $vehicle->customer->email,
                        ] : null,
                    ];
                })
        ];
        
        return response()->json($results);
    }

    /**
     * Връща информация за клиент
     */
    public function customerInfo($id)
    {
        $customer = Customer::findOrFail($id);
        return response()->json([
            'id' => $customer->id,
            'name' => $customer->name,
            'phone' => $customer->phone,
            'email' => $customer->email,
            'address' => $customer->address,
            'created_at' => $customer->created_at->format('d.m.Y'),
        ]);
    }

    /**
     * Връща информация за автомобил
     */
    public function vehicleInfo($id)
    {
        $vehicle = Vehicle::with('customer')->findOrFail($id);
        return response()->json([
            'id' => $vehicle->id,
            'plate' => $vehicle->plate,
            'make' => $vehicle->make,
            'model' => $vehicle->model,
            'year' => $vehicle->year,
            'engine' => $vehicle->engine,
            'vin' => $vehicle->vin,
            'color' => $vehicle->color,
            'mileage' => $vehicle->mileage, // АКТУАЛИЗИРАНО: от current_mileage на mileage
            'customer_id' => $vehicle->customer_id,
            'customer' => $vehicle->customer ? [
                'id' => $vehicle->customer->id,
                'name' => $vehicle->customer->name,
                'phone' => $vehicle->customer->phone,
                'email' => $vehicle->customer->email,
            ] : null,
        ]);
    }

    /**
     * Връща всички автомобили на клиент
     */
    public function customerVehicles($id)
    {
        $vehicles = Vehicle::where('customer_id', $id)
            ->get()
            ->map(function ($vehicle) {
                return [
                    'id' => $vehicle->id,
                    'plate' => $vehicle->plate,
                    'make' => $vehicle->make,
                    'model' => $vehicle->model,
                    'year' => $vehicle->year,
                    'engine' => $vehicle->engine,
                    'mileage' => $vehicle->mileage, // АКТУАЛИЗИРАНО: от current_mileage на mileage
                    'full_text' => $vehicle->plate . ' - ' . $vehicle->make . ' ' . $vehicle->model . ($vehicle->year ? ' (' . $vehicle->year . ')' : ''),
                ];
            });
            
        return response()->json($vehicles);
    }

    /**
     * Връща всички продукти и услуги за autocomplete
     */
    public function productsAutocomplete(Request $request)
    {
        $query = $request->get('q');
        $type = $request->get('type', 'all'); // all, product, service
        
        $results = [];
        
        if ($type === 'all' || $type === 'product') {
            $products = Product::query();
            
            if ($query) {
                $products->where(function ($q) use ($query) {
                    $q->where('sku', 'like', "%{$query}%")
                      ->orWhere('name', 'like', "%{$query}%")
                      ->orWhere('description', 'like', "%{$query}%");
                });
            }
            
            $products = $products->select('id', 'sku', 'name', 'price', 'vat_percent')
                ->orderBy('name')
                ->limit(10)
                ->get()
                ->map(function ($product) {
                    return [
                        'id' => 'product_' . $product->id,
                        'sku' => $product->sku,
                        'name' => $product->name,
                        'price' => $product->price,
                        'vat_percent' => $product->vat_percent,
                        'type' => 'product',
                        'full_text' => $product->sku . ' - ' . $product->name . ' (' . number_format($product->price, 2) . ' лв.)',
                    ];
                });
            
            $results = array_merge($results, $products->toArray());
        }
        
        if ($type === 'all' || $type === 'service') {
            $services = Service::active()->query();
            
            if ($query) {
                $services->where(function ($q) use ($query) {
                    $q->where('code', 'like', "%{$query}%")
                      ->orWhere('name', 'like', "%{$query}%")
                      ->orWhere('description', 'like', "%{$query}%");
                });
            }
            
            $services = $services->select('id', 'code', 'name', 'price', 'vat_percent', 'duration_minutes')
                ->orderBy('name')
                ->limit(10)
                ->get()
                ->map(function ($service) {
                    return [
                        'id' => 'service_' . $service->id,
                        'sku' => $service->code,
                        'name' => $service->name,
                        'price' => $service->price,
                        'vat_percent' => $service->vat_percent,
                        'type' => 'service',
                        'duration' => $service->duration_minutes,
                        'full_text' => $service->code . ' - ' . $service->name . ' (' . number_format($service->price, 2) . ' лв.)',
                    ];
                });
            
            $results = array_merge($results, $services->toArray());
        }
        
        return response()->json($results);
    }
}