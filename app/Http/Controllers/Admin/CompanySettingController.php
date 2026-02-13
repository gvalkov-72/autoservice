<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompanySetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class CompanySettingController extends Controller
{
    /**
     * Валидационни правила за всички методи
     */
    protected function validationRules($id = null): array
    {
        $rules = [
            'name'            => 'required|string|max:255',
            'city'            => 'nullable|string|max:255',
            'address'         => 'nullable|string|max:255',
            'vat_number'      => 'nullable|string|max:20',
            'contact_person'  => 'nullable|string|max:255',
            'iban'            => 'nullable|string|max:34',
            'bank_name'       => 'nullable|string|max:255',
            'bic'             => 'nullable|string|max:11',
            'phone'           => 'nullable|string|max:255',
            'email'           => 'nullable|email|max:255',
            'website'         => 'nullable|url|max:255',
            'invoice_footer'  => 'nullable|string|max:500',
            'logo'            => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active'       => 'sometimes|boolean',
        ];

        return $rules;
    }

    /**
     * Деактивира всички други активни записи,
     * ако подаденият запис е маркиран като активен.
     *
     * @param \App\Models\CompanySetting $companySetting
     * @return void
     */
    protected function deactivateOthers(CompanySetting $companySetting): void
    {
        if ($companySetting->is_active) {
            CompanySetting::where('is_active', true)
                ->where('id', '!=', $companySetting->id)
                ->update(['is_active' => false]);
        }
    }

    /* ------------------------------------------------------------------
       LIST & LIVE SEARCH
    ------------------------------------------------------------------ */

    public function index(Request $request)
    {
        $companySettings = CompanySetting::query()
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('city', 'like', "%{$search}%")
                        ->orWhere('vat_number', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->orderBy('is_active', 'desc')  // активните най-отгоре
            ->orderBy('name', 'asc')
            ->paginate(15)
            ->withQueryString();

        if ($request->wantsJson()) {
            $html = view('admin.company-settings.partials.rows', compact('companySettings'))->render();
            return response()->json([
                'html' => $html,
                'total' => $companySettings->total()
            ]);
        }

        return view('admin.company-settings.index', [
            'companySettings' => $companySettings,
            'search' => $request->search,
        ]);
    }

    public function liveSearch(Request $request)
    {
        $companySettings = CompanySetting::query()
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('city', 'like', "%{$search}%")
                        ->orWhere('vat_number', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->orderBy('is_active', 'desc')
            ->orderBy('name', 'asc')
            ->paginate(15);

        $html = view('admin.company-settings.partials.rows', compact('companySettings'))->render();

        return response()->json([
            'html' => $html,
            'total' => $companySettings->total()
        ]);
    }

    /* ------------------------------------------------------------------
       CREATE & STORE
    ------------------------------------------------------------------ */

    public function create()
    {
        return view('admin.company-settings.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->validationRules());

        // Обработка на булево поле
        $validated['is_active'] = $request->has('is_active');

        DB::beginTransaction();
        try {
            // Обработка на лого
            if ($request->hasFile('logo')) {
                $path = $request->file('logo')->store('company-logos', 'public');
                $validated['logo_path'] = $path;
            }

            $companySetting = CompanySetting::create($validated);

            // Ако е активен, деактивираме всички други
            if ($companySetting->is_active) {
                $this->deactivateOthers($companySetting);
            }

            DB::commit();

            return redirect()
                ->route('admin.company-settings.show', $companySetting->id)
                ->with('success', 'Фирмените данни са създадени успешно.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Грешка при запис: ' . $e->getMessage());
        }
    }

    /* ------------------------------------------------------------------
       SHOW, EDIT, UPDATE, DESTROY
    ------------------------------------------------------------------ */

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
        $validated = $request->validate($this->validationRules($companySetting->id));

        $validated['is_active'] = $request->has('is_active');

        DB::beginTransaction();
        try {
            // Обработка на лого
            if ($request->hasFile('logo')) {
                // Изтриване на старо лого
                if ($companySetting->logo_path) {
                    Storage::disk('public')->delete($companySetting->logo_path);
                }
                $path = $request->file('logo')->store('company-logos', 'public');
                $validated['logo_path'] = $path;
            }

            $companySetting->update($validated);

            // Ако е активен, деактивираме всички други
            if ($companySetting->is_active) {
                $this->deactivateOthers($companySetting);
            }

            DB::commit();

            return redirect()
                ->route('admin.company-settings.show', $companySetting->id)
                ->with('success', 'Фирмените данни са обновени успешно.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Грешка при обновяване: ' . $e->getMessage());
        }
    }

    public function destroy(CompanySetting $companySetting)
    {
        try {
            // Изтриване на логото от storage
            if ($companySetting->logo_path) {
                Storage::disk('public')->delete($companySetting->logo_path);
            }

            $companySetting->delete();

            return redirect()
                ->route('admin.company-settings.index')
                ->with('success', 'Фирмените данни са изтрити успешно.');
        } catch (\Exception $e) {
            return back()->with('error', 'Грешка при изтриване: ' . $e->getMessage());
        }
    }

    /* ------------------------------------------------------------------
       PRINT & PDF
    ------------------------------------------------------------------ */

    public function print(CompanySetting $companySetting)
    {
        return view('admin.company-settings.print', compact('companySetting'));
    }

    public function pdf(CompanySetting $companySetting)
    {
        $pdf = Pdf::loadView('admin.company-settings.pdf', compact('companySetting'));
        return $pdf->stream('firma-' . $companySetting->id . '.pdf');
    }
}