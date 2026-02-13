<!DOCTYPE html>
<html lang="bg">

<head>
    <meta charset="UTF-8">
    <title>Клиент: {!! $customer->name !!}</title>
    <style>
        /* ⚡ СЪЩИЯТ CSS – БЕЗ ПРОМЕНИ ⚡ */
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
        .customer-title {
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 2mm;
        }
        .customer-number {
            font-size: 6.5pt;
            font-weight: bold;
        }
        .customer-date {
            font-size: 6.5pt;
            font-weight: bold;
        }
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
        .vehicles-table-container {
            width: 197.5mm;
            margin-left: 4.5mm;
            border: 0.75pt solid #ccc;
            border-radius: 2px;
            background-color: #f9f9f9;
            padding: 1.5mm;
            margin-top: 2mm;
        }
        .vehicles-title {
            font-weight: bold;
            font-size: 7.5pt;
            margin-bottom: 1.5mm;
            color: #666;
        }
        .vehicles-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 6.2pt;
        }
        .vehicles-table th {
            padding: 0.4mm;
            text-align: center;
            font-weight: bold;
            color: #666;
            border-bottom: 0.75pt solid #ddd;
            background-color: #f5f5f5;
        }
        .vehicles-table td {
            padding: 0.4mm;
            text-align: left;
            border-bottom: 0.4pt solid #eee;
        }
        .vehicles-table td.center {
            text-align: center;
        }
        .workorders-table-container {
            width: 197.5mm;
            margin-left: 4.5mm;
            border: 0.75pt solid #ccc;
            border-radius: 2px;
            background-color: #f9f9f9;
            padding: 1.5mm;
            margin-top: 2mm;
        }
        .workorders-title {
            font-weight: bold;
            font-size: 7.5pt;
            margin-bottom: 1.5mm;
            color: #666;
        }
        .workorders-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 6.2pt;
        }
        .workorders-table th {
            padding: 0.4mm;
            text-align: center;
            font-weight: bold;
            color: #666;
            border-bottom: 0.75pt solid #ddd;
            background-color: #f5f5f5;
        }
        .workorders-table td {
            padding: 0.4mm;
            text-align: left;
            border-bottom: 0.4pt solid #eee;
        }
        .workorders-table td.center {
            text-align: center;
        }
        .workorders-table td.right {
            text-align: right;
        }
        .no-break {
            page-break-inside: avoid;
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
        .vin-display {
            font-family: 'Courier New', monospace;
            letter-spacing: 1px;
            background: #f0f0f0;
            padding: 0.5mm 1mm;
            border-radius: 2px;
            font-size: 5.8pt;
        }
        .col-v1 { width: 20%; }
        .col-v2 { width: 15%; }
        .col-v3 { width: 20%; }
        .col-v4 { width: 10%; }
        .col-v5 { width: 25%; }
        .col-v6 { width: 10%; text-align: center; }
        .col-w1 { width: 8%; }
        .col-w2 { width: 12%; }
        .col-w3 { width: 25%; }
        .col-w4 { width: 15%; }
        .col-w5 { width: 15%; }
        .col-w6 { width: 15%; text-align: right; }
        .col-w7 { width: 10%; text-align: center; }
    </style>
</head>

<body style="padding-top: 5mm;">

    <!-- ТРИ КОЛОНИ -->
    <table class="three-columns no-break">
        <tr>
            <!-- ЛЯВА КОЛОНА: ДЕТАЙЛИ ЗА КЛИЕНТА (без данъчни) -->
            <td class="left-column">
                <div class="section-title">Данни за клиента</div>
                <table class="info-table">
                    <tr><td class="label-cell">Клиент №:</td><td class="value-cell">{{ $customer->customer_number ?? '—' }}</td></tr>
                    <tr><td class="label-cell">Стар ID:</td><td class="value-cell">{{ $customer->old_id ?? '—' }}</td></tr>
                    <tr><td class="label-cell">Име:</td><td class="value-cell">{!! $customer->name !!}</td></tr>
                    <tr><td class="label-cell">Телефон:</td><td class="value-cell">{{ $customer->phone ?? '—' }}</td></tr>
                    <tr><td class="label-cell">Факс:</td><td class="value-cell">{{ $customer->fax ?? '—' }}</td></tr>
                    <tr><td class="label-cell">E-mail:</td><td class="value-cell">{{ $customer->email ?? '—' }}</td></tr>
                    <tr><td class="label-cell">Адрес:</td><td class="value-cell">{!! $customer->address ?? '—' !!} @if($customer->address_2)<br>{!! $customer->address_2 !!}@endif</td></tr>
                    <tr><td class="label-cell">Жил. адрес:</td><td class="value-cell">{!! $customer->res_address_1 ?? '—' !!} @if($customer->res_address_2)<br>{!! $customer->res_address_2 !!}@endif</td></tr>
                </table>
            </td>

            <!-- ЦЕНТРАЛНА КОЛОНА: ЗАГЛАВИЕ -->
            <td class="center-column">
                <div class="customer-title">КЛИЕНТСКА КАРТА</div>
                <div class="customer-number">Рег. № {{ $customer->customer_number ?? $customer->id }}</div>
                <div class="customer-date">Печат: {{ now()->format('d.m.Y H:i') }}</div>
                <div style="margin-top: 3mm;">
                    @if($customer->is_active)
                        <span style="color: #28a745; font-size: 7pt;">АКТИВЕН</span>
                    @else
                        <span style="color: #6c757d; font-size: 7pt;">НЕАКТИВЕН</span>
                    @endif
                </div>
                <div style="margin-top: 1mm; font-size: 6pt;">
                    @if($customer->is_customer && $customer->is_supplier)
                        Клиент/Доставчик
                    @elseif($customer->is_customer)
                        Клиент
                    @elseif($customer->is_supplier)
                        Доставчик
                    @endif
                </div>
            </td>

            <!-- ДЯСНА КОЛОНА: ДАНЪЧНИ ДАННИ НА КЛИЕНТА -->
            <td class="right-column">
                <div class="section-title">Данъчни данни</div>
                <table class="info-table">
                    <tr><td class="label-cell">БУЛСТАТ/ЕИК:</td><td class="value-cell">{{ $customer->bulstat ?? '—' }}</td></tr>
                    <tr><td class="label-cell">ДДС №:</td><td class="value-cell">{{ $customer->tax_number ?? '—' }}</td></tr>
                    <tr><td class="label-cell">МОЛ:</td><td class="value-cell">{!! $customer->mol ?? '—' !!}</td></tr>
                    <tr><td class="label-cell">Лице за контакт:</td><td class="value-cell">{!! $customer->contact_person ?? '—' !!}</td></tr>
                    <tr><td class="label-cell">Тип документ:</td><td class="value-cell">{{ $customer->doc_type ?? '—' }}</td></tr>
                    <tr><td class="label-cell">Дата ЕИ:</td><td class="value-cell">{{ $customer->eidate ? $customer->eidate->format('d.m.Y') : '—' }}</td></tr>
                    <tr><td class="label-cell">Получател:</td><td class="value-cell">{!! $customer->receiver ?? '—' !!}</td></tr>
                    @if($customer->receiver_details)
                        <tr><td class="label-cell" style="vertical-align: top;">Детайли:</td>
                            <td class="value-cell" style="border-bottom: none;">{!! nl2br(e($customer->receiver_details)) !!}</td>
                        </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>

    <!-- ⚡ ПРЕМАХНАТ Е ЦЕЛИЯТ БЛОК С ДАНЪЧНИ ДАННИ ПОД ХЕДЪРА – вече са в дясната колона ⚡ -->

    <!-- АВТОМОБИЛИ -->
    <table class="full-width-table no-break">
        <tr><td><div class="vehicles-table-container">
            <div class="vehicles-title">Автомобили ({{ $customer->vehicles->count() }})</div>
            @if($customer->vehicles->count())
                <table class="vehicles-table">
                    <thead><tr><th class="col-v1">Марка/Модел</th><th class="col-v2">Рег. номер</th><th class="col-v3">VIN/Рама</th><th class="col-v4">Пробег</th><th class="col-v5">Бележки</th><th class="col-v6">Актив.</th></tr></thead>
                    <tbody>
                        @foreach($customer->vehicles as $v)
                        <tr>
                            <td>{!! $v->vehicle ?? '—' !!}</td>
                            <td><strong>{{ $v->plate_number ?? '—' }}</strong></td>
                            <td>@if($v->chassis_number)<span class="vin-display">{{ $v->chassis_number }}</span>@else—@endif</td>
                            <td class="center">{{ $v->last_mileage ? number_format($v->last_mileage, 0, ',', ' ').' км' : '—' }}</td>
                            <td>{{ $v->notes ?? '—' }}</td>
                            <td class="center">{{ $v->is_active ? 'Да' : 'Не' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div style="text-align: center; padding: 2mm; color: #666; font-size: 6.5pt;">Няма регистрирани автомобили за този клиент.</div>
            @endif
        </div></td></tr>
    </table>

    <!-- РАБОТНИ ПОРЪЧКИ -->
    <table class="full-width-table no-break">
        <tr><td><div class="workorders-table-container">
            <div class="workorders-title">Работни поръчки ({{ $workOrders->count() }})</div>
            @if($workOrders->count())
                <table class="workorders-table">
                    <thead><tr><th class="col-w1">№</th><th class="col-w2">Дата</th><th class="col-w3">Автомобил</th><th class="col-w4">Рег. номер</th><th class="col-w5">Механик</th><th class="col-w6">Сума (€)</th><th class="col-w7">Статус</th></tr></thead>
                    <tbody>
                        @foreach($workOrders as $wo)
                        <tr>
                            <td class="center">{{ $wo->old_id }}</td>
                            <td class="center">{{ $wo->order_date?->format('d.m.Y') }}</td>
                            <td>{!! $wo->vehicle ?? '—' !!}</td>
                            <td>{{ $wo->plate_number ?? '—' }}</td>
                            <td class="center">{{ $wo->mechanic_code ?? '—' }}</td>
                            <td class="right">{{ number_format($wo->total, 2, ',', ' ') }}</td>
                            <td class="center">
                                @if($wo->paid)
                                    <span style="color: #28a745;">Платена</span>
                                @else
                                    <span style="color: #dc3545;">Неплатена</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div style="text-align: center; padding: 2mm; color: #666; font-size: 6.5pt;">Няма намерени работни поръчки за този клиент.</div>
            @endif
        </div></td></tr>
    </table>

    <!-- БЕЛЕЖКИ -->
    @if($customer->notes)
        <table class="full-width-table no-break">
            <tr><td><div class="notes-box"><div class="notes-title">Бележки</div><div style="font-size: 6.2pt;">{!! nl2br(e($customer->notes)) !!}</div></div></td></tr>
        </table>
    @endif

    <!-- ДОПЪЛНИТЕЛНА ИНФОРМАЦИЯ (СЪЗДАДЕН/ОБНОВЕН) -->
    <table class="full-width-table no-break" style="margin-top: 2mm;">
        <tr><td><div style="width: 197.5mm; margin-left: 4.5mm; display: flex; justify-content: space-between; font-size: 5.8pt; color: #666;">
            <span>Създаден: {{ $customer->created_at->format('d.m.Y H:i:s') }}</span>
            <span>Обновен: {{ $customer->updated_at->format('d.m.Y H:i:s') }}</span>
        </div></td></tr>
    </table>

    <div class="footer"><span class="page-number"></span></div>

</body>
</html>