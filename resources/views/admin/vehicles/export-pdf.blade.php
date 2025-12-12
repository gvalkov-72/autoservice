<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Автомобил {{ $vehicle->plate }}</title>
    <style>
        body        { font-family: DejaVu Sans, sans-serif; margin: 30px; color:#333; }
        .header     { text-align:center; margin-bottom:30px; }
        .footer     { margin-top:40px; font-size:12px; text-align:center; }
        table       { width:100%; border-collapse:collapse; }
        th, td      { padding:8px; border:1px solid #ccc; }
        .label      { font-weight:bold; background:#f5f5f5; }
    </style>
</head>
<body>
    <div class="header">
        <h2>ДАННИ ЗА АВТОМОБИЛ</h2>
        <p>Рег. №: <strong>{{ $vehicle->plate }}</strong></p>
    </div>

    <table>
        <tr>
            <td class="label">Собственик</td>
            <td>{{ $vehicle->customer->name }}</td>
            <td class="label">VIN</td>
            <td>{{ $vehicle->vin }}</td>
        </tr>
        <tr>
            <td class="label">Марка / Модел</td>
            <td>{{ $vehicle->make }} / {{ $vehicle->model }}</td>
            <td class="label">Година</td>
            <td>{{ $vehicle->year ?? '–' }}</td>
        </tr>
        <tr>
            <td class="label">Пробег</td>
            <td>{{ number_format($vehicle->mileage,0,',',' ') }} км</td>
            <td class="label">ДК №</td>
            <td>{{ $vehicle->dk_no ?? '–' }}</td>
        </tr>
        <tr>
            <td class="label">Бележки</td>
            <td colspan="3">{{ $vehicle->notes ?? 'Няма' }}</td>
        </tr>
    </table>

    <h4 style="margin-top:30px;">История на поръчките</h4>
    @if($vehicle->workOrders->isEmpty())
        <p>Няма записани поръчки.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>№</th>
                    <th>Статус</th>
                    <th>Дата</th>
                    <th>Общо (лв.)</th>
                    <th>Фактура</th>
                </tr>
            </thead>
            <tbody>
                @foreach($vehicle->workOrders as $wo)
                    <tr>
                        <td>{{ $wo->number }}</td>
                        <td>{{ $wo->status }}</td>
                        <td>{{ $wo->created_at->format('d.m.Y') }}</td>
                        <td style="text-align:right;">{{ number_format($wo->total,2) }}</td>
                        <td>
                            @if($wo->invoices->isNotEmpty())
                                {{ $wo->invoices->first()->number }}
                            @else
                                –
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="footer">
        Автосервиз ООД – {{ now()->format('d.m.Y') }}
    </div>
</body>
</html>