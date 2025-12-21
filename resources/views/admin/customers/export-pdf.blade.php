<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Клиент № {{ $customer->id }} - {{ $customer->name }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; margin: 20px 30px; font-size: 12px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 15px; }
        .footer { margin-top: 40px; font-size: 10px; text-align: center; color: #666; border-top: 1px solid #ccc; padding-top: 10px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { padding: 8px 10px; border: 1px solid #ccc; text-align: left; vertical-align: top; }
        th { background-color: #f5f5f5; font-weight: bold; }
        .section-title { background-color: #e9ecef; padding: 8px 10px; margin: 20px 0 10px 0; font-weight: bold; border-left: 4px solid #007bff; }
        .status-active { color: green; font-weight: bold; }
        .status-inactive { color: #999; }
        .copy-stamp { color: red; font-weight: bold; text-align: center; margin: 10px 0; border: 2px solid red; padding: 5px; }
        .company-info { font-size: 10px; text-align: center; margin-bottom: 20px; color: #666; }
        .right { text-align: right; }
        .center { text-align: center; }
    </style>
</head>
<body>
    <!-- Горен колонтитул -->
    <div class="company-info">
        <strong>Автосервиз ООД</strong><br>
        гр. София, ул. Примерна 123<br>
        Тел: 02 123 4567 | Факс: 02 123 4568<br>
        ИН: BG123456789 | Булстат: 123456789
    </div>

    <div class="header">
        <h2 style="margin: 5px 0;">КЛИЕНТСКА КАРТА</h2>
        <h3 style="margin: 5px 0; color: #333;">№ {{ $customer->id }} | {{ $customer->name }}</h3>
        <p style="margin: 5px 0;">Изготвено на: {{ now()->format('d.m.Y H:i') }}</p>
        
        @if($copy)
            <div class="copy-stamp">КОПИЕ</div>
        @else
            <div style="color: green; font-weight: bold; text-align: center;">ОРИГИНАЛ</div>
        @endif
    </div>

    <!-- Основна информация -->
    <div class="section-title">1. ОСНОВНА ИНФОРМАЦИЯ</div>
    <table>
        <tr>
            <th style="width: 25%">Вътрешен №</th>
            <td>{{ $customer->id }}</td>
            <th style="width: 25%">Старо ID (Access)</th>
            <td>{{ $customer->old_system_id ?? '-' }}</td>
        </tr>
        <tr>
            <th>Тип клиент</th>
            <td>{{ $customer->type_label }}</td>
            <th>Име / Фирма</th>
            <td>{{ $customer->name }}</td>
        </tr>
        <tr>
            <th>Контактно лице</th>
            <td>{{ $customer->contact_person ?? '-' }}</td>
            <th>ДДС номер</th>
            <td>{{ $customer->vat_number ?? '-' }}</td>
        </tr>
        <tr>
            <th>Булстат</th>
            <td>{{ $customer->bulstat ?? '-' }}</td>
            <th>Пълен булстат</th>
            <td>{{ $customer->full_bulstat ?? '-' }}</td>
        </tr>
        <tr>
            <th>Съдебен регистър</th>
            <td>{{ $customer->court_registration ?? '-' }}</td>
            <th>Буква (Булстат)</th>
            <td>{{ $customer->bulstat_letter ?? '-' }}</td>
        </tr>
    </table>

    <!-- Контактна информация -->
    <div class="section-title">2. КОНТАКТНА ИНФОРМАЦИЯ</div>
    <table>
        <tr>
            <th style="width: 25%">Телефон</th>
            <td>{{ $customer->phone ?? '-' }}</td>
            <th style="width: 25%">Факс</th>
            <td>{{ $customer->fax ?? '-' }}</td>
        </tr>
        <tr>
            <th>Имейл</th>
            <td>{{ $customer->email ?? '-' }}</td>
            <th>Град</th>
            <td>{{ $customer->city ?? '-' }}</td>
        </tr>
        <tr>
            <th>Адрес (ред 1)</th>
            <td colspan="3">{{ $customer->address_line1 ?? '-' }}</td>
        </tr>
        <tr>
            <th>Адрес (ред 2)</th>
            <td colspan="3">{{ $customer->address_line2 ?? '-' }}</td>
        </tr>
        <tr>
            <th>Форматиран адрес</th>
            <td colspan="3">{{ $customer->formatted_address ?? '-' }}</td>
        </tr>
    </table>

    <!-- Статус и бележки -->
    <div class="section-title">3. СТАТУС И БЕЛЕЖКИ</div>
    <table>
        <tr>
            <th style="width: 25%">Статус</th>
            <td>
                @if($customer->is_active)
                    <span class="status-active">● Активен клиент</span>
                @else
                    <span class="status-inactive">● Неактивен клиент</span>
                @endif
            </td>
            <th style="width: 25%">Включване в справки</th>
            <td>
                @if($customer->include_in_reports)
                    <span class="status-active">● Да</span>
                @else
                    <span class="status-inactive">● Не</span>
                @endif
            </td>
        </tr>
        <tr>
            <th>Бележки</th>
            <td colspan="3">{{ $customer->notes ?? '-' }}</td>
        </tr>
    </table>

    <!-- Автомобили -->
    @if($customer->vehicles->count() > 0)
    <div class="section-title">4. АВТОМОБИЛИ ({{ $customer->vehicles->count() }})</div>
    <table>
        <thead>
            <tr>
                <th style="width: 15%">Рег. номер</th>
                <th style="width: 25%">VIN</th>
                <th style="width: 30%">Марка / Модел</th>
                <th style="width: 10%">Година</th>
                <th style="width: 20%">Пробег (км)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($customer->vehicles as $v)
                <tr>
                    <td class="center">{{ $v->plate }}</td>
                    <td>{{ $v->vin }}</td>
                    <td>{{ $v->make }} / {{ $v->model }}</td>
                    <td class="center">{{ $v->year }}</td>
                    <td class="right">{{ number_format($v->mileage, 0) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <!-- Работни поръчки -->
    @if($customer->workOrders->count() > 0)
    <div class="section-title">5. РАБОТНИ ПОРЪЧКИ ({{ $customer->workOrders->count() }})</div>
    <table>
        <thead>
            <tr>
                <th style="width: 15%">Номер</th>
                <th style="width: 25%">Статус</th>
                <th style="width: 20%">Дата</th>
                <th style="width: 40%">Обща сума</th>
            </tr>
        </thead>
        <tbody>
            @foreach($customer->workOrders as $wo)
                <tr>
                    <td class="center">{{ $wo->number }}</td>
                    <td>{{ $wo->status }}</td>
                    <td class="center">{{ optional($wo->received_at)->format('d.m.Y') ?? '-' }}</td>
                    <td class="right">{{ number_format($wo->total, 2) }} лв.</td>
                </tr>
            @endforeach
            @if($customer->workOrders->count() > 0)
                <tr style="font-weight: bold;">
                    <td colspan="3" class="right">Общо:</td>
                    <td class="right">{{ number_format($customer->workOrders->sum('total'), 2) }} лв.</td>
                </tr>
            @endif
        </tbody>
    </table>
    @endif

    <!-- Долен колонтитул -->
    <div class="footer">
        <p>Документът е автоматично генериран от системата за управление на автосервиз.<br>
        За валидността на данните отговаря администраторът на системата.</p>
        <p>Страница <span class="pageNumber"></span> от <span class="totalPages"></span></p>
    </div>

    <script type="text/javascript">
        // Номериране на страници за PDF
        var totalPages = Math.ceil(document.body.scrollHeight / 1123); // Приблизително изчисление
        document.querySelectorAll('.pageNumber').forEach(function(el) {
            el.textContent = 1; // Текуща страница - може да се сложи по-сложна логика
        });
        document.querySelectorAll('.totalPages').forEach(function(el) {
            el.textContent = totalPages;
        });
    </script>
</body>
</html>