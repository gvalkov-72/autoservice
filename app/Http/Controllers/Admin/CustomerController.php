<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function search(Request $request)
    {
        try {
            $query = trim($request->get('q'));
            
            if (strlen($query) < 2) {
                return response()->json([]);
            }
            
            // ПО-ДОБРО ТЪРСЕНЕ С ПРИОРИТЕТИ
            $customers = Customer::query()
                ->where(function ($qry) use ($query) {
                    // ПРИОРИТЕТ 1: Имената, които започват с търсения текст
                    $qry->where('name', 'like', "{$query}%")
                        // ПРИОРИТЕТ 2: Телефони, които започват с търсения текст
                        ->orWhere('phone', 'like', "{$query}%")
                        // ПРИОРИТЕТ 3: Клиентски номера, които започват с търсения текст
                        ->orWhere('customer_number', 'like', "{$query}%")
                        // ПРИОРИТЕТ 4: Съдържа търсения текст някъде в името
                        ->orWhere('name', 'like', "%{$query}%");
                })
                // Сортиране: първо тези, чиито имена започват с търсения текст
                ->orderByRaw("CASE WHEN name LIKE '{$query}%' THEN 0 ELSE 1 END")
                ->orderBy('name', 'asc')  // След това азбучно
                ->orderBy('id', 'asc')    // И накрая по ID
                ->limit(50)
                ->get(['id', 'name', 'phone', 'customer_number']);
            
            return response()->json($customers);
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}