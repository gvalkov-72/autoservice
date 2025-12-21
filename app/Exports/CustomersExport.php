<?php

namespace App\Exports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class CustomerExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $customers;

    public function __construct($customers)
    {
        // Приема или единичен Customer, или колекция
        $this->customers = $customers instanceof Customer ? collect([$customers]) : $customers;
    }

    public function collection()
    {
        return $this->customers;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Име / Фирма',
            'Тип',
            'ДДС номер',
            'Булстат',
            'Контактно лице',
            'Телефон',
            'Факс',
            'Имейл',
            'Адрес',
            'Град',
            'Статус',
            'Включен в справки',
            'Създаден на'
        ];
    }

    public function map($customer): array
    {
        return [
            $customer->id,
            $customer->name,
            $this->getTypeLabel($customer->type),
            $customer->vat_number ?? '-',
            $customer->bulstat ?? '-',
            $customer->contact_person ?? '-',
            $customer->phone ?? '-',
            $customer->fax ?? '-',
            $customer->email ?? '-',
            $customer->address ?? '-',
            $customer->city ?? '-',
            $customer->is_active ? 'Активен' : 'Неактивен',
            $customer->include_in_reports ? 'Да' : 'Не',
            $customer->created_at->format('d.m.Y H:i'),
        ];
    }

    private function getTypeLabel($type)
    {
        $labels = [
            'customer' => 'Клиент',
            'supplier' => 'Доставчик',
            'both' => 'Клиент и доставчик'
        ];
        
        return $labels[$type] ?? $type;
    }
}