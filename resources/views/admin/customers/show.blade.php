@extends('adminlte::page')

@section('title', 'Детайли за клиент')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <div class="mr-3">
                @if($customer->is_customer && $customer->is_supplier)
                    <span class="badge badge-primary p-2" title="Клиент и доставчик">
                        <i class="fas fa-user-tie fa-2x"></i>
                    </span>
                @elseif($customer->is_supplier)
                    <span class="badge badge-warning p-2" title="Доставчик">
                        <i class="fas fa-truck fa-2x"></i>
                    </span>
                @else
                    <span class="badge badge-info p-2" title="Клиент">
                        <i class="fas fa-user fa-2x"></i>
                    </span>
                @endif
            </div>
            <div>
                <h1 class="m-0">{{ $customer->name }}</h1>
                <small class="text-muted">
                    <i class="fas fa-id-card mr-1"></i>ID: #{{ $customer->id }}
                    @if($customer->old_id)
                        <span class="ml-3">
                            <i class="fas fa-database mr-1"></i>Старо ID: {{ $customer->old_id }}
                        </span>
                    @endif
                </small>
            </div>
        </div>
        
        <div class="btn-group" role="group">
            <button onclick="window.print()" class="btn btn-outline-secondary">
                <i class="fas fa-print"></i>
            </button>
            <div class="btn-group" role="group">
                <button id="exportDropdown" type="button" class="btn btn-outline-secondary dropdown-toggle" data-toggle="dropdown">
                    <i class="fas fa-file-export"></i>
                </button>
                <div class="dropdown-menu">
                    <a href="{{ route('admin.customers.export.pdf', $customer) }}" class="dropdown-item">
                        <i class="fas fa-file-pdf text-danger mr-2"></i>PDF
                    </a>
                    <a href="{{ route('admin.customers.export.excel', $customer) }}" class="dropdown-item">
                        <i class="fas fa-file-excel text-success mr-2"></i>Excel
                    </a>
                    <a href="{{ route('admin.customers.export.csv', $customer) }}" class="dropdown-item">
                        <i class="fas fa-file-csv text-info mr-2"></i>CSV
                    </a>
                </div>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <!-- Лява колона - Основна информация -->
        <div class="col-lg-8">
            <!-- Карта с информация -->
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" id="customerTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="info-tab" data-toggle="tab" href="#info" role="tab">
                                <i class="fas fa-info-circle mr-2"></i>Информация
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="activity-tab" data-toggle="tab" href="#activity" role="tab">
                                <i class="fas fa-history mr-2"></i>Активност
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="documents-tab" data-toggle="tab" href="#documents" role="tab">
                                <i class="fas fa-folder mr-2"></i>Документи
                            </a>
                        </li>
                    </ul>
                </div>
                
                <div class="card-body">
                    <div class="tab-content" id="customerTabsContent">
                        <!-- Таб Информация -->
                        <div class="tab-pane fade show active" id="info" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-card mb-4">
                                        <h6 class="text-muted mb-3">
                                            <i class="fas fa-user-tag mr-2"></i>Контактна информация
                                        </h6>
                                        <div class="list-group list-group-flush">
                                            @if($customer->contact_person)
                                                <div class="list-group-item px-0 d-flex justify-content-between">
                                                    <span>Контактно лице</span>
                                                    <strong>{{ $customer->contact_person }}</strong>
                                                </div>
                                            @endif
                                            @if($customer->mol)
                                                <div class="list-group-item px-0 d-flex justify-content-between">
                                                    <span>МОЛ</span>
                                                    <strong>{{ $customer->mol }}</strong>
                                                </div>
                                            @endif
                                            @if($customer->phone)
                                                <div class="list-group-item px-0 d-flex justify-content-between">
                                                    <span>Телефон</span>
                                                    <a href="tel:{{ $customer->phone }}" class="text-primary">
                                                        <i class="fas fa-phone mr-1"></i>{{ $customer->phone }}
                                                    </a>
                                                </div>
                                            @endif
                                            @if($customer->email)
                                                <div class="list-group-item px-0 d-flex justify-content-between">
                                                    <span>Имейл</span>
                                                    <a href="mailto:{{ $customer->email }}" class="text-primary">
                                                        <i class="fas fa-envelope mr-1"></i>{{ $customer->email }}
                                                    </a>
                                                </div>
                                            @endif
                                            @if($customer->fax)
                                                <div class="list-group-item px-0 d-flex justify-content-between">
                                                    <span>Факс</span>
                                                    <span>{{ $customer->fax }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="info-card">
                                        <h6 class="text-muted mb-3">
                                            <i class="fas fa-map-marker-alt mr-2"></i>Адреси
                                        </h6>
                                        <div class="list-group list-group-flush">
                                            @if($customer->address)
                                                <div class="list-group-item px-0">
                                                    <div class="font-weight-bold mb-1">Основен адрес</div>
                                                    <div>{{ $customer->address }}</div>
                                                    @if($customer->address_2)
                                                        <div class="text-muted small">{{ $customer->address_2 }}</div>
                                                    @endif
                                                </div>
                                            @endif
                                            @if($customer->res_address_1)
                                                <div class="list-group-item px-0">
                                                    <div class="font-weight-bold mb-1">Резервен адрес</div>
                                                    <div>{{ $customer->res_address_1 }}</div>
                                                    @if($customer->res_address_2)
                                                        <div class="text-muted small">{{ $customer->res_address_2 }}</div>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="info-card mb-4">
                                        <h6 class="text-muted mb-3">
                                            <i class="fas fa-file-contract mr-2"></i>Юридически данни
                                        </h6>
                                        <div class="list-group list-group-flush">
                                            @if($customer->tax_number)
                                                <div class="list-group-item px-0 d-flex justify-content-between">
                                                    <span>Данъчен номер</span>
                                                    <strong>{{ $customer->tax_number }}</strong>
                                                </div>
                                            @endif
                                            @if($customer->bulstat)
                                                <div class="list-group-item px-0 d-flex justify-content-between">
                                                    <span>Булстат</span>
                                                    <strong>{{ $customer->bulstat }}</strong>
                                                </div>
                                            @endif
                                            @if($customer->doc_type)
                                                <div class="list-group-item px-0 d-flex justify-content-between">
                                                    <span>Вид документ</span>
                                                    <span>{{ $customer->doc_type }}</span>
                                                </div>
                                            @endif
                                            @if($customer->customer_number)
                                                <div class="list-group-item px-0 d-flex justify-content-between">
                                                    <span>Клиентски номер</span>
                                                    <span class="badge badge-light">{{ $customer->customer_number }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="info-card">
                                        <h6 class="text-muted mb-3">
                                            <i class="fas fa-chart-pie mr-2"></i>Статистика
                                        </h6>
                                        <div class="row text-center">
                                            <div class="col-4">
                                                <div class="stat-box">
                                                    <div class="stat-number text-primary">{{ $customerStats['total_vehicles'] }}</div>
                                                    <div class="stat-label small">Автомобила</div>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="stat-box">
                                                    <div class="stat-number text-success">{{ $customerStats['total_work_orders'] }}</div>
                                                    <div class="stat-label small">Поръчки</div>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="stat-box">
                                                    <div class="stat-number text-info">{{ $customerStats['total_invoices'] }}</div>
                                                    <div class="stat-label small">Фактури</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Бележки -->
                            @if($customer->notes)
                            <div class="mt-4">
                                <h6 class="text-muted mb-3">
                                    <i class="fas fa-sticky-note mr-2"></i>Бележки
                                </h6>
                                <div class="card bg-light">
                                    <div class="card-body">
                                        {{ nl2br(e($customer->notes)) }}
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                        
                        <!-- Таб Активност -->
                        <div class="tab-pane fade" id="activity" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-muted mb-3">
                                        <i class="fas fa-car mr-2"></i>Автомобили ({{ $customer->vehicles->count() }})
                                    </h6>
                                    @forelse($customer->vehicles as $vehicle)
                                        <div class="activity-item mb-3">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <div class="font-weight-bold">{{ $vehicle->make }} {{ $vehicle->model }}</div>
                                                    <div class="small text-muted">
                                                        <span class="badge badge-light">{{ $vehicle->plate }}</span>
                                                        <span class="mx-2">•</span>
                                                        {{ $vehicle->year }}
                                                        <span class="mx-2">•</span>
                                                        {{ number_format($vehicle->mileage) }} км
                                                    </div>
                                                </div>
                                                <a href="{{ route('admin.vehicles.show', $vehicle) }}" class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center text-muted py-4">
                                            <i class="fas fa-car fa-2x mb-3"></i>
                                            <p>Няма регистрирани автомобили</p>
                                        </div>
                                    @endforelse
                                </div>
                                
                                <div class="col-md-6">
                                    <h6 class="text-muted mb-3">
                                        <i class="fas fa-wrench mr-2"></i>Последни поръчки
                                    </h6>
                                    @forelse($customer->workOrders->take(5) as $order)
                                        <div class="activity-item mb-3">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <div class="font-weight-bold">Поръчка #{{ $order->number }}</div>
                                                    <div class="small text-muted">
                                                        {{ optional($order->received_at)->format('d.m.Y') }}
                                                        <span class="mx-2">•</span>
                                                        {{ number_format($order->total, 2) }} лв.
                                                        <span class="mx-2">•</span>
                                                        <span class="badge badge-{{ $order->status == 'completed' ? 'success' : ($order->status == 'in_progress' ? 'warning' : 'secondary') }}">
                                                            {{ $order->status }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <a href="{{ route('admin.work-orders.show', $order) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center text-muted py-4">
                                            <i class="fas fa-wrench fa-2x mb-3"></i>
                                            <p>Няма работни поръчки</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                        
                        <!-- Таб Документи -->
                        <div class="tab-pane fade" id="documents" role="tabpanel">
                            <div class="text-center py-5">
                                <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Модулът за документи е в процес на разработка</h5>
                                <p class="text-muted">Тук ще се показват всички документи, свързани с клиента</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Дясна колона - Действия и статус -->
        <div class="col-lg-4">
            <!-- Статус карта -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-bar mr-2"></i>Статус
                    </h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <span>Статус на клиента</span>
                            @if($customer->is_active)
                                <span class="badge badge-success badge-pill">Активен</span>
                            @else
                                <span class="badge badge-secondary badge-pill">Неактивен</span>
                            @endif
                        </div>
                        <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <span>Тип</span>
                            <div>
                                @if($customer->is_customer)
                                    <span class="badge badge-info mr-1">Клиент</span>
                                @endif
                                @if($customer->is_supplier)
                                    <span class="badge badge-warning">Доставчик</span>
                                @endif
                            </div>
                        </div>
                        <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <span>Бюлетин</span>
                            @if($customer->include_in_mailing)
                                <span class="badge badge-primary badge-pill">Включен</span>
                            @else
                                <span class="badge badge-light badge-pill">Изключен</span>
                            @endif
                        </div>
                        <div class="list-group-item px-0">
                            <small class="text-muted">Създаден на: {{ $customer->created_at->format('d.m.Y H:i') }}</small>
                        </div>
                        @if($customer->updated_at != $customer->created_at)
                            <div class="list-group-item px-0">
                                <small class="text-muted">Актуализиран на: {{ $customer->updated_at->format('d.m.Y H:i') }}</small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Действия -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-cogs mr-2"></i>Действия
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.customers.edit', $customer) }}" class="btn btn-primary">
                            <i class="fas fa-edit mr-2"></i>Редактирай клиент
                        </a>
                        
                        <div class="btn-group w-100 mt-2" role="group">
                            <a href="{{ route('admin.vehicles.create', ['customer_id' => $customer->id]) }}" 
                               class="btn btn-outline-success">
                                <i class="fas fa-plus mr-2"></i>Автомобил
                            </a>
                            <a href="{{ route('admin.work-orders.create', ['customer_id' => $customer->id]) }}" 
                               class="btn btn-outline-primary">
                                <i class="fas fa-plus mr-2"></i>Поръчка
                            </a>
                            <a href="{{ route('admin.invoices.create', ['customer_id' => $customer->id]) }}" 
                               class="btn btn-outline-info">
                                <i class="fas fa-plus mr-2"></i>Фактура
                            </a>
                        </div>
                        
                        <div class="mt-3">
                            <form action="{{ route('admin.customers.destroy', $customer) }}" method="POST" 
                                  onsubmit="return confirm('Сигурни ли сте, че искате да деактивирате този клиент?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger w-100">
                                    <i class="fas fa-user-slash mr-2"></i>Деактивирай клиент
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Финансова информация -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-money-bill-wave mr-2"></i>Финанси
                    </h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item px-0 d-flex justify-content-between">
                            <span>Общо похарчени</span>
                            <strong class="text-success">{{ number_format($customerStats['total_spent'], 2) }} лв.</strong>
                        </div>
                        <div class="list-group-item px-0 d-flex justify-content-between">
                            <span>Активни автомобили</span>
                            <strong>{{ $customerStats['active_vehicles'] }}</strong>
                        </div>
                        @if($customerStats['last_service'])
                            <div class="list-group-item px-0">
                                <small class="text-muted">Последна услуга: {{ $customerStats['last_service']->format('d.m.Y') }}</small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Допълнителна информация -->
            @if($customer->receiver || $customer->receiver_details || $customer->eidale || $customer->partida)
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle mr-2"></i>Допълнително
                    </h5>
                </div>
                <div class="card-body">
                    @if($customer->receiver)
                        <div class="mb-2">
                            <small class="text-muted">Получател:</small>
                            <div>{{ $customer->receiver }}</div>
                        </div>
                    @endif
                    @if($customer->receiver_details)
                        <div class="mb-2">
                            <small class="text-muted">Детайли:</small>
                            <div class="small">{{ $customer->receiver_details }}</div>
                        </div>
                    @endif
                    @if($customer->eidale)
                        <div class="mb-2">
                            <small class="text-muted">ЕИДАЛЕ:</small>
                            <div>{{ $customer->eidale }}</div>
                        </div>
                    @endif
                    @if($customer->partida)
                        <div class="mb-2">
                            <small class="text-muted">Партида:</small>
                            <div>{{ $customer->partida }}</div>
                        </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
@stop

@push('css')
<style>
    .info-card {
        background: #fff;
        border-radius: 8px;
        padding: 1.25rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    
    .stat-box {
        padding: 0.75rem;
        border-radius: 6px;
        background: #f8f9fa;
    }
    
    .stat-number {
        font-size: 1.75rem;
        font-weight: 700;
        line-height: 1;
    }
    
    .stat-label {
        color: #6c757d;
        margin-top: 0.25rem;
    }
    
    .activity-item {
        padding: 0.75rem;
        border-left: 3px solid #007bff;
        background: #f8f9fa;
        border-radius: 0 4px 4px 0;
    }
    
    .nav-tabs .nav-link {
        border: none;
        color: #6c757d;
        font-weight: 500;
        padding: 0.75rem 1.25rem;
    }
    
    .nav-tabs .nav-link.active {
        color: #007bff;
        border-bottom: 3px solid #007bff;
        background: transparent;
    }
    
    .badge-pill {
        padding: 0.5em 1em;
    }
    
    .list-group-item {
        border-color: rgba(0,0,0,0.05);
    }
    
    .card-header {
        background: #fff;
        border-bottom: 1px solid rgba(0,0,0,0.1);
    }
    
    .btn-group .btn {
        flex: 1;
    }
</style>
@endpush

@push('js')
<script>
    $(document).ready(function() {
        // Активиране на табовете
        $('#customerTabs a').on('click', function (e) {
            e.preventDefault();
            $(this).tab('show');
        });
        
        // Софтуер за преглед на бележки
        $('.notes-toggle').click(function() {
            $(this).find('i').toggleClass('fa-chevron-down fa-chevron-up');
            $('.notes-content').toggleClass('d-none');
        });
        
        // Инициализация на DataTables за таблиците (ако се добавят по-късно)
        if ($.fn.DataTable) {
            $('#vehiclesTable, #ordersTable').DataTable({
                pageLength: 5,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/bg.json'
                }
            });
        }
        
        // Анимация при превключване на табовете
        $('a[data-toggle="tab"]').on('shown.bs.tab', function () {
            $('.tab-pane.active').addClass('animate__animated animate__fadeIn');
        });
    });
</script>
@endpush