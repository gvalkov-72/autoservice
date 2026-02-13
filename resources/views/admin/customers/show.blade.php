@extends('adminlte::page')

@section('title', 'Преглед на клиент')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>
            <i class="fas fa-user mr-2"></i> {!! $customer->name !!}
            @if (!$customer->is_active)
                <span class="badge badge-secondary">Неактивен</span>
            @endif
        </h1>
        <div>
            <a href="{{ route('admin.customers.print', $customer->id) }}" class="btn btn-default btn-sm" target="_blank">
                <i class="fas fa-print"></i> Печат
            </a>
            <a href="{{ route('admin.customers.pdf', $customer->id) }}" class="btn btn-danger btn-sm" target="_blank">
                <i class="fas fa-file-pdf"></i> PDF
            </a>
            <a href="{{ route('admin.customers.edit', $customer->id) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit"></i> Редактиране
            </a>
            <a href="{{ route('admin.customers.index') }}" class="btn btn-default btn-sm ml-2">
                <i class="fas fa-arrow-left"></i> Назад
            </a>
        </div>
    </div>
@stop

@section('content')
    {{-- КАРТА С ОСНОВНИ ДАННИ --}}
    <div class="row">
        <div class="col-md-6">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Основни данни</h3>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th style="width:140px;">Клиентски №:</th>
                            <td>{{ $customer->customer_number ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th>Стар ID:</th>
                            <td>{{ $customer->old_id ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th>Име/Фирма:</th>
                            <td><strong>{!! $customer->name !!}</strong></td>
                        </tr>
                        <tr>
                            <th>Телефон:</th>
                            <td>{{ $customer->phone ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th>Факс:</th>
                            <td>{{ $customer->fax ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th>E-mail:</th>
                            <td>{{ $customer->email ? '<a href="mailto:' . $customer->email . '">' . $customer->email . '</a>' : '—' }}
                            </td>
                        </tr>
                        <tr>
                            <th>Адрес:</th>
                            <td>
                                {!! $customer->address ?? '—' !!}
                                @if ($customer->address_2)
                                    <br>{!! $customer->address_2 !!}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Жилищен адрес:</th>
                            <td>
                                {!! $customer->res_address_1 ?? '—' !!}
                                @if ($customer->res_address_2)
                                    <br>{!! $customer->res_address_2 !!}
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card card-secondary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Данъчни и контакт</h3>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th style="width:140px;">БУЛСТАТ/ЕИК:</th>
                            <td>{{ $customer->bulstat ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th>ДДС номер:</th>
                            <td>{{ $customer->tax_number ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th>МОЛ:</th>
                            <td>{!! $customer->mol ?? '—' !!}</td>
                        </tr>
                        <tr>
                            <th>Лице за контакт:</th>
                            <td>{!! $customer->contact_person ?? '—' !!}</td>
                        </tr>
                        <tr>
                            <th>Тип документ:</th>
                            <td>{{ $customer->doc_type ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th>Получател:</th>
                            <td>{!! $customer->receiver ?? '—' !!}</td>
                        </tr>
                        <tr>
                            <th>Детайли получател:</th>
                            <td>{!! nl2br(e($customer->receiver_details)) ?? '—' !!}</td>
                        </tr>
                        <tr>
                            <th>Дата ЕИ:</th>
                            <td>{{ $customer->eidate ? $customer->eidate->format('d.m.Y') : '—' }}</td>
                        </tr>
                        <tr>
                            <th>Partida:</th>
                            <td>{{ $customer->partida ?? '—' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- БЕЛЕЖКИ --}}
    @if ($customer->notes)
        <div class="card card-info card-outline">
            <div class="card-header">
                <h3 class="card-title">Бележки</h3>
            </div>
            <div class="card-body">
                {!! nl2br(e($customer->notes)) !!}
            </div>
        </div>
    @endif

    {{-- АВТОМОБИЛИ --}}
    <div class="card card-success card-outline">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-car mr-1"></i> Автомобили ({{ $customer->vehicles->count() }})
            </h3>
        </div>
        <div class="card-body p-0">
            @if ($customer->vehicles->count())
                <table class="table table-striped table-hover mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Марка/Модел</th>
                            <th>Рег. номер</th>
                            <th>VIN/Рама</th>
                            <th>Последен пробег</th>
                            <th>Бележки</th>
                            <th>Статус</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($customer->vehicles as $v)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{!! $v->vehicle ?? '—' !!}</td>
                                <td><strong>{{ $v->plate_number ?? '—' }}</strong></td>
                                <td>{{ $v->chassis_number ?? '—' }}</td>
                                <td>{{ $v->last_mileage ? number_format($v->last_mileage, 0, ',', ' ') . ' км' : '—' }}
                                </td>
                                <td>{{ $v->notes ?? '—' }}</td>
                                <td>
                                    @if ($v->is_active)
                                        <span class="badge badge-success">Активен</span>
                                    @else
                                        <span class="badge badge-secondary">Неактивен</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="p-3 text-muted">
                    Няма регистрирани автомобили за този клиент.
                </div>
            @endif
        </div>
    </div>

    {{-- РАБОТНИ ПОРЪЧКИ --}}
    <div class="card card-warning card-outline">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-clipboard-list mr-1"></i> Работни поръчки ({{ $workOrders->count() }})
            </h3>
            @if ($workOrders->count())
                <div class="card-tools">
                    <a href="{{ route('admin.work-orders.index', ['customer_id' => $customer->id]) }}"
                        class="btn btn-sm btn-default">
                        Виж всички
                    </a>
                </div>
            @endif
        </div>
        <div class="card-body p-0">
            @if ($workOrders->count())
                <table class="table table-sm table-striped mb-0">
                    <thead>
                        <tr>
                            <th>№</th>
                            <th>Дата</th>
                            <th>Автомобил</th>
                            <th>Рег. номер</th>
                            <th>Механик</th>
                            <th>Сума</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($workOrders->take(10) as $wo)
                            <tr>
                                <td>{{ $wo->old_id }}</td>
                                <td>{{ $wo->order_date?->format('d.m.Y') }}</td>
                                <td>{!! $wo->vehicle ?? '—' !!}</td>
                                <td>{{ $wo->plate_number ?? '—' }}</td>
                                <td>{{ $wo->mechanic_code ?? '—' }}</td>
                                <td>{{ number_format($wo->total, 2, ',', ' ') }} €</td>
                                <td>
                                    <a href="{{ route('admin.work-orders.show', $wo->id) }}"
                                        class="btn btn-xs btn-default">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="p-3 text-muted">
                    Няма намерени работни поръчки за този клиент.
                </div>
            @endif
        </div>
    </div>
@stop
