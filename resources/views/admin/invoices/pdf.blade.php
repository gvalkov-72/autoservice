<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <title>Фактура №{{ $invoice->invoice_number }}</title>
    <style>
        @page {
            margin: 15mm 15mm 15mm 15mm;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10pt;
            color: #333;
            line-height: 1.2;
            width: 180mm; /* 210mm - 2x15mm margins */
            margin: 0 auto;
            padding: 0;
        }
        
        .full-width-table {
            width: 180mm;
            margin-bottom: 3mm;
            border-collapse: collapse;
        }
        
        /* ТРИКОЛОННА ТАБЛИЦА */
        .three-columns {
            width: 180mm;
            margin-bottom: 3mm;
            border-spacing: 2mm;
        }
        
        .three-columns td {
            vertical-align: top;
            padding: 1.5mm;
            background-color: #f9f9f9;
            border: 1pt solid #ccc;
            border-radius: 3px;
        }
        
        .left-column,
        .right-column {
            width: 76mm;
        }
        
        .center-column {
            width: 24mm;
            text-align: center;
        }
        
        .section-title {
            font-weight: bold;
            font-size: 8pt;
            margin-bottom: 1mm;
            padding-bottom: 0.5mm;
            border-bottom: 1pt dotted #000;
        }
        
        .invoice-title {
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 3mm;
        }
        
        .invoice-number {
            font-size: 7pt;
            font-weight: bold;
        }
        
        .invoice-date {
            font-size: 7pt;
            font-weight: bold;
        }
        
        /* ИНФОРМАЦИОННИ ТАБЛИЦИ В КОЛОНИТЕ */
        .info-table {
            width: 100%;
            border: none;
            border-collapse: collapse;
        }
        
        .info-table tr {
            height: 3.5mm;
        }
        
        .label-cell {
            width: 17mm;
            font-weight: bold;
            font-size: 6pt;
            color: #999;
            vertical-align: middle;
            border: none;
            padding: 0.2mm 0;
            padding-right: 0.5mm;
        }
        
        .value-cell {
            border-bottom: 0.5pt dotted #666;
            font-size: 6.5pt;
            vertical-align: middle;
            border-left: none;
            border-right: none;
            border-top: none;
            padding-bottom: 0.2mm;
        }
        
        /* ТАБЛИЦА С АРТИКУЛИ */
        .items-table-container {
            width: 180mm;
            border: 1pt solid #ccc;
            border-radius: 3px;
            background-color: #f9f9f9;
            padding: 2mm;
        }
        
        .items-title {
            font-weight: bold;
            font-size: 8pt;
            margin-bottom: 2mm;
            color: #999;
        }
        
        .items-table {
            width: 176mm;
            border-collapse: collapse;
            font-size: 6.5pt;
        }
        
        .items-table th {
            padding: 0.5mm;
            text-align: center;
            font-weight: bold;
            color: #999;
            border-bottom: 1pt solid #ddd;
            background-color: #f5f5f5;
        }
        
        .items-table td {
            padding: 0.5mm;
            text-align: center;
            border-bottom: 0.5pt solid #eee;
        }
        
        /* ШИРИНИ НА КОЛОНИ ЗА АРТИКУЛИ */
        .col-1 { width: 3%; }
        .col-2 { width: 25%; text-align: left; padding-left: 1mm !important; }
        .col-3 { width: 5%; }
        .col-4 { width: 7%; }
        .col-5 { width: 8%; }
        .col-6 { width: 7%; }
        .col-7 { width: 9%; }
        .col-8 { width: 8%; }
        .col-9 { width: 6%; }
        .col-10 { width: 9%; }
        .col-11 { width: 13%; }
        
        /* ТАБЛИЦА ЗА ДДС И ПЛАЩАНЕ */
        .two-columns {
            width: 180mm;
            border-spacing: 2mm;
            margin-bottom: 3mm;
        }
        
        .two-columns td {
            vertical-align: top;
            width: 89mm;
        }
        
        .vat-box,
        .payment-box {
            border: 1pt solid #ccc;
            border-radius: 3px;
            background-color: #f9f9f9;
            padding: 2mm;
            height: auto;
        }
        
        .box-title {
            font-weight: bold;
            font-size: 7pt;
            color: #999;
            margin-bottom: 1mm;
            padding-bottom: 0.5mm;
            border-bottom: 1pt dotted #000;
        }
        
        .line-table {
            width: 100%;
            margin-bottom: 1mm;
            border-collapse: collapse;
        }
        
        .line-table td.label {
            width: auto;
            white-space: nowrap;
            font-weight: bold;
            font-size: 6.5pt;
            color: #999;
            padding-right: 0.5mm;
            border: none;
            vertical-align: middle;
        }
        
        .line-table td.dots {
            width: 100%;
            border-bottom: 1pt dotted #000;
            border-left: none;
            border-right: none;
            border-top: none;
        }
        
        .line-table td.value {
            width: auto;
            white-space: nowrap;
            font-weight: bold;
            font-size: 6.5pt;
            text-align: right;
            padding-left: 0.5mm;
            border: none;
            vertical-align: middle;
        }
        
        .total-line {
            border-top: 1pt solid #333;
            padding-top: 0.5mm;
            margin-top: 1mm;
        }
        
        /* ТАБЛИЦА ЗА ДОЛНА ИНФОРМАЦИЯ */
        .details-table {
            width: 180mm;
            margin-bottom: 3mm;
            border-collapse: collapse;
        }
        
        .details-table td.label {
            width: 25mm;
            font-weight: bold;
            font-size: 6.5pt;
            color: #999;
            vertical-align: middle;
            border: none;
            padding: 0.2mm 0;
            padding-right: 1mm;
            white-space: nowrap;
        }
        
        .details-table td.value {
            border-bottom: 0.5pt dotted #666;
            font-size: 6.5pt;
            vertical-align: middle;
            border-left: none;
            border-right: none;
            border-top: none;
            padding-bottom: 0.3mm;
            width: 100%;
        }
        
        /* ТАБЛИЦА ЗА ПОДПИСИ */
        .signatures-table {
            width: 180mm;
            margin-top: 10mm;
            border-collapse: collapse;
        }
        
        .signatures-table td {
            width: 50%;
            text-align: center;
            padding: 0;
        }
        
        .signature-text {
            margin-bottom: 8mm;
            font-size: 7pt;
            color: #999;
        }
        
        .signature-line {
            border-top: 1pt dotted #000;
            height: 8mm;
            width: 60%;
            margin: 0 auto;
        }
        
        /* ФУТЪР ЗА НОМЕРАЦИЯ */
        .footer-table {
            width: 180mm;
            margin-top: 15mm;
            border-collapse: collapse;
        }
        
        .footer-table td {
            text-align: center;
            font-size: 8pt;
            color: #666;
            border-top: 0.5pt solid #ccc;
            padding-top: 1mm;
        }
        
        /* ПРЕВЕНТИРАНЕ НА РАЗДЕЛЯНЕ */
        .no-break {
            page-break-inside: avoid;
        }
    </style>
</head>

<body>

<!-- ТРИ КОЛОНИ -->
<table class="three-columns no-break">
    <tr>
        <!-- ЛЯВА КОЛОНА: КЛИЕНТ -->
        <td class="left-column">
            <div class="section-title">Клиент</div>
            <table class="info-table">
                <tr>
                    <td class="label-cell">Клиент:</td>
                    <td class="value-cell">{{ $invoice->customer->name ?? 'Няма данни' }}</td>
                </tr>
                <tr>
                    <td class="label-cell">Град:</td>
                    <td class="value-cell">{{ $invoice->customer->city ?? '' }}</td>
                </tr>
                <tr>
                    <td class="label-cell">Адрес:</td>
                    <td class="value-cell">{{ $invoice->customer->address ?? '' }}</td>
                </tr>
                <tr>
                    <td class="label-cell">ЕИК / ЕГН:</td>
                    <td class="value-cell">{{ $invoice->customer->eik ?? $invoice->customer->vat_number ?? '' }}</td>
                </tr>
                <tr>
                    <td class="label-cell">ДДС №:</td>
                    <td class="value-cell">{{ $invoice->customer->vat_number ?? '______' }}</td>
                </tr>
                <tr>
                    <td class="label-cell">МОЛ:</td>
                    <td class="value-cell">{{ $invoice->customer->contact_person ?? '' }}</td>
                </tr>
                <tr>
                    <td class="label-cell">IBAN:</td>
                    <td class="value-cell">{{ $invoice->customer->iban ?? '' }}</td>
                </tr>
                <tr>
                    <td class="label-cell">Банка:</td>
                    <td class="value-cell">{{ $invoice->customer->bank_name ?? '' }}</td>
                </tr>
                <tr>
                    <td class="label-cell">BIC:</td>
                    <td class="value-cell">{{ $invoice->customer->bic ?? '' }}</td>
                </tr>
            </table>
        </td>
        
        <!-- ЦЕНТРАЛНА КОЛОНА: ФАКТУРА -->
        <td class="center-column">
            <div class="invoice-title">ФАКТУРА</div>
            @php
                $issueDate = $invoice->issue_date;
                if (is_string($issueDate)) {
                    $issueDate = \Carbon\Carbon::parse($issueDate);
                }
            @endphp
            <div class="invoice-number">№ {{ $invoice->invoice_number }}</div>
            <div class="invoice-date">
                Дата {{ $issueDate ? $issueDate->format('d.m.Y') : now()->format('d.m.Y') }}
            </div>
            @if($invoice->due_date)
                <div class="invoice-date" style="margin-top: 0.5mm;">
                    Падеж: {{ \Carbon\Carbon::parse($invoice->due_date)->format('d.m.Y') }}
                </div>
            @endif
        </td>
        
        <!-- ДЯСНА КОЛОНА: ДОСТАВЧИК -->
        <td class="right-column">
            <div class="section-title">Доставчик</div>
            @if(isset($companySettings) && $companySettings)
                <table class="info-table">
                    <tr>
                        <td class="label-cell">Доставчик:</td>
                        <td class="value-cell">{{ $companySettings->name }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">Град:</td>
                        <td class="value-cell">{{ $companySettings->city }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">Адрес:</td>
                        <td class="value-cell">{{ $companySettings->address }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">ЕИК / ЕГН:</td>
                        <td class="value-cell">{{ $companySettings->eik ?? $companySettings->vat_number }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">ДДС №:</td>
                        <td class="value-cell">{{ $companySettings->vat_number }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">МОЛ:</td>
                        <td class="value-cell">{{ $companySettings->contact_person }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">IBAN:</td>
                        <td class="value-cell">{{ $companySettings->iban }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">Банка:</td>
                        <td class="value-cell">{{ $companySettings->bank_name }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">BIC:</td>
                        <td class="value-cell">{{ $companySettings->bic }}</td>
                    </tr>
                </table>
            @else
                <table class="info-table">
                    <tr>
                        <td class="label-cell">Доставчик:</td>
                        <td class="value-cell">ВАШАТА КОМПАНИЯ АД</td>
                    </tr>
                    <tr>
                        <td class="label-cell">Град:</td>
                        <td class="value-cell">ВАШИЯТ ГРАД</td>
                    </tr>
                    <tr>
                        <td class="label-cell">Адрес:</td>
                        <td class="value-cell">ул. ВАШАТА АДРЕС</td>
                    </tr>
                    <tr>
                        <td class="label-cell">ЕИК / ЕГН:</td>
                        <td class="value-cell">000000000</td>
                    </tr>
                    <tr>
                        <td class="label-cell">ДДС №:</td>
                        <td class="value-cell">000000000</td>
                    </tr>
                    <tr>
                        <td class="label-cell">МОЛ:</td>
                        <td class="value-cell">ВАШЕТО ИМЕ</td>
                    </tr>
                    <tr>
                        <td class="label-cell">IBAN:</td>
                        <td class="value-cell">BG00XXXX00000000000000</td>
                    </tr>
                    <tr>
                        <td class="label-cell">Банка:</td>
                        <td class="value-cell">ВАШАТА БАНКА АД</td>
                    </tr>
                    <tr>
                        <td class="label-cell">BIC:</td>
                        <td class="value-cell">XXXXXXXX</td>
                    </tr>
                </table>
            @endif
        </td>
    </tr>
</table>

<!-- АРТИКУЛИ -->
<table class="full-width-table no-break">
    <tr>
        <td>
            <div class="items-table-container">
                <div class="items-title">Артикули</div>
                
                <table class="items-table">
                    <thead>
                        <tr>
                            <th class="col-1">№</th>
                            <th class="col-2">Наименование</th>
                            <th class="col-3">МЕ</th>
                            <th class="col-4">Кол-во</th>
                            <th class="col-5">Цена</th>
                            <th class="col-6">Отст. %</th>
                            <th class="col-7">Отст. ст-т</th>
                            <th class="col-8">Стойност</th>
                            <th class="col-9">ДДС %</th>
                            <th class="col-10">Стойност ДДС</th>
                            <th class="col-11">Обща стойност</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($invoice->items && count($invoice->items) > 0)
                            @php
                                $totalSubtotal = 0;
                                $totalVatAmount = 0;
                                $totalGrandTotal = 0;
                            @endphp
                            @foreach($invoice->items as $index => $item)
                                @php
                                    $quantity = $item->quantity ?? 1;
                                    $unitPrice = $item->unit_price ?? 0;
                                    $discountPercent = $item->discount_percent ?? 0;
                                    $discountAmount = $quantity * $unitPrice * ($discountPercent / 100);
                                    $subtotal = ($quantity * $unitPrice) - $discountAmount;
                                    $vatPercent = $item->vat_percent ?? 20;
                                    $vatAmount = $subtotal * ($vatPercent / 100);
                                    $totalWithVat = $subtotal + $vatAmount;
                                    
                                    $totalSubtotal += $subtotal;
                                    $totalVatAmount += $vatAmount;
                                    $totalGrandTotal += $totalWithVat;
                                @endphp
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td class="col-2">{{ $item->description ?? 'Няма описание' }}</td>
                                    <td>{{ $item->unit ?? 'бр.' }}</td>
                                    <td>{{ number_format($quantity, 2) }}</td>
                                    <td>{{ number_format($unitPrice, 2) }}</td>
                                    <td>{{ number_format($discountPercent, 2) }}</td>
                                    <td>{{ number_format($discountAmount, 4) }}</td>
                                    <td>{{ number_format($subtotal, 2) }}</td>
                                    <td>{{ number_format($vatPercent, 2) }}</td>
                                    <td>{{ number_format($vatAmount, 2) }}</td>
                                    <td>{{ number_format($totalWithVat, 2) }}</td>
                                </tr>
                            @endforeach
                            
                            <!-- ОБЩА СУМА -->
                            <tr style="font-weight: bold; background-color: #f0f0f0;">
                                <td colspan="7" style="text-align: right; padding-right: 3mm;">Общо:</td>
                                <td>{{ number_format($totalSubtotal, 2) }}</td>
                                <td></td>
                                <td>{{ number_format($totalVatAmount, 2) }}</td>
                                <td>{{ number_format($totalGrandTotal, 2) }}</td>
                            </tr>
                        @else
                            <tr>
                                <td colspan="11" style="text-align: center; padding: 3mm; color: #999;">
                                    Няма добавени артикули
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </td>
    </tr>
</table>

<!-- ДВА БЛОКА: ДДС И ПЛАЩАНЕ -->
<table class="two-columns no-break">
    <tr>
        <!-- ИНФОРМАЦИЯ ЗА ДДС -->
        <td>
            <div class="vat-box">
                <div class="box-title">Информация за ДДС</div>
                @php
                    $vatGroups = [];
                    if(isset($invoice->items) && count($invoice->items) > 0) {
                        foreach($invoice->items as $item) {
                            $vatPercent = $item->vat_percent ?? 20;
                            $quantity = $item->quantity ?? 1;
                            $unitPrice = $item->unit_price ?? 0;
                            $discountPercent = $item->discount_percent ?? 0;
                            
                            $subtotal = ($quantity * $unitPrice) * (1 - $discountPercent/100);
                            $vatAmount = $subtotal * ($vatPercent/100);
                            
                            if(!isset($vatGroups[$vatPercent])) {
                                $vatGroups[$vatPercent] = 0;
                            }
                            $vatGroups[$vatPercent] += $vatAmount;
                        }
                    }
                @endphp
                
                @if(count($vatGroups) > 0)
                    @foreach($vatGroups as $percent => $amount)
                        <table class="line-table">
                            <tr>
                                <td class="label">ДДС {{ number_format($percent, 2) }}%:</td>
                                <td class="dots"></td>
                                <td class="value">{{ number_format($amount, 2) }}</td>
                            </tr>
                        </table>
                    @endforeach
                @else
                    <table class="line-table">
                        <tr>
                            <td class="label">ДДС 20.00%:</td>
                            <td class="dots"></td>
                            <td class="value">{{ number_format($totalVatAmount ?? 0, 2) }}</td>
                        </tr>
                    </table>
                @endif
                
                <!-- СЛОВОМ -->
                <table class="line-table">
                    <tr>
                        <td class="label">Словом:</td>
                        <td class="dots"></td>
                        <td class="value">
                            @php
                                function simpleNumberToWords($num) {
                                    $num = round($num, 2);
                                    return number_format($num, 2) . ' лв.';
                                }
                            @endphp
                            {{ simpleNumberToWords($totalGrandTotal ?? 0) }}
                        </td>
                    </tr>
                </table>
            </div>
        </td>
        
        <!-- СУМИ ЗА ПЛАЩАНЕ -->
        <td>
            <div class="payment-box">
                <div class="box-title">Сума за плащане</div>
                <table class="line-table">
                    <tr>
                        <td class="label">Стойност на сделката:</td>
                        <td class="dots"></td>
                        <td class="value">{{ number_format($totalSubtotal ?? 0, 2) }}</td>
                    </tr>
                </table>
                <table class="line-table">
                    <tr>
                        <td class="label">Отстъпка %:</td>
                        <td class="dots"></td>
                        <td class="value">{{ number_format($invoice->discount_percent ?? 0, 2) }}</td>
                    </tr>
                </table>
                <table class="line-table">
                    <tr>
                        <td class="label">Даначна основа:</td>
                        <td class="dots"></td>
                        <td class="value">{{ number_format($totalSubtotal ?? 0, 2) }}</td>
                    </tr>
                </table>
                <table class="line-table">
                    <tr>
                        <td class="label">ДДС:</td>
                        <td class="dots"></td>
                        <td class="value">{{ number_format($totalVatAmount ?? 0, 2) }}</td>
                    </tr>
                </table>
                <table class="line-table total-line">
                    <tr>
                        <td class="label">Сума за плащане:</td>
                        <td class="dots"></td>
                        <td class="value">{{ number_format($totalGrandTotal ?? 0, 2) }}</td>
                    </tr>
                </table>
            </div>
        </td>
    </tr>
</table>

<!-- ДОЛНА ИНФОРМАЦИЯ -->
<table class="details-table">
    <tr>
        <td class="label">Начин на плащане:</td>
        <td class="value">{{ $invoice->payment_method ?? 'Банков превод' }}</td>
    </tr>
    <tr>
        <td class="label">Срок на плащане:</td>
        <td class="value">{{ $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('d.m.Y') : '30 дни' }}</td>
    </tr>
    @if($invoice->workOrder && $invoice->workOrder->number)
    <tr>
        <td class="label">Поръчка:</td>
        <td class="value">{{ $invoice->workOrder->number }}</td>
    </tr>
    @endif
</table>

<!-- ПОДПИСИ -->
<table class="signatures-table">
    <tr>
        <td>
            <div class="signature-text">Изготвил:</div>
            <div class="signature-line"></div>
        </td>
        <td>
            <div class="signature-text">Получил:</div>
            <div class="signature-line"></div>
        </td>
    </tr>
</table>

<!-- ФУТЪР С НОМЕРАЦИЯ -->
<table class="footer-table">
    <tr>
        <td>
            Страница 1
        </td>
    </tr>
</table>
Проба ..............................................
</body>
</html>