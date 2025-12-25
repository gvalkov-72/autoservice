<?php
// app/Exports/ProductExport.php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $products;

    public function __construct($products)
    {
        $this->products = $products;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        if ($this->products instanceof Product) {
            return collect([$this->products]);
        }
        
        return $this->products;
    }

    /**
     * Заглавия на колоните
     */
    public function headings(): array
    {
        return [
            'ID',
            'Старо ID (Access)',
            'PLU код',
            'Вътрешен код',
            'Име на продукт',
            'Описание',
            'Продажна цена (лв.)',
            'Себестойност (лв.)',
            'Налично количество',
            'Мерна единица',
            'Местоположение',
            'Мин. наличност',
            'Макс. наличност',
            'Баркод',
            'Код доставчик',
            'Производител',
            'ДДС ставка',
            'Счетоводен код',
            'Активен',
            'Услуга',
            'Проследяване на склад',
            'Облагаем с ДДС',
            'Създаден на',
            'Актуализиран на',
        ];
    }

    /**
     * Мапиране на данните
     */
    public function map($product): array
    {
        return [
            $product->id,
            $product->old_id ?? '',
            $product->plu ?? '',
            $product->code ?? '',
            $product->name,
            $product->description ?? '',
            number_format($product->price, 2),
            number_format($product->cost_price, 2),
            number_format($product->quantity, 3),
            $product->unit_of_measure,
            $product->location ?? '',
            $product->min_stock,
            $product->max_stock ?? '',
            $product->barcode ?? '',
            $product->vendor_code ?? '',
            $product->manufacturer ?? '',
            $product->vat_rate ?? '20%',
            $product->accounting_code ?? '',
            $product->is_active ? 'Да' : 'Не',
            $product->is_service ? 'Да' : 'Не',
            $product->track_stock ? 'Да' : 'Не',
            $product->is_taxable ? 'Да' : 'Не',
            $product->created_at->format('d.m.Y H:i'),
            $product->updated_at->format('d.m.Y H:i'),
        ];
    }

    /**
     * Стилове за Excel файла
     */
    public function styles(Worksheet $sheet)
    {
        // Стил за заглавния ред
        $sheet->getStyle('A1:X1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F81BD'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Автоматично филтриране
        $sheet->setAutoFilter('A1:X1');

        // Форматиране на колони с числа
        $sheet->getStyle('G2:H' . ($sheet->getHighestRow()))
            ->getNumberFormat()
            ->setFormatCode('#,##0.00');
        
        $sheet->getStyle('I2:I' . ($sheet->getHighestRow()))
            ->getNumberFormat()
            ->setFormatCode('#,##0.000');

        // Редуващи се цветове на редовете
        for ($row = 2; $row <= $sheet->getHighestRow(); $row++) {
            $color = $row % 2 == 0 ? 'FFFFFF' : 'F2F2F2';
            $sheet->getStyle('A' . $row . ':X' . $row)
                ->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => $color],
                    ],
                ]);
        }

        // Подравняване на колони
        $sheet->getStyle('A2:X' . $sheet->getHighestRow())
            ->applyFromArray([
                'alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
                ],
                'borders' => [
                    'outline' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                    'inside' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ]);

        // Автоматично размер на колоните
        foreach (range('A', 'X') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Замразяване на първия ред
        $sheet->freezePane('A2');

        return [];
    }

    /**
     * Имена на колоните за свойствата
     */
    public function columnFormats(): array
    {
        return [
            'G' => '#,##0.00',
            'H' => '#,##0.00', 
            'I' => '#,##0.000',
        ];
    }
}