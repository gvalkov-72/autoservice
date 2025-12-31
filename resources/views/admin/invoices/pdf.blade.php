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

        .container {
            width: 100%;
        }

        h1 {
            text-align: center;
            font-size: 20px;
            margin-bottom: 5px;
        }

        .subtitle {
            text-align: center;
            font-size: 12px;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 6px;
            border: 1px solid #000;
        }

        .no-border td {
            border: none;
            padding: 3px;
        }

        .header-table td {
            vertical-align: top;
        }

        .right {
            text-align: right;
        }

        .center {
            text-align: center;
        }

        .bold {
            font-weight: bold;
        }

        .items th {
            background-color: #f2f2f2;
        }

        .totals {
            width: 40%;
            float: right;
            margin-top: 10px;
        }

        .clearfix {
            clear: both;
        }

        .footer {
            margin-top: 40px;
            font-size: 11px;
        }
    </style>
</head>
<body>

<div class="container">

    <!-- Заглавие -->
    <h1>ФАКТУРА</h1>
    <div class="subtitle">Оригинал</div>

    <!-- Горна част: доставчик / фактура -->
    <table class="no-border header-table">
        <tr>
            <td width="60%">
                <strong>Доставчик:</strong><br>
                {{ config('company.name') }}<br>
                {{ config('company.address') }}<br>
                ЕИК: {{ config('company.eik') }}<br>
                ДДС №: {{ config('company.vat_number') }}
            </td>
            <td width="40%">
                <table>
                    <tr>
                        <td class="bold">Фактура №</td>
                        <td class="right">{{ $invoice->invoice_number }}</td>
                    </tr>
                    <tr>
                        <td class="bold">Дата на издаване</td>
                        <td class="right">{{ $invoice->issue_date->format('d.m.Y') }}</td>
                    </tr>
                    <tr>
                        <td class="bold">Данъчно събитие</td>
                        <td class="right">{{ $invoice->tax_event_date->format('d.m.Y') }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <br>

    <!-- Получател -->
    <table>
        <tr>
            <td>
                <strong>Получател:</strong><br>
                {{ $invoice->customer->name }}<br>
                {{ $invoice->customer->address }}<br>
                ЕИК: {{ $invoice->customer->eik }}<br>
                ДДС №: {{ $invoice->customer->vat_number }}
            </td>
        </tr>
    </table>

    <br>

    <!-- Позиции -->
    <table class="items">
        <thead>
        <tr>
            <th width="5%">№</th>
            <th width="45%">Описание</th>
            <th width="10%" class="right">Кол.</th>
            <th width="15%" class="right">Ед. цена</th>
            <th width="25%" class="right">Общо</th>
        </tr>
        </thead>
        <tbody>
        @foreach($invoice->items as $i => $item)
            <tr>
                <td class="center">{{ $i + 1 }}</td>
                <td>{{ $item->description }}</td>
                <td class="right">{{ number_format($item->quantity, 2) }}</td>
                <td class="right">{{ number_format($item->unit_price, 2) }} лв</td>
                <td class="right">{{ number_format($item->total, 2) }} лв</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <!-- Обобщение -->
    <table class="totals">
        <tr>
            <td class="bold">Данъчна основа</td>
            <td class="right">{{ number_format($invoice->net_total, 2) }} лв</td>
        </tr>
        <tr>
            <td class="bold">ДДС ({{ $invoice->vat_rate }}%)</td>
            <td class="right">{{ number_format($invoice->vat_amount, 2) }} лв</td>
        </tr>
        <tr>
            <td class="bold">ОБЩО</td>
            <td class="right bold">{{ number_format($invoice->grand_total, 2) }} лв</td>
        </tr>
    </table>

    <div class="clearfix"></div>

    <!-- Плащане -->
    <div class="footer">
        <strong>Начин на плащане:</strong>
        {{ $invoice->payments->pluck('payment_method')->unique()->join(', ') }}
    </div>

</div>

</body>
</html>
