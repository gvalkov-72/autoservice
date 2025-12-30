<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\Vehicle;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;

class VehicleImportSeeder extends Seeder
{
    /**
     * Seeder за автоматично създаване на автомобили
     * за всеки клиент, който няма такъв,
     * и връзване на старите фактури към тях.
     */
    public function run(): void
    {
        DB::transaction(function () {

            $this->command->info('Започва обработка на клиенти и автомобили...');

            $customers = Customer::with(['vehicles'])->get();

            foreach ($customers as $customer) {

                // Ако клиентът няма автомобил – създаваме служебен
                if ($customer->vehicles->count() === 0) {

                    $vehicle = Vehicle::create([
                        'customer_id' => $customer->id,
                        'make'        => 'Импортиран',   // марка
                        'model'       => 'Неизвестен',  // модел
                        'vin'         => null,          // VIN (ако няма)
                        'plate'       => null,          // регистрационен номер
                        'notes'       => 'Автоматично създаден автомобил при импорт от Access',
                        'is_active'   => true,
                    ]);

                    $this->command->info(
                        "✔ Създаден служебен автомобил за клиент ID {$customer->id}"
                    );

                } else {
                    // Ако вече има автомобил – ползваме първия
                    $vehicle = $customer->vehicles->first();
                }

                // Връзваме всички фактури без vehicle_id към този автомобил
                $updated = Invoice::where('customer_id', $customer->id)
                    ->whereNull('vehicle_id')
                    ->update([
                        'vehicle_id' => $vehicle->id
                    ]);

                if ($updated > 0) {
                    $this->command->info(
                        "↳ Връзани {$updated} фактури към автомобил ID {$vehicle->id}"
                    );
                }
            }

            $this->command->info('✔ Импортът на автомобили приключи успешно.');
        });
    }
}
