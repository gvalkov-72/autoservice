<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VehicleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $customer_id = $request->get('customer_id');
        
        $vehicles = Vehicle::with('customer')
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('vehicle', 'like', "%{$search}%")
                      ->orWhere('plate_number', 'like', "%{$search}%")
                      ->orWhere('chassis_number', 'like', "%{$search}%");
                });
            })
            ->when($customer_id, function ($query, $customer_id) {
                return $query->where('customer_id', $customer_id);
            })
            ->orderBy('plate_number')
            ->paginate(20);
            
        $customers = Customer::where('is_active', true)->orderBy('name')->get();
        
        return view('admin.vehicles.index', compact('vehicles', 'customers', 'search', 'customer_id'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $customer_id = $request->get('customer_id');
        $customer = null;
        
        if ($customer_id) {
            $customer = Customer::find($customer_id);
        }
        
        $customers = Customer::where('is_active', true)->orderBy('name')->get();
        
        return view('admin.vehicles.create', compact('customers', 'customer'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'vehicle' => 'nullable|string|max:255',
            'plate_number' => 'required|string|max:255|unique:vehicles,plate_number',
            'chassis_number' => 'nullable|string|max:255',
            'last_mileage' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        
        $validated['is_active'] = $request->has('is_active');
        
        $vehicle = Vehicle::create($validated);
        
        activity()
            ->causedBy(Auth::user())
            ->performedOn($vehicle)
            ->log('Създадено ново превозно средство: ' . $vehicle->plate_number);
        
        return redirect()
            ->route('admin.vehicles.show', $vehicle)
            ->with('success', 'Превозното средство "' . $vehicle->plate_number . '" е създадено успешно.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Vehicle $vehicle)
    {
        $vehicle->load(['customer', 'workOrders' => function ($query) {
            $query->orderBy('order_date', 'desc');
        }]);
        
        return view('admin.vehicles.show', compact('vehicle'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vehicle $vehicle)
    {
        $customers = Customer::where('is_active', true)->orderBy('name')->get();
        return view('admin.vehicles.edit', compact('vehicle', 'customers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Vehicle $vehicle)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'vehicle' => 'nullable|string|max:255',
            'plate_number' => 'required|string|max:255|unique:vehicles,plate_number,' . $vehicle->id,
            'chassis_number' => 'nullable|string|max:255',
            'last_mileage' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        
        $validated['is_active'] = $request->has('is_active');
        
        $oldData = $vehicle->toArray();
        
        $vehicle->update($validated);
        
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
                ->performedOn($vehicle)
                ->withProperties(['changes' => $changes])
                ->log('Актуализирано превозно средство: ' . $vehicle->plate_number);
        }
        
        return redirect()
            ->route('admin.vehicles.show', $vehicle)
            ->with('success', 'Данните на превозното средство "' . $vehicle->plate_number . '" са актуализирани успешно.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vehicle $vehicle)
    {
        if ($vehicle->workOrders()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Не можете да изтриете превозно средство, което има свързани работни поръчки!');
        }
        
        $vehiclePlate = $vehicle->plate_number;
        $vehicle->delete();
        
        activity()
            ->causedBy(Auth::user())
            ->performedOn($vehicle)
            ->log('Изтрито превозно средство: ' . $vehiclePlate);
        
        return redirect()->route('admin.vehicles.index')
            ->with('success', 'Превозното средство "' . $vehiclePlate . '" е изтрито успешно.');
    }

    /**
     * Live search for vehicles (AJAX).
     */
    public function search(Request $request)
    {
        try {
            $query = trim($request->get('q'));
            
            if (strlen($query) < 2) {
                return response()->json(['html' => '', 'total' => 0]);
            }
            
            $vehicles = Vehicle::with('customer')
                ->where(function ($qry) use ($query) {
                    $qry->where('vehicle', 'like', "%{$query}%")
                        ->orWhere('plate_number', 'like', "%{$query}%")
                        ->orWhere('chassis_number', 'like', "%{$query}%");
                })
                ->where('is_active', true)
                ->orderBy('plate_number', 'asc')
                ->limit(50)
                ->get();
            
            $html = view('admin.vehicles.partials.rows', ['vehicles' => $vehicles])->render();
            
            return response()->json([
                'html' => $html,
                'total' => $vehicles->count()
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}