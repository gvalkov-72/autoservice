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
            color: #333;
        }

        /* ТРИ КОЛОНИ С РАЗСТОЯНИЕ МЕЖДУ ТЯХ */
        .three-column-container {
            width: 100%;
            margin-bottom: 10mm;
            display: table;
            border-spacing: 3mm; /* РАЗСТОЯНИЕ МЕЖДУ КОЛОНИТЕ */
        }

        .client-box, .supplier-box, .invoice-box {
            display: table-cell;
            border: 1pt solid #ccc; /* НЕПРЕКЪСНАТА БЛЕДА ЛИНИЯ */
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
            font-size: 9pt;
            margin-bottom: 2mm;
            padding-bottom: 1mm;
            border-bottom: 1pt dotted #000;
            color: #333;
        }

        .invoice-title {
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            text-align: center;
            margin-bottom: 5mm;
            margin-top: 2mm;
            color: #333;
        }

        .invoice-number-large {
            font-size: 10pt;
            font-weight: bold;
            text-align: center;
            margin-bottom: 0;
            color: #333;
        }

        .invoice-date-large {
            font-size: 8pt;
            font-weight: bold;
            text-align: center;
            margin-top: 0;
            color: #333;
        }

        /* ПОЛЕТА */
        .info-line {
            margin-bottom: 1.5mm;
            clear: both;
        }

        .label {
            float: left;
            width: 15mm;
            font-weight: bold;
            font-size: 6pt;
            color: #999; /* БЛЕД ЛЕЙБЪЛ */
        }

        .value {
            margin-left: 16mm;
            border-bottom: 0.5pt dotted #666;
            padding-bottom: 0.5mm;
            font-size: 7pt;
            color: #333;
        }

        /* ТАБЛИЦА ЗА АРТИКУЛИ С РАМКА */
        .items-container {
            border: 1pt solid #ccc; /* НЕПРЕКЪСНАТА БЛЕДА РАМКА */
            border-radius: 5px;
            padding: 3mm 2mm;
            margin-bottom: 5mm;
            /* ДИНАМИЧНА ВИСОЧИНА - НЯМА FIXED HEIGHT */
            min-height: 20mm;
        }

        .items-title {
            font-weight: bold;
            font-size: 9pt;
            margin-bottom: 3mm;
            color: #999; /* БЛЕД ЗАГЛАВИЕ */
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 3mm;
            font-size: 7pt;
        }

        .items-table th {
            padding: 1mm;
            text-align: center;
            font-weight: bold;
            color: #999; /* БЛЕДИ ЗАГЛАВИЯ НА КОЛОНИ */
            border-bottom: 1pt solid #ddd;
        }

        .items-table td {
            padding: 1mm;
            text-align: center;
            color: #333;
            border-bottom: 0.5pt solid #eee;
        }

        .items-table tr:last-child td {
            border-bottom: none; /* Без линия на последния ред */
        }

        /* ШИРИНИ НА КОЛОНИ - ПРЕНАСТРОЕНИ ЗА НОВИТЕ КОЛОНИ */
        .col-no { width: 4%; }
        .col-name { 
            width: 28%;
            text-align: left;
        }
        .col-unit { width: 6%; }       /* МЕ */
        .col-qty { width: 7%; }        /* Кол-во */
        .col-unit-price { width: 8%; } /* Цена */
        .col-discount-percent { width: 7%; } /* Отст. % */
        .col-discount-amount { width: 9%; }  /* Отст. ст-т */
        .col-subtotal { width: 9%; }   /* Стойност */
        .col-vat-percent { width: 6%; } /* ДДС % */
        .col-vat-amount { width: 9%; }  /* Стойност ДДС */
        .col-total { width: 13%; }     /* Обща стойност */

        /* КОНТЕЙНЕР ЗА СУМИ И ДДС - ВЪТРЕ В БАЛОНА */
        .totals-vat-container {
            width: 100%;
            margin-top: 5mm;
            display: table;
            table-layout: fixed; /* ФИКСИРАНА ТАБЛИЦА */
        }

        .vat-box {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            font-size: 7pt;
            padding-right: 1mm; /* ПО-МАЛКО РАЗСТОЯНИЕ */
        }

        .totals-box {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            font-size: 7pt;
        }

        .total-row {
            margin-bottom: 3mm;
            clear: both;
            white-space: nowrap;
            height: 5mm;
            line-height: 5mm;
            position: relative; /* ЗА ПОДЧЕРТАВАНЕ НА ЦЕЛИЯ РЕД */
        }

        .total-label {
            float: left;
            width: 25mm; /* ПО-МАЛКО МЯСТО (10 знака) */
            font-weight: bold;
            text-align: left;
            font-size: 8pt;
            color: #999;
            padding-right: 2mm; /* 2 ШПАЦИИ */
            white-space: nowrap;
            overflow: hidden;
        }

        .total-value {
            float: right;
            width: 15mm; /* ПО-МАЛКО МЯСТО */
            font-weight: bold;
            text-align: right;
            font-size: 8pt;
            position: relative;
            white-space: nowrap;
            padding-left: 2mm; /* 2 ШПАЦИИ */
        }

        /* ДОЛНА РАМКА НА ЦЕЛИЯ РЕД (точки) */
        .total-row::after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            border-bottom: 1pt dotted #000; /* ТОЧКИ ПО ЦЕЛИЯ РЕД */
            z-index: 1;
        }

        /* СТИЛ ЗА СУМИТЕ (над точките) */
        .total-value .amount {
            display: inline-block;
            min-width: 12mm;
            text-align: right;
            color: #333;
            vertical-align: middle;
            position: relative;
            z-index: 2; /* НАД ТОЧКИТЕ */
            background-color: white; /* ЗА ДА ПРЕКРИЕ ТОЧКИТЕ ПОД ТЕКСТА */
            padding: 0 0.5mm;
        }

        .vat-title {
            font-weight: bold;
            margin-bottom: 1mm;
            color: #999;
            white-space: nowrap;
        }

        .vat-content {
            color: #333;
        }

        /* "СЛОВОМ" ОТ ЛЯВАТА СТРАНА (под ДДС информацията) */
        .words-left-container {
            margin-top: 2mm;
            clear: both;
            white-space: nowrap;
            position: relative;
        }

        .words-left-label {
            float: left;
            width: 10mm; /* ПО-МАЛКО (2 шпации) */
            font-weight: bold;
            font-size: 8pt;
            color: #999;
            white-space: nowrap;
            padding-right: 2mm; /* 2 ШПАЦИИ */
            text-align: left;
        }

        .words-left-value {
            float: left;
            width: calc(100% - 12mm); /* АДАПТИВНА ШИРОЧИНА */
            font-weight: bold;
            font-size: 8pt;
            color: #333;
            white-space: nowrap;
            border-bottom: 0.5pt dotted #666;
            padding-bottom: 0.5mm;
            margin-left: 0;
        }

        /* ДОЛНА ИНФОРМАЦИЯ - ОБНОВЕНА */
        .details-container {
            width: 100%;
            margin-bottom: 5mm;
            clear: both;
        }

        .details-left {
            width: 48%;
            float: left;
        }

        .detail-line {
            margin-bottom: 1.5mm;
            clear: both;
            white-space: nowrap; /* НЕ СЕ РАЗБИВА НА ДВА РЕДА */
        }

        .detail-label {
            float: left;
            width: 25mm;
            font-weight: bold;
            font-size: 7pt;
            color: #999;
            white-space: nowrap;
        }

        .detail-value {
            margin-left: 27mm;
            border-bottom: 0.5pt dotted #666;
            padding-bottom: 0.5mm;
            font-size: 7pt;
            color: #333;
            white-space: nowrap;
        }

        /* ПОДПИСИ */
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
            font-size: 8pt;
            color: #999;
        }

        .signature-line {
            border-top: 1pt dotted #000;
            width: 100%;
            height: 10mm;
        }

        /* CLEARFIX */
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
        <!-- ТРИ КОЛОНИ С РАЗСТОЯНИЕ -->
        <div class="three-column-container">
            <!-- КЛИЕНТ (ляво) -->
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
                    <div class="label">ЕИК / ЕГН:</div>
                    <div class="value">{{ $invoice->customer->vat_number ?? '' }}</div>
                </div>
                <div class="info-line">
                    <div class="label">ЗДДС №:</div>
                    <div class="value">{{ $invoice->customer->vat_number ?? '______' }}</div>
                </div>
                <div class="info-line">
                    <div class="label">МОЛ:</div>
                    <div class="value">{{ $invoice->customer->contact_person ?? '' }}</div>
                </div>
                <div class="info-line">
                    <div class="label">IBAN:</div>
                    <div class="value">{{ $invoice->customer->iban ?? '' }}</div>
                </div>
                <div class="info-line">
                    <div class="label">Банка:</div>
                    <div class="value">{{ $invoice->customer->bank_name ?? '' }}</div>
                </div>
                <div class="info-line">
                    <div class="label">BIC:</div>
                    <div class="value">{{ $invoice->customer->bic ?? '' }}</div>
                </div>
            </div>

            <!-- ФАКТУРА (център) -->
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

            <!-- ДОСТАВЧИК (дясно) -->
            <div class="supplier-box">
                <div class="section-title">Доставчик</div>
                @php
                    // Вземете тези данни от базата или .env файл
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
                    <div class="label">ЕИК / ЕГН:</div>
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
                                // Изчисляване на стойностите за всеки артикул
                                $quantity = $item->quantity ?? 1;
                                $unitPrice = $item->unit_price ?? 0;
                                
                                // Ако няма данни за отстъпка, приемаме 0%
                                $discountPercent = $item->discount_percent ?? 0;
                                $discountAmount = $quantity * $unitPrice * ($discountPercent / 100);
                                
                                // Стойност без ДДС (след отстъпка)
                                $subtotal = ($quantity * $unitPrice) - $discountAmount;
                                
                                // Ако няма данни за ДДС, приемаме 20%
                                $vatPercent = $item->vat_percent ?? 20;
                                $vatAmount = $subtotal * ($vatPercent / 100);
                                
                                // Обща стойност (с ДДС)
                                $totalWithVat = $subtotal + $vatAmount;
                                
                                // Сумиране на общите суми
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
                        <!-- Ако няма артикули, показваме празна таблица -->
                        <tr>
                            <td colspan="11" style="text-align: center; padding: 5mm; color: #999;">
                                Няма добавени артикули
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>

            <!-- КОНТЕЙНЕР ЗА СУМИ И ДДС - ВЕЧЕ ВЪТРЕ В БАЛОНА -->
            <div class="totals-vat-container">
                <!-- ЛЯВА ЧАСТ: ИНФОРМАЦИЯ ЗА ДДС И СЛОВОМ -->
                <div class="vat-box">
                    <div class="vat-title">Информация за ДДС</div>
                    <div class="vat-content">
                        @php
                            // Групиране на ДДС по проценти
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
                                <div style="white-space: nowrap; margin-bottom: 1mm;">ДДС {{ number_format($percent, 2) }}%: {{ number_format($amount, 2) }}</div>
                            @endforeach
                        @else
                            <div style="white-space: nowrap;">ДДС 20%: {{ number_format($totalVatAmount ?? 0, 2) }}</div>
                        @endif
                    </div>
                    
                    <!-- "СЛОВОМ" ОТ ЛЯВАТА СТРАНА (под ДДС информацията) -->
                    <div class="words-left-container">
                        <div class="words-left-label">Словом:</div>
                        <div class="words-left-value">
                            @php
                                // Проста функция за преобразуване на число в думи (на български)
                                function numberToWordsBg($num) {
                                    $ones = array('', 'един', 'два', 'три', 'четири', 'пет', 'шест', 'седем', 'осем', 'девет');
                                    $tens = array('', 'десет', 'двадесет', 'тридесет', 'четиридесет', 'петдесет', 'шестдесет', 'седемдесет', 'осемдесет', 'деветдесет');
                                    $teens = array('десет', 'единадесет', 'дванадесет', 'тринадесет', 'четиринадесет', 'петнадесет', 'шестнадесет', 'седемнадесет', 'осемнадесет', 'деветнадесет');
                                    
                                    $num = round($num, 2);
                                    $leva = floor($num);
                                    $stotinki = round(($num - $leva) * 100);
                                    
                                    $words = '';
                                    
                                    // Хиляди
                                    if ($leva >= 1000) {
                                        $thousands = floor($leva / 1000);
                                        $words .= $ones[$thousands] . ' хиляди ';
                                        $leva %= 1000;
                                    }
                                    
                                    // Стотици
                                    if ($leva >= 100) {
                                        $hundreds = floor($leva / 100);
                                        $words .= $ones[$hundreds] . 'сто ';
                                        $leva %= 100;
                                    }
                                    
                                    // Десетици и единици
                                    if ($leva >= 20) {
                                        $tensDigit = floor($leva / 10);
                                        $words .= $tens[$tensDigit] . ' и ';
                                        $leva %= 10;
                                    } elseif ($leva >= 10) {
                                        $words .= $teens[$leva - 10] . ' ';
                                        $leva = 0;
                                    }
                                    
                                    // Единици
                                    if ($leva > 0) {
                                        $words .= $ones[$leva] . ' ';
                                    }
                                    
                                    if (empty(trim($words))) {
                                        $words = 'нула ';
                                    }
                                    
                                    $words .= 'лева';
                                    
                                    // Стотинки
                                    if ($stotinki > 0) {
                                        $words .= ' и ' . $stotinki . ' стотинки';
                                    }
                                    
                                    return trim($words);
                                }
                            @endphp
                            {{ numberToWordsBg($totalGrandTotal ?? 0) }}
                        </div>
                    </div>
                </div>

                <!-- ДЯСНА ЧАСТ: ОБЩИ СУМИ С ТОЧКИ ПОД ЦЕЛИЯ РЕД -->
                <div class="totals-box">
                    <div class="total-row">
                        <div class="total-label">Стойност на сделката:</div>
                        <div class="total-value">
                            <span class="amount">{{ number_format($totalSubtotal ?? 0, 2) }}</span>
                        </div>
                    </div>
                    <div class="total-row">
                        <div class="total-label">Отстъпка %:</div>
                        <div class="total-value">
                            <span class="amount">0.00</span>
                        </div>
                    </div>
                    <div class="total-row">
                        <div class="total-label">Даначна основа:</div>
                        <div class="total-value">
                            <span class="amount">{{ number_format($totalSubtotal ?? 0, 2) }}</span>
                        </div>
                    </div>
                    <div class="total-row">
                        <div class="total-label">ДДС:</div>
                        <div class="total-value">
                            <span class="amount">{{ number_format($totalVatAmount ?? 0, 2) }}</span>
                        </div>
                    </div>
                    <div class="total-row" style="border-top: 1pt solid #333; padding-top: 1mm; margin-top: 2mm; height: 6mm;">
                        <div class="total-label">Сума за плащане:</div>
                        <div class="total-value">
                            <span class="amount">{{ number_format($totalGrandTotal ?? 0, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ДОЛНА ИНФОРМАЦИЯ -->
        <div class="details-container">
            <!-- ЛЯВА КОЛОНА -->
            <div class="details-left">
                <div class="detail-line">
                    <div class="detail-label">Начин на плащане:</div>
                    <div class="detail-value">{{ $invoice->payment_method ?? 'Банков превод' }}</div>
                </div>
                <div class="detail-line">
                    <div class="detail-label">Срок на плащане:</div>
                    <div class="detail-value">{{ $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('d.m.Y') : '30 дни' }}</div>
                </div>
                <!-- ПРЕМАХНАТО: ПЛС изискуемо от получателя -->
            </div>
        </div>

        <!-- ПОДПИСИ -->
        <div class="signatures-container">
            <!-- ЛЯВ ПОДПИС -->
            <div class="signature-left">
                <div class="signature-text">Изготвил:</div>
                <div class="signature-line"></div>
            </div>

            <!-- ДЕСЕН ПОДПИС -->
            <div class="signature-right">
                <div class="signature-text">Получил:</div>
                <div class="signature-line"></div>
            </div>
        </div>
    </div>
</body>
</html>