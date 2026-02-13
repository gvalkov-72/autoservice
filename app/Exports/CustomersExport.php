<?php

namespace App\Exports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CustomersExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithColumnWidths
{
    public function collection()
    {
        return Customer::withCount(['vehicles', 'workOrders'])->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Старо ID',
            'Клиентски номер',
            'Име/Фирма',
            'Имейл',
            'Телефон',
            'Факс',
            'Адрес',
            'Адрес 2',
            'Адрес на управление 1',
            'Адрес на управление 2',
            'МОЛ',
            'Лице за контакт',
            'ДДС номер',
            'Булстат',
            'Булстат буква',
            'Тип документ',
            'Получател',
            'Данни за получател',
            'Дата на ЕИК',
            'Партида/Том',
            'Бележки',
            'Включен в бюлетин',
            'Активен',
            'Клиент',
            'Доставчик',
            'Брой автомобили',
            'Брой поръчки',
            'Създаден на',
            'Обновен на'
        ];
    }

    public function map($customer): array
    {
        return [
            $customer->id,
            $customer->old_id,
            $customer->customer_number,
            $customer->name,
            $customer->email,
            $customer->phone,
            $customer->fax,
            $customer->address,
            $customer->address_2,
            $customer->res_address_1,
            $customer->res_address_2,
            $customer->mol,
            $customer->contact_person,
            $customer->tax_number,
            $customer->bulstat,
            $customer->bulstat_letter,
            $customer->doc_type,
            $customer->receiver,
            $customer->receiver_details,
            $customer->eidate ? $customer->eidate->format('d.m.Y') : '',
            $customer->partida,
            $customer->notes,
            $customer->include_in_mailing ? 'Да' : 'Не',
            $customer->is_active ? 'Активен' : 'Неактивен',
            $customer->is_customer ? 'Да' : 'Не',
            $customer->is_supplier ? 'Да' : 'Не',
            $customer->vehicles_count ?? 0,
            $customer->work_orders_count ?? 0,
            $customer->created_at->format('d.m.Y H:i'),
            $customer->updated_at->format('d.m.Y H:i')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 11,
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E0E0E0']
                ],
                'alignment' => [
                    'wrapText' => true,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ]
            ],
            
            // Set all cells to wrap text
            'A:Z' => [
                'alignment' => [
                    'wrapText' => true,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
                ]
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,  // ID
            'B' => 10,  // Старо ID
            'C' => 15,  // Клиентски номер
            'D' => 30,  // Име/Фирма
            'E' => 25,  // Имейл
            'F' => 15,  // Телефон
            'G' => 15,  // Факс
            'H' => 25,  // Адрес
            'I' => 25,  // Адрес 2
            'J' => 25,  // Адрес на управление 1
            'K' => 25,  // Адрес на управление 2
            'L' => 20,  // МОЛ
            'M' => 20,  // Лице за контакт
            'N' => 15,  // ДДС номер
            'O' => 15,  // Булстат
            'P' => 15,  // Булстат буква
            'Q' => 15,  // Тип документ
            'R' => 20,  // Получател
            'S' => 30,  // Данни за получател
            'T' => 15,  // Дата на ЕИК
            'U' => 15,  // Партида/Том
            'V' => 40,  // Бележки
            'W' => 15,  // Включен в бюлетин
            'X' => 15,  // Активен
            'Y' => 10,  // Клиент
            'Z' => 10,  // Доставчик
            'AA' => 15, // Брой автомобили
            'AB' => 15, // Брой поръчки
            'AC' => 15, // Създаден на
            'AD' => 15, // Обновен на
        ];
    }
}