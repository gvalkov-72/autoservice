<!DOCTYPE html>
<html lang="bg">

<head>
    <meta charset="UTF-8">
    <title>Работна поръчка №{{ $work_order->old_id }}</title>
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

        /* ОСНОВНА ТАБЛИЦА ЗА МАКЕТ */
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

        .work-order-title {
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 2mm;
        }

        .work-order-number {
            font-size: 6.5pt;
            font-weight: bold;
        }

        .work-order-date {
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
            width: 45%;
            text-align: left;
            padding-left: 0.8mm !important;
        }

        .col-3 {
            width: 8%;
        }

        .col-4 {
            width: 10%;
        }

        .col-5 {
            width: 12%;
        }

        .col-6 {
            width: 12%;
        }

        .col-7 {
            width: 10%;
        }

        /* ТАБЛИЦА ЗА ПОДАВАНЕ И ОБЩА СУМА */
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

        .info-box,
        .summary-box {
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

        .footer {
            position: fixed;
            bottom: 5mm;
            left: 0;
            right: 0;
            height: 8mm;
            text-align: center;
            font-size: 7pt;
            color: #666;
            border-top: 0.4pt solid #ccc;
            padding-top: 2mm;
        }

        .page-number:before {
            content: "Страница " counter(page) " от " counter(pages);
        }

        /* СТИЛОВЕ ЗА ВАЛУТИ */
        .currency {
            font-family: 'Courier New', monospace;
        }

        /* БЕЛЕЖКИ */
        .notes-box {
            border: 0.75pt solid #ccc;
            border-radius: 2px;
            background-color: #f9f9f9;
            padding: 1.5mm;
            margin-top: 2mm;
            font-size: 6.2pt;
        }

        .notes-title {
            font-weight: bold;
            font-size: 6.8pt;
            color: #666;
            margin-bottom: 0.8mm;
            padding-bottom: 0.4mm;
            border-bottom: 0.75pt dotted #000;
        }

        /* ВИН НОМЕР */
        .vin-display {
            font-family: 'Courier New', monospace;
            letter-spacing: 1px;
            background: #f0f0f0;
            padding: 0.5mm 1mm;
            border-radius: 2px;
            font-size: 5.8pt;
        }
    </style>
</head>

<body style="padding-top: 5mm;">

    <!-- ТРИ КОЛОНИ -->
    <table class="three-columns no-break">
        <tr>
            <!-- ЛЯВА КОЛОНА: КЛИЕНТ И АВТОМОБИЛ -->
            <td class="left-column">
                <div class="section-title">Клиент и автомобил</div>
                <table class="info-table">
                    <tr>
                        <td class="label-cell">Клиент:</td>
                        <td class="value-cell">{{ $work_order->client_name ?: '—' }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">Телефон:</td>
                        <td class="value-cell">{{ $work_order->phone ?: '—' }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">Автомобил:</td>
                        <td class="value-cell">{{ $work_order->vehicle ?: '—' }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">Рег. номер:</td>
                        <td class="value-cell">{{ $work_order->plate_number ?: '—' }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">VIN номер:</td>
                        <td class="value-cell">
                            @if($work_order->chassis_number)
                                <span class="vin-display">{{ $work_order->chassis_number }}</span>
                            @else
                                —
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="label-cell">Пробег:</td>
                        <td class="value-cell">
                            @if($work_order->mileage)
                                {{ number_format($work_order->mileage, 0, ',', ' ') }} км
                            @else
                                —
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="label-cell">Механик:</td>
                        <td class="value-cell">{{ $work_order->mechanic_code ?: '—' }}</td>
                    </tr>
                </table>
            </td>

            <!-- ЦЕНТРАЛНА КОЛОНА: ПОРЪЧКА -->
            <td class="center-column">
                <div class="work-order-title">РАБОТНА ПОРЪЧКА</div>
                <div class="work-order-number">№ {{ $work_order->old_id }}</div>
                <div class="work-order-date">
                    Дата {{ $work_order->order_date ? \Carbon\Carbon::parse($work_order->order_date)->format('d.m.Y') : now()->format('d.m.Y') }}
                </div>

                <!-- СЪЗДАДЕНА ОТ -->
                @if ($work_order->created_by)
                    <div class="work-order-date" style="margin-top: 1mm;">
                        Създал: {{ $work_order->created_by }}
                    </div>
                @endif

                <!-- ИНФОРМАЦИЯ ЗА ПЕЧАТ -->
                <div class="work-order-date" style="margin-top: 2mm; font-size: 5.5pt;">
                    Принтирана: {{ now()->format('d.m.Y H:i') }}
                </div>
            </td>

            <!-- ДЯСНА КОЛОНА: АВТОСЕРВИЗ -->
            <td class="right-column">
                <div class="section-title">Автосервиз</div>
                <table class="info-table">
                    <tr>
                        <td class="label-cell">Автосервиз:</td>
                        <td class="value-cell">ВАШИЯТ АВТОСЕРВИЗ ЕООД</td>
                    </tr>
                    <tr>
                        <td class="label-cell">Адрес:</td>
                        <td class="value-cell">ул. ВАШАТА АДРЕС, ВАШИЯТ ГРАД</td>
                    </tr>
                    <tr>
                        <td class="label-cell">Телефон:</td>
                        <td class="value-cell">+359 00 000 000</td>
                    </tr>
                    <tr>
                        <td class="label-cell">ЕИК:</td>
                        <td class="value-cell">000000000</td>
                    </tr>
                    <tr>
                        <td class="label-cell">ДДС №:</td>
                        <td class="value-cell">BG000000000</td>
                    </tr>
                    <tr>
                        <td class="label-cell">МОЛ:</td>
                        <td class="value-cell">ВАШЕТО ИМЕ</td>
                    </tr>
                    <tr>
                        <td class="label-cell">Имейл:</td>
                        <td class="value-cell">autoservice@example.com</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- АРТИКУЛИ И УСЛУГИ -->
    <table class="full-width-table no-break">
        <tr>
            <td>
                <div class="items-table-container">
                    <div class="items-title">Артикули и услуги</div>

                    <table class="items-table">
                        <thead>
                            <tr>
                                <th class="col-1">№</th>
                                <th class="col-2">Наименование</th>
                                <th class="col-3">Мярка</th>
                                <th class="col-4">Количество</th>
                                <th class="col-5">Ед. цена (€)</th>
                                <th class="col-6">Сума (€)</th>
                                <th class="col-7">Сума (лв)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $itemsTotalEur = 0;
                                $itemsTotalBgn = 0;
                                $rate = 1.95583;
                            @endphp

                            @forelse($work_order->items as $index => $item)
                                @php
                                    $rowTotalEur = $item->quantity * $item->price_each;
                                    $rowTotalBgn = $rowTotalEur * $rate;
                                    $itemsTotalEur += $rowTotalEur;
                                    $itemsTotalBgn += $rowTotalBgn;
                                @endphp
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td class="col-2">{{ $item->item_name }}</td>
                                    <td>{{ $item->item_measure ?: 'бр.' }}</td>
                                    <td>{{ number_format($item->quantity, 2, ',', ' ') }}</td>
                                    <td class="currency">{{ number_format($item->price_each, 2, ',', ' ') }}</td>
                                    <td class="currency">{{ number_format($rowTotalEur, 2, ',', ' ') }}</td>
                                    <td class="currency">{{ number_format($rowTotalBgn, 2, ',', ' ') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" style="text-align: center; padding: 2mm; color: #666;">
                                        Няма добавени артикули
                                    </td>
                                </tr>
                            @endforelse

                            <!-- СТОЙНОСТ НА ТРУДА -->
                            @if($work_order->service_amount > 0)
                                @php
                                    $serviceAmountEur = $work_order->service_amount;
                                    $serviceAmountBgn = $serviceAmountEur * $rate;
                                @endphp
                                <tr style="background-color: #f0f8ff;">
                                    <td colspan="5" style="text-align: right; font-weight: bold;">
                                        Стойност на труда:
                                    </td>
                                    <td class="currency" style="font-weight: bold;">
                                        {{ number_format($serviceAmountEur, 2, ',', ' ') }}
                                    </td>
                                    <td class="currency" style="font-weight: bold;">
                                        {{ number_format($serviceAmountBgn, 2, ',', ' ') }}
                                    </td>
                                </tr>
                            @endif

                            <!-- ОБЩА СУМА -->
                            @php
                                $grandTotalEur = $itemsTotalEur + ($work_order->service_amount ?? 0);
                                $grandTotalBgn = $grandTotalEur * $rate;
                            @endphp
                            <tr style="font-weight: bold; background-color: #e8f5e9;">
                                <td colspan="5" style="text-align: right; padding-right: 2mm;">
                                    ОБЩА СУМА:
                                </td>
                                <td class="currency" style="border-top: 0.75pt solid #333;">
                                    {{ number_format($grandTotalEur, 2, ',', ' ') }}
                                </td>
                                <td class="currency" style="border-top: 0.75pt solid #333;">
                                    {{ number_format($grandTotalBgn, 2, ',', ' ') }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </td>
        </tr>
    </table>

    <!-- ДВА БЛОКА: ИНФОРМАЦИЯ И СУМИ -->
    <table class="two-columns no-break">
        <tr>
            <!-- ДОПЪЛНИТЕЛНА ИНФОРМАЦИЯ -->
            <td class="two-columns1">
                <div class="info-box">
                    <div class="box-title">Допълнителна информация</div>
                    @if($work_order->note)
                        <table class="line-table">
                            <tr>
                                <td class="label">Бележки:</td>
                                <td class="dots"></td>
                                <td class="value"></td>
                            </tr>
                        </table>
                        <div style="font-size: 6pt; margin-top: 1mm; padding: 1mm; background: #ffffcc; border-radius: 2px;">
                            {{ $work_order->note }}
                        </div>
                    @endif
                    <table class="line-table">
                        <tr>
                            <td class="label">Създадена:</td>
                            <td class="dots"></td>
                            <td class="value">{{ $work_order->created_at->format('d.m.Y H:i') }}</td>
                        </tr>
                    </table>
                    <table class="line-table">
                        <tr>
                            <td class="label">Обновена:</td>
                            <td class="dots"></td>
                            <td class="value">{{ $work_order->updated_at->format('d.m.Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </td>

            <!-- ОБОБЩЕНИЕ НА СУМИТЕ -->
            <td class="two-columns2">
                <div class="summary-box">
                    <div class="box-title">Обобщение на сумите</div>
                    <table class="line-table">
                        <tr>
                            <td class="label">Артикули (€):</td>
                            <td class="dots"></td>
                            <td class="value">{{ number_format($itemsTotalEur, 2, ',', ' ') }}</td>
                        </tr>
                    </table>
                    @if($work_order->service_amount > 0)
                        <table class="line-table">
                            <tr>
                                <td class="label">Труд (€):</td>
                                <td class="dots"></td>
                                <td class="value">{{ number_format($work_order->service_amount, 2, ',', ' ') }}</td>
                            </tr>
                        </table>
                    @endif
                    <table class="line-table">
                        <tr>
                            <td class="label">Общо (€):</td>
                            <td class="dots"></td>
                            <td class="value">{{ number_format($grandTotalEur, 2, ',', ' ') }}</td>
                        </tr>
                    </table>
                    <table class="line-table">
                        <tr>
                            <td class="label">Общо (лв):</td>
                            <td class="dots"></td>
                            <td class="value">{{ number_format($grandTotalBgn, 2, ',', ' ') }}</td>
                        </tr>
                    </table>
                    <table class="line-table total-line">
                        <tr>
                            <td class="label">Валутен курс:</td>
                            <td class="dots"></td>
                            <td class="value">1 € = {{ number_format($rate, 5, ',', ' ') }} лв</td>
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
                <div class="signature-text">Извършил услугата:</div>
                <div class="signature-line"></div>
                @if ($work_order->mechanic_code)
                    <div class="responsible-name">Механик #{{ $work_order->mechanic_code }}</div>
                @else
                    <div class="responsible-name">_________________________</div>
                @endif
            </td>
            <td>
                <div class="signature-text">Получил/Приел:</div>
                <div class="signature-line"></div>
                <div class="responsible-name">{{ $work_order->client_name ?: 'Клиент' }}</div>
            </td>
        </tr>
    </table>

    <!-- ФУТЪР С НОМЕРАЦИЯ -->
    <div class="footer">
        <span class="page-number"></span>
    </div>

</body>

</html>