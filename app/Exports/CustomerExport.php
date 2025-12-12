<?php

namespace App\Exports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CustomerExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private Customer $customer) {}

    public function collection()
    {
        // основен ред + автомобили + поръчки като отделни редове
        $rows = collect([$this->customer]);
        foreach ($this->customer->vehicles as $v) $rows->push(['type' => 'vehicle', 'data' => $v]);
        foreach ($this->customer->workOrders as $o) $rows->push(['type' => 'order', 'data' => $o]);
        return $rows;
    }

    public function headings(): array
    {
        return [
            'ID', 'Тип', 'Име', 'ДДС №', 'Контакт', 'Телефон', 'Имейл', 'Адрес', 'Град', 'Бележки',
            'Автомобил/Поръчка', 'Детайли'
        ];
    }

    public function map($row): array
    {
        if ($row instanceof Customer) {
            return [
                $row->id, 'Клиент', $row->name, $row->vat_number, $row->contact_person,
                $row->phone, $row->email, $row->address, $row->city, $row->notes, '', ''
            ];
        }

        if (($row['type'] ?? null) === 'vehicle') {
            $v = $row['data'];
            return [
                '', 'Автомобил', '', '', '', '', '', '', '', '',
                $v->plate, $v->make.' / '.$v->model.' ('.$v->year.')'
            ];
        }

        if (($row['type'] ?? null) === 'order') {
            $o = $row['data'];
            return [
                '', 'Поръчка', '', '', '', '', '', '', '', '',
                $o->number, $o->status.' – '.number_format($o->total, 2).' лв.'
            ];
        }

        return array_fill(0, 12, '');
    }
}