@extends('adminlte::page')

@section('title', 'Поръчка #' . $work_order->old_id)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Поръчка #{{ $work_order->old_id }}</h1>
        <div>
            <a href="{{ route('admin.work-orders.index') }}" class="btn btn-sm btn-default">
                <i class="fas fa-arrow-left"></i> Назад към списъка
            </a>
            {{-- Бутон за редакция -- ще го добавим по-късно --}}
            {{-- <a href="{{ route('admin.work-orders.edit', $work_order) }}" class="btn btn-sm btn-primary ml-1">
            <i class="fas fa-edit"></i> Редактирай
        </a> --}}
        </div>
    </div>
@stop

@section('content')
    @php
        $rate = 1.95583;
        $showBgn = now()->lte('2026-01-31');

        function toBgn($amountEur, $rate = 1.95583, $decimals = 2)
        {
            return number_format($amountEur * $rate, $decimals, ',', ' ');
        }

        function formatEur($amountEur, $decimals = 2)
        {
            return number_format($amountEur, $decimals, ',', ' ');
        }
    @endphp
    <div class="row">
        {{-- Лява колона - Основна информация --}}
        <div class="col-md-8">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Основна информация</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Клиент:</strong><br>{{ $work_order->client_name ?: '—' }}</p>
                            <p><strong>Телефон:</strong><br>{{ $work_order->phone ?: '—' }}</p>
                            <p><strong>Автомобил:</strong><br>{{ $work_order->vehicle ?: '—' }}</p>
                            <p><strong>Регистрационен номер:</strong><br>{{ $work_order->plate_number ?: '—' }}</p>
                            <p><strong>Номер на рама (VIN), шаси или
                                    двигател:</strong><br>{{ $work_order->chassis_number ?: '—' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Дата на поръчка:</strong><br>{{ $work_order->order_date?->format('d.m.Y') ?: '—' }}
                            </p>
                            <p><strong>Пробег
                                    (км):</strong><br>{{ $work_order->mileage ? number_format($work_order->mileage, 0, ',', ' ') : '—' }}
                            </p>
                            <p><strong>Механик (код):</strong><br>{{ $work_order->mechanic_code ?: '—' }}</p>
                            <p><strong>Създадена от:</strong><br>{{ $work_order->created_by ?: '—' }}</p>
                            <p><strong>Стойност на труда:</strong><br>
                                {{ formatEur($work_order->service_amount) }} €
                                @if ($showBgn)
                                    <br><small class="text-muted">{{ toBgn($work_order->service_amount, $rate) }}
                                        лв</small>
                                @endif
                            </p>
                        </div>
                    </div>

                    @if ($work_order->note)
                        <div class="row mt-3">
                            <div class="col-12">
                                <p><strong>Бележки:</strong></p>
                                <div class="border rounded p-3 bg-light">
                                    {{ $work_order->note }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Дясна колона - Обобщение и действия --}}
        <div class="col-md-4">
            <div class="card card-success card-outline">
                <div class="card-header">
                    <h3 class="card-title">Финансово обобщение</h3>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td><strong>Стойност на труда:</strong></td>
                            <td class="text-right">
                                <div>{{ formatEur($work_order->service_amount) }} €</div>
                                @if ($showBgn)
                                    <small class="text-muted">{{ toBgn($work_order->service_amount, $rate) }} лв</small>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Стойност на артикулите:</strong></td>
                            @php
                                $itemsTotalEur = $work_order->items->sum('row_total');
                            @endphp
                            <td class="text-right">
                                <div>{{ formatEur($itemsTotalEur) }} €</div>
                                @if ($showBgn)
                                    <small class="text-muted">{{ toBgn($itemsTotalEur, $rate) }} лв</small>
                                @endif
                            </td>
                        </tr>
                        <tr class="table-active">
                            <td><strong>ОБЩО:</strong></td>
                            <td class="text-right">
                                <h4 class="m-0">{{ formatEur($work_order->total) }} €</h4>
                                @if ($showBgn)
                                    <small class="text-muted">{{ toBgn($work_order->total, $rate) }} лв</small>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card card-secondary card-outline mt-3">
                <div class="card-header">
                    <h3 class="card-title">Действия</h3>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary">
                            <i class="fas fa-print"></i> Принтирай
                        </button>
                        <button class="btn btn-outline-success">
                            <i class="fas fa-file-invoice"></i> Създай фактура
                        </button>
                        <button class="btn btn-outline-info">
                            <i class="fas fa-envelope"></i> Изпрати имейл
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Секция с артикулите от поръчката --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Артикули и услуги в поръчката</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover table-striped mb-0">
                        <thead>
                            <tr>
                                <th style="width: 50px">#</th>
                                <th>Код</th>
                                <th>Описание</th>
                                <th>Мярка</th>
                                <th class="text-right">Количество</th>
                                <th class="text-right">Ед. цена</th>
                                <th class="text-right">Сума</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($work_order->items as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->item_code ?: '—' }}</td>
                                    <td>{{ $item->item_name ?: '—' }}</td>
                                    <td>{{ $item->item_measure ?: '—' }}</td>
                                    <td class="text-right">{{ number_format($item->quantity, 2, ',', ' ') }}</td>
                                    <td class="text-right">
                                        <div>{{ formatEur($item->price_each) }} €</div>
                                        @if ($showBgn)
                                            <small class="text-muted">{{ toBgn($item->price_each, $rate) }} лв</small>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        <div class="font-weight-bold">{{ formatEur($item->row_total) }} €</div>
                                        @if ($showBgn)
                                            <small class="text-muted">{{ toBgn($item->row_total, $rate) }} лв</small>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">Няма артикули в тази поръчка</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="table-active">
                                <th colspan="6" class="text-right">Общо:</th>
                                <th class="text-right">
                                    <div class="font-weight-bold">{{ formatEur($work_order->items->sum('row_total')) }} €
                                    </div>
                                    @if ($showBgn)
                                        <small class="text-muted">{{ toBgn($work_order->items->sum('row_total'), $rate) }}
                                            лв</small>
                                    @endif
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Системна информация --}}
    <div class="row mt-3">
        <div class="col-md-6">
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">Системна информация</h3>
                </div>
                <div class="card-body">
                    <p><small><strong>ID в системата:</strong> {{ $work_order->id }}</small></p>
                    <p><small><strong>ID от старата система (old_id):</strong> {{ $work_order->old_id }}</small></p>
                    <p><small><strong>Създадена на:</strong>
                            {{ $work_order->created_at?->format('d.m.Y H:i:s') ?: '—' }}</small></p>
                    <p><small><strong>Обновена на:</strong>
                            {{ $work_order->updated_at?->format('d.m.Y H:i:s') ?: '—' }}</small></p>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .card-body p {
            margin-bottom: 0.8rem;
        }

        .table th {
            border-top: none;
        }
    </style>
@stop
