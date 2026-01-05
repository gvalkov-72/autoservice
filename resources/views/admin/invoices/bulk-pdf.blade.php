<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <style>
        body { 
            font-family: DejaVu Sans, sans-serif; 
            font-size: 12px; 
            color: #000;
        }
        .invoice { 
            page-break-after: always; 
            margin-bottom: 30px;
            padding: 20px;
        }
        .invoice:last-child { 
            page-break-after: auto; 
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
        }
        th, td { 
            border: 1px solid #000; 
            padding: 8px; 
            text-align: left;
        }
        th { 
            background-color: #f2f2f2; 
            text-align: center;
        }
        .header { 
            text-align: center; 
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
        }
        .company-info, .customer-info {
            margin-bottom: 15px;
        }
        .right {
            text-align: right;
        }
        .totals-table {
            width: 40%;
            border-collapse: collapse;
            margin-top: 15px;
            float: right;
        }
        .totals-table td {
            border: 1px solid #000;
            padding: 6px;
        }
        .clearfix {
            clear: both;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>

@foreach($invoices as $invoice)
    <div class="invoice">
        <div class="header">
            <h2>ФАКТУРА №: {{ $invoice->invoice_number }}</h2>
            <p><strong>Дата на издаване:</strong> {{ $invoice->issue_date ? $invoice->issue_date->format('d.m.Y') : 'N/A' }}</p>
        </div>
        
        <div class="company-info">
            <h4>Доставчик:</h4>
            <p>{{ config('company.name', 'Няма фирма') }}<br>
            {{ config('company.address', '') }}<br>
            ЕИК: {{ config('company.eik', '') }}<br>
            ДДС №: {{ config('company.vat_number', '') }}</p>
        </div>
        
        <div class="customer-info">
            <h4>Клиент:</h4>
            <p>{{ $invoice->customer ? $invoice->customer->name : 'Няма клиент' }}<br>
            {{ $invoice->customer ? $invoice->customer->address : '' }}<br>
            ЕИК: {{ $invoice->customer ? $invoice->customer->eik : '' }}<br>
            ДДС №: {{ $invoice->customer ? $invoice->customer->vat_number : '' }}</p>
        </div>
        
        @if($invoice->items->isNotEmpty())
        <table style="margin-top: 20px;">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Описание</th>
                    <th>Количество</th>
                    <th>Ед. цена</th>
                    <th>Общо</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $index => $item)
                <tr>
                    <td class="right">{{ $index + 1 }}</td>
                    <td>{{ $item->description ?? 'N/A' }}</td>
                    <td class="right">{{ number_format($item->quantity ?? 0, 2) }}</td>
                    <td class="right">{{ number_format($item->unit_price ?? 0, 2) }} лв</td>
                    <td class="right">{{ number_format($item->total_price ?? 0, 2) }} лв</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
        
        {{-- СУМИ --}}
        <table class="totals-table">
            <tr>
                <td>Данъчна основа</td>
                <td class="right">{{ number_format($invoice->net_total ?? 0, 2) }} лв</td>
            </tr>
            <tr>
                <td>ДДС ({{ $invoice->vat_rate ?? 0 }}%)</td>
                <td class="right">{{ number_format($invoice->vat_amount ?? 0, 2) }} лв</td>
            </tr>
            <tr>
                <td><strong>ОБЩО</strong></td>
                <td class="right"><strong>{{ number_format($invoice->grand_total ?? 0, 2) }} лв</strong></td>
            </tr>
        </table>
        
        <div class="clearfix"></div>
        
        <div style="margin-top: 30px;">
            <p><strong>Статус на плащане:</strong> 
                <span style="color: {{ $invoice->payment_status === 'paid' ? 'green' : 'red' }};">
                    {{ $invoice->payment_status === 'paid' ? 'ПЛАТЕНА' : 'НЕПЛАТЕНА' }}
                </span>
            </p>
            <p><strong>Дата на плащане:</strong> {{ $invoice->payment_date ? $invoice->payment_date->format('d.m.Y') : 'Не е платена' }}</p>
        </div>
        
        <div class="footer">
            Страница {{ $loop->iteration }} от {{ $loop->count }} | Генерирано на: {{ now()->format('d.m.Y H:i') }}
        </div>
    </div>
@endforeach

</body>
</html>