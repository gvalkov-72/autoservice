<?php
// database/migrations/[timestamp]_migrate_vehicles_from_work_orders_v2.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Customer;
use App\Models\Vehicle;
use App\Models\WorkOrder;

return new class extends Migration
{
    public function up(): void
    {
        echo "Започва миграция на автомобили от поръчки...\n";
        echo "=========================================\n";
        
        // Изключване на foreign key checks за по-бърза работа
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        $start_time = microtime(true);
        $totalProcessed = 0;
        $vehiclesCreated = 0;
        $ordersUpdated = 0;
        
        // 1. ВЗИМАМЕ ВСИЧКИ ПОРЪЧКИ С ДАННИ ЗА АВТОМОБИЛ
        echo "1. Извличане на поръчки с данни за автомобили...\n";
        $orders = DB::table('work_orders')
            ->where(function($query) {
                $query->whereNotNull('vehicle')
                      ->orWhereNotNull('plate_number')
                      ->orWhereNotNull('chassis_number');
            })
            ->whereNull('vehicle_id') // само тези без vehicle_id
            ->orderBy('id')
            ->cursor(); // Използваме cursor за по-добра памет
            
        $orderCount = DB::table('work_orders')
            ->where(function($query) {
                $query->whereNotNull('vehicle')
                      ->orWhereNotNull('plate_number')
                      ->orWhereNotNull('chassis_number');
            })
            ->whereNull('vehicle_id')
            ->count();
            
        echo "   Намерени поръчки за обработка: " . number_format($orderCount) . "\n\n";
        
        // Масив за проследяване на вече създадени автомобили
        $existingVehicles = [];
        
        // 2. ПРЕДВАРИТЕЛНО ИЗВЛИЧАНЕ НА ВСИЧКИ СЪЩЕСТВАЩИ АВТОМОБИЛИ
        echo "2. Зареждане на съществуващи автомобили...\n";
        $vehicles = Vehicle::all();
        foreach ($vehicles as $vehicle) {
            $key = $this->generateVehicleKey(
                $vehicle->customer_id,
                $vehicle->vehicle,
                $vehicle->plate_number,
                $vehicle->chassis_number
            );
            $existingVehicles[$key] = $vehicle->id;
        }
        echo "   Заредени " . count($existingVehicles) . " съществуващи автомобила\n\n";
        
        // 3. ПРЕДВАРИТЕЛНО ИЗВЛИЧАНЕ НА КЛИЕНТИ (за по-бързо търсене)
        echo "3. Индексиране на клиенти по име...\n";
        $customersByName = [];
        $customers = Customer::all(['id', 'name']);
        foreach ($customers as $customer) {
            $normalizedName = $this->normalizeName($customer->name);
            $customersByName[$normalizedName] = $customer->id;
        }
        echo "   Индексирани " . count($customersByName) . " клиента\n\n";
        
        echo "4. Обработка на поръчки:\n";
        echo "   [Прогрес: 0%]\r";
        
        $batchUpdates = [];
        $batchSize = 500;
        
        foreach ($orders as $order) {
            $totalProcessed++;
            
            // Показване на прогрес
            if ($totalProcessed % 1000 == 0) {
                $percentage = round(($totalProcessed / $orderCount) * 100, 1);
                echo "   [Прогрес: {$percentage}%]\r";
            }
            
            // Пропускаме поръчки без име на клиент или без данни за автомобил
            if (empty($order->client_name) || 
                (empty($order->vehicle) && empty($order->plate_number) && empty($order->chassis_number))) {
                continue;
            }
            
            // 4. НАМИРАМЕ КЛИЕНТА (по име)
            $normalizedClientName = $this->normalizeName($order->client_name);
            $customerId = $customersByName[$normalizedClientName] ?? null;
            
            if (!$customerId) {
                // Опитваме се да намерим частично съвпадение
                foreach ($customersByName as $name => $cid) {
                    if (str_contains($name, $normalizedClientName) || 
                        str_contains($normalizedClientName, $name)) {
                        $customerId = $cid;
                        break;
                    }
                }
            }
            
            if (!$customerId) {
                // Използваме специалния клиент за автомобили без ясен клиент
                $customerId = 13690; // ID на 'Автомобили без ясен клиент'
            }
            
            // 5. СОЗДАВАМЕ УНИКАЛЕН КЛЮЧ ЗА АВТОМОБИЛА
            $vehicleKey = $this->generateVehicleKey(
                $customerId,
                $order->vehicle,
                $order->plate_number,
                $order->chassis_number
            );
            
            // 6. ПРОВЕРЯВАМЕ ДАЛИ ВЕЧЕ СЪЩЕСТВУВА
            if (!isset($existingVehicles[$vehicleKey])) {
                // Създаваме нов автомобил
                $newVehicle = Vehicle::create([
                    'customer_id'     => $customerId,
                    'vehicle'         => $order->vehicle,
                    'plate_number'    => $order->plate_number,
                    'chassis_number'  => $order->chassis_number,
                    'last_mileage'    => $order->mileage,
                    'notes'           => 'Мигрирано от поръчка #' . $order->old_id,
                    'is_active'       => true,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);
                
                $existingVehicles[$vehicleKey] = $newVehicle->id;
                $vehiclesCreated++;
            }
            
            // 7. ЗАПАЗВАМЕ ЗА BATCH UPDATE
            $batchUpdates[] = [
                'id' => $order->id,
                'vehicle_id' => $existingVehicles[$vehicleKey]
            ];
            
            // Извършваме batch update
            if (count($batchUpdates) >= $batchSize) {
                $this->batchUpdateWorkOrders($batchUpdates);
                $ordersUpdated += count($batchUpdates);
                $batchUpdates = [];
            }
        }
        
        // Обработка на останалите updates
        if (!empty($batchUpdates)) {
            $this->batchUpdateWorkOrders($batchUpdates);
            $ordersUpdated += count($batchUpdates);
        }
        
        // Включване на foreign key checks обратно
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        
        // 8. СТАТИСТИКА
        $end_time = microtime(true);
        $execution_time = round($end_time - $start_time, 2);
        
        echo "\n\n5. СТАТИСТИКА:\n";
        echo "   =========================================\n";
        echo "   Обработени поръчки: " . number_format($totalProcessed) . "\n";
        echo "   Създадени нови автомобили: " . number_format($vehiclesCreated) . "\n";
        echo "   Общо автомобили в системата: " . number_format(count($existingVehicles)) . "\n";
        echo "   Обновени поръчки с vehicle_id: " . number_format($ordersUpdated) . "\n";
        echo "   Време за изпълнение: " . $execution_time . " секунди\n";
        
        // 9. ФИНАЛНА ПРОВЕРКА
        $ordersWithoutVehicle = DB::table('work_orders')
            ->whereNull('vehicle_id')
            ->where(function($query) {
                $query->whereNotNull('vehicle')
                      ->orWhereNotNull('plate_number')
                      ->orWhereNotNull('chassis_number');
            })
            ->count();
            
        echo "   Поръчки без vehicle_id (след миграция): " . number_format($ordersWithoutVehicle) . "\n";
        echo "   =========================================\n";
    }
    
    /**
     * Генерира уникален ключ за автомобил
     */
    private function generateVehicleKey($customerId, $vehicle, $plateNumber, $chassisNumber): string
    {
        return md5(implode('|', [
            $customerId,
            strtolower(trim($vehicle ?? '')),
            strtolower(trim($plateNumber ?? '')),
            strtolower(trim($chassisNumber ?? ''))
        ]));
    }
    
    /**
     * Нормализира името за сравнение
     */
    private function normalizeName($name): string
    {
        return mb_strtolower(trim(preg_replace('/\s+/', ' ', $name)), 'UTF-8');
    }
    
    /**
     * Batch update на work_orders
     */
    private function batchUpdateWorkOrders(array $updates): void
    {
        if (empty($updates)) {
            return;
        }
        
        $caseSQL = "CASE id ";
        $ids = [];
        
        foreach ($updates as $update) {
            $caseSQL .= "WHEN {$update['id']} THEN {$update['vehicle_id']} ";
            $ids[] = $update['id'];
        }
        
        $caseSQL .= "END";
        $idsStr = implode(',', $ids);
        
        DB::update("
            UPDATE work_orders 
            SET vehicle_id = {$caseSQL}, 
                updated_at = NOW() 
            WHERE id IN ({$idsStr})
        ");
    }
    
    public function down(): void
    {
        echo "Отмяна на миграцията на автомобили...\n";
        
        // Запазваме ID-тата на автомобилите, за да можем да ги възстановим
        $vehicleIds = DB::table('work_orders')
            ->whereNotNull('vehicle_id')
            ->distinct()
            ->pluck('vehicle_id')
            ->toArray();
        
        // Нулираме vehicle_id в поръчките
        DB::table('work_orders')->update(['vehicle_id' => null]);
        
        // Изтриваме само автомобилите, създадени от тази миграция
        if (!empty($vehicleIds)) {
            DB::table('vehicles')
                ->whereIn('id', $vehicleIds)
                ->where('notes', 'like', 'Мигрирано от поръчка #%')
                ->delete();
        }
        
        echo "Миграцията беше отменена.\n";
        echo "• vehicle_id в work_orders е NULL\n";
        echo "• Автомобилите създадени от миграцията са изтрити\n";
    }
};