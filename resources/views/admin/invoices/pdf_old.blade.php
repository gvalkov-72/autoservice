<!DOCTYPE html>
<html lang="bg">

<head>
    <meta charset="UTF-8">
    <title>Фактура №{{ $invoice->number }}</title>
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
            color: #333;
        }

        .three-column-container {
            width: 100%;
            margin-bottom: 10mm;
            border-collapse: separate;
            border-spacing: 3mm;
        }

        .three-column-container td {
            padding: 2mm;
            vertical-align: top;
            background-color: #f9f9f9;
        }

        .client-box,
        .supplier-box {
            width: 40%;
            border: 1pt solid #ccc;
            border-radius: 5px;
        }

        .invoice-box {
            width: 20%;
            text-align: center;
        }

        .section-title {
            font-weight: bold;
            font-size: 9pt;
            margin-bottom: 2mm;
            padding-bottom: 1mm;
            border-bottom: 1pt dotted #000;
        }

        .invoice-title {
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 5mm;
        }

        .invoice-number-large {
            font-size: 7pt;
            font-weight: bold;
        }

        .invoice-date-large {
            font-size: 7pt;
            font-weight: bold;
        }

        .info-table {
            width: 100%;
            margin-bottom: 1.5mm;
            border: none;
        }

        .label-cell {
            width: 15mm;
            font-weight: bold;
            font-size: 6pt;
            color: #999;
            vertical-align: bottom;
            border: none;
            padding: 0;
            padding-right: 1mm;
        }

        .value-cell {
            border-bottom: 0.5pt dotted #666;
            padding-bottom: 0.5mm;
            font-size: 7pt;
            vertical-align: bottom;
            border-left: none;
            border-right: none;
            border-top: none;
        }
    </style>
    <style>
        /* АРТИКУЛИ С РАМКА */
        .items-container {
            border: 1pt solid #ccc;
            border-radius: 5px;
            padding: 3mm 2mm;
            margin-bottom: 5mm;
        }

        .items-title {
            font-weight: bold;
            font-size: 9pt;
            margin-bottom: 3mm;
            color: #999;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7pt;
        }

        .items-table th {
            padding: 1mm;
            text-align: center;
            font-weight: bold;
            color: #999;
            border-bottom: 1pt solid #ddd;
        }

        .items-table td {
            padding: 1mm;
            text-align: center;
            border-bottom: 0.5pt solid #eee;
        }

        .items-table tr:last-child td {
            border-bottom: none;
        }

        /* ШИРИНИ НА КОЛОНИ */
        .col-no {
            width: 4%;
        }

        .col-name {
            width: 28%;
            text-align: left;
        }

        .col-unit {
            width: 6%;
        }

        .col-qty {
            width: 7%;
        }

        .col-unit-price {
            width: 8%;
        }

        .col-discount-percent {
            width: 7%;
        }

        .col-discount-amount {
            width: 9%;
        }

        .col-subtotal {
            width: 9%;
        }

        .col-vat-percent {
            width: 6%;
        }

        .col-vat-amount {
            width: 9%;
        }

        .col-total {
            width: 13%;
        }

        /* СУМИ + ДДС КАТО ТАБЛИЦА */
        .totals-vat-table {
            width: 100%;
            margin-top: 5mm;
            border-collapse: collapse;
        }

        .totals-vat-table td {
            width: 50%;
            vertical-align: top;
            padding: 0 1mm 0 0;
        }

        .totals-vat-table td:last-child {
            padding: 0 0 0 1mm;
        }

        .vat-title {
            font-weight: bold;
            font-size: 8pt;
            color: #999;
            margin-bottom: 1mm;
            white-space: nowrap;
        }

        /* РЕДОВЕ В ТАБЛИЦИ КАТО ТАБЛИЦИ */
        .line-table {
            width: 100%;
            margin-bottom: 1.5mm;
            border-collapse: collapse;
        }

        .line-table td.label-cell {
            width: auto;
            white-space: nowrap;
            font-weight: bold;
            font-size: 8pt;
            color: #999;
            padding-right: 0.5mm;
            border: none;
        }

        .line-table td.dots-cell {
            width: 100%;
            border-bottom: 1pt dotted #000;
            border-left: none;
            border-right: none;
            border-top: none;
        }

        .line-table td.value-cell {
            width: auto;
            white-space: nowrap;
            font-weight: bold;
            font-size: 8pt;
            text-align: right;
            padding-left: 0.5mm;
            border: none;
        }

        .value-cell .amount {
            background: #fff;
            padding: 0 0.2mm;
        }

        /* ПОСЛЕДЕН РЕД С ДЕБЕЛА ЛИНИЯ */
        .last-row-table {
            border-top: 1pt solid #333;
            padding-top: 1mm;
            margin-top: 2mm;
            height: 6mm;
        }

        /* СЛОВОМ В ТАБЛИЦА */
        .words-table {
            width: 100%;
            margin-top: 2mm;
            border-collapse: collapse;
        }

        .words-table td.label-cell {
            width: 10mm;
            font-weight: bold;
            font-size: 8pt;
            color: #999;
            vertical-align: bottom;
            border: none;
            padding: 0;
            padding-right: 1mm;
        }

        .words-table td.value-cell {
            border-bottom: 0.5pt dotted #666;
            font-weight: bold;
            font-size: 8pt;
            vertical-align: bottom;
            border-left: none;
            border-right: none;
            border-top: none;
        }

        /* ДОЛНА ИНФОРМАЦИЯ В ТАБЛИЦА */
        .details-table {
            width: 48%;
            margin-top: 5mm;
            border-collapse: collapse;
        }

        .details-table td.label-cell {
            width: 25mm;
            font-weight: bold;
            font-size: 7pt;
            color: #999;
            vertical-align: bottom;
            border: none;
            padding: 0;
            padding-right: 1mm;
        }

        .details-table td.value-cell {
            border-bottom: 0.5pt dotted #666;
            font-size: 7pt;
            vertical-align: bottom;
            border-left: none;
            border-right: none;
            border-top: none;
        }

        /* ПОДПИСИ КАТО ТАБЛИЦА */
        .signatures-table {
            width: 100%;
            margin-top: 15mm;
            border-collapse: collapse;
        }

        .signatures-table td {
            width: 48%;
            text-align: center;
        }

        .signature-text {
            margin-bottom: 10mm;
            font-size: 8pt;
            color: #999;
        }

        .signature-line {
            border-top: 1pt dotted #000;
            height: 10mm;
        }
    </style>
</head>

<body>
    <!-- ТРИ КОЛОНИ КАТО ТАБЛИЦА С РАМКА НА ВЪНШНИТЕ КОЛОНИ -->
    <table class="three-column-container">
        <tr>
            <!-- КЛИЕНТ (лява колона) -->
            <td class="client-box">
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
                        <td class="value-cell">{{ $invoice->customer->vat_number ?? '' }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">ЗДДС №:</td>
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

            <!-- ФАКТУРА (централна колона) -->
            <td class="invoice-box">
                <div class="invoice-title">ФАКТУРА</div>
                @php
                    $issueDate = $invoice->issue_date;
                    if (is_string($issueDate)) {
                        $issueDate = \Carbon\Carbon::parse($issueDate);
                    }
                @endphp
                <div class="invoice-number-large">№ {{ $invoice->number }}</div>
                <div class="invoice-date-large">Дата
                    {{ $issueDate ? $issueDate->format('d.m.Y') : now()->format('d.m.Y') }}</div>
            </td>

            <!-- ДОСТАВЧИК (дясна колона) -->
            <!-- Замяна на полетата в доставчика -->
            <td class="supplier-box">
                <div class="section-title">Доставчик</div>
                <table class="info-table">
                    @if (isset($companySettings) && $companySettings)
                        <!-- company_name не съществува, използваме name -->
                        <tr>
                            <td class="label-cell">Доставчик:</td>
                            <td class="value-cell">{{ $companySettings->name ?? 'ВАШАТА КОМПАНИЯ АД' }}</td>
                        </tr>
                        <tr>
                            <td class="label-cell">Град:</td>
                            <td class="value-cell">{{ $companySettings->city ?? 'ВАШИЯТ ГРАД' }}</td>
                        </tr>
                        <tr>
                            <td class="label-cell">Адрес:</td>
                            <td class="value-cell">{{ $companySettings->address ?? 'ул. ВАШАТА АДРЕС' }}</td>
                        </tr>
                        <!-- eik не съществува, използваме vat_number и за двете -->
                        <tr>
                            <td class="label-cell">ЕИК / ЕГН:</td>
                            <td class="value-cell">{{ $companySettings->vat_number ?? '000000000' }}</td>
                        </tr>
                        <tr>
                            <td class="label-cell">ЗДДС №:</td>
                            <td class="value-cell">{{ $companySettings->vat_number ?? '000000000' }}</td>
                        </tr>
                        <!-- mol не съществува, използваме contact_person -->
                        <tr>
                            <td class="label-cell">МОЛ:</td>
                            <td class="value-cell">{{ $companySettings->contact_person ?? 'ВАШЕТО ИМЕ' }}</td>
                        </tr>
                        <tr>
                            <td class="label-cell">IBAN:</td>
                            <td class="value-cell">{{ $companySettings->iban ?? 'BG00XXXX00000000000000' }}</td>
                        </tr>
                        <tr>
                            <td class="label-cell">Банка:</td>
                            <td class="value-cell">{{ $companySettings->bank_name ?? 'ВАШАТА БАНКА АД' }}</td>
                        </tr>
                        <tr>
                            <td class="label-cell">BIC:</td>
                            <td class="value-cell">{{ $companySettings->bic ?? 'XXXXXXXX' }}</td>
                        </tr>
                    @else
                        <!-- fallback данни -->
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
                            <td class="label-cell">ЗДДС №:</td>
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
                    @endif
                </table>
            </td>
        </tr>
    </table>
<!------------------- LВТОРАТА ПОЛОВИНА ОТ ФАКТУРАТА ---------------------------->

    <!-- АРТИКУЛИ С РАМКА -->
<div class="items-container">
    <div class="items-title">Артикули</div>

    <!-- ТАБЛИЦА С АРТИКУЛИ -->
    <table class="items-table">
        <thead>
            <tr>
                <th class="col-no">№</th>
                <th class="col-name">Наименование</th>
                <th class="col-unit">МЕ</th>
                <th class="col-qty">Кол-во</th>
                <th class="col-unit-price">Цена</th>
                <th class="col-discount-percent">Отст. %</th>
                <th class="col-discount-amount">Отст. ст-т</th>
                <th class="col-subtotal">Стойност</th>
                <th class="col-vat-percent">ДДС %</th>
                <th class="col-vat-amount">Стойност ДДС</th>
                <th class="col-total">Обща стойност</th>
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
                        <td class="col-name">{{ $item->description }}</td>
                        <td class="col-unit">{{ $item->unit ?? 'бр.' }}</td>
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
                
                <!-- РЕД ЗА ОБЩИ СУМИ -->
                <tr style="font-weight: bold; border-top: 1pt solid #ddd;">
                    <td colspan="7" style="text-align: right; padding-right: 5mm;">Общо:</td>
                    <td>{{ number_format($totalSubtotal, 2) }}</td>
                    <td></td>
                    <td>{{ number_format($totalVatAmount, 2) }}</td>
                    <td>{{ number_format($totalGrandTotal, 2) }}</td>
                </tr>
            @else
                <tr>
                    <td colspan="11" style="text-align: center; padding: 5mm; color: #999;">
                        Няма добавени артикули
                    </td>
                </tr>
            @endif
        </tbody>
    </table>

    <!-- СУМИ + ДДС КАТО ТАБЛИЦА -->
    <table class="totals-vat-table">
        <tr>
            <!-- ЛЯВ БЛОК: ИНФОРМАЦИЯ ЗА ДДС -->
            <td>
                <div class="vat-title">Информация за ДДС</div>
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
                                <td class="label-cell">ДДС {{ number_format($percent, 2) }}%:</td>
                                <td class="dots-cell"></td>
                                <td class="value-cell"><span class="amount">{{ number_format($amount, 2) }}</span></td>
                            </tr>
                        </table>
                    @endforeach
                @else
                    <table class="line-table">
                        <tr>
                            <td class="label-cell">ДДС 20.00%:</td>
                            <td class="dots-cell"></td>
                            <td class="value-cell"><span class="amount">{{ number_format($totalVatAmount ?? 0, 2) }}</span></td>
                        </tr>
                    </table>
                @endif
                
                <!-- "СЛОВОМ" В ТАБЛИЦА -->
                <table class="words-table">
                    <tr>
                        <td class="label-cell">Словом:</td>
                        <td class="value-cell">
                            @php
                                function numberToWordsBg($num) {
                                    $ones = array('', 'едно', 'две', 'три', 'четири', 'пет', 'шест', 'седем', 'осем', 'девет');
                                    $tens = array('', 'десет', 'двадесет', 'тридесет', 'четиридесет', 'петдесет', 'шестдесет', 'седемдесет', 'осемдесет', 'деветдесет');
                                    $teens = array('десет', 'единадесет', 'дванадесет', 'тринадесет', 'четиринадесет', 'петнадесет', 'шестнадесет', 'седемнадесет', 'осемнадесет', 'деветнадесет');
                                    
                                    $num = round($num, 2);
                                    $leva = floor($num);
                                    $stotinki = round(($num - $leva) * 100);
                                    
                                    $words = '';
                                    
                                    if ($leva >= 1000) {
                                        $thousands = floor($leva / 1000);
                                        $words .= $ones[$thousands] . ' хиляди ';
                                        $leva %= 1000;
                                    }
                                    
                                    if ($leva >= 100) {
                                        $hundreds = floor($leva / 100);
                                        $words .= $ones[$hundreds] . 'сто ';
                                        $leva %= 100;
                                    }
                                    
                                    if ($leva >= 20) {
                                        $tensDigit = floor($leva / 10);
                                        $words .= $tens[$tensDigit] . ' и ';
                                        $leva %= 10;
                                    } elseif ($leva >= 10) {
                                        $words .= $teens[$leva - 10] . ' ';
                                        $leva = 0;
                                    }
                                    
                                    if ($leva > 0) {
                                        $words .= $ones[$leva] . ' ';
                                    }
                                    
                                    if (empty(trim($words))) {
                                        $words = 'нула ';
                                    }
                                    
                                    $words .= 'лева';
                                    
                                    if ($stotinki > 0) {
                                        $words .= ' и ' . $stotinki . ' стотинки';
                                    }
                                    
                                    return trim($words);
                                }
                            @endphp
                            {{ numberToWordsBg($totalGrandTotal ?? 0) }}
                        </td>
                    </tr>
                </table>
            </td>
            
            <!-- ДЕСЕН БЛОК: СУМИ ЗА ПЛАЩАНЕ -->
            <td>
                <div class="vat-title">Сума за плащане</div>
                <table class="line-table">
                    <tr>
                        <td class="label-cell">Стойност на сделката:</td>
                        <td class="dots-cell"></td>
                        <td class="value-cell"><span class="amount">{{ number_format($totalSubtotal ?? 0, 2) }}</span></td>
                    </tr>
                </table>
                <table class="line-table">
                    <tr>
                        <td class="label-cell">Отстъпка %:</td>
                        <td class="dots-cell"></td>
                        <td class="value-cell"><span class="amount">0.00</span></td>
                    </tr>
                </table>
                <table class="line-table">
                    <tr>
                        <td class="label-cell">Даначна основа:</td>
                        <td class="dots-cell"></td>
                        <td class="value-cell"><span class="amount">{{ number_format($totalSubtotal ?? 0, 2) }}</span></td>
                    </tr>
                </table>
                <table class="line-table">
                    <tr>
                        <td class="label-cell">ДДС:</td>
                        <td class="dots-cell"></td>
                        <td class="value-cell"><span class="amount">{{ number_format($totalVatAmount ?? 0, 2) }}</span></td>
                    </tr>
                </table>
                <table class="line-table last-row-table">
                    <tr>
                        <td class="label-cell">Сума за плащане:</td>
                        <td class="dots-cell"></td>
                        <td class="value-cell"><span class="amount">{{ number_format($totalGrandTotal ?? 0, 2) }}</span></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>

<!-- ДОЛНА ИНФОРМАЦИЯ В ТАБЛИЦА -->
<table class="details-table">
    <tr>
        <td class="label-cell">Начин на плащане:</td>
        <td class="value-cell">{{ $invoice->payment_method ?? 'Банков превод' }}</td>
    </tr>
    <tr>
        <td class="label-cell">Срок на плащане:</td>
        <td class="value-cell">{{ $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('d.m.Y') : '30 дни' }}</td>
    </tr>
</table>

<!-- ПОДПИСИ КАТО ТАБЛИЦА -->
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
</body>

</html>
