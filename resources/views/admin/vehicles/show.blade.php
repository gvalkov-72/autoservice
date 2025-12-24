@extends('adminlte::page')

@section('title', 'Преглед на превозно средство - ' . $vehicle->plate)

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1>
        <i class="fas fa-car text-primary mr-2"></i>Преглед на превозно средство
    </h1>
    <div class="btn-group">
        <a href="{{ route('admin.vehicles.edit', $vehicle) }}" class="btn btn-primary">
            <i class="fas fa-edit mr-1"></i>Редактирай
        </a>
        <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-file-export mr-1"></i>Експорт
        </button>
        <div class="dropdown-menu dropdown-menu-right">
            <a class="dropdown-item" href="{{ route('admin.vehicles.export.pdf', $vehicle) }}" target="_blank">
                <i class="fas fa-file-pdf text-danger mr-2"></i>PDF
            </a>
            <a class="dropdown-item" href="{{ route('admin.vehicles.export.excel', $vehicle) }}">
                <i class="fas fa-file-excel text-success mr-2"></i>Excel
            </a>
            <a class="dropdown-item" href="{{ route('admin.vehicles.export.csv', $vehicle) }}">
                <i class="fas fa-file-csv text-info mr-2"></i>CSV
            </a>
        </div>
        <a href="{{ route('admin.vehicles.index') }}" class="btn btn-secondary ml-2">
            <i class="fas fa-arrow-left mr-1"></i>Назад
        </a>
    </div>
</div>
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <!-- Заглавие и статус -->
        <div class="card card-primary card-outline mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <h2 class="mb-1">{{ $vehicle->make }} {{ $vehicle->model }}</h2>
                        <h3 class="text-muted mb-0" style="font-size: 1.8rem;">{{ $vehicle->plate }}</h3>
                        @if($vehicle->vin)
                        <p class="mb-0">
                            <small class="text-muted">
                                <i class="fas fa-barcode fa-xs mr-1"></i>VIN: {{ $vehicle->vin }}
                            </small>
                        </p>
                        @endif
                    </div>
                    <div class="col-md-4 text-right">
                        @if($vehicle->is_active)
                        <span class="badge badge-success p-2" style="font-size: 1rem;">
                            <i class="fas fa-check mr-1"></i>Активно
                        </span>
                        @else
                        <span class="badge badge-secondary p-2" style="font-size: 1rem;">
                            <i class="fas fa-times mr-1"></i>Неактивно
                        </span>
                        @endif
                        @if($vehicle->import_batch)
                        <p class="mt-2 mb-0">
                            <small class="text-muted">
                                <i class="fas fa-database fa-xs mr-1"></i>Импортна партида: {{ $vehicle->import_batch }}
                            </small>
                        </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Лява колона - Информация за превозното средство -->
            <div class="col-md-8">
                <!-- Основна информация -->
                <div class="card card-info card-outline mb-4">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-info-circle mr-2"></i>Основна информация
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-sm">
                                    <tr>
                                        <th style="width: 40%;">Рег. номер:</th>
                                        <td><strong>{{ $vehicle->plate }}</strong></td>
                                    </tr>
                                    <tr>
                                        <th>ДК номер:</th>
                                        <td>{{ $vehicle->dk_no ?: 'Няма' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Година:</th>
                                        <td>
                                            {{ $vehicle->year ?: 'Няма' }}
                                            @if($vehicle->year)
                                            <span class="text-muted ml-2">({{ $vehicleStats['age_years'] ?? '' }} години)</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Пробег:</th>
                                        <td>
                                            @if($vehicle->mileage)
                                            {{ number_format($vehicle->mileage, 0) }} км
                                            @else
                                            Няма
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-sm">
                                    <tr>
                                        <th style="width: 40%;">Марка:</th>
                                        <td>{{ $vehicle->make }}</td>
                                    </tr>
                                    <tr>
                                        <th>Модел:</th>
                                        <td>{{ $vehicle->model }}</td>
                                    </tr>
                                    <tr>
                                        <th>VIN номер:</th>
                                        <td>{{ $vehicle->vin ?: 'Няма' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Шаси номер:</th>
                                        <td>{{ $vehicle->chassis ?: 'Няма' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <table class="table table-sm">
                                    <tr>
                                        <th style="width: 20%;">Код на монитора:</th>
                                        <td>{{ $vehicle->monitor_code ?: 'Няма' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Бележки:</th>
                                        <td>
                                            @if($vehicle->notes)
                                            <div class="bg-light p-2 rounded">
                                                {{ $vehicle->notes }}
                                            </div>
                                            @else
                                            Няма
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Метаданни от старата система -->
                @if($vehicle->old_system_id || $vehicle->order_reference || $vehicle->author)
                <div class="card card-warning card-outline mb-4">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-database mr-2"></i>Метаданни от стара система
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @if($vehicle->old_system_id)
                            <div class="col-md-6">
                                <table class="table table-sm">
                                    <tr>
                                        <th style="width: 40%;">Старо ID:</th>
                                        <td>{{ $vehicle->old_system_id }}</td>
                                    </tr>
                                </table>
                            </div>
                            @endif
                            @if($vehicle->order_reference)
                            <div class="col-md-6">
                                <table class="table table-sm">
                                    <tr>
                                        <th style="width: 40%;">Поръчка от Access:</th>
                                        <td>{{ $vehicle->order_reference }}</td>
                                    </tr>
                                </table>
                            </div>
                            @endif
                        </div>
                        <div class="row">
                            @if($vehicle->po_date)
                            <div class="col-md-6">
                                <table class="table table-sm">
                                    <tr>
                                        <th style="width: 40%;">Дата на поръчка:</th>
                                        <td>{{ $vehicle->po_date->format('d.m.Y') }}</td>
                                    </tr>
                                </table>
                            </div>
                            @endif
                            @if($vehicle->author)
                            <div class="col-md-6">
                                <table class="table table-sm">
                                    <tr>
                                        <th style="width: 40%;">Автор:</th>
                                        <td>{{ $vehicle->author }}</td>
                                    </tr>
                                </table>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                <!-- Работни поръчки (ако има) -->
                @if($vehicleStats['total_work_orders'] > 0)
                <div class="card card-success card-outline mb-4">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-tools mr-2"></i>Работни поръчки
                            <span class="badge badge-light ml-2">{{ $vehicleStats['total_work_orders'] }}</span>
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Номер</th>
                                        <th>Получена на</th>
                                        <th>Статус</th>
                                        <th class="text-right">Сума</th>
                                        <th class="text-center">Действия</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($vehicle->workOrders as $workOrder)
                                    <tr>
                                        <td>
                                            <strong>{{ $workOrder->number }}</strong>
                                        </td>
                                        <td>
                                            {{ $workOrder->received_at->format('d.m.Y') }}
                                        </td>
                                        <td>
                                            @php
                                            $statusColors = [
                                                'received' => 'warning',
                                                'in_progress' => 'info',
                                                'completed' => 'success',
                                                'delivered' => 'primary',
                                                'cancelled' => 'danger'
                                            ];
                                            $statusLabels = [
                                                'received' => 'Получена',
                                                'in_progress' => 'В работа',
                                                'completed' => 'Завършена',
                                                'delivered' => 'Предадена',
                                                'cancelled' => 'Отказана'
                                            ];
                                            @endphp
                                            <span class="badge badge-{{ $statusColors[$workOrder->status] ?? 'secondary' }}">
                                                {{ $statusLabels[$workOrder->status] ?? $workOrder->status }}
                                            </span>
                                        </td>
                                        <td class="text-right">
                                            <strong>{{ number_format($workOrder->total, 2) }} лв.</strong>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('admin.work-orders.show', $workOrder) }}" 
                                               class="btn btn-sm btn-info" title="Преглед">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="bg-light">
                                        <td colspan="3" class="text-right"><strong>Общо:</strong></td>
                                        <td class="text-right"><strong>{{ number_format($vehicleStats['total_spent'], 2) }} лв.</strong></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    @if($vehicle->workOrders->count() < $vehicleStats['total_work_orders'])
                    <div class="card-footer text-center">
                        <a href="{{ route('admin.work-orders.index', ['vehicle_id' => $vehicle->id]) }}" 
                           class="btn btn-sm btn-outline-success">
                            <i class="fas fa-list mr-1"></i>Виж всички работни поръчки ({{ $vehicleStats['total_work_orders'] }})
                        </a>
                    </div>
                    @endif
                </div>
                @else
                <div class="card card-outline mb-4">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-tools fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Няма работни поръчки</h5>
                        <p class="text-muted">Все още няма регистрирани работни поръчки за това превозно средство.</p>
                        <a href="{{ route('admin.work-orders.create') }}?vehicle_id={{ $vehicle->id }}" 
                           class="btn btn-success">
                            <i class="fas fa-plus mr-1"></i>Създай работна поръчка
                        </a>
                    </div>
                </div>
                @endif
            </div>

            <!-- Дясна колона - Клиент и статистики -->
            <div class="col-md-4">
                <!-- Информация за клиента -->
                <div class="card card-primary card-outline mb-4">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-user mr-2"></i>Клиент
                        </h3>
                    </div>
                    <div class="card-body">
                        @if($vehicle->customer)
                        <div class="text-center mb-3">
                            <div class="user-avatar mb-3">
                                <i class="fas fa-user-circle fa-4x text-primary"></i>
                            </div>
                            <h4>{{ $vehicle->customer->name }}</h4>
                        </div>
                        <table class="table table-sm">
                            @if($vehicle->customer->phone)
                            <tr>
                                <th style="width: 40%;"><i class="fas fa-phone mr-1"></i>Телефон:</th>
                                <td>
                                    <a href="tel:{{ $vehicle->customer->phone }}" class="text-dark">
                                        {{ $vehicle->customer->phone }}
                                    </a>
                                </td>
                            </tr>
                            @endif
                            @if($vehicle->customer->email)
                            <tr>
                                <th><i class="fas fa-envelope mr-1"></i>Имейл:</th>
                                <td>
                                    <a href="mailto:{{ $vehicle->customer->email }}" class="text-dark">
                                        {{ $vehicle->customer->email }}
                                    </a>
                                </td>
                            </tr>
                            @endif
                            @if($vehicle->customer->address)
                            <tr>
                                <th><i class="fas fa-map-marker-alt mr-1"></i>Адрес:</th>
                                <td>{{ $vehicle->customer->address }}</td>
                            </tr>
                            @endif
                            @if($vehicle->customer->notes)
                            <tr>
                                <th><i class="fas fa-sticky-note mr-1"></i>Бележки:</th>
                                <td>{{ Str::limit($vehicle->customer->notes, 100) }}</td>
                            </tr>
                            @endif
                        </table>
                        <div class="text-center mt-3">
                            <a href="{{ route('admin.customers.show', $vehicle->customer) }}" 
                               class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-external-link-alt mr-1"></i>Виж детайли
                            </a>
                        </div>
                        @else
                        <div class="text-center py-3">
                            <i class="fas fa-user-slash fa-2x text-muted mb-3"></i>
                            <h5 class="text-muted">Няма клиент</h5>
                            <p class="text-muted">Това превозно средство не е свързано с клиент.</p>
                            <a href="{{ route('admin.vehicles.edit', $vehicle) }}" 
                               class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-link mr-1"></i>Свържи с клиент
                            </a>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Статистики -->
                <div class="card card-success card-outline mb-4">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-bar mr-2"></i>Статистики
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="stats-list">
                            <div class="stat-item d-flex justify-content-between mb-3">
                                <span>
                                    <i class="fas fa-tools text-info mr-2"></i>Работни поръчки:
                                </span>
                                <span class="font-weight-bold">{{ $vehicleStats['total_work_orders'] }}</span>
                            </div>
                            <div class="stat-item d-flex justify-content-between mb-3">
                                <span>
                                    <i class="fas fa-money-bill-wave text-success mr-2"></i>Общо похарчени:
                                </span>
                                <span class="font-weight-bold">{{ number_format($vehicleStats['total_spent'], 2) }} лв.</span>
                            </div>
                            <div class="stat-item d-flex justify-content-between mb-3">
                                <span>
                                    <i class="fas fa-calendar-alt text-warning mr-2"></i>Възраст:
                                </span>
                                <span class="font-weight-bold">
                                    @if($vehicleStats['age_years'])
                                    {{ $vehicleStats['age_years'] }} години
                                    @else
                                    Няма
                                    @endif
                                </span>
                            </div>
                            <div class="stat-item d-flex justify-content-between mb-3">
                                <span>
                                    <i class="fas fa-history text-primary mr-2"></i>Последна услуга:
                                </span>
                                <span class="font-weight-bold">
                                    @if($vehicleStats['last_service'])
                                    {{ $vehicleStats['last_service']->format('d.m.Y') }}
                                    @else
                                    Няма
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Системна информация -->
                <div class="card card-secondary card-outline mb-4">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-cogs mr-2"></i>Системна информация
                        </h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr>
                                <th style="width: 50%;">Създаден на:</th>
                                <td>{{ $vehicle->created_at->format('d.m.Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Актуализиран на:</th>
                                <td>{{ $vehicle->updated_at->format('d.m.Y H:i') }}</td>
                            </tr>
                            @if($vehicle->deleted_at)
                            <tr>
                                <th>Изтрит на:</th>
                                <td>{{ $vehicle->deleted_at->format('d.m.Y H:i') }}</td>
                            </tr>
                            @endif
                            @if($vehicle->import_batch)
                            <tr>
                                <th>Импортна партида:</th>
                                <td>{{ $vehicle->import_batch }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>

                <!-- Бързи действия -->
                <div class="card card-outline mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-3">
                            <i class="fas fa-bolt text-warning mr-2"></i>Бързи действия
                        </h5>
                        <div class="row">
                            <div class="col-6 mb-2">
                                <a href="{{ route('admin.work-orders.create') }}?vehicle_id={{ $vehicle->id }}" 
                                   class="btn btn-success btn-block btn-sm">
                                    <i class="fas fa-plus mr-1"></i>Нова поръчка
                                </a>
                            </div>
                            <div class="col-6 mb-2">
                                <a href="{{ route('admin.vehicles.edit', $vehicle) }}" 
                                   class="btn btn-primary btn-block btn-sm">
                                    <i class="fas fa-edit mr-1"></i>Редактирай
                                </a>
                            </div>
                            <div class="col-6 mb-2">
                                <button type="button" class="btn btn-info btn-block btn-sm" data-toggle="modal" data-target="#printQrModal">
                                    <i class="fas fa-qrcode mr-1"></i>QR код
                                </button>
                            </div>
                            <div class="col-6 mb-2">
                                <form action="{{ route('admin.vehicles.destroy', $vehicle) }}" method="POST" 
                                      onsubmit="return confirm('Сигурни ли сте, че искате да деактивирате това превозно средство?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-block btn-sm">
                                        <i class="fas fa-trash mr-1"></i>Деактивирай
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Модален прозорец за QR код -->
<div class="modal fade" id="printQrModal" tabindex="-1" role="dialog" aria-labelledby="printQrModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="printQrModalLabel">QR код за превозно средство</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <div id="qrcode" class="mb-3" style="display: inline-block;"></div>
                <p class="text-muted">
                    <small>
                        Сканирайте този QR код, за да отворите бързо детайлите за това превозно средство.<br>
                        URL: {{ route('admin.vehicles.show', $vehicle) }}
                    </small>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Затвори</button>
                <button type="button" class="btn btn-primary" onclick="printQrCode()">
                    <i class="fas fa-print mr-1"></i>Принтирай
                </button>
            </div>
        </div>
    </div>
</div>
@stop

@push('css')
<style>
.card.card-primary.card-outline {
    border-top: 3px solid #007bff;
}

.card.card-info.card-outline {
    border-top: 3px solid #17a2b8;
}

.card.card-success.card-outline {
    border-top: 3px solid #28a745;
}

.card.card-warning.card-outline {
    border-top: 3px solid #ffc107;
}

.card.card-secondary.card-outline {
    border-top: 3px solid #6c757d;
}

.stat-item {
    border-bottom: 1px solid #eee;
    padding-bottom: 10px;
}

.stat-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.user-avatar {
    width: 80px;
    height: 80px;
    margin: 0 auto;
    border-radius: 50%;
    background-color: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
}

.table th {
    font-weight: 600;
    color: #495057;
}

.table-sm td, .table-sm th {
    padding: 0.5rem;
}

.badge {
    font-size: 0.8em;
    padding: 0.4em 0.8em;
}
</style>
@endpush

@push('js')
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
$(document).ready(function() {
    // Генериране на QR код при отваряне на модалния прозорец
    $('#printQrModal').on('shown.bs.modal', function () {
        // Изчистваме предишния QR код
        $('#qrcode').empty();
        
        // Генерираме нов QR код
        const qrUrl = '{{ route("admin.vehicles.show", $vehicle) }}';
        new QRCode(document.getElementById("qrcode"), {
            text: qrUrl,
            width: 200,
            height: 200,
            colorDark : "#000000",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });
    });

    // Изчистваме QR кода при затваряне на модалния прозорец
    $('#printQrModal').on('hidden.bs.modal', function () {
        $('#qrcode').empty();
    });
});

// Функция за принтиране на QR кода
function printQrCode() {
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>QR код - {{ $vehicle->plate }}</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; text-align: center; }
                .qr-container { margin: 20px auto; }
                .vehicle-info { margin: 20px 0; }
                .vehicle-info h3 { margin: 10px 0; color: #333; }
                .vehicle-info p { color: #666; }
                @media print {
                    body { margin: 0; }
                    .no-print { display: none; }
                }
            </style>
        </head>
        <body>
            <div class="vehicle-info">
                <h3>{{ $vehicle->make }} {{ $vehicle->model }}</h3>
                <p>Рег. номер: <strong>{{ $vehicle->plate }}</strong></p>
                <p>Година: {{ $vehicle->year ?: 'Няма' }}</p>
                <p>VIN: {{ $vehicle->vin ?: 'Няма' }}</p>
            </div>
            <div class="qr-container">
                <div id="qrcode-print"></div>
            </div>
            <div class="no-print" style="margin-top: 20px;">
                <button onclick="window.print()" style="padding: 10px 20px; background: #007bff; color: white; border: none; cursor: pointer;">
                    Принтирай
                </button>
                <button onclick="window.close()" style="padding: 10px 20px; background: #6c757d; color: white; border: none; cursor: pointer; margin-left: 10px;">
                    Затвори
                </button>
            </div>
            <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"><\/script>
            <script>
                new QRCode(document.getElementById("qrcode-print"), {
                    text: "{{ route('admin.vehicles.show', $vehicle) }}",
                    width: 300,
                    height: 300,
                    colorDark : "#000000",
                    colorLight : "#ffffff",
                    correctLevel : QRCode.CorrectLevel.H
                });
            <\/script>
        </body>
        </html>
    `);
    printWindow.document.close();
}
</script>
@endpush