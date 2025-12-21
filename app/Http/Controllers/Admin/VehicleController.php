<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\Customer;
use App\Exports\VehicleExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VehicleController extends Controller
{
    /* ---------- CRUD ---------- */

    public function index(Request $request)
    {
        // Заявка с филтри (може да добавиш филтри по марка, модел и т.н.)
        $query = Vehicle::with('customer');

        // Пагинация
        $vehicles = $query->orderBy('plate')->paginate(20);

        return view('admin.vehicles.index', compact('vehicles'));
    }

    public function create()
    {
        $customers = Customer::active()->orderBy('name')->get();
        return view('admin.vehicles.create', compact('customers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // Връзка с клиент
            'customer_id' => 'required|exists:customers,id',
            
            // Основни идентификатори
            'vin' => 'nullable|string|max:50',
            'chassis' => 'nullable|string|max:50',
            'plate' => 'required|string|max:20|unique:vehicles,plate',
            'dk_no' => 'nullable|string|max:50',
            
            // Марка и модел
            'make' => 'required|string|max:100',
            'model' => 'required|string|max:100',
            
            // Допълнителни данни
            'year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'mileage' => 'nullable|integer|min:0',
            'monitor_code' => 'nullable|string|max:50',
            
            // Метаданни
            'order_reference' => 'nullable|string|max:100',
            'po_date' => 'nullable|date',
            'author' => 'nullable|string|max:100',
            
            // Бележки и статус
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
            
            // Стари системни данни
            'old_system_id' => 'nullable|string|max:50',
            'import_batch' => 'nullable|string|max:100',
        ]);

        // Създаване на превозното средство
        $vehicle = Vehicle::create($validated);

        // Логване на действие
        activity()
            ->causedBy(Auth::user())
            ->performedOn($vehicle)
            ->log('Създадено ново превозно средство: ' . $vehicle->plate);

        return redirect()
            ->route('admin.vehicles.show', $vehicle)
            ->with('success', 'Превозното средство "' . $vehicle->plate . '" е създадено успешно.');
    }

    public function show(Vehicle $vehicle)
    {
        $vehicle->load(['customer', 'workOrders' => function ($query) {
            $query->orderBy('received_at', 'desc')->limit(10);
        }]);

        // Статистики за това превозно средство
        $vehicleStats = [
            'total_work_orders' => $vehicle->workOrders()->count(),
            'total_spent' => $vehicle->workOrders()->sum('total'),
            'last_service' => $vehicle->workOrders()->latest()->first()?->received_at,
            'age_years' => $vehicle->year ? date('Y') - $vehicle->year : null,
        ];

        return view('admin.vehicles.show', compact('vehicle', 'vehicleStats'));
    }

    public function edit(Vehicle $vehicle)
    {
        $customers = Customer::active()->orderBy('name')->get();
        return view('admin.vehicles.edit', compact('vehicle', 'customers'));
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $validated = $request->validate([
            // Връзка с клиент
            'customer_id' => 'required|exists:customers,id',
            
            // Основни идентификатори
            'vin' => 'nullable|string|max:50',
            'chassis' => 'nullable|string|max:50',
            'plate' => 'required|string|max:20|unique:vehicles,plate,' . $vehicle->id,
            'dk_no' => 'nullable|string|max:50',
            
            // Марка и модел
            'make' => 'required|string|max:100',
            'model' => 'required|string|max:100',
            
            // Допълнителни данни
            'year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'mileage' => 'nullable|integer|min:0',
            'monitor_code' => 'nullable|string|max:50',
            
            // Метаданни
            'order_reference' => 'nullable|string|max:100',
            'po_date' => 'nullable|date',
            'author' => 'nullable|string|max:100',
            
            // Бележки и статус
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // Запазване на старите данни за лог
        $oldData = $vehicle->toArray();

        // Актуализиране на превозното средство
        $vehicle->update($validated);

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
                ->performedOn($vehicle)
                ->withProperties(['changes' => $changes])
                ->log('Актуализирано превозно средство: ' . $vehicle->plate);
        }

        return redirect()
            ->route('admin.vehicles.show', $vehicle)
            ->with('success', 'Данните на превозното средство "' . $vehicle->plate . '" са актуализирани успешно.');
    }

    public function destroy(Vehicle $vehicle)
    {
        try {
            $vehiclePlate = $vehicle->plate;
            $vehicle->delete();

            // Логване на действието
            activity()
                ->causedBy(Auth::user())
                ->performedOn($vehicle)
                ->log('Деактивирано превозно средство: ' . $vehiclePlate);
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('admin.vehicles.index')
                ->with('error', 'Не може да изтриете превозното средство, защото има свързани работни поръчки.');
        }
        return redirect()->route('admin.vehicles.index')
            ->with('success', 'Превозното средство "' . $vehiclePlate . '" е деактивирано успешно.');
    }

    /* ---------- ДОПЪЛНИТЕЛНИ МЕТОДИ (по избор) ---------- */

    public function restore($id)
    {
        $vehicle = Vehicle::withTrashed()->findOrFail($id);
        $vehicle->restore();

        activity()
            ->causedBy(Auth::user())
            ->performedOn($vehicle)
            ->log('Възстановено превозно средство: ' . $vehicle->plate);

        return redirect()->route('admin.vehicles.index')
            ->with('success', 'Превозното средство "' . $vehicle->plate . '" е възстановено успешно.');
    }

    /* ---------- EXPORT ---------- */

    public function exportPdf(Vehicle $vehicle)
    {
        $vehicle->load(['customer', 'workOrders' => function ($query) {
            $query->orderBy('received_at', 'desc')->limit(10);
        }]);

        $copy = request()->get('copy', 0);

        return Pdf::loadView('admin.vehicles.export-pdf', compact('vehicle', 'copy'))
            ->setPaper('a4', 'portrait')
            ->stream('vehicle_' . $vehicle->id . '_' . $vehicle->plate . '.pdf');
    }

    public function exportExcel(Vehicle $vehicle)
    {
        return Excel::download(
            new VehicleExport($vehicle),
            'vehicle_' . $vehicle->id . '_' . $vehicle->plate . '.xlsx'
        );
    }

    public function exportCsv(Vehicle $vehicle)
    {
        return Excel::download(
            new VehicleExport($vehicle),
            'vehicle_' . $vehicle->id . '_' . $vehicle->plate . '.csv',
            \Maatwebsite\Excel\Excel::CSV
        );
    }
}