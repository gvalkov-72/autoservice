<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;

class ServiceCategoryController extends Controller
{
    public function index()
    {
        $categories = ServiceCategory::ordered()->paginate(20);
        return view('admin.service-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.service-categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255|unique:service_categories,name',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        ServiceCategory::create($validated);

        return redirect()->route('admin.service-categories.index')
            ->with('success', 'Категорията е създадена успешно.');
    }

    public function show(ServiceCategory $category)
    {
        return view('admin.service-categories.show', compact('category'));
    }

    public function edit(ServiceCategory $category)
    {
        return view('admin.service-categories.edit', compact('category'));
    }

    public function update(Request $request, ServiceCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|max:255|unique:service_categories,name,' . $category->id,
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $category->update($validated);

        return redirect()->route('admin.service-categories.index')
            ->with('success', 'Категорията е обновена успешно.');
    }

    public function destroy(ServiceCategory $category)
    {
        if ($category->services()->count() > 0) {
            return redirect()->route('admin.service-categories.index')
                ->with('error', 'Не можете да изтриете категория, която има услуги.');
        }

        $category->delete();

        return redirect()->route('admin.service-categories.index')
            ->with('success', 'Категорията е изтрита успешно.');
    }
}