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
                    <a class="dropdown-item" href="{{ route('admin.work-orders.export', ['workOrder' => $workOrder->id, 'type' => 'excel']) }}">
                        <i class="fas fa-file-excel mr-2"></i> Excel
                    </a>
                    <a class="dropdown-item" href="{{ route('admin.work-orders.export', ['workOrder' => $workOrder->id, 'type' => 'csv']) }}">
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
                            <tfoot class="thead-light">
                                <tr>
                                    <th colspan="3" class="text-right">Общо:</th>
                                    <th class="text-right">
                                        {{ number_format($workOrder->total_without_vat, 2, ',', ' ') }} лв.
                                    </th>
                                    <th class="text-center"></th>
                                    <th class="text-right">
                                        <strong>{{ number_format($workOrder->total, 2, ',', ' ') }} лв.</strong>
                                        <br>
                                        <small class="text-muted">
                                            ДДС: {{ number_format($workOrder->vat_amount, 2, ',', ' ') }} лв.
                                        </small>
                                    </th>
                                </tr>
                            </tfoot>
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
                    <div class="row">
                        <div class="col-12">
                            <div class="info-box bg-light mb-3">
                                <span class="info-box-icon bg-warning"><i class="fas fa-file-invoice-dollar"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Обща стойност</span>
                                    <span class="info-box-number">{{ number_format($workOrder->total, 2, ',', ' ') }} лв.</span>
                                    <div class="progress">
                                        <div class="progress-bar bg-warning" style="width: 100%"></div>
                                    </div>
                                    <span class="progress-description">
                                        Без ДДС: {{ number_format($workOrder->total_without_vat, 2, ',', ' ') }} лв.
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-6">
                            <div class="small-box bg-light p-2 border rounded mb-2">
                                <div class="inner">
                                    <h4>{{ number_format($workOrder->vat_amount, 2, ',', ' ') }} <small>лв.</small></h4>
                                    <p>ДДС</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-percentage"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="small-box bg-light p-2 border rounded mb-2">
                                <div class="inner">
                                    <h4>{{ $workOrder->items->count() }}</h4>
                                    <p>Позиции</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-list"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Карта с действия -->
            <div class="card card-danger card-outline mt-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-cogs mr-2"></i>Действия
                    </h3>
                </div>
                <div class="card-body">
                    <div class="btn-group-vertical w-100" role="group">
                        <a href="{{ route('admin.work-orders.edit', $workOrder) }}" class="btn btn-outline-primary btn-lg text-left mb-2">
                            <i class="fas fa-edit mr-2"></i> Редактирай поръчка
                        </a>
                        
                        <a href="{{ route('admin.work-orders.pdf', $workOrder) }}" target="_blank" class="btn btn-outline-info btn-lg text-left mb-2">
                            <i class="fas fa-file-pdf mr-2"></i> Генерирай PDF
                        </a>
                        
                        @if(!$workOrder->invoices()->exists())
                        <a href="{{ route('admin.invoices.create', ['work_order' => $workOrder->id]) }}" class="btn btn-outline-success btn-lg text-left mb-2">
                            <i class="fas fa-file-invoice mr-2"></i> Създай фактура
                        </a>
                        @else
                        <a href="{{ route('admin.invoices.show', $workOrder->invoices()->first()) }}" class="btn btn-outline-secondary btn-lg text-left mb-2">
                            <i class="fas fa-eye mr-2"></i> Преглед на фактура
                        </a>
                        @endif
                        
                        <form action="{{ route('admin.work-orders.destroy', $workOrder) }}" method="POST" class="w-100 mb-2" onsubmit="return confirm('Сигурни ли сте, че искате да изтриете тази поръчка?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-lg w-100 text-left">
                                <i class="fas fa-trash mr-2"></i> Изтрий поръчка
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .info-box {
        min-height: 90px;
    }
    .info-box-icon {
        width: 70px;
    }
    .small-box {
        min-height: 120px;
    }
</style>
@stop
@section('js')
<script>
    $(document).ready(function() {
        // Инициализиране на tooltips
        $('[data-toggle="tooltip"]').tooltip();
        
        // Инициализиране на popovers
        $('[data-toggle="popover"]').popover();
        
        // Автоматично скриване на алерти след 5 секунди
        setTimeout(function() {
            $('.alert:not(.alert-permanent)').fadeOut('slow');
        }, 5000);
        
        // Подсказка за експорт бутоните
        $('.export-btn').tooltip({
            title: 'Кликнете за изтегляне',
            placement: 'top'
        });
        
        // Подтверждение за изтриване на поръчка
        $('.delete-btn').on('click', function(e) {
            if (!confirm('Сигурни ли сте, че искате да изтриете тази поръчка? Това действие е необратимо.')) {
                e.preventDefault();
            }
        });
        
        // Функция за принтиране на поръчката
        window.printWorkOrder = function() {
            window.print();
        };
        
        // Функция за експорт в PDF
        window.exportToPDF = function() {
            window.location.href = "{{ route('admin.work-orders.pdf', $workOrder) }}";
        };
        
        // Функция за експорт в Excel
        function exportToExcel() {
            window.location.href = "{{ route('admin.work-orders.export', ['workOrder' => $workOrder->id, 'type' => 'excel']) }}";
        }
        
        // Функция за експорт в CSV
        function exportToCSV() {
            window.location.href = "{{ route('admin.work-orders.export', ['workOrder' => $workOrder->id, 'type' => 'csv']) }}";
        }
        
        // Автоматично скриване на dropdown след избор
        $('.dropdown-menu').on('click', function(e) {
            e.stopPropagation();
        });
        
        // Анимация при кликване на бутони
        $('.btn').on('click', function() {
            $(this).blur();
        });
        
        // Проверка за грешки в формата
        @if($errors->any())
            $('#errorModal').modal('show');
        @endif
        
        // Инициализиране на DataTables за таблицата с позициите (ако е необходимо)
        @if($workOrder->items->count() > 10)
            $('.table').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Bulgarian.json"
                }
            });
        @endif
    });
    
    // Функция за копиране на номера на поръчката
    function copyOrderNumber() {
        const orderNumber = "{{ $workOrder->number }}";
        navigator.clipboard.writeText(orderNumber).then(function() {
            // Показване на съобщение за успех
            toastr.success('Номерът на поръчката е копиран в клипборда: ' + orderNumber);
        }, function(err) {
            // Fallback за стари браузъри
            const textArea = document.createElement('textarea');
            textArea.value = orderNumber;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
            toastr.success('Номерът на поръчката е копиран в клипборда: ' + orderNumber);
        });
    }
    
    // Функция за споделяне на поръчката
    function shareWorkOrder() {
        const url = window.location.href;
        if (navigator.share) {
            navigator.share({
                title: 'Ремонтна поръчка #{{ $workOrder->number }}',
                text: 'Преглед на ремонтна поръчка #{{ $workOrder->number }} от {{ $workOrder->customer->name }}',
                url: url
            }).then(() => {
                toastr.success('Поръчката е споделена успешно!');
            }).catch(error => {
                console.log('Грешка при споделяне:', error);
                copyToClipboard(url);
            });
        } else {
            copyToClipboard(url);
        }
    }
    
    // Помощна функция за копиране в клипборд
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function() {
            toastr.success('Линкът е копиран в клипборда!');
        }, function(err) {
            const textArea = document.createElement('textarea');
            textArea.value = text;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
            toastr.success('Линкът е копиран в клипборда!');
        });
    }
    
    // Функция за показване на QR код
    function showQRCode() {
        const qrCodeUrl = "{{ route('admin.work-orders.show', $workOrder) }}";
        $('#qrCodeImage').attr('src', 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' + encodeURIComponent(qrCodeUrl));
        $('#qrCodeModal').modal('show');
    }
    
    // Функция за изтегляне на QR код
    function downloadQRCode() {
        const qrCodeUrl = "{{ route('admin.work-orders.show', $workOrder) }}";
        const downloadUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=500x500&data=' + encodeURIComponent(qrCodeUrl);
        window.open(downloadUrl, '_blank');
    }
</script>

<!-- Модал за QR код -->
<div class="modal fade" id="qrCodeModal" tabindex="-1" role="dialog" aria-labelledby="qrCodeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="qrCodeModalLabel">
                    <i class="fas fa-qrcode mr-2"></i>QR код за поръчка #{{ $workOrder->number }}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img id="qrCodeImage" src="" alt="QR Code" class="img-fluid mb-3">
                <p class="text-muted small">Сканирайте QR кода за бърз достъп до тази поръчка</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Затвори</button>
                <button type="button" class="btn btn-primary" onclick="downloadQRCode()">
                    <i class="fas fa-download mr-2"></i> Изтегли
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Toastr съобщения (ако не са включени глобално) -->
@if(!isset($toastrIncluded))
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "5000",
            "extendedTimeOut": "1000"
        };
    </script>
    @php $toastrIncluded = true; @endphp
@endif
@stop