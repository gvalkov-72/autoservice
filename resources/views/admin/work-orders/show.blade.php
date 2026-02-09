@extends('adminlte::page')

@section('title', 'Преглед на поръчка #' . $work_order->old_id)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>
            <i class="fas fa-eye text-primary mr-2"></i>
            Поръчка #{{ $work_order->old_id }}
        </h1>
        <div>
            <button onclick="printWorkOrder()" class="btn btn-success btn-sm mr-2">
                <i class="fas fa-print mr-1"></i> Принтирай
            </button>
            <a href="{{ route('admin.work-orders.pdf', $work_order) }}" target="_blank" class="btn btn-danger btn-sm">
                <i class="fas fa-file-pdf"></i> PDF
            </a>
            <a href="{{ route('admin.work-orders.edit', $work_order->id) }}" class="btn btn-warning btn-sm mr-2">
                <i class="fas fa-edit mr-1"></i> Редактирай
            </a>
            <a href="{{ route('admin.work-orders.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left mr-1"></i> Назад
            </a>
        </div>
    </div>
@stop

@section('content')
    @php
        $rate = 1.95583;
        $showBgn = now()->lte('2026-03-31');

        function toBgn($amountEur, $rate = 1.95583, $decimals = 2)
        {
            return number_format($amountEur * $rate, $decimals, ',', ' ');
        }

        function formatEur($amountEur, $decimals = 2)
        {
            return number_format($amountEur, $decimals, ',', ' ');
        }
    @endphp

    <div class="container-fluid">
        <!-- Печатен шаблон (скрит по подразбиране) -->
        <div id="print-template" style="display: none;">
            @include('admin.work-orders.print', [
                'work_order' => $work_order,
                'rate' => $rate,
                'showBgn' => $showBgn,
            ])
        </div>

        <!-- Основен преглед -->
        <div class="row">
            <div class="col-lg-8">
                <!-- Основна информация -->
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-info-circle mr-1"></i>
                            Основна информация
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <dl class="row mb-0">
                                    <dt class="col-sm-4">Клиент:</dt>
                                    <dd class="col-sm-8"><strong>{{ $work_order->client_name }}</strong></dd>

                                    <dt class="col-sm-4">Телефон:</dt>
                                    <dd class="col-sm-8">{{ $work_order->phone ?: '—' }}</dd>

                                    <dt class="col-sm-4">Дата:</dt>
                                    <dd class="col-sm-8">{{ $work_order->order_date?->format('d.m.Y') }}</dd>

                                    <dt class="col-sm-4">Създадена от:</dt>
                                    <dd class="col-sm-8">{{ $work_order->created_by ?: '—' }}</dd>
                                </dl>
                            </div>
                            <div class="col-md-6">
                                <dl class="row mb-0">
                                    <dt class="col-sm-4">Автомобил:</dt>
                                    <dd class="col-sm-8"><strong>{{ $work_order->vehicle }}</strong></dd>

                                    <dt class="col-sm-4">Рег. номер:</dt>
                                    <dd class="col-sm-8">{{ $work_order->plate_number }}</dd>

                                    <dt class="col-sm-4">VIN номер:</dt>
                                    <dd class="col-sm-8">{{ $work_order->chassis_number ?: '—' }}</dd>

                                    <dt class="col-sm-4">Пробег:</dt>
                                    <dd class="col-sm-8">
                                        {{ $work_order->mileage ? number_format($work_order->mileage, 0, ',', ' ') . ' км' : '—' }}
                                    </dd>

                                    <dt class="col-sm-4">Механик:</dt>
                                    <dd class="col-sm-8">{{ $work_order->mechanic_code ?: '—' }}</dd>
                                </dl>
                            </div>
                        </div>

                        @if ($work_order->note)
                            <div class="row mt-3">
                                <div class="col-12">
                                    <dt>Бележки:</dt>
                                    <dd class="border rounded p-2 bg-light">{{ $work_order->note }}</dd>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Артикули -->
                <div class="card card-outline card-primary mt-3">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-boxes mr-1"></i>
                            Артикули и услуги
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 40px">#</th>
                                        <th style="width: 100px">Код</th>
                                        <th>Описание</th>
                                        <th style="width: 80px" class="text-center">Мярка</th>
                                        <th style="width: 100px" class="text-center">Количество</th>
                                        <th style="width: 120px" class="text-right">Ед. цена</th>
                                        <th style="width: 120px" class="text-right">Сума</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $itemsTotal = 0;
                                    @endphp

                                    @forelse($work_order->items as $item)
                                        @php
                                            $rowTotal = $item->quantity * $item->price_each;
                                            $itemsTotal += $rowTotal;
                                        @endphp
                                        <tr>
                                            <td class="text-center">{{ $item->row_number }}</td>
                                            <td>{{ $item->item_code ?: '—' }}</td>
                                            <td>{{ $item->item_name }}</td>
                                            <td class="text-center">{{ $item->item_measure }}</td>
                                            <td class="text-center">{{ number_format($item->quantity, 2, ',', ' ') }}</td>
                                            <td class="text-right">{{ formatEur($item->price_each) }} €</td>
                                            <td class="text-right font-weight-bold">{{ formatEur($rowTotal) }} €</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-3">
                                                Няма добавени артикули
                                            </td>
                                        </tr>
                                    @endforelse

                                    <!-- Труд -->
                                    @if ($work_order->service_amount > 0)
                                        <tr class="table-info">
                                            <td colspan="5" class="text-right font-weight-bold">
                                                <i class="fas fa-hard-hat mr-1"></i> Стойност на труда:
                                            </td>
                                            <td colspan="2" class="text-right font-weight-bold">
                                                {{ formatEur($work_order->service_amount) }} €
                                                @if ($showBgn)
                                                    <br><small
                                                        class="text-muted">{{ toBgn($work_order->service_amount, $rate) }}
                                                        лв</small>
                                                @endif
                                            </td>
                                        </tr>
                                    @endif

                                    <!-- Общо -->
                                    @php
                                        $grandTotal = $itemsTotal + $work_order->service_amount;
                                    @endphp
                                    <tr class="table-success font-weight-bold">
                                        <td colspan="5" class="text-right" style="font-size: 1.1em;">
                                            ОБЩА СУМА:
                                        </td>
                                        <td colspan="2" class="text-right" style="font-size: 1.1em;">
                                            {{ formatEur($grandTotal) }} €
                                            @if ($showBgn)
                                                <br><small class="text-muted">{{ toBgn($grandTotal, $rate) }} лв</small>
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Странична панел -->
            <div class="col-lg-4">
                <!-- Финансово обобщение -->
                <div class="card card-outline card-success">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-calculator mr-1"></i>
                            Финансово обобщение
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <div class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                                <span>Артикули:</span>
                                <strong>
                                    {{ formatEur($itemsTotal) }} €
                                    @if ($showBgn)
                                        <br><small class="text-muted">{{ toBgn($itemsTotal, $rate) }} лв</small>
                                    @endif
                                </strong>
                            </div>

                            @if ($work_order->service_amount > 0)
                                <div
                                    class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                                    <span>Труд:</span>
                                    <strong>
                                        {{ formatEur($work_order->service_amount) }} €
                                        @if ($showBgn)
                                            <br><small class="text-muted">{{ toBgn($work_order->service_amount, $rate) }}
                                                лв</small>
                                        @endif
                                    </strong>
                                </div>
                            @endif

                            <div
                                class="list-group-item d-flex justify-content-between align-items-center border-0 px-0 mt-3 pt-3 border-top">
                                <h5 class="mb-0">ОБЩО:</h5>
                                <h4 class="mb-0 text-success">
                                    {{ formatEur($grandTotal) }} €
                                    @if ($showBgn)
                                        <br><small class="text-muted">{{ toBgn($grandTotal, $rate) }} лв</small>
                                    @endif
                                </h4>
                            </div>
                        </div>

                        <div class="mt-4">
                            <div class="alert alert-info">
                                <small>
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Сумите се съхраняват в <strong>евро</strong> в базата данни.
                                    @if ($showBgn)
                                        <br>Показването в лева е активна до 31.03.2026 г.
                                    @endif
                                </small>
                            </div>
                        </div>

                        <!-- Мета информация -->
                        <div class="mt-4">
                            <h5><i class="fas fa-history mr-1"></i> История</h5>
                            <dl class="row mb-0">
                                <dt class="col-sm-5">Създадена:</dt>
                                <dd class="col-sm-7">{{ $work_order->created_at->format('d.m.Y H:i') }}</dd>

                                <dt class="col-sm-5">Последна промяна:</dt>
                                <dd class="col-sm-7">{{ $work_order->updated_at->format('d.m.Y H:i') }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        /* Стилове за принтиране */
        @media print {
            body * {
                visibility: hidden;
            }

            #print-content,
            #print-content * {
                visibility: visible;
            }

            #print-content {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                background: white;
                font-size: 12pt;
                line-height: 1.4;
            }

            /* Скриване на елементи при печат */
            .no-print,
            .btn,
            .card-header,
            .content-header,
            .card-footer,
            .sidebar,
            .main-footer,
            .alert,
            .content-header,
            .breadcrumb {
                display: none !important;
            }

            /* Стилове за таблици */
            .table {
                border-collapse: collapse !important;
                width: 100% !important;
                font-size: 10pt !important;
            }

            .table-bordered {
                border: 1px solid #000 !important;
            }

            .table-bordered th,
            .table-bordered td {
                border: 1px solid #000 !important;
                padding: 5px !important;
                background: white !important;
                color: black !important;
            }

            .table thead th {
                background-color: #f8f9fa !important;
                font-weight: bold !important;
            }

            /* Предотвратяване на прекъсване */
            h1,
            h2,
            h3,
            h4,
            h5,
            h6 {
                color: black !important;
                page-break-after: avoid;
            }

            table {
                page-break-inside: avoid;
            }

            tr {
                page-break-inside: avoid;
            }

            /* Подравняване */
            .text-right {
                text-align: right !important;
            }

            .text-center {
                text-align: center !important;
            }

            /* Маржове при печат */
            @page {
                margin: 1cm;
            }

            body {
                margin: 0;
                padding: 0;
            }

            /* Увеличаване на шрифта за заглавия */
            h1.print-title {
                font-size: 18pt !important;
                margin-bottom: 10px !important;
                padding-bottom: 5px !important;
                border-bottom: 2px solid #000 !important;
            }

            h2.print-subtitle {
                font-size: 14pt !important;
                margin-top: 15px !important;
                margin-bottom: 10px !important;
            }

            /* Удобно копиране на VIN */
            .vin-box {
                background: #f8f9fa;
                border: 1px solid #dee2e6;
                padding: 5px 10px;
                font-family: monospace;
                font-size: 11pt;
                letter-spacing: 1px;
            }

            /* Информация за фирмата */
            .company-info {
                border: 2px solid #000;
                padding: 10px;
                margin-bottom: 15px;
                background: #f8f9fa;
            }

            /* Подписи */
            .signature-area {
                margin-top: 30px;
                padding-top: 20px;
                border-top: 1px dashed #000;
            }

            .signature-line {
                margin-top: 40px;
                width: 50%;
                float: left;
                text-align: center;
            }
        }

        /* Обикновени стилове (не за принт) */
        dl.row dt {
            font-weight: 600;
            color: #495057;
        }

        dl.row dd {
            color: #212529;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.05);
        }

        .table-success {
            background-color: #d4edda !important;
        }

        .table-info {
            background-color: #d1ecf1 !important;
        }
    </style>
@stop

@section('js')
    <script>
        function printWorkOrder() {
            // Създаваме нов прозорец за печат
            const printWindow = window.open('', '_blank', 'width=800,height=600');

            // Взимаме съдържанието на шаблона за печат
            const printContent = document.getElementById('print-template').innerHTML;

            // HTML структура за печата
            const printHtml = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Поръчка #{{ $work_order->old_id }} - Автосервиз</title>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <style>
                body {
                    font-family: 'Arial', sans-serif;
                    font-size: 12pt;
                    line-height: 1.4;
                    color: #000;
                    margin: 0;
                    padding: 0;
                }
                
                @page {
                    margin: 1cm;
                    size: A4 portrait;
                }
                
                .container {
                    width: 100%;
                    max-width: 210mm;
                    margin: 0 auto;
                    padding: 0;
                }
                
                .print-header {
                    text-align: center;
                    margin-bottom: 20px;
                    border-bottom: 2px solid #000;
                    padding-bottom: 10px;
                }
                
                .print-title {
                    font-size: 18pt;
                    font-weight: bold;
                    margin: 0;
                    color: #000;
                }
                
                .print-subtitle {
                    font-size: 14pt;
                    font-weight: bold;
                    margin-top: 20px;
                    margin-bottom: 10px;
                    color: #000;
                    border-bottom: 1px solid #000;
                    padding-bottom: 5px;
                }
                
                .company-info {
                    font-size: 10pt;
                    color: #666;
                    margin-bottom: 15px;
                }
                
                .table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 15px;
                    font-size: 10pt;
                }
                
                .table th {
                    background-color: #f2f2f2;
                    border: 1px solid #000;
                    padding: 6px 8px;
                    text-align: left;
                    font-weight: bold;
                }
                
                .table td {
                    border: 1px solid #000;
                    padding: 6px 8px;
                }
                
                .text-right {
                    text-align: right;
                }
                
                .text-center {
                    text-align: center;
                }
                
                .text-bold {
                    font-weight: bold;
                }
                
                .total-row {
                    background-color: #f0f8ff;
                    font-weight: bold;
                }
                
                .signature-area {
                    margin-top: 40px;
                    padding-top: 20px;
                    border-top: 1px dashed #000;
                }
                
                .signature-line {
                    width: 45%;
                    display: inline-block;
                    text-align: center;
                    margin: 0 2.5%;
                }
                
                .footer {
                    margin-top: 30px;
                    font-size: 9pt;
                    color: #666;
                    text-align: center;
                }
                
                .no-break {
                    page-break-inside: avoid;
                }
                
                .page-break {
                    page-break-before: always;
                }
                
                /* Информация за клиент и автомобил */
                .info-box {
                    border: 1px solid #000;
                    padding: 10px;
                    margin-bottom: 15px;
                    background-color: #f9f9f9;
                }
                
                .info-row {
                    margin-bottom: 8px;
                }
                
                .info-label {
                    font-weight: bold;
                    display: inline-block;
                    width: 120px;
                }
                
                /* Валути */
                .currency {
                    font-family: 'Courier New', monospace;
                }
                
                /* Вин номер с специален стил */
                .vin {
                    font-family: 'Courier New', monospace;
                    letter-spacing: 1px;
                    background: #f0f0f0;
                    padding: 2px 5px;
                    border-radius: 3px;
                }
                
                /* Бележки */
                .notes {
                    border: 1px dashed #000;
                    padding: 10px;
                    margin-top: 15px;
                    background: #ffffcc;
                }
            </style>
        </head>
        <body>
            <div class="container" id="print-content">
                ${printContent}
            </div>
            
            <script>
                window.onload = function() {
                    // Автоматично отпечатване при зареждане
                    window.print();
                    
                    // Затваряне на прозореца след печат (ако потребителят не го е затворил)
                    setTimeout(function() {
                        window.close();
                    }, 1000);
                };
            <\/script>
        </body>
        </html>
    `;

            // Записваме HTML в новия прозорец
            printWindow.document.write(printHtml);
            printWindow.document.close();

            // Фокус върху новия прозорец
            printWindow.focus();
        }

        // Автоматично принтиране при натискане на Ctrl+P
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
                e.preventDefault();
                printWorkOrder();
            }
        });
    </script>
@stop
