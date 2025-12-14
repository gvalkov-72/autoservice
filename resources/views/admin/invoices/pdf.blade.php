<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <title>Фактура №{{ $invoice->invoice_number }}</title>
    <style>
        * { 
            margin: 0; 
            padding: 0; 
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10pt;
            width: 180mm;
            margin: 0 auto;
            padding: 10mm;
            line-height: 1.2;
        }
        
        /* ТРИ КОЛОНИ */
        .three-column-container {
            width: 100%;
            margin-bottom: 10mm;
            display: table;
        }
        
        .client-box, .supplier-box, .invoice-box {
            display: table-cell;
            border: 1pt solid #000;
            border-radius: 5px;
            padding: 2mm;
            vertical-align: top;
        }
        
        .client-box, .supplier-box {
            width: 40%;
        }
        
        .invoice-box {
            width: 20%;
            text-align: center;
        }
        
        .section-title {
            font-weight: bold;
            font-size: 11pt;
            margin-bottom: 2mm;
            padding-bottom: 1mm;
            border-bottom: 1pt solid #000;
        }
        
        .invoice-title {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            text-align: center;
            margin-bottom: 5mm;
            margin-top: 2mm;
        }
        
        .invoice-number-large {
            font-size: 12pt;
            font-weight: bold;
            text-align: center;
            margin-bottom: 0;
        }
        
        .invoice-date-large {
            font-size: 10pt;
            font-weight: bold;
            text-align: center;
            margin-top: 0;
        }
        
        /* ПОЛЕТА - БЕЗ overflow: hidden */
        .info-line {
            margin-bottom: 1.5mm;
            clear: both;
        }
        
        .label {
            float: left;
            width: 15mm;
            font-weight: bold;
            font-size: 8pt;
            color: #666;
        }
        
        .value {
            margin-left: 16mm;
            border-bottom: 0.5pt dotted #666;
            padding-bottom: 0.5mm;
            font-size: 9pt;
        }
        
        /* ТАБЛИЦА */
        .items-title {
            font-weight: bold;
            font-size: 11pt;
            margin-bottom: 2mm;
            margin-top: 5mm;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5mm;
            font-size: 9pt;
            border: 1pt solid #000;
        }
        
        .items-table th {
            border: 1pt solid #000;
            background: #f0f0f0;
            padding: 1mm;
            text-align: center;
            font-weight: bold;
        }
        
        .items-table td {
            border: 1pt solid #000;
            padding: 1mm;
        }
        
        .col-no { width: 5%; }
        .col-name { width: 45%; }
        .col-brand { width: 15%; }
        .col-qty { width: 10%; }
        .col-unit-price { width: 12%; }
        .col-total { width: 13%; }
        
        /* ОБЩИ СУМИ - БЕЗ overflow: hidden */
        .totals-vat-container {
            width: 100%;
            margin-bottom: 10mm;
            clear: both;
        }
        
        .totals-box {
            width: 48%;
            float: left;
        }
        
        .vat-box {
            width: 48%;
            float: right;
            border: 1pt solid #000;
            padding: 2mm;
            font-size: 9pt;
            border-radius: 5px;
        }
        
        .total-row {
            margin-bottom: 2mm;
            clear: both;
        }
        
        .total-label {
            float: left;
            width: 30mm;
            font-weight: bold;
            text-align: right;
            padding-right: 3mm;
            font-size: 10pt;
        }
        
        .total-value {
            float: right;
            width: 25mm;
            font-weight: bold;
            text-align: right;
            border-bottom: 1pt solid #000;
            padding-bottom: 1mm;
            font-size: 10pt;
        }
        
        .vat-title {
            font-weight: bold;
            margin-bottom: 1mm;
        }
        
        /* ДОЛНА ИНФОРМАЦИЯ - БЕЗ overflow: hidden */
        .details-container {
            width: 100%;
            margin-bottom: 10mm;
            clear: both;
        }
        
        .details-left, .details-right {
            width: 48%;
        }
        
        .details-left {
            float: left;
        }
        
        .details-right {
            float: right;
        }
        
        .detail-line {
            margin-bottom: 1.5mm;
            clear: both;
        }
        
        .detail-label {
            float: left;
            width: 25mm;
            font-weight: bold;
            font-size: 9pt;
        }
        
        .detail-value {
            margin-left: 27mm;
            border-bottom: 0.5pt dotted #666;
            padding-bottom: 0.5mm;
            font-size: 9pt;
        }
        
        /* ПОДПИСИ - БЕЗ overflow: hidden */
        .signatures-container {
            width: 100%;
            margin-top: 15mm;
            clear: both;
        }
        
        .signature-left, .signature-right {
            width: 48%;
            text-align: center;
            float: left;
        }
        
        .signature-right {
            float: right;
        }
        
        .signature-text {
            margin-bottom: 10mm;
            font-size: 10pt;
        }
        
        .signature-line {
            border-top: 1pt solid #000;
            width: 100%;
            height: 10mm;
        }
        
        /* ПРЕХОД МЕЖДУ СТРАНИЦИ */
        .page-break {
            page-break-after: always;
        }
        
        /* CLEARFIX ЗА FLOAT ЕЛЕМЕНТИ */
        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }
    </style>
</head>
<body>
    <!-- ПЪРВА СТРАНИЦА -->
    <div>
        <!-- ТРИ КОЛОНИ -->
        <div class="three-column-container">
            <div class="client-box">
                <div class="section-title">Клиент</div>
                <div class="info-line">
                    <div class="label">Клиент:</div>
                    <div class="value">{{ $invoice->customer->name ?? 'Няма данни' }}</div>
                </div>
                <div class="info-line">
                    <div class="label">Град:</div>
                    <div class="value">{{ $invoice->customer->city ?? '' }}</div>
                </div>
                <div class="info-line">
                    <div class="label">Адрес:</div>
                    <div class="value">{{ $invoice->customer->address ?? '' }}</div>
                </div>
                <div class="info-line">
                    <div class="label">БИК / ЕГН:</div>
                    <div class="value">{{ $invoice->customer->vat_number ?? '' }}</div>
                </div>
                <div class="info-line">
                    <div class="label">ЗДДС №:</div>
                    <div class="value">{{ $invoice->customer->vat_number ?? '' }}</div>
                </div>
                <div class="info-line">
                    <div class="label">МОЛ:</div>
                    <div class="value">{{ $invoice->customer->contact_person ?? '' }}</div>
                </div>
                <div class="info-line">
                    <div class="label">IBAN:</div>
                    <div class="value">{{ $invoice->customer->iban ?? ' ' }}</div>
                </div>
                <div class="info-line">
                    <div class="label">Банка:</div>
                    <div class="value">{{ $invoice->customer->bank_name ?? ' ' }}</div>
                </div>
                <div class="info-line">
                    <div class="label">BIC:</div>
                    <div class="value">{{ $invoice->customer->bic ?? ' ' }}</div>
                </div>
            </div>
            
            <div class="invoice-box">
                <div class="invoice-title">ФАКТУРА</div>
                @php
                    $issueDate = $invoice->issue_date;
                    if (is_string($issueDate)) {
                        $issueDate = \Carbon\Carbon::parse($issueDate);
                    }
                @endphp
                <div class="invoice-number-large">№ {{ $invoice->invoice_number }}</div>
                <div class="invoice-date-large">Дата {{ $issueDate ? $issueDate->format('d.m.Y') : now()->format('d.m.Y') }}</div>
            </div>
            
            <div class="supplier-box">
                <div class="section-title">Доставчик</div>
                @php
                    $company = [
                        'name' => 'ВАШАТА КОМПАНИЯ АД',
                        'city' => 'ВАШИЯТ ГРАД',
                        'address' => 'ул. ВАШАТА АДРЕС',
                        'vat_number' => '000000000',
                        'contact_person' => 'ВАШЕТО ИМЕ',
                        'iban' => 'BG00XXXX00000000000000',
                        'bank_name' => 'ВАШАТА БАНКА АД',
                        'bic' => 'XXXXXXXX'
                    ];
                @endphp
                <div class="info-line">
                    <div class="label">Доставчик:</div>
                    <div class="value">{{ $company['name'] }}</div>
                </div>
                <div class="info-line">
                    <div class="label">Град:</div>
                    <div class="value">{{ $company['city'] }}</div>
                </div>
                <div class="info-line">
                    <div class="label">Адрес:</div>
                    <div class="value">{{ $company['address'] }}</div>
                </div>
                <div class="info-line">
                    <div class="label">БИК / ЕГН:</div>
                    <div class="value">{{ $company['vat_number'] }}</div>
                </div>
                <div class="info-line">
                    <div class="label">ЗДДС №:</div>
                    <div class="value">{{ $company['vat_number'] }}</div>
                </div>
                <div class="info-line">
                    <div class="label">МОЛ:</div>
                    <div class="value">{{ $company['contact_person'] }}</div>
                </div>
                <div class="info-line">
                    <div class="label">IBAN:</div>
                    <div class="value">{{ $company['iban'] }}</div>
                </div>
                <div class="info-line">
                    <div class="label">Банка:</div>
                    <div class="value">{{ $company['bank_name'] }}</div>
                </div>
                <div class="info-line">
                    <div class="label">BIC:</div>
                    <div class="value">{{ $company['bic'] }}</div>
                </div>
            </div>
        </div>
        
        <!-- АРТИКУЛИ -->
        <div class="items-title">Артикули</div>
        <table class="items-table">
            <thead>
                <tr>
                    <th class="col-no">№</th>
                    <th class="col-name">Наименование</th>
                    <th class="col-brand">Марка</th>
                    <th class="col-qty">Количество</th>
                    <th class="col-unit-price">Ед. цена</th>
                    <th class="col-total">Цена</th>
                </tr>
            </thead>
            <tbody>
                @if($invoice->items && count($invoice->items) > 0)
                    @foreach($invoice->items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="col-name">{{ $item->description }}</td>
                        <td class="col-brand">{{ $item->brand ?? $item->unit ?? 'бр.' }}</td>
                        <td>{{ number_format($item->quantity, 2) }}</td>
                        <td>{{ number_format($item->unit_price, 2) }}</td>
                        <td>{{ number_format($item->quantity * $item->unit_price, 2) }}</td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td>1</td>
                        <td class="col-name">Листи за принтер</td>
                        <td class="col-brand">пакет</td>
                        <td>3</td>
                        <td>7.25</td>
                        <td>21.75</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td class="col-name">Списание за риболов</td>
                        <td class="col-brand">бр.</td>
                        <td>5</td>
                        <td>2.45</td>
                        <td>12.25</td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td class="col-name">Учебник по право</td>
                        <td class="col-brand">бр.</td>
                        <td>1</td>
                        <td>10.69</td>
                        <td>10.69</td>
                    </tr>
                @endif
            </tbody>
        </table>
        
        <!-- ОБЩИ СУМИ -->
        <div class="totals-vat-container">
            <div class="totals-box">
                @php
                    $subtotal = $invoice->items ? $invoice->items->sum(function($item) {
                        return $item->quantity * $item->unit_price;
                    }) : 44.69;
                    $total = $subtotal;
                @endphp
                <div class="total-row">
                    <div class="total-label">Стойност:</div>
                    <div class="total-value">{{ number_format($subtotal, 2) }}</div>
                </div>
                <div class="total-row">
                    <div class="total-label">Сума за плащане:</div>
                    <div class="total-value">{{ number_format($total, 2) }}</div>
                </div>
            </div>
            <div class="vat-box">
                <div class="vat-title">Основание за неизискване на ДДС</div>
                <div>чл. 113, ал. 9 от ЗДДС</div>
            </div>
        </div>
    </div>
    
    <!-- ВТОРА СТРАНИЦА -->
    <div class="page-break"></div>
    
    <div>
        <!-- ДОЛНА ИНФОРМАЦИЯ -->
        <div class="details-container">
            <div class="details-left">
                <div class="detail-line">
                    <div class="detail-label">Данъчно събитие:</div>
                    <div class="detail-value">{{ $issueDate ? $issueDate->format('d.m.Y') : now()->format('d.m.Y') }}</div>
                </div>
                <div class="detail-line">
                    <div class="detail-label">Валута:</div>
                    <div class="detail-value">BGN</div>
                </div>
                <div class="detail-line">
                    <div class="detail-label">Получател:</div>
                    <div class="detail-value">{{ $invoice->customer->contact_person ?? 'Анна Димитрова' }}</div>
                </div>
            </div>
            <div class="details-right">
                <div class="detail-line">
                    <div class="detail-label">Място на сделката:</div>
                    <div class="detail-value">{{ $company['city'] }}</div>
                </div>
                <div class="detail-line">
                    <div class="detail-label">Начин на плащане:</div>
                    <div class="detail-value">По банков път</div>
                </div>
                <div class="detail-line">
                    <div class="detail-label">Съставил:</div>
                    <div class="detail-value">{{ $company['contact_person'] }}</div>
                </div>
            </div>
        </div>
        
        <!-- ПОДПИСИ -->
        <div class="signatures-container">
            <div class="signature-left">
                <div class="signature-text">Подпис</div>
                <div class="signature-line"></div>
            </div>
            <div class="signature-right">
                <div class="signature-text">Подпис</div>
                <div class="signature-line"></div>
            </div>
        </div>
    </div>
</body>
</html>