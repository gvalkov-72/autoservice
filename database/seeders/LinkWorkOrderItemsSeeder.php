<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WorkOrder;
use App\Models\WorkOrderItem;

class LinkWorkOrderItemsSeeder extends Seeder
{
    public function run(): void
    {
        $linked = 0;
        $missing = 0;

        $map = WorkOrder::pluck('id', 'old_id')->toArray();

        WorkOrderItem::orderBy('id')->chunk(1000, function ($items) use (&$linked, &$missing, $map) {

            foreach ($items as $item) {

                $oldId = $item->work_order_old_id;

                if (isset($map[$oldId])) {
                    $item->update([
                        'work_order_id' => $map[$oldId],
                    ]);
                    $linked++;
                } else {
                    $missing++;
                }
            }
        });

        $this->command->info("========================================");
        $this->command->info("🔗 LINK WORK ORDER ITEMS → WORK ORDERS");
        $this->command->info("✅ Свързани редове: {$linked}");
        $this->command->warn("⚠️ Без намерена поръчка: {$missing}");
        $this->command->info("========================================");
    }
}
