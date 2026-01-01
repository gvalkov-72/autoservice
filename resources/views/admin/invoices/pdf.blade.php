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

        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .header-table td {
            vertical-align: top;
        }

        .logo {
            width: 180px;
        }

        h1 {
            text-align: center;
            margin: 10px 0 20px 0;
            font-size: 20px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .info-table td {
            width: 50%;
            padding: 6px;
            border: 1px solid #000;
            vertical-align: top;
        }

        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table.items th,
        table.items td {
            border: 1px solid #000;
            padding: 6px;
        }

        table.items th {
            background-color: #f2f2f2;
            text-align: center;
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

        .signatures {
            width: 100%;
            margin-top: 80px;
            border-collapse: collapse;
        }

        .signatures td {
            width: 50%;
            text-align: center;
            padding-top: 40px;
        }

        .signature-line {
            border-top: 1px solid #000;
            width: 70%;
            margin: 0 auto 5px auto;
        }

        .clearfix {
            clear: both;
        }
    </style>
</head>
<body>

{{-- HEADER --}}
<table class="header-table">
    <tr>
        <td>
            <img
                src="{{ public_path('images/logo.png') }}"
                class="logo"
                alt="Logo">
        </td>
        <td class="right">
            <strong>Фактура №:</strong> {{ $invoice->invoice_number }}<br>
            <strong>Дата на издаване:</strong> {{ $invoice->issue_date->format('d.m.Y') }}<br>
            <strong>Дата на данъчно събитие:</strong> {{ $invoice->tax_event_date->format('d.m.Y') }}
        </td>
    </tr>
</table>

<h1>ФАКТУРА – ОРИГИНАЛ</h1>

{{-- ДОСТАВЧИК / ПОЛУЧАТЕЛ --}}
<table class="info-table">
    <tr>
        <td>
            <strong>Доставчик:</strong><br>
            {{ config('company.name') }}<br>
            {{ config('company.address') }}<br>
            ЕИК: {{ config('company.eik') }}<br>
            ДДС №: {{ config('company.vat_number') }}
        </td>
        <td>
            <strong>Получател:</strong><br>
            {{ $invoice->customer->name }}<br>
            {{ $invoice->customer->address }}<br>
            ЕИК: {{ $invoice->customer->eik }}<br>
            ДДС №: {{ $invoice->customer->vat_number }}
        </td>
    </tr>
</table>

{{-- ПОЗИЦИИ --}}
<table class="items">
    <thead>
        <tr>
            <th>#</th>
            <th>Описание</th>
            <th>Кол.</th>
            <th>Ед. цена</th>
            <th>Общо</th>
        </tr>
    </thead>
    <tbody>
        @foreach($invoice->items as $index => $item)
            <tr>
                <td class="right">{{ $index + 1 }}</td>
                <td>{{ $item->description }}</td>
                <td class="right">{{ number_format($item->quantity, 2) }}</td>
                <td class="right">{{ number_format($item->unit_price, 2) }} лв</td>
                <td class="right">{{ number_format($item->total_price, 2) }} лв</td>
            </tr>
        @endforeach
    </tbody>
</table>

{{-- СУМИ --}}
<table class="totals-table">
    <tr>
        <td>Данъчна основа</td>
        <td class="right">{{ number_format($invoice->net_total, 2) }} лв</td>
    </tr>
    <tr>
        <td>ДДС ({{ $invoice->vat_rate }}%)</td>
        <td class="right">{{ number_format($invoice->vat_amount, 2) }} лв</td>
    </tr>
    <tr>
        <td><strong>ОБЩО</strong></td>
        <td class="right"><strong>{{ number_format($invoice->grand_total, 2) }} лв</strong></td>
    </tr>
</table>

<div class="clearfix"></div>

{{-- ПОДПИСИ --}}
<table class="signatures">
    <tr>
        <td>
            <div class="signature-line"></div>
            <strong>Издал</strong>
        </td>
        <td>
            <div class="signature-line"></div>
            <strong>Получил</strong>
        </td>
    </tr>
</table>

</body>
</html>
