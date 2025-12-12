<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Касова бележка № {{ $payment->id }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; margin: 30px; }
        .header { text-align: center; margin-bottom: 30px; }
        .footer { margin-top: 40px; font-size: 12px; text-align: center; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 8px; border: 1px solid #ccc; }
        .right { text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h2>КАСОВА БЕЛЕЖКА № {{ $payment->id }}</h2>
        @if($copy)
            <p style="color:red;font-weight:bold;">КОПИЕ</p>
        @else
            <p style="color:red;font-weight:bold;">ОРИГИНАЛ</p>
        @endif
    </div>

    <table>
        <tr>
            <td><strong>Фактура:</strong></td>
            <td>{{ $payment->invoice->number }}</td>
        </tr>
        <tr>
            <td><strong>Клиент:</strong></td>
            <td>{{ $payment->invoice->customer->name }}</td>
        </tr>
        <tr>
            <td><strong>Сума:</strong></td>
            <td class="right">{{ number_format($payment->amount, 2) }} лв.</td>
        </tr>
        <tr>
            <td><strong>Начин:</strong></td>
            <td>{{ $payment->method }}</td>
        </tr>
        <tr>
            <td><strong>Дата:</strong></td>
            <td>{{ $payment->paid_at->format('d.m.Y') }}</td>
        </tr>
        <tr>
            <td><strong>Реф.:</strong></td>
            <td>{{ $payment->reference ?? '-' }}</td>
        </tr>
    </table>

    <div class="footer">
        Благодарим за доверието!<br>
        Автосервиз ООД
    </div>
</body>
</html>