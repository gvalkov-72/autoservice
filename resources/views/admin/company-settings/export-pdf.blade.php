<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Данни на автосервиза № {{ $companySetting->id }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; margin: 30px; }
        .header { text-align: center; margin-bottom: 30px; }
        .footer { margin-top: 40px; font-size: 12px; text-align: center; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 8px; border: 1px solid #ccc; }
    </style>
</head>
<body>
    <div class="header">
        <h2>ДАННИ НА АВТОСЕРВИЗА</h2>
        @if($copy)
            <p style="color:red;font-weight:bold;">КОПИЕ</p>
        @else
            <p style="color:red;font-weight:bold;">ОРИГИНАЛ</p>
        @endif
    </div>

    <table>
        <tr><th>ID</th><td>{{ $companySetting->id }}</td></tr>
        <tr><th>Име на фирма</th><td>{{ $companySetting->name }}</td></tr>
        <tr><th>МОЛ</th><td>{{ $companySetting->contact_person ?? '-' }}</td></tr>
        <tr><th>Град</th><td>{{ $companySetting->city ?? '-' }}</td></tr>
        <tr><th>Адрес</th><td>{{ $companySetting->address ?? '-' }}</td></tr>
        <tr><th>ЕИК / ЕГН / ЗДДС номер</th><td>{{ $companySetting->vat_number ?? '-' }}</td></tr>
        <tr><th>Телефон</th><td>{{ $companySetting->phone ?? '-' }}</td></tr>
        <tr><th>Имейл</th><td>{{ $companySetting->email ?? '-' }}</td></tr>
        <tr><th>IBAN</th><td>{{ $companySetting->iban ?? '-' }}</td></tr>
        <tr><th>Име на банката</th><td>{{ $companySetting->bank_name ?? '-' }}</td></tr>
        <tr><th>BIC код</th><td>{{ $companySetting->bic ?? '-' }}</td></tr>
        <tr><th>Уебсайт</th><td>{{ $companySetting->website ?? '-' }}</td></tr>
        <tr><th>Статус</th><td>{{ $companySetting->is_active ? 'Активен' : 'Неактивен' }}</td></tr>
        @if($companySetting->invoice_footer)
        <tr><th>Текст за фактури</th><td>{{ $companySetting->invoice_footer }}</td></tr>
        @endif
    </table>

    <div class="footer">
        Генерирано на: {{ now()->format('d.m.Y H:i') }}<br>
        {{ $companySetting->name }}
    </div>
</body>
</html>