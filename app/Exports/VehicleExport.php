<?php

namespace App\Exports;

use App\Models\Vehicle;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class VehicleExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private Vehicle $vehicle) {}

    public function collection()
    {
        // основен ред + работни поръчки като отделни редове
        $rows = collect([$this->vehicle]);
        foreach ($this->vehicle->workOrders as $o) $rows->push(['type' => 'work_order', 'data' => $o]);
        return $rows;
    }

    public function headings(): array
    {
        return [
            'ID', 'Тип', 'Рег. №', 'Марка', 'Модел', 'Година', 'VIN', 'Шаси',
            'Клиент', 'Пробег (км)', 'Статус', 'Бележки'
        ];
    }

    public function map($row): array
    {
        if ($row instanceof Vehicle) {
            return [
                $row->id,
                'Превозно средство',
                $row->plate,
                $row->make,
                $row->model,
                $row->year,
                $row->vin,
                $row->chassis,
                $row->customer->name ?? '-',
                $row->mileage,
                $row->is_active ? 'Активен' : 'Неактивен',
                $row->notes
            ];
        }

        if (($row['type'] ?? null) === 'work_order') {
            $o = $row['data'];
            return [
                '', // ID
                'Работна поръчка',
                '', // Рег. №
                '', // Марка
                '', // Модел
                '', // Година
                '', // VIN
                '', // Шаси
                '', // Клиент
                '', // Пробег
                $o->status,
                $o->number . ' – ' . number_format($o->total, 2) . ' лв. (' . ($o->received_at?->format('d.m.Y') ?? '-') . ')'
            ];
        }

        return array_fill(0, 12, '');
    }
}