<!DOCTYPE html>
<html lang="bg">

<head>
    <meta charset="UTF-8">
    <title>Фактура №{{ $invoice->old_id }}</title>
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

        .responsible-name {
            font-size: 6.5pt;
            margin-top: 1mm;
            font-weight: bold;
        }

        /* ФУТЪР ЗА НОМЕРАЦИЯ */
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

        .no-break {
            page-break-inside: avoid;
        }
    </style>
</head>

<body style="padding-top: 5mm;">

    @php
        $company = \App\Models\CompanySetting::where('is_active', true)->first();
        $rate = 1.95583;
        $totalEur = $invoice->total;
        $totalBgn = $totalEur * $rate;
    @endphp

    <!-- ТРИ КОЛОНИ -->
    <table class="three-columns no-break">
        <tr>
            <!-- ЛЯВА КОЛОНА: КЛИЕНТ -->
            <td class="left-column">
                <div class="section-title">Клиент</div>
                <table class="info-table">
                    <tr>
                        <td class="label-cell">Клиент:</td>
                        <td class="value-cell">{!! $invoice->customer->name ?? '—' !!}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">Клиентски №:</td>
                        <td class="value-cell">{{ $invoice->customer->customer_number ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">Стар ID:</td>
                        <td class="value-cell">{{ $invoice->customer->old_id ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">Телефон:</td>
                        <td class="value-cell">{{ $invoice->customer->phone ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">E-mail:</td>
                        <td class="value-cell">{{ $invoice->customer->email ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">Адрес:</td>
                        <td class="value-cell">
                            {!! $invoice->customer->address ?? '—' !!}
                            @if($invoice->customer->address_2)
                                <br>{!! $invoice->customer->address_2 !!}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="label-cell">МОЛ:</td>
                        <td class="value-cell">{!! $invoice->customer->mol ?? '—' !!}</td>
                    </tr>
                </table>
            </td>

            <!-- ЦЕНТРАЛНА КОЛОНА: ЗАГЛАВИЕ -->
            <td class="center-column">
                <div class="invoice-title">ФАКТУРА</div>
                <div class="invoice-number">№ {{ $invoice->old_id }}</div>
                <div class="invoice-date">
                    Дата: {{ $invoice->invoice_date ? $invoice->invoice_date->format('d.m.Y') : now()->format('d.m.Y') }}
                </div>
                <div class="invoice-date" style="margin-top: 1mm;">
                    Падеж: {{ $invoice->date_due ? $invoice->date_due->format('d.m.Y') : '—' }}
                </div>
                <div style="margin-top: 2mm; font-size: 6pt;">
                    @if($invoice->is_void)
                        <span style="color: #dc3545;">АНУЛИРАНА</span>
                    @elseif($invoice->paid)
                        <span style="color: #28a745;">ПЛАТЕНА</span>
                    @else
                        <span style="color: #ffc107;">НЕПЛАТЕНА</span>
                    @endif
                </div>
                <div style="margin-top: 2mm; font-size: 5.5pt;">
                    Принтирана: {{ now()->format('d.m.Y H:i') }}
                </div>
            </td>

            <!-- ДЯСНА КОЛОНА: ДОСТАВЧИК (НАШАТА ФИРМА) -->
            <td class="right-column">
                <div class="section-title">Доставчик</div>
                <table class="info-table">
                    @if($company)
                        <tr>
                            <td class="label-cell">Фирма:</td>
                            <td class="value-cell">{!! $company->name ?? '—' !!}</td>
                        </tr>
                        <tr>
                            <td class="label-cell">Адрес:</td>
                            <td class="value-cell">
                                @if($company->city && $company->address)
                                    {{ $company->city }}, {{ $company->address }}
                                @elseif($company->address)
                                    {{ $company->address }}
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="label-cell">Телефон:</td>
                            <td class="value-cell">{{ $company->phone ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="label-cell">ЕИК:</td>
                            <td class="value-cell">{{ $company->vat_number ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="label-cell">ДДС №:</td>
                            <td class="value-cell">BG{{ $company->vat_number ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="label-cell">МОЛ:</td>
                            <td class="value-cell">{!! $company->contact_person ?? '—' !!}</td>
                        </tr>
                        <tr>
                            <td class="label-cell">Имейл:</td>
                            <td class="value-cell">{{ $company->email ?? '—' }}</td>
                        </tr>
                        @if($company->iban)
                        <tr>
                            <td class="label-cell">IBAN:</td>
                            <td class="value-cell">{{ $company->iban }}</td>
                        </tr>
                        <tr>
                            <td class="label-cell">BIC:</td>
                            <td class="value-cell">{{ $company->bic ?? '—' }}</td>
                        </tr>
                        @endif
                    @else
                        <tr>
                            <td class="label-cell" colspan="2" style="text-align: center; color: #999;">
                                Няма въведени фирмени настройки
                            </td>
                        </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>

    <!-- ТАБЛИЦА С АРТИКУЛИ -->
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
                            @forelse($invoice->items as $index => $item)
                                @php
                                    $rowTotalEur = $item->quantity * $item->price_each;
                                    $rowTotalBgn = $rowTotalEur * $rate;
                                @endphp
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td class="col-2">
                                        @if($item->item_code)
                                            <span style="font-size: 5.5pt; color: #666;">[{{ $item->item_code }}]</span><br>
                                        @endif
                                        {{ $item->item_name }}
                                    </td>
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
                        </tbody>
                    </table>
                </div>
            </td>
        </tr>
    </table>

    <!-- ДВА БЛОКА: ДАНЪЧНИ И ОБЩА СУМА -->
    <table class="two-columns no-break">
        <tr>
            <!-- ЛЯВ БЛОК: ДАНЪЧНИ / БЕЛЕЖКИ -->
            <td class="two-columns1">
                <div class="info-box">
                    <div class="box-title">Данъчна информация</div>
                    @if($invoice->doctype)
                        <table class="line-table">
                            <tr>
                                <td class="label">Тип фактура:</td>
                                <td class="dots"></td>
                                <td class="value">{{ $invoice->doctype->name }}</td>
                            </tr>
                        </table>
                        @if($invoice->doctype->ddstype)
                        <table class="line-table">
                            <tr>
                                <td class="label">ДДС тип:</td>
                                <td class="dots"></td>
                                <td class="value">{{ $invoice->doctype->ddstype }}</td>
                            </tr>
                        </table>
                        @endif
                    @endif

                    @if($invoice->zeroexplain)
                        <div style="margin-top: 1.5mm;">
                            <span style="font-weight: bold; font-size: 6.2pt; color: #666;">Обяснение нулева ставка:</span>
                            <div style="font-size: 6pt; margin-top: 0.5mm; padding: 1mm; background: #ffffcc; border-radius: 2px;">
                                {{ $invoice->zeroexplain }}
                            </div>
                        </div>
                    @endif

                    @if($invoice->note)
                        <div style="margin-top: 1.5mm;">
                            <span style="font-weight: bold; font-size: 6.2pt; color: #666;">Бележки:</span>
                            <div style="font-size: 6pt; margin-top: 0.5mm; padding: 1mm; background: #f0f0f0; border-radius: 2px;">
                                {!! nl2br(e($invoice->note)) !!}
                            </div>
                        </div>
                    @endif
                </div>
            </td>

            <!-- ДЕСЕН БЛОК: ОБОБЩЕНИЕ НА СУМИТЕ -->
            <td class="two-columns2">
                <div class="summary-box">
                    <div class="box-title">Обобщение на сумите</div>
                    <table class="line-table">
                        <tr>
                            <td class="label">Общо (€):</td>
                            <td class="dots"></td>
                            <td class="value">{{ number_format($totalEur, 2, ',', ' ') }}</td>
                        </tr>
                    </table>
                    <table class="line-table">
                        <tr>
                            <td class="label">Общо (лв):</td>
                            <td class="dots"></td>
                            <td class="value">{{ number_format($totalBgn, 2, ',', ' ') }}</td>
                        </tr>
                    </table>
                    @if($invoice->tipsdelka > 0)
                    <table class="line-table">
                        <tr>
                            <td class="label">Tipsdelka:</td>
                            <td class="dots"></td>
                            <td class="value">{{ $invoice->tipsdelka }}</td>
                        </tr>
                    </table>
                    @endif
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

    <!-- ИНФОРМАЦИЯ ЗА ПЛАЩАНЕ -->
    <table class="full-width-table no-break">
        <tr>
            <td>
                <div style="width: 197.5mm; margin-left: 4.5mm; border: 0.75pt solid #ccc; border-radius: 2px; background-color: #f9f9f9; padding: 1.5mm;">
                    <div style="font-weight: bold; font-size: 6.8pt; color: #666; margin-bottom: 0.8mm;">
                        Информация за плащане
                    </div>
                    <table style="width: 100%; font-size: 6.2pt; border-collapse: collapse;">
                        <tr>
                            <td style="width: 12%; font-weight: bold;">Статус:</td>
                            <td style="width: 23%;">
                                @if($invoice->is_void)
                                    <span style="color: #dc3545;">Анулирана</span>
                                @elseif($invoice->paid)
                                    <span style="color: #28a745;">Платена</span>
                                @else
                                    <span style="color: #ffc107;">Неплатена</span>
                                @endif
                            </td>
                            <td style="width: 15%; font-weight: bold;">Метод:</td>
                            <td style="width: 25%;">
                                @php
                                    $methods = [0 => 'Банков превод', 1 => 'В брой', 2 => 'Карта'];
                                @endphp
                                {{ $methods[$invoice->pay_method] ?? '—' }}
                                @if($invoice->payment_cash)
                                    (в брой)
                                @endif
                            </td>
                            <td style="width: 10%; font-weight: bold;">Отпечатана:</td>
                            <td style="width: 15%;">{{ $invoice->printed ? 'Да' : 'Не' }}</td>
                        </tr>
                    </table>
                    @if($company && $company->iban && !$invoice->payment_cash)
                    <div style="margin-top: 1mm; padding-top: 0.5mm; border-top: 0.4pt dotted #ccc;">
                        <span style="font-weight: bold;">IBAN:</span> {{ $company->iban }} 
                        @if($company->bic) <span style="font-weight: bold; margin-left: 2mm;">BIC:</span> {{ $company->bic }} @endif
                        @if($company->bank_name) <span style="font-weight: bold; margin-left: 2mm;">Банка:</span> {{ $company->bank_name }} @endif
                    </div>
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <!-- ПОДПИСИ -->
    <table class="signatures-table">
        <tr>
            <td>
                <div class="signature-text">Съставил:</div>
                <div class="signature-line"></div>
                <div class="responsible-name">
                    {{ $invoice->invoice_created_by ?? auth()->user()->name ?? '—' }}
                </div>
            </td>
            <td>
                <div class="signature-text">Получил/Приел:</div>
                <div class="signature-line"></div>
                <div class="responsible-name">
                    {{ $invoice->invoice_received_person ?? $invoice->customer->name ?? 'Клиент' }}
                </div>
            </td>
        </tr>
    </table>

    <!-- ФУТЪР С НОМЕРАЦИЯ -->
    <div class="footer">
        <span class="page-number"></span>
    </div>

</body>

</html>