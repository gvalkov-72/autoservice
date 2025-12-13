@extends('adminlte::page')

@section('title', 'Преглед на поръчка')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="text-primary">
            <i class="fas fa-file-invoice mr-2"></i>Поръчка: {{ $workOrder->number }}
            <span class="badge badge-{{ $workOrder->status_color }} ml-2">{{ $workOrder->status_text }}</span>
        </h1>
        <div>
            <div class="btn-group">
                <button type="button" class="btn btn-outline-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-download mr-1"></i> Експорт
                </button>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="#" onclick="window.print();">
                        <i class="fas fa-print mr-2"></i> Принтирай
                    </a>
                    <a class="dropdown-item" href="{{ route('admin.work-orders.pdf', $workOrder) }}" target="_blank">
                        <i class="fas fa-file-pdf mr-2"></i> PDF
                    </a>
                    <a class="dropdown-item" href="#">
                        <i class="fas fa-file-excel mr-2"></i> Excel
                    </a>
                    <a class="dropdown-item" href="#">
                        <i class="fas fa-file-csv mr-2"></i> CSV
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="{{ route('admin.work-orders.edit', $workOrder) }}">
                        <i class="fas fa-edit mr-2"></i> Редактирай
                    </a>
                </div>
            </div>
            <a href="{{ route('admin.work-orders.index') }}" class="btn btn-outline-secondary ml-2">
                <i class="fas fa-arrow-left mr-1"></i> Назад
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <!-- Лява колона - Основна информация -->
        <div class="col-md-8">
            <!-- Карта с детайли на поръчката -->
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle mr-2"></i>Детайли на поръчката
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <dl class="row mb-0">
                                <dt class="col-sm-5">Номер:</dt>
                                <dd class="col-sm-7"><strong>{{ $workOrder->number }}</strong></dd>
                                
                                <dt class="col-sm-5">Статус:</dt>
                                <dd class="col-sm-7">
                                    <span class="badge badge-{{ $workOrder->status_color }}">
                                        {{ $workOrder->status_text }}
                                    </span>
                                </dd>
                                
                                <dt class="col-sm-5">Дата приемане:</dt>
                                <dd class="col-sm-7">
                                    {{ $workOrder->received_at ? $workOrder->received_at->format('d.m.Y H:i') : '-' }}
                                </dd>
                                
                                <dt class="col-sm-5">Пробег (км):</dt>
                                <dd class="col-sm-7">
                                    {{ $workOrder->km_on_receive ? number_format($workOrder->km_on_receive, 0, ',', ' ') : '-' }}
                                </dd>
                                
                                <dt class="col-sm-5">Назначен на:</dt>
                                <dd class="col-sm-7">
                                    {{ $workOrder->mechanic->name ?? 'Не е назначен' }}
                                </dd>
                            </dl>
                        </div>
                        <div class="col-md-6">
                            <dl class="row mb-0">
                                <dt class="col-sm-5">Създадена на:</dt>
                                <dd class="col-sm-7">
                                    {{ $workOrder->created_at->format('d.m.Y H:i') }}
                                </dd>
                                
                                <dt class="col-sm-5">Последна промяна:</dt>
                                <dd class="col-sm-7">
                                    {{ $workOrder->updated_at->format('d.m.Y H:i') }}
                                </dd>
                                
                                <dt class="col-sm-5">Създадена от:</dt>
                                <dd class="col-sm-7">
                                    {{ $workOrder->creator->name ?? 'Система' }}
                                </dd>
                                
                                @if($workOrder->estimated_completion)
                                <dt class="col-sm-5">Очаквана дата:</dt>
                                <dd class="col-sm-7">
                                    {{ $workOrder->estimated_completion->format('d.m.Y') }}
                                </dd>
                                @endif
                            </dl>
                        </div>
                    </div>
                    
                    @if($workOrder->notes)
                    <div class="row mt-3">
                        <div class="col-12">
                            <dt>Бележки:</dt>
                            <dd class="border rounded p-2 bg-light">
                                {{ $workOrder->notes }}
                            </dd>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Карта с клиент и автомобил -->
            <div class="card card-success card-outline mt-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user-tie mr-2"></i>Клиент и автомобил
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Информация за клиента -->
                        <div class="col-md-6">
                            <div class="info-box bg-light">
                                <span class="info-box-icon bg-primary"><i class="fas fa-user"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Клиент</span>
                                    <span class="info-box-number">{{ $workOrder->customer->name }}</span>
                                    <div class="progress">
                                        <div class="progress-bar bg-primary" style="width: 100%"></div>
                                    </div>
                                    <span class="progress-description">
                                        @if($workOrder->customer->phone)
                                            <i class="fas fa-phone mr-1"></i>{{ $workOrder->customer->phone }}
                                        @endif
                                        @if($workOrder->customer->email)
                                            <br><i class="fas fa-envelope mr-1"></i>{{ $workOrder->customer->email }}
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Информация за автомобила -->
                        <div class="col-md-6">
                            <div class="info-box bg-light">
                                <span class="info-box-icon bg-success"><i class="fas fa-car"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Автомобил</span>
                                    <span class="info-box-number">{{ $workOrder->vehicle->plate ?? 'Няма автомобил' }}</span>
                                    <div class="progress">
                                        <div class="progress-bar bg-success" style="width: 100%"></div>
                                    </div>
                                    <span class="progress-description">
                                        @if($workOrder->vehicle)
                                            {{ $workOrder->vehicle->make }} {{ $workOrder->vehicle->model }}
                                            @if($workOrder->vehicle->year)
                                                ({{ $workOrder->vehicle->year }})
                                            @endif
                                            @if($workOrder->vehicle->mileage)
                                                <br><i class="fas fa-tachometer-alt mr-1"></i>{{ number_format($workOrder->vehicle->mileage, 0, ',', ' ') }} км
                                            @endif
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Карта с позиции в поръчката -->
            <div class="card card-info card-outline mt-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-list-alt mr-2"></i>Позиции в поръчката
                        <span class="badge badge-light ml-2">{{ $workOrder->items->count() }} позиции</span>
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th width="5%">№</th>
                                    <th width="35%">Описание</th>
                                    <th width="15%" class="text-center">Кол-во</th>
                                    <th width="15%" class="text-right">Цена без ДДС</th>
                                    <th width="10%" class="text-center">ДДС %</th>
                                    <th width="20%" class="text-right">Общо</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($workOrder->items as $index => $item)
                                <tr>
                                    <td class="align-middle">{{ $index + 1 }}</td>
                                    <td class="align-middle">
                                        <div>
                                            <strong>{{ $item->description }}</strong>
                                            @if($item->product)
                                                <br>
                                                <small class="text-muted">
                                                    <i class="fas fa-box mr-1"></i>
                                                    {{ $item->product->sku }} - {{ $item->product->name }}
                                                </small>
                                            @elseif($item->service)
                                                <br>
                                                <small class="text-muted">
                                                    <i class="fas fa-tools mr-1"></i>
                                                    {{ $item->service->code }} - {{ $item->service->name }}
                                                </small>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="align-middle text-center">
                                        {{ number_format($item->quantity, 2, ',', ' ') }}
                                    </td>
                                    <td class="align-middle text-right">
                                        {{ number_format($item->unit_price, 2, ',', ' ') }} лв.
                                    </td>
                                    <td class="align-middle text-center">
                                        {{ number_format($item->vat_percent, 2, ',', ' ') }}%
                                    </td>
                                    <td class="align-middle text-right">
                                        <strong>{{ number_format($item->line_total, 2, ',', ' ') }} лв.</strong>
                                        <br>
                                        <small class="text-muted">
                                            Без ДДС: {{ number_format($item->line_total_without_vat, 2, ',', ' ') }} лв.
                                        </small>
                                    </td>
                                </tr>
                                @endforeach
                                
                                @if($workOrder->items->isEmpty())
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-3">
                                        <i class="fas fa-info-circle mr-2"></i>Няма добавени позиции
                                    </td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Дясна колона - Суми и действия -->
        <div class="col-md-4">
            <!-- Карта с общи суми -->
            <div class="card card-warning card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-calculator mr-2"></i>Общи суми
                    </h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless mb-0">
                            <tr>
                                <th class="text-right">Общо без ДДС:</th>
                                <td class="text-right">
                                    <span class="font-weight-bold text-primary">
                                        {{ number_format($workOrder->total_without_vat, 2, ',', ' ') }}
                                    </span> лв.
                                </td>
                            </tr>
                            <tr>
                                <th class="text-right">ДДС:</th>
                                <td class="text-right">
                                    <span class="font-weight-bold text-warning">
                                        {{ number_format($workOrder->vat_amount, 2, ',', ' ') }}
                                    </span> лв.
                                </td>
                            </tr>
                            <tr class="border-top">
                                <th class="text-right font-weight-bold">Общо с ДДС:</th>
                                <td class="text-right">
                                    <span class="h4 font-weight-bold text-success">
                                        {{ number_format($workOrder->total, 2, ',', ' ') }}
                                    </span> лв.
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Карта с бързи действия -->
            <div class="card card-default mt-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-bolt mr-2"></i>Бързи действия
                    </h3>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.work-orders.edit', $workOrder) }}" class="btn btn-primary btn-block">
                            <i class="fas fa-edit mr-2"></i> Редактирай поръчка
                        </a>
                        
                        <a href="{{ route('admin.work-orders.pdf', $workOrder) }}" target="_blank" class="btn btn-danger btn-block">
                            <i class="fas fa-file-pdf mr-2"></i> Генерирай PDF
                        </a>
                        
                        <button type="button" class="btn btn-success btn-block" onclick="window.print();">
                            <i class="fas fa-print mr-2"></i> Принтирай
                        </button>
                        
                        @if($workOrder->status !== 'completed' && $workOrder->status !== 'cancelled')
                        <form action="{{ route('admin.work-orders.update', $workOrder) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="completed">
                            <button type="submit" class="btn btn-success btn-block" onclick="return confirm('Маркиране на поръчката като завършена?')">
                                <i class="fas fa-check mr-2"></i> Маркирай като завършена
                            </button>
                        </form>
                        @endif
                        
                        @if($workOrder->status !== 'cancelled')
                        <form action="{{ route('admin.work-orders.update', $workOrder) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="cancelled">
                            <button type="submit" class="btn btn-danger btn-block" onclick="return confirm('Отмяна на поръчката?')">
                                <i class="fas fa-times mr-2"></i> Отмени поръчката
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Карта с история -->
            <div class="card card-secondary card-outline mt-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-history mr-2"></i>История
                    </h3>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <i class="fas fa-plus-circle text-success mr-2"></i>
                            Създадена на
                            <span class="float-right text-muted">
                                {{ $workOrder->created_at->format('d.m.Y H:i') }}
                            </span>
                        </li>
                        <li class="list-group-item">
                            <i class="fas fa-edit text-primary mr-2"></i>
                            Последна промяна
                            <span class="float-right text-muted">
                                {{ $workOrder->updated_at->format('d.m.Y H:i') }}
                            </span>
                        </li>
                        @if($workOrder->invoices->count() > 0)
                        <li class="list-group-item">
                            <i class="fas fa-file-invoice-dollar text-warning mr-2"></i>
                            Фактурирана
                            <span class="float-right text-muted">
                                {{ $workOrder->invoices->first()->created_at->format('d.m.Y') }}
                            </span>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
@stop

@push('css')
    <style>
        /* Стилове за принтиране */
        @media print {
            .no-print, .card-tools, .btn, .dropdown, .content-header {
                display: none !important;
            }
            
            .card {
                border: 1px solid #ddd !important;
                box-shadow: none !important;
            }
            
            .card-header {
                background-color: #f8f9fa !important;
                color: #000 !important;
                border-bottom: 1px solid #ddd !important;
            }
            
            body {
                background-color: #fff !important;
                color: #000 !important;
            }
            
            .container-fluid {
                width: 100% !important;
                max-width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            
            .row {
                margin: 0 !important;
            }
            
            .col-md-8, .col-md-4 {
                width: 100% !important;
                max-width: 100% !important;
                flex: 0 0 100% !important;
            }
            
            .table {
                border-collapse: collapse !important;
                width: 100% !important;
            }
            
            .table th, .table td {
                border: 1px solid #ddd !important;
                padding: 8px !important;
            }
        }
        
        /* Стилове за статуси */
        .badge-draft { background-color: #6c757d; }
        .badge-open { background-color: #007bff; }
        .badge-in_progress { background-color: #17a2b8; }
        .badge-completed { background-color: #28a745; }
        .badge-invoiced { background-color: #6610f2; }
        .badge-closed { background-color: #343a40; }
        .badge-cancelled { background-color: #dc3545; }
        
        /* Подобрения на таблицата */
        .table-hover tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.05);
        }
        
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, 0.02);
        }
        
        /* Стилове за info-box */
        .info-box {
            box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
            border-radius: .25rem;
            background-color: #fff;
            display: flex;
            margin-bottom: 1rem;
            min-height: 80px;
            padding: .5rem;
            position: relative;
        }
        
        .info-box .info-box-icon {
            border-radius: .25rem;
            align-items: center;
            display: flex;
            font-size: 1.875rem;
            justify-content: center;
            text-align: center;
            width: 70px;
        }
        
        .info-box .info-box-content {
            display: flex;
            flex-direction: column;
            justify-content: center;
            line-height: 1.8;
            flex: 1;
            padding: 0 10px;
        }
        
        .info-box .info-box-number {
            font-size: 1.5rem;
            font-weight: 700;
        }
        
        /* Стилове за бутони */
        .btn-block {
            margin-bottom: 0.5rem;
        }
        
        .dropdown-menu {
            min-width: 180px;
        }
    </style>
@endpush

@push('js')
    <script>
        $(function () {
            // Инициализация на картените инструменти
            $('[data-card-widget="collapse"]').click(function() {
                $(this).closest('.card').toggleClass('collapsed-card');
            });
            
            // Добавяне на функционалност за Excel и CSV експорт
            $('.dropdown-item').on('click', function(e) {
                const text = $(this).text().trim();
                
                if (text.includes('Excel')) {
                    e.preventDefault();
                    exportToExcel();
                } else if (text.includes('CSV')) {
                    e.preventDefault();
                    exportToCSV();
                }
            });
            
            function exportToExcel() {
                // Тук може да се добави логика за генериране на Excel файл
                alert('Експорт към Excel ще бъде имплементиран скоро.');
                // Пример: window.location.href = "{{ route('admin.work-orders.export', ['id' => $workOrder->id, 'type' => 'excel']) }}";
            }
            
            function exportToCSV() {
                // Тук може да се добави логика за генериране на CSV файл
                alert('Експорт към CSV ще бъде имплементиран скоро.');
                // Пример: window.location.href = "{{ route('admin.work-orders.export', ['id' => $workOrder->id, 'type' => 'csv']) }}";
            }
            
            // Автоматично скриване на dropdown след избор
            $('.dropdown-menu').on('click', function(e) {
                e.stopPropagation();
            });
        });
    </script>
@endpush