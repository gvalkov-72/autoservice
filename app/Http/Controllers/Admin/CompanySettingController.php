<?php
// app/Http/Controllers/Admin/CompanySettingController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompanySetting;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompanySettingController extends Controller
{
    /* ---------- CRUD ---------- */

    public function index()
    {
        $companySettings = CompanySetting::paginate(25);
        return view('admin.company-settings.index', compact('companySettings'));
    }

    public function create()
    {
        return view('admin.company-settings.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'city'           => 'nullable|string|max:255',
            'address'        => 'nullable|string|max:500',
            'vat_number'     => 'nullable|string|max:50',
            'contact_person' => 'nullable|string|max:255',
            'iban'           => 'nullable|string|max:34',
            'bank_name'      => 'nullable|string|max:100',
            'bic'            => 'nullable|string|max:11',
            'phone'          => 'nullable|string|max:20',
            'email'          => 'nullable|email|max:100',
            'website'        => 'nullable|url|max:100',
            'invoice_footer' => 'nullable|string',
            'logo'           => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Обработка на лого
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('company-logos', 'public');
            $validated['logo_path'] = $path;
        }

        // Ако се маркира като активен, деактивираме всички останали
        if ($request->has('is_active') && $request->is_active) {
            CompanySetting::where('is_active', true)->update(['is_active' => false]);
            $validated['is_active'] = true;
        }

        CompanySetting::create($validated);

        return redirect()->route('admin.company-settings.index')->with('success', 'Данните на автосервиза са добавени.');
    }

    public function show(CompanySetting $companySetting)
    {
        return view('admin.company-settings.show', compact('companySetting'));
    }

    public function edit(CompanySetting $companySetting)
    {
        return view('admin.company-settings.edit', compact('companySetting'));
    }

    public function update(Request $request, CompanySetting $companySetting)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'city'           => 'nullable|string|max:255',
            'address'        => 'nullable|string|max:500',
            'vat_number'     => 'nullable|string|max:50',
            'contact_person' => 'nullable|string|max:255',
            'iban'           => 'nullable|string|max:34',
            'bank_name'      => 'nullable|string|max:100',
            'bic'            => 'nullable|string|max:11',
            'phone'          => 'nullable|string|max:20',
            'email'          => 'nullable|email|max:100',
            'website'        => 'nullable|url|max:100',
            'invoice_footer' => 'nullable|string',
            'logo'           => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Обработка на лого
        if ($request->hasFile('logo')) {
            // Изтриваме старото лого
            if ($companySetting->logo_path) {
                Storage::disk('public')->delete($companySetting->logo_path);
            }
            
            $path = $request->file('logo')->store('company-logos', 'public');
            $validated['logo_path'] = $path;
        }

        // Ако се маркира като активен, деактивираме всички останали
        if ($request->has('is_active') && $request->is_active) {
            CompanySetting::where('id', '!=', $companySetting->id)
                ->where('is_active', true)
                ->update(['is_active' => false]);
            $validated['is_active'] = true;
        } else {
            $validated['is_active'] = false;
        }

        $companySetting->update($validated);

        return redirect()->route('admin.company-settings.index')->with('success', 'Данните на автосервиза са обновени.');
    }

    public function destroy(CompanySetting $companySetting)
    {
        try {
            // Не позволяваме изтриване на активните настройки
            if ($companySetting->is_active) {
                return redirect()->route('admin.company-settings.index')
                    ->with('error','Не може да изтриете активните настройки.');
            }

            // Изтриваме логото ако има
            if ($companySetting->logo_path) {
                Storage::disk('public')->delete($companySetting->logo_path);
            }

            $companySetting->delete();
            
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('admin.company-settings.index')
                ->with('error','Не може да изтриете настройките.');
        }
        
        return redirect()->route('admin.company-settings.index')
            ->with('success','Настройките са изтрити.');
    }

    /* ---------- EXPORT ---------- */

    public function exportPdf(CompanySetting $companySetting)
    {
        $copy = request()->get('copy', 0);
        return Pdf::loadView('admin.company-settings.export-pdf', compact('companySetting', 'copy'))
            ->stream('company_setting_' . $companySetting->id . '.pdf');
    }

    public function exportExcel(CompanySetting $companySetting)
    {
        return Excel::download(new \App\Exports\CompanySettingExport($companySetting), 'company_setting_' . $companySetting->id . '.xlsx');
    }

    public function exportCsv(CompanySetting $companySetting)
    {
        return Excel::download(new \App\Exports\CompanySettingExport($companySetting), 'company_setting_' . $companySetting->id . '.csv', \Maatwebsite\Excel\Excel::CSV);
    }
}