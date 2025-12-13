<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\Customer;
use App\Exports\VehicleExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::with('customer')->paginate(25);
        return view('admin.vehicles.index', compact('vehicles'));
    }

    public function create()
    {
        $customers = Customer::pluck('name', 'id');
        return view('admin.vehicles.create', compact('customers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'vin'         => 'required|unique:vehicles,vin',
            'plate'       => 'required|unique:vehicles,plate',
            'make'        => 'required|string|max:100',
            'model'       => 'required|string|max:100',
            'year'        => 'nullable|integer|min:1900|max:' . date('Y'),
            'mileage'     => 'nullable|integer|min:0',
            'dk_no'       => 'nullable|string|max:50',
            'notes'       => 'nullable|string',
        ]);

        Vehicle::create($validated);

        return redirect()->route('admin.vehicles.index')->with('success', 'Автомобилът е добавен.');
    }

    public function show(Vehicle $vehicle)
    {
        $vehicle->load(['customer', 'workOrders']);
        return view('admin.vehicles.show', compact('vehicle'));
    }

    public function edit(Vehicle $vehicle)
    {
        $customers = Customer::pluck('name', 'id');
        return view('admin.vehicles.edit', compact('vehicle', 'customers'));
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'vin'         => 'required|unique:vehicles,vin,' . $vehicle->id,
            'plate'       => 'required|unique:vehicles,plate,' . $vehicle->id,
            'make'        => 'required|string|max:100',
            'model'       => 'required|string|max:100',
            'year'        => 'nullable|integer|min:1900|max:' . date('Y'),
            'mileage'     => 'nullable|integer|min:0',
            'dk_no'       => 'nullable|string|max:50',
            'notes'       => 'nullable|string',
        ]);

        $vehicle->update($validated);

        return redirect()->route('admin.vehicles.index')->with('success', 'Автомобилът е обновен.');
    }

    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();
        return redirect()->route('admin.vehicles.index')->with('success', 'Автомобилът е изтрит.');
    }

    /* ---------- EXPORT ---------- */

    public function exportPdf(Vehicle $vehicle)
    {
        $vehicle->load(['customer', 'workOrders']);
        $copy = request()->get('copy', 0);
        return Pdf::loadView('admin.vehicles.export-pdf', compact('vehicle', 'copy'))
                  ->stream('vehicle_'.$vehicle->id.'.pdf');
    }

    public function exportExcel(Vehicle $vehicle)
    {
        return Excel::download(new VehicleExport($vehicle), 'vehicle_'.$vehicle->id.'.xlsx');
    }

    public function exportCsv(Vehicle $vehicle)
    {
        return Excel::download(new VehicleExport($vehicle), 'vehicle_'.$vehicle->id.'.csv', \Maatwebsite\Excel\Excel::CSV);
    }
}