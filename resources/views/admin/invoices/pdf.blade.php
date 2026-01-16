<!DOCTYPE html>
<html lang="bg">

<head>
    <meta charset="UTF-8">
    <title>Фактура №{{ $invoice->invoice_number }}</title>
    <style>
        @page {
            margin: 8mm 8mm 8mm 8mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'DejaVu Sans', Arial, sans-serif;
        }

        .full-width-table {
            width: 194mm;
            margin-bottom: 2mm;
            border-collapse: collapse;
        }

        /* ТРИКОЛОННА ТАБЛИЦА */
        .three-columns {
            width: 194mm;
            margin-left: 3mm;
            margin-bottom: 2mm;
            border-spacing: 1.5mm;
        }

        .three-columns td {
            vertical-align: top;
            padding: 1.2mm;
            background-color: #f9f9f9;
            border: 0.75pt solid #ccc;
            border-radius: 2px;
        }

        .left-column,
        .right-column {
            width: 74mm;
        }

        .center-column {
            width: 41.5mm;
            text-align: center;
        }

        .section-title {
            font-weight: bold;
            font-size: 7pt;
            margin-bottom: 0.8mm;
            padding-bottom: 0.4mm;
            border-bottom: 0.75pt dotted #000;
        }

        .invoice-title {
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 2mm;
        }

        .invoice-number {
            font-size: 6.5pt;
            font-weight: bold;
        }

        .invoice-date {
            font-size: 6.5pt;
            font-weight: bold;
        }

        /* ИНФОРМАЦИОННИ ТАБЛИЦИ В КОЛОНИТЕ */
        .info-table {
            width: 100%;
            border: none;
            border-collapse: collapse;
        }

        .info-table tr {
            height: 3mm;
        }

        .label-cell {
            width: 16mm;
            font-weight: bold;
            font-size: 5.8pt;
            color: #666;
            vertical-align: middle;
            border: none;
            padding: 0.15mm 0;
            padding-right: 0.4mm;
        }

        .value-cell {
            border-bottom: 0.4pt dotted #666;
            font-size: 6.2pt;
            vertical-align: middle;
            border-left: none;
            border-right: none;
            border-top: none;
            padding-bottom: 0.15mm;
        }

        /* ТАБЛИЦА С АРТИКУЛИ */
        .items-table-container {
            width: 197.5mm;
            margin-left: 4.5mm;
            border: 0.75pt solid #ccc;
            border-radius: 2px;
            background-color: #f9f9f9;
            padding: 1.5mm;
        }

        .items-title {
            font-weight: bold;
            font-size: 7.5pt;
            margin-bottom: 1.5mm;
            color: #666;
        }

        .items-table {
            width: 197.5mm;
            border-collapse: collapse;
            font-size: 6.2pt;
        }

        .items-table th {
            padding: 0.4mm;
            text-align: center;
            font-weight: bold;
            color: #666;
            border-bottom: 0.75pt solid #ddd;
            background-color: #f5f5f5;
        }

        .items-table td {
            padding: 0.4mm;
            text-align: center;
            border-bottom: 0.4pt solid #eee;
        }

        /* ШИРИНИ НА КОЛОНИ ЗА АРТИКУЛИ */
        .col-1 {
            width: 3%;
        }

        .col-2 {
            width: 24.5%;
            text-align: left;
            padding-left: 0.8mm !important;
        }

        .col-3 {
            width: 4.8%;
        }

        .col-4 {
            width: 6.8%;
        }

        .col-5 {
            width: 7.8%;
        }

        .col-6 {
            width: 6.8%;
        }

        .col-7 {
            width: 8.8%;
        }

        .col-8 {
            width: 7.8%;
        }

        .col-9 {
            width: 5.8%;
        }

        .col-10 {
            width: 8.8%;
        }

        .col-11 {
            width: 12%;
        }

        /* ТАБЛИЦА ЗА ДДС И ПЛАЩАНЕ */
        .two-columns {
            width: 194mm;
            margin-left: 3mm;
            border-spacing: 1.5mm;
            margin-bottom: 2mm;
        }

        .two-columns1 {
            vertical-align: top;
            width: 70mm;
        }

        .two-columns2 {
            vertical-align: top;
            width: 90mm;
            padding-left: 39.5mm;
        }

        .vat-box,
        .payment-box {
            border: 0.75pt solid #ccc;
            border-radius: 2px;
            background-color: #f9f9f9;
            padding: 1.5mm;
            height: auto;
        }

        .box-title {
            font-weight: bold;
            font-size: 6.8pt;
            color: #666;
            margin-bottom: 0.8mm;
            padding-bottom: 0.4mm;
            border-bottom: 0.75pt dotted #000;
        }

        .line-table {
            width: 100%;
            margin-bottom: 0.8mm;
            border-collapse: collapse;
        }

        .line-table td.label {
            width: auto;
            white-space: nowrap;
            font-weight: bold;
            font-size: 6.2pt;
            color: #666;
            padding-right: 0.4mm;
            border: none;
            vertical-align: middle;
        }

        .line-table td.dots {
            width: 100%;
            border-bottom: 0.4pt dotted #000;
            border-left: none;
            border-right: none;
            border-top: none;
        }

        .line-table td.value {
            width: auto;
            white-space: nowrap;
            font-weight: bold;
            font-size: 6.2pt;
            text-align: right;
            padding-left: 0.4mm;
            border: none;
            vertical-align: middle;
        }

        .total-line {
            border-top: 0.75pt solid #333;
            padding-top: 0.4mm;
            margin-top: 0.8mm;
        }

        /* ТАБЛИЦА ЗА ДОЛНА ИНФОРМАЦИЯ */
        .details-table {
            width: 194mm;
            margin-bottom: 2mm;
            border-collapse: collapse;
        }

        .details-table td.label {
            width: 23mm;
            font-weight: bold;
            font-size: 6.2pt;
            color: #666;
            vertical-align: middle;
            border: none;
            padding: 0.15mm 0;
            padding-right: 0.8mm;
            white-space: nowrap;
        }

        .details-table td.value {
            border-bottom: 0.4pt dotted #666;
            font-size: 6.2pt;
            vertical-align: middle;
            border-left: none;
            border-right: none;
            border-top: none;
            padding-bottom: 0.25mm;
            width: 100%;
        }

        /* ТАБЛИЦА ЗА ПОДПИСИ */
        .signatures-table {
            width: 194mm;
            margin-top: 6mm;
            border-collapse: collapse;
        }

        .signatures-table td {
            width: 50%;
            text-align: center;
            padding: 0;
        }

        .signature-text {
            margin-bottom: 5mm;
            font-size: 6.8pt;
            color: #666;
        }

        .signature-line {
            border-top: 0.75pt dotted #000;
            height: 6mm;
            width: 55%;
            margin: 0 auto;
        }

        /* ФУТЪР ЗА НОМЕРАЦИЯ */
        .footer-table {
            width: 194mm;
            margin-top: 8mm;
            border-collapse: collapse;
        }

        .footer-table td {
            text-align: center;
            font-size: 7.5pt;
            color: #666;
            border-top: 0.4pt solid #ccc;
            padding-top: 0.8mm;
        }

        /* ПРЕВЕНТИРАНЕ НА РАЗДЕЛЯНЕ */
        .no-break {
            page-break-inside: avoid;
        }
        
        .responsible-name {
            font-size: 6.5pt;
            margin-top: 1mm;
            font-weight: bold;
        }
    </style>
</head>

<body style="padding-top: 5mm;">

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
                        <td class="value-cell">{{ $invoice->customer->eik ?? ($invoice->customer->vat_number ?? '') }}
                        </td>
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
                @if ($invoice->due_date)
                    <div class="invoice-date" style="margin-top: 0.3mm;">
                        Падеж: {{ \Carbon\Carbon::parse($invoice->due_date)->format('d.m.Y') }}
                    </div>
                @endif
                
                <!-- ОТГОВОРНИК ЗА СЪЗДАВАНЕ -->
                @if($invoice->invoice_cre_responsible)
                <div class="invoice-date" style="margin-top: 1mm;">
                    Създал: {{ $invoice->invoice_cre_responsible }}
                </div>
                @endif
                
                <!-- ОТГОВОРНИК ЗА ПОЛУЧАВАНЕ -->
                @if($invoice->invoice_rec_responsible)
                <div class="invoice-date" style="margin-top: 0.3mm;">
                    Получил: {{ $invoice->invoice_rec_responsible }}
                </div>
                @endif
            </td>

            <!-- ДЯСНА КОЛОНА: ДОСТАВЧИК -->
            <td class="right-column">
                <div class="section-title">Доставчик</div>
                @if (isset($companySettings) && $companySettings)
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
                            @if ($invoice->items && count($invoice->items) > 0)
                                @php
                                    $totalSubtotal = 0;
                                    $totalVatAmount = 0;
                                    $totalGrandTotal = 0;
                                @endphp
                                @foreach ($invoice->items as $index => $item)
                                    @php
                                        $quantity = $item->quantity ?? 1;
                                        $unitPrice = $item->unit_price ?? 0;
                                        $discountPercent = $item->discount_percent ?? 0;
                                        $discountAmount = $quantity * $unitPrice * ($discountPercent / 100);
                                        $subtotal = $quantity * $unitPrice - $discountAmount;
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
                                    <td colspan="7" style="text-align: right; padding-right: 2mm;">Общо:</td>
                                    <td>{{ number_format($totalSubtotal, 2) }}</td>
                                    <td></td>
                                    <td>{{ number_format($totalVatAmount, 2) }}</td>
                                    <td>{{ number_format($totalGrandTotal, 2) }}</td>
                                </tr>
                            @else
                                <tr>
                                    <td colspan="11" style="text-align: center; padding: 2mm; color: #666;">
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
            <!-- ДОПЪЛНИТЕЛНА ИНФОРМАЦИЯ -->
            <td class="two-columns1">
                <div class="vat-box">
                    <div class="box-title">Допълнителна информация</div>
                    <table class="line-table">
                        <tr>
                            <td class="label">Начин на плащане:</td>
                            <td class="dots"></td>
                            <td class="value">{{ $invoice->payment_method ?? 'Банков превод' }}</td>
                        </tr>
                    </table>
                    <table class="line-table">
                        <tr>
                            <td class="label">Срок на плащане:</td>
                            <td class="dots"></td>
                            <td class="value">
                                {{ $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('d.m.Y') : '30 дни' }}
                            </td>
                        </tr>
                    </table>
                    @if ($invoice->workOrder && $invoice->workOrder->number)
                    <table class="line-table">
                        <tr>
                            <td class="label">Поръчка:</td>
                            <td class="dots"></td>
                            <td class="value">{{ $invoice->workOrder->number }}</td>
                        </tr>
                    </table>
                    @endif
                </div>
            </td>

            <!-- СУМИ ЗА ПЛАЩАНЕ -->
            <td class="two-columns2">
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
                        <tr>
                            <td class="label">&nbsp;</td>
                            <td>&nbsp;</td>
                            <td class="value">&nbsp;</td>
                        </tr>
                        <tr style="font-weight: bold; background-color: #f0f0f0;">
                            <td class="label">Сума за плащане:</td>
                            <td class="dots"></td>
                            <td class="value">{{ number_format($totalGrandTotal ?? 0, 2) }}</td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>

    <!-- ПОДПИСИ -->
    <table class="signatures-table">
        <tr>
            <td>
                <div class="signature-text">Изготвил:</div>
                <div class="signature-line"></div>
                @if($invoice->invoice_cre_responsible)
                <div class="responsible-name">{{ $invoice->invoice_cre_responsible }}</div>
                @endif
            </td>
            <td>
                <div class="signature-text">Получил:</div>
                <div class="signature-line"></div>
                @if($invoice->invoice_rec_responsible)
                <div class="responsible-name">{{ $invoice->invoice_rec_responsible }}</div>
                @endif
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
</body>

</html>