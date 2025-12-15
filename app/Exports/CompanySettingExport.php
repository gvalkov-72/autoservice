<?php
// app/Exports/CompanySettingExport.php

namespace App\Exports;

use App\Models\CompanySetting;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CompanySettingExport implements FromCollection, WithHeadings, WithMapping
{
    protected $companySetting;

    public function __construct(CompanySetting $companySetting)
    {
        $this->companySetting = $companySetting;
    }

    public function collection()
    {
        return collect([$this->companySetting]);
    }

    public function headings(): array
    {
        return [
            'ID',
            'Име на фирма',
            'Град',
            'Адрес',
            'ЕИК/ЕГН',
            'МОЛ',
            'Телефон',
            'Имейл',
            'IBAN',
            'Банка',
            'BIC',
            'Уебсайт',
            'Статус'
        ];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->name,
            $row->city ?? '-',
            $row->address ?? '-',
            $row->vat_number ?? '-',
            $row->contact_person ?? '-',
            $row->phone ?? '-',
            $row->email ?? '-',
            $row->iban ?? '-',
            $row->bank_name ?? '-',
            $row->bic ?? '-',
            $row->website ?? '-',
            $row->is_active ? 'Активен' : 'Неактивен'
        ];
    }
}