@extends('adminlte::page')

@section('title', 'Детайли за клиент')

@push('css')
    <link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css" rel="stylesheet"/>
@endpush

@section('content_header')
    <h1>Детайли за клиент: <strong>{{ $customer->name }}</strong></h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="btn-group" role="group">
                <button onclick="window.print()" class="btn btn-sm btn-secondary">
                    <i class="fas fa-print"></i> Печат
                </button>
                <a href="{{ route('admin.customers.export.pdf', $customer) }}" class="btn btn-sm btn-danger">
                    <i class="fas fa-file-pdf"></i> PDF
                </a>
                <a href="{{ route('admin.customers.export.excel', $customer) }}" class="btn btn-sm btn-success">
                    <i class="fas fa-file-excel"></i> Excel
                </a>
                <a href="{{ route('admin.customers.export.csv', $customer) }}" class="btn btn-sm btn-info">
                    <i class="fas fa-file-csv"></i> CSV
                </a>
            </div>
        </div>

        <div class="card-body">
            <div class="row">
                <!-- Основна информация -->
                <div class="col-md-6">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Основна информация</h3>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-sm table-bordered mb-0">
                                <tr><th style="width: 40%">ID</th><td>{{ $customer->id }}</td></tr>
                                <tr><th>Старо ID (Access)</th><td>{{ $customer->old_system_id ?? '-' }}</td></tr>
                                <tr><th>Тип</th><td>{{ $customer->type_label }}</td></tr>
                                <tr><th>Име / Фирма</th><td>{{ $customer->name }}</td></tr>
                                <tr><th>Контактно лице</th><td>{{ $customer->contact_person ?? '-' }}</td></tr>
                                <tr><th>ДДС номер</th><td>{{ $customer->vat_number ?? '-' }}</td></tr>
                                <tr><th>Булстат</th><td>
                                    @if($customer->bulstat)
                                        {{ $customer->bulstat }}
                                        @if($customer->bulstat_letter)
                                            (Буква: {{ $customer->bulstat_letter }})
                                        @endif
                                    @else
                                        -
                                    @endif
                                </td></tr>
                                <tr><th>Пълен булстат</th><td>{{ $customer->full_bulstat ?? '-' }}</td></tr>
                                <tr><th>Съдебен регистър</th><td>{{ $customer->court_registration ?? '-' }}</td></tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Контакти и статус -->
                <div class="col-md-6">
                    <div class="card card-info card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Контакти и статус</h3>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-sm table-bordered mb-0">
                                <tr><th style="width: 40%">Телефон</th><td>{{ $customer->phone ?? '-' }}</td></tr>
                                <tr><th>Факс</th><td>{{ $customer->fax ?? '-' }}</td></tr>
                                <tr><th>Имейл</th><td>
                                    @if($customer->email)
                                        <a href="mailto:{{ $customer->email }}">{{ $customer->email }}</a>
                                    @else
                                        -
                                    @endif
                                </td></tr>
                                <tr><th>Форматиран адрес</th><td>{{ $customer->formatted_address ?? '-' }}</td></tr>
                                <tr><th>Адрес ред 1</th><td>{{ $customer->address_line1 ?? '-' }}</td></tr>
                                <tr><th>Адрес ред 2</th><td>{{ $customer->address_line2 ?? '-' }}</td></tr>
                                <tr><th>Град</th><td>{{ $customer->city ?? '-' }}</td></tr>
                                <tr><th>Статус</th>
                                    <td>
                                        @if($customer->is_active)
                                            <span class="badge badge-success">Активен</span>
                                        @else
                                            <span class="badge badge-secondary">Неактивен</span>
                                        @endif
                                        @if($customer->include_in_reports)
                                            <span class="badge badge-info ml-1">Включен в справки</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Бележки -->
            @if($customer->notes)
            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="card card-warning card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Бележки</h3>
                        </div>
                        <div class="card-body">
                            {{ nl2br(e($customer->notes)) }}
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Автомобили на клиента -->
            <h4 class="mt-4">Автомобили ({{ $customer->vehicles->count() }})</h4>
            <table class="table table-sm table-striped" id="vehiclesTable">
                <thead>
                    <tr>
                        <th>Рег. №</th>
                        <th>VIN</th>
                        <th>Марка/Модел</th>
                        <th>Година</th>
                        <th>Пробег</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customer->vehicles as $v)
                        <tr>
                            <td>{{ $v->plate }}</td>
                            <td>{{ $v->vin }}</td>
                            <td>{{ $v->make }} / {{ $v->model }}</td>
                            <td>{{ $v->year }}</td>
                            <td>{{ $v->mileage }} км</td>
                            <td>
                                <a href="{{ route('admin.vehicles.show', $v) }}" class="btn btn-xs btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center">Няма регистрирани автомобили</td></tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Поръчки на клиента -->
            <h4 class="mt-4">Работни поръчки ({{ $customer->workOrders->count() }})</h4>
            <table class="table table-sm table-striped" id="ordersTable">
                <thead>
                    <tr>
                        <th>№</th>
                        <th>Статус</th>
                        <th>Приета на</th>
                        <th>Общо</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customer->workOrders as $wo)
                        <tr>
                            <td>{{ $wo->number }}</td>
                            <td>{{ $wo->status }}</td>
                            <td>{{ optional($wo->received_at)->format('d.m.Y') ?? '-' }}</td>
                            <td>{{ number_format($wo->total, 2) }} лв.</td>
                            <td>
                                <a href="{{ route('admin.work-orders.show', $wo) }}" class="btn btn-xs btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center">Няма работни поръчки</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer">
            <a href="{{ route('admin.customers.edit', $customer) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Редактирай
            </a>
            <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Назад към списъка
            </a>
            <form action="{{ route('admin.customers.destroy', $customer) }}" method="POST" class="d-inline">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger" 
                        onclick="return confirm('Сигурни ли сте, че искате да деактивирате този клиент?')">
                    <i class="fas fa-trash"></i> Деактивирай
                </button>
            </form>
        </div>
    </div>
@stop

@section('css')
    <link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css" rel="stylesheet"/>
@stop

@push('js')
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

    <script>
        $(function () {
            $('#vehiclesTable, #ordersTable').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    { extend: 'print', text: 'Печат' },
                    { extend: 'excelHtml5', text: 'Excel' },
                    { extend: 'csvHtml5', text: 'CSV' },
                    { extend: 'pdfHtml5', text: 'PDF' }
                ],
                language: { 
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/bg.json',
                    search: "Търсене:",
                    lengthMenu: "Покажи _MENU_ записа",
                    info: "Показване на _START_ до _END_ от общо _TOTAL_ записа",
                    paginate: {
                        first: "Първа",
                        last: "Последна",
                        next: "Следваща",
                        previous: "Предишна"
                    }
                }
            });
        });
    </script>
@endpush