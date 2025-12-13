<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Фактура {{ $invoice->number }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { margin: 0; color: #333; }
        .info-section { margin-bottom: 20px; }
        .info-section h3 { background-color: #f5f5f5; padding: 5px 10px; margin: 10px 0; }
        .info-row { display: flex; justify-content: space-between; margin-bottom: 5px; }
        .info-label { font-weight: bold; min-width: 150px; }
        .table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .table th { background-color: #f2f2f2; text-align: left; padding: 8px; border: 1px solid #ddd; }
        .table td { padding: 8px; border: 1px solid #ddd; }
        .table .total-row td { font-weight: bold; background-color: #f9f9f9; }
        .footer { margin-top: 50px; padding-top: 20px; border-top: 1px solid #ddd; }
        .signature { margin-top: 40px; }
        .signature-line { width: 300px; border-top: 1px solid #000; margin-top: 40px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>ФАКТУРА</h1>
        <h2>№ {{ $invoice->number }}</h2>
    </div>

    <div class="info-section">
        <h3>Информация за фактурата</h3>
        <div class="info-row">
            <span class="info-label">Дата на издаване:</span>
            <span>{{ date('d.m.Y', strtotime($invoice->issue_date)) }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Краен срок за плащане:</span>
            <span>{{ date('d.m.Y', strtotime($invoice->due_date)) }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Статус:</span>
            <span>
                @if($invoice->status == 'draft')
                    Чернова
                @elseif($invoice->status == 'sent')
                    Изпратена
                @elseif($invoice->status == 'paid')
                    Платена
                @elseif($invoice->status == 'cancelled')
                    Анулирана
                @endif
            </span>
        </div>
    </div>

    <div class="info-section">
        <h3>Информация за клиента</h3>
        <div class="info-row">
            <span class="info-label">Име:</span>
            <span>{{ $invoice->customer->name ?? 'N/A' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Телефон:</span>
            <span>{{ $invoice->customer->phone ?? 'N/A' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Имейл:</span>
            <span>{{ $invoice->customer->email ?? 'N/A' }}</span>
        </div>
        @if(isset($invoice->customer->address) && $invoice->customer->address)
        <div class="info-row">
            <span class="info-label">Адрес:</span>
            <span>{{ $invoice->customer->address }}</span>
        </div>
        @endif
    </div>

    <div class="info-section">
        <h3>Информация за поръчката</h3>
        <div class="info-row">
            <span class="info-label">Номер на поръчка:</span>
            <span>{{ $invoice->workOrder->number ?? 'N/A' }}</span>
        </div>
    </div>

    <div class="info-section">
        <h3>Артикули</h3>
        @if(isset($invoice->items) && $invoice->items->count() > 0)
        <table class="table">
            <thead>
                <tr>
                    <th>№</th>
                    <th>Описание</th>
                    <th>Количество</th>
                    <th>Ед. цена</th>
                    <th>ДДС %</th>
                    <th>Общо</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->description }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ number_format($item->unit_price, 2) }} лв.</td>
                    <td>{{ number_format($item->vat_percent, 2) }}%</td>
                    <td>{{ number_format($item->line_total, 2) }} лв.</td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="5" style="text-align: right;"><strong>Общо без ДДС:</strong></td>
                    <td><strong>{{ number_format($invoice->subtotal, 2) }} лв.</strong></td>
                </tr>
                <tr class="total-row">
                    <td colspan="5" style="text-align: right;"><strong>ДДС:</strong></td>
                    <td><strong>{{ number_format($invoice->vat_total, 2) }} лв.</strong></td>
                </tr>
                <tr class="total-row">
                    <td colspan="5" style="text-align: right;"><strong>Крайна сума:</strong></td>
                    <td><strong>{{ number_format($invoice->grand_total, 2) }} лв.</strong></td>
                </tr>
            </tbody>
        </table>
        @else
        <p>Няма добавени артикули.</p>
        @endif
    </div>

    <div class="footer">
        <p>Генерирано на: {{ date('d.m.Y H:i:s') }}</p>
        
        @if(isset($copy) && $copy > 0)
        <p><strong>Копие №{{ $copy }}</strong></p>
        @endif
        
        <div class="signature">
            <p>С уважение,</p>
            <p><strong>{{ config('app.name', 'Автосервиз') }}</strong></p>
            <div class="signature-line"></div>
            <p>Подпис и печат</p>
        </div>
    </div>
</body>
</html>