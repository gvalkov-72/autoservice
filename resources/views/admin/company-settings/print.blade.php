<!DOCTYPE html>
<html lang="bg">

<head>
    <meta charset="UTF-8">
    <title>Фирмени данни: {{ $companySetting->name }}</title>
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

        .company-title {
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 2mm;
        }

        .company-subtitle {
            font-size: 6.5pt;
            font-weight: bold;
            color: #666;
        }

        .company-date {
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

        /* ТАБЛИЦА ЗА ДАННИ */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 6.2pt;
            margin-bottom: 2mm;
        }

        .data-table th {
            background-color: #f5f5f5;
            font-weight: bold;
            color: #666;
            border-bottom: 0.75pt solid #ddd;
            padding: 0.8mm;
            text-align: left;
        }

        .data-table td {
            padding: 0.8mm;
            border-bottom: 0.4pt solid #eee;
        }

        .data-table td.label {
            width: 30mm;
            font-weight: bold;
            color: #666;
            background-color: #fafafa;
        }

        /* КОНТЕЙНЕР ЗА ДАННИ */
        .data-container {
            width: 197.5mm;
            margin-left: 4.5mm;
            border: 0.75pt solid #ccc;
            border-radius: 2px;
            background-color: #f9f9f9;
            padding: 1.5mm;
            margin-top: 2mm;
        }

        .data-title {
            font-weight: bold;
            font-size: 7.5pt;
            margin-bottom: 1.5mm;
            color: #666;
        }

        /* ЛОГО */
        .logo-container {
            text-align: center;
            margin-bottom: 2mm;
        }

        .logo {
            max-height: 80px;
            max-width: 160px;
        }

        /* БЕЛЕЖКИ */
        .footer-note {
            border: 0.75pt solid #ccc;
            border-radius: 2px;
            background-color: #f9f9f9;
            padding: 1.5mm;
            margin-top: 2mm;
            font-size: 6.2pt;
        }

        .footer-title {
            font-weight: bold;
            font-size: 6.8pt;
            color: #666;
            margin-bottom: 0.8mm;
            padding-bottom: 0.4mm;
            border-bottom: 0.75pt dotted #000;
        }

        /* НЕ РАЗДЕЛЯЙ */
        .no-break {
            page-break-inside: avoid;
        }

        /* ФУТЪР С НОМЕРАЦИЯ */
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
    </style>
</head>

<body style="padding-top: 5mm;">

    <!-- ТРИ КОЛОНИ -->
    <table class="three-columns no-break">
        <tr>
            <!-- ЛЯВА КОЛОНА: ЛОГО И АКТИВЕН СТАТУС -->
            <td class="left-column">
                <div class="section-title">Фирмен профил</div>
                <div class="logo-container">
                    @if($companySetting->logo_path)
                        <img src="{{ storage_path('app/public/' . $companySetting->logo_path) }}" 
                             alt="Лого" class="logo">
                    @else
                        <div style="font-size: 20pt; color: #ccc;">
                            <i class="fas fa-building"></i>
                        </div>
                    @endif
                </div>
                <table class="info-table">
                    <tr>
                        <td class="label-cell">Статус:</td>
                        <td class="value-cell">
                            @if($companySetting->is_active)
                                <span style="color: #28a745;">АКТИВЕН</span>
                            @else
                                <span style="color: #6c757d;">НЕАКТИВЕН</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="label-cell">ID:</td>
                        <td class="value-cell">{{ $companySetting->id }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">Създаден:</td>
                        <td class="value-cell">{{ $companySetting->created_at->format('d.m.Y') }}</td>
                    </tr>
                </table>
            </td>

            <!-- ЦЕНТРАЛНА КОЛОНА: ЗАГЛАВИЕ -->
            <td class="center-column">
                <div class="company-title">ФИРМЕНИ ДАННИ</div>
                <div class="company-subtitle">{!! $companySetting->name !!}</div>
                <div class="company-date">
                    Печат: {{ now()->format('d.m.Y H:i') }}
                </div>
            </td>

            <!-- ДЯСНА КОЛОНА: КРАТЪК КОНТАКТ -->
            <td class="right-column">
                <div class="section-title">Бърз контакт</div>
                <table class="info-table">
                    <tr>
                        <td class="label-cell">Телефон:</td>
                        <td class="value-cell">{{ $companySetting->phone ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">E-mail:</td>
                        <td class="value-cell">{{ $companySetting->email ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">Уебсайт:</td>
                        <td class="value-cell">{{ $companySetting->website ?? '—' }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- ОСНОВНА ИНФОРМАЦИЯ -->
    <table class="full-width-table no-break">
        <tr>
            <td>
                <div class="data-container">
                    <div class="data-title">Основна информация</div>
                    <table class="data-table">
                        <tr>
                            <td class="label">Име на фирма:</td>
                            <td><strong>{!! $companySetting->name !!}</strong></td>
                        </tr>
                        <tr>
                            <td class="label">Град:</td>
                            <td>{{ $companySetting->city ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Адрес:</td>
                            <td>{{ $companySetting->address ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="label">ЕИК/БУЛСТАТ:</td>
                            <td>{{ $companySetting->vat_number ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="label">ДДС номер:</td>
                            <td>@if($companySetting->vat_number) BG{{ $companySetting->vat_number }} @else — @endif</td>
                        </tr>
                        <tr>
                            <td class="label">МОЛ / Лице за контакт:</td>
                            <td>{!! $companySetting->contact_person ?? '—' !!}</td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>

    <!-- КОНТАКТИ -->
    <table class="full-width-table no-break">
        <tr>
            <td>
                <div class="data-container">
                    <div class="data-title">Контакти</div>
                    <table class="data-table">
                        <tr>
                            <td class="label">Телефон:</td>
                            <td>{{ $companySetting->phone ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="label">E-mail:</td>
                            <td>{{ $companySetting->email ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Уебсайт:</td>
                            <td>{{ $companySetting->website ?? '—' }}</td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>

    <!-- БАНКОВИ ДАННИ -->
    <table class="full-width-table no-break">
        <tr>
            <td>
                <div class="data-container">
                    <div class="data-title">Банкови данни</div>
                    @if($companySetting->iban || $companySetting->bank_name || $companySetting->bic)
                        <table class="data-table">
                            <tr>
                                <td class="label">IBAN:</td>
                                <td>{{ $companySetting->iban ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="label">Банка:</td>
                                <td>{{ $companySetting->bank_name ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="label">BIC/SWIFT:</td>
                                <td>{{ $companySetting->bic ?? '—' }}</td>
                            </tr>
                        </table>
                    @else
                        <p style="text-align: center; color: #666; padding: 2mm;">Няма въведени банкови данни.</p>
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <!-- ДОПЪЛНИТЕЛНО: ТЕКСТ В КОЛОНТИТУЛ -->
    @if($companySetting->invoice_footer)
        <table class="full-width-table no-break">
            <tr>
                <td>
                    <div class="footer-note">
                        <div class="footer-title">Текст в колонтитул на фактура</div>
                        <div style="font-size: 6.2pt;">{!! nl2br(e($companySetting->invoice_footer)) !!}</div>
                    </div>
                </td>
            </tr>
        </table>
    @endif

    <!-- СИСТЕМНА ИНФОРМАЦИЯ -->
    <table class="full-width-table no-break" style="margin-top: 3mm;">
        <tr>
            <td>
                <div style="width: 197.5mm; margin-left: 4.5mm; display: flex; justify-content: space-between; font-size: 5.8pt; color: #666;">
                    <span>Създаден: {{ $companySetting->created_at->format('d.m.Y H:i:s') }}</span>
                    <span>Обновен: {{ $companySetting->updated_at->format('d.m.Y H:i:s') }}</span>
                </div>
            </td>
        </tr>
    </table>

    <div class="footer">
        <span class="page-number"></span>
    </div>

</body>
</html>