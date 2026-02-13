<?php

namespace App\Imports;

use App\Models\Customer;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CustomersImport implements ToCollection, WithHeadingRow
{
    protected $updateExisting;
    protected $errors = [];
    protected $importedCount = 0;
    protected $updatedCount = 0;
    
    public function __construct($updateExisting = false)
    {
        $this->updateExisting = $updateExisting;
    }
    
    public function collection(Collection $rows)
    {
        DB::beginTransaction();
        
        try {
            foreach ($rows as $index => $row) {
                $rowNumber = $index + 2; // +2 защото първият ред е заглавният
                
                // Пропускаме празни редове
                if (empty($row['name']) && empty($row['customer_number'])) {
                    continue;
                }
                
                // Подготвяме данните за валидация
                $data = [
                    'name' => $row['name'] ?? null,
                    'customer_number' => $row['customer_number'] ?? null,
                    'email' => $row['email'] ?? null,
                    'phone' => $row['phone'] ?? null,
                    'mol' => $row['mol'] ?? null,
                    'contact_person' => $row['contact_person'] ?? null,
                    'tax_number' => $row['tax_number'] ?? null,
                    'bulstat' => $row['bulstat'] ?? null,
                    'address' => $row['address'] ?? null,
                    'is_customer' => $row['is_customer'] ?? 1,
                    'is_supplier' => $row['is_supplier'] ?? 0,
                    'is_active' => $row['is_active'] ?? 1,
                ];
                
                // Валидация на данните
                $validator = Validator::make($data, [
                    'name' => 'required|string|max:255',
                    'customer_number' => 'nullable|string|max:255',
                    'email' => 'nullable|email|max:255',
                    'phone' => 'nullable|string|max:50',
                    'mol' => 'nullable|string|max:255',
                    'contact_person' => 'nullable|string|max:255',
                    'tax_number' => 'nullable|string|max:50',
                    'bulstat' => 'nullable|string|max:50',
                    'address' => 'nullable|string|max:500',
                    'is_customer' => 'boolean',
                    'is_supplier' => 'boolean',
                    'is_active' => 'boolean',
                ]);
                
                if ($validator->fails()) {
                    $this->errors[] = "Ред $rowNumber: " . implode(', ', $validator->errors()->all());
                    continue;
                }
                
                // Преобразуване на булеви стойности
                $isCustomer = filter_var($data['is_customer'], FILTER_VALIDATE_BOOLEAN);
                $isSupplier = filter_var($data['is_supplier'], FILTER_VALIDATE_BOOLEAN);
                $isActive = filter_var($data['is_active'], FILTER_VALIDATE_BOOLEAN);
                
                // Проверка дали клиентът съществува
                $existingCustomer = null;
                
                // Първо търсим по customer_number
                if (!empty($data['customer_number'])) {
                    $existingCustomer = Customer::where('customer_number', $data['customer_number'])->first();
                }
                
                // Ако не намерим по customer_number, търсим по bulstat
                if (!$existingCustomer && !empty($data['bulstat'])) {
                    $existingCustomer = Customer::where('bulstat', $data['bulstat'])->first();
                }
                
                // Ако не намерим по bulstat, търсим по име и телефон
                if (!$existingCustomer && !empty($data['name']) && !empty($data['phone'])) {
                    $existingCustomer = Customer::where('name', $data['name'])
                        ->where('phone', $data['phone'])
                        ->first();
                }
                
                if ($existingCustomer && $this->updateExisting) {
                    // Обновяване на съществуващ клиент
                    $existingCustomer->update([
                        'name' => $data['name'],
                        'customer_number' => $data['customer_number'] ?? $existingCustomer->customer_number,
                        'email' => $data['email'] ?? $existingCustomer->email,
                        'phone' => $data['phone'] ?? $existingCustomer->phone,
                        'mol' => $data['mol'] ?? $existingCustomer->mol,
                        'contact_person' => $data['contact_person'] ?? $existingCustomer->contact_person,
                        'tax_number' => $data['tax_number'] ?? $existingCustomer->tax_number,
                        'bulstat' => $data['bulstat'] ?? $existingCustomer->bulstat,
                        'address' => $data['address'] ?? $existingCustomer->address,
                        'is_customer' => $isCustomer,
                        'is_supplier' => $isSupplier,
                        'is_active' => $isActive,
                        'include_in_mailing' => true,
                    ]);
                    $this->updatedCount++;
                } elseif (!$existingCustomer) {
                    // Създаване на нов клиент
                    $customerData = [
                        'name' => $data['name'],
                        'customer_number' => $data['customer_number'] ?? $this->generateCustomerNumber(),
                        'email' => $data['email'],
                        'phone' => $data['phone'],
                        'mol' => $data['mol'],
                        'contact_person' => $data['contact_person'],
                        'tax_number' => $data['tax_number'],
                        'bulstat' => $data['bulstat'],
                        'address' => $data['address'],
                        'is_customer' => $isCustomer,
                        'is_supplier' => $isSupplier,
                        'is_active' => $isActive,
                        'include_in_mailing' => true,
                    ];
                    
                    // Попълваме и други полета, ако са предоставени
                    if (isset($row['fax'])) $customerData['fax'] = $row['fax'];
                    if (isset($row['address_2'])) $customerData['address_2'] = $row['address_2'];
                    if (isset($row['bulstat_letter'])) $customerData['bulstat_letter'] = $row['bulstat_letter'];
                    if (isset($row['doc_type'])) $customerData['doc_type'] = $row['doc_type'];
                    if (isset($row['receiver'])) $customerData['receiver'] = $row['receiver'];
                    if (isset($row['notes'])) $customerData['notes'] = $row['notes'];
                    
                    Customer::create($customerData);
                    $this->importedCount++;
                }
            }
            
            DB::commit();
            
            // Запазваме резултатите в сесия
            session()->flash('import_results', [
                'imported' => $this->importedCount,
                'updated' => $this->updatedCount,
                'errors' => $this->errors
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    private function generateCustomerNumber()
    {
        $date = date('ymd');
        $lastCustomer = Customer::orderBy('id', 'desc')->first();
        $lastId = $lastCustomer ? $lastCustomer->id : 0;
        return 'IMP' . $date . str_pad($lastId + 1, 4, '0', STR_PAD_LEFT);
    }
    
    public function getErrors()
    {
        return $this->errors;
    }
    
    public function getImportedCount()
    {
        return $this->importedCount;
    }
    
    public function getUpdatedCount()
    {
        return $this->updatedCount;
    }
}