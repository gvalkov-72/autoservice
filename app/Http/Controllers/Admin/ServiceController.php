<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::with('category')
            ->orderBy('name')
            ->paginate(20);
            
        return view('admin.services.index', compact('services'));
    }

    public function create()
    {
        $categories = ServiceCategory::active()->ordered()->pluck('name', 'id');
        return view('admin.services.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|unique:services,code|max:50',
            'name' => 'required|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'vat_percent' => 'required|numeric|min:0|max:100',
            'duration_minutes' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
            'category_id' => 'nullable|exists:service_categories,id',
            'notes' => 'nullable|string',
        ]);

        Service::create($validated);

        return redirect()->route('admin.services.index')
            ->with('success', 'Услугата е създадена успешно.');
    }

    public function show(Service $service)
    {
        $service->load('category');
        return view('admin.services.show', compact('service'));
    }

    public function edit(Service $service)
    {
        $categories = ServiceCategory::active()->ordered()->pluck('name', 'id');
        return view('admin.services.edit', compact('service', 'categories'));
    }

    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'code' => 'required|unique:services,code,' . $service->id . '|max:50',
            'name' => 'required|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'vat_percent' => 'required|numeric|min:0|max:100',
            'duration_minutes' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
            'category_id' => 'nullable|exists:service_categories,id',
            'notes' => 'nullable|string',
        ]);

        $service->update($validated);

        return redirect()->route('admin.services.index')
            ->with('success', 'Услугата е обновена успешно.');
    }

    public function destroy(Service $service)
    {
        $service->delete();

        return redirect()->route('admin.services.index')
            ->with('success', 'Услугата е изтрита успешно.');
    }
}