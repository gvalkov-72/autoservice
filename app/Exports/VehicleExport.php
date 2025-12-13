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
        $rows = collect([$this->vehicle]);
        foreach ($this->vehicle->workOrders as $o) $rows->push(['type' => 'order', 'data' => $o]);
        return $rows;
    }

    public function headings(): array
    {
        return [
            'ID', 'Рег. №', 'VIN', 'Марка', 'Модел', 'Година', 'Пробег', 'ДК №', 'Бележки',
            'Тип ред', 'Детайли'
        ];
    }

    public function map($row): array
    {
        if ($row instanceof Vehicle) {
            return [
                $row->id, $row->plate, $row->vin, $row->make, $row->model,
                $row->year, $row->mileage, $row->dk_no, $row->notes, '', ''
            ];
        }
        if (($row['type'] ?? null) === 'order') {
            $o = $row['data'];
            return [
                '', '', '', '', '', '', '', '', '',
                'Поръчка', $o->number.' – '.$o->status.' ('.number_format($o->total, 2).' лв.)'
            ];
        }
        return array_fill(0, 11, '');
    }
}