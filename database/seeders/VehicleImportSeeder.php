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
     * Seeder за създаване на служебни автомобили
     * за всички клиенти, импортирани от Access,
     * и връзване на старите фактури към тях.
     */
    public function run(): void
    {
        DB::transaction(function () {

            $this->command->info('Започва обработка на клиенти и автомобили...');

            $customers = Customer::with(['vehicles', 'invoices'])->get();

            foreach ($customers as $customer) {

                // Проверка дали клиентът вече има автомобил
                if ($customer->vehicles->count() === 0) {

                    // Създаваме служебен автомобил
                    $vehicle = Vehicle::create([
                        'customer_id' => $customer->id,
                        'brand'       => 'Импортиран',
                        'model'       => 'Неизвестен',
                        'vin'         => null,
                        'year'        => null,
                        'engine'      => null,
                        'fuel_type'   => null,
                        'notes'       => 'Автоматично създаден автомобил при импорт от Access'
                    ]);

                    $this->command->info(
                        "✔ Създаден автомобил за клиент ID {$customer->id}"
                    );

                } else {
                    // Ако вече има автомобил – използваме първия
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
