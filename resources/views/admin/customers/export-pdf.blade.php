<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Клиент № {{ $customer->id }}</title>
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
        <h2>КЛИЕНТ № {{ $customer->id }}</h2>
        @if($copy)
            <p style="color:red;font-weight:bold;">КОПИЕ</p>
        @else
            <p style="color:red;font-weight:bold;">ОРИГИНАЛ</p>
        @endif
    </div>

    <table>
        <tr><th>Име</th><td>{{ $customer->name }}</td></tr>
        <tr><th>Тип</th><td>{{ $customer->type == 'company' ? 'Фирма' : 'Физ. лице' }}</td></tr>
        <tr><th>ДДС №</th><td>{{ $customer->vat_number ?? '-' }}</td></tr>
        <tr><th>Телефон</th><td>{{ $customer->phone ?? '-' }}</td></tr>
        <tr><th>Имейл</th><td>{{ $customer->email ?? '-' }}</td></tr>
        <tr><th>Адрес</th><td>{{ $customer->address ?? '-' }}</td></tr>
        <tr><th>Град</th><td>{{ $customer->city ?? '-' }}</td></tr>
        <tr><th>Бележки</th><td>{{ $customer->notes ?? '-' }}</td></tr>
    </table>

    <h4>Автомобили</h4>
    <table>
        <thead><tr><th>Рег. №</th><th>VIN</th><th>Марка/Модел</th><th>Година</th></tr></thead>
        <tbody>
            @forelse($customer->vehicles as $v)
                <tr><td>{{ $v->plate }}</td><td>{{ $v->vin }}</td><td>{{ $v->make }} / {{ $v->model }}</td><td>{{ $v->year }}</td></tr>
            @empty
                <tr><td colspan="4">Няма автомобили</td></tr>
            @endforelse
        </tbody>
    </table>

    <h4>Поръчки</h4>
    <table>
        <thead><tr><th>№</th><th>Статус</th><th>Дата</th><th>Общо</th></tr></thead>
        <tbody>
            @forelse($customer->workOrders as $o)
                <tr><td>{{ $o->number }}</td><td>{{ $o->status }}</td><td>{{ $o->received_at?->format('d.m.Y') }}</td><td class="right">{{ number_format($o->total, 2) }} лв.</td></tr>
            @empty
                <tr><td colspan="4">Няма поръчки</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Благодарим за доверието!<br>
        Автосервиз ООД
    </div>
</body>
</html>