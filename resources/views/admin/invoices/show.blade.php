@extends('adminlte::page')

@section('title', 'Фактура №' . $invoice->old_id)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>
            <i class="fas fa-file-invoice mr-2"></i> Фактура №{{ $invoice->old_id }}
            @if($invoice->is_void)
                <span class="badge badge-secondary ml-2">Анулирана</span>
            @elseif($invoice->paid)
                <span class="badge badge-success ml-2">Платена</span>
            @else
                <span class="badge badge-warning ml-2">Неплатена</span>
            @endif
        </h1>
        <div>
            <a href="{{ route('admin.invoices.print', $invoice->id) }}" class="btn btn-default btn-sm" target="_blank">
                <i class="fas fa-print"></i> Печат
            </a>
            <a href="{{ route('admin.invoices.pdf', $invoice->id) }}" class="btn btn-danger btn-sm" target="_blank">
                <i class="fas fa-file-pdf"></i> PDF
            </a>
            @if(!$invoice->is_void)
                <a href="{{ route('admin.invoices.edit', $invoice->id) }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-edit"></i> Редактиране
                </a>
            @endif
            <a href="{{ route('admin.invoices.index') }}" class="btn btn-default btn-sm ml-2">
                <i class="fas fa-arrow-left"></i> Назад
            </a>
        </div>
    </div>
@stop

@section('content')
    @php
        $rate = 1.95583;
        $totalEur = $invoice->total;
        $totalBgn = $totalEur * $rate;
    @endphp

    {{-- КАРТА С ОСНОВНА ИНФОРМАЦИЯ --}}
    <div class="row">
        <div class="col-md-6">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle mr-1"></i> Данни за фактурата
                    </h3>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th style="width: 140px;">Номер:</th>
                            <td><strong>{{ $invoice->old_id }}</strong></td>
                        </tr>
                        <tr>
                            <th>Дата фактура:</th>
                            <td>{{ $invoice->invoice_date ? $invoice->invoice_date->format('d.m.Y') : '—' }}</td>
                        </tr>
                        <tr>
                            <th>Падежна дата:</th>
                            <td>
                                {{ $invoice->date_due ? $invoice->date_due->format('d.m.Y') : '—' }}
                                @if($invoice->date_due && $invoice->date_due < now() && !$invoice->paid && !$invoice->is_void)
                                    <span class="badge badge-danger ml-2">Просрочена</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Тип фактура:</th>
                            <td>
                                @if($invoice->doctype)
                                    {{ $invoice->doctype->name }} ({{ $invoice->doctype->short ?? $invoice->doctype->type }})
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Дата на получаване:</th>
                            <td>{{ $invoice->invoice_received_date ? $invoice->invoice_received_date->format('d.m.Y') : '—' }}</td>
                        </tr>
                        <tr>
                            <th>Получил:</th>
                            <td>{{ $invoice->invoice_received_person ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th>Създадена от:</th>
                            <td>{{ $invoice->invoice_created_by ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th>Отговорник (получаване):</th>
                            <td>{{ $invoice->invoice_rec_responsible ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th>Отговорник (създаване):</th>
                            <td>{{ $invoice->invoice_cre_responsible ?? '—' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card card-info card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user mr-1"></i> Клиент
                    </h3>
                </div>
                <div class="card-body">
                    @if($invoice->customer)
                        <table class="table table-sm table-borderless">
                            <tr>
                                <th style="width: 140px;">Име/Фирма:</th>
                                <td><strong>{!! $invoice->customer->name !!}</strong></td>
                            </tr>
                            <tr>
                                <th>Клиентски №:</th>
                                <td>{{ $invoice->customer->customer_number ?? '—' }}</td>
                            </tr>
                            <tr>
                                <th>Стар ID:</th>
                                <td>{{ $invoice->customer->old_id ?? '—' }}</td>
                            </tr>
                            <tr>
                                <th>Телефон:</th>
                                <td>{{ $invoice->customer->phone ?? '—' }}</td>
                            </tr>
                            <tr>
                                <th>E-mail:</th>
                                <td>{{ $invoice->customer->email ?? '—' }}</td>
                            </tr>
                            <tr>
                                <th>Адрес:</th>
                                <td>
                                    {!! $invoice->customer->address ?? '—' !!}
                                    @if($invoice->customer->address_2)
                                        <br>{!! $invoice->customer->address_2 !!}
                                    @endif
                                </td>
                            </tr>
                            @if($invoice->customer->bulstat)
                                <tr>
                                    <th>БУЛСТАТ/ЕИК:</th>
                                    <td>{{ $invoice->customer->bulstat }}</td>
                                </tr>
                            @endif
                            @if($invoice->customer->tax_number)
                                <tr>
                                    <th>ДДС №:</th>
                                    <td>{{ $invoice->customer->tax_number }}</td>
                                </tr>
                            @endif
                            @if($invoice->customer->mol)
                                <tr>
                                    <th>МОЛ:</th>
                                    <td>{!! $invoice->customer->mol !!}</td>
                                </tr>
                            @endif
                        </table>
                    @else
                        <p class="text-muted">Няма информация за клиент</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ТАБЛИЦА С АРТИКУЛИ --}}
    <div class="card card-success card-outline">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-boxes mr-1"></i> Артикули и услуги
            </h3>
        </div>
        <div class="card-body p-0">
            @if($invoice->items->count())
                <table class="table table-striped table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width: 5%;">№</th>
                            <th style="width: 15%;">Код</th>
                            <th style="width: 35%;">Наименование</th>
                            <th style="width: 8%;">Мярка</th>
                            <th style="width: 10%;">Количество</th>
                            <th style="width: 12%;">Ед. цена (€)</th>
                            <th style="width: 15%;">Сума (€)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->items as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->item_code ?? '—' }}</td>
                                <td>{{ $item->item_name }}</td>
                                <td>{{ $item->item_measure ?? 'бр.' }}</td>
                                <td class="text-right">{{ number_format($item->quantity, 2, ',', ' ') }}</td>
                                <td class="text-right">{{ number_format($item->price_each, 2, ',', ' ') }}</td>
                                <td class="text-right">{{ number_format($item->row_total, 2, ',', ' ') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="6" class="text-right">Обща сума:</th>
                            <th class="text-right">{{ number_format($totalEur, 2, ',', ' ') }} €</th>
                        </tr>
                        <tr>
                            <th colspan="6" class="text-right">Обща сума в лева:</th>
                            <th class="text-right">{{ number_format($totalBgn, 2, ',', ' ') }} лв</th>
                        </tr>
                        @if($invoice->tipsdelka > 0)
                            <tr>
                                <td colspan="6" class="text-right">Tipsdelka:</td>
                                <td class="text-right">{{ $invoice->tipsdelka }}</td>
                            </tr>
                        @endif
                    </tfoot>
                </table>
            @else
                <div class="p-3 text-muted">
                    Няма добавени артикули към фактурата.
                </div>
            @endif
        </div>
    </div>

    {{-- ДОПЪЛНИТЕЛНА ИНФОРМАЦИЯ --}}
    <div class="row">
        <div class="col-md-6">
            <div class="card card-secondary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-credit-card mr-1"></i> Плащане
                    </h3>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th style="width: 140px;">Статус:</th>
                            <td>
                                @if($invoice->is_void)
                                    <span class="badge badge-secondary">Анулирана</span>
                                @elseif($invoice->paid)
                                    <span class="badge badge-success">Платена</span>
                                @else
                                    <span class="badge badge-warning">Неплатена</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Метод на плащане:</th>
                            <td>
                                @php
                                    $methods = [0 => 'Банков превод', 1 => 'В брой', 2 => 'Карта'];
                                @endphp
                                {{ $methods[$invoice->pay_method] ?? '—' }}
                                @if($invoice->payment_cash)
                                    <span class="badge badge-info ml-2">в брой</span>
                                @endif
                            </td>
                        </tr>
                        @if($invoice->paid)
                            <tr>
                                <th>Отпечатана:</th>
                                <td>{{ $invoice->printed ? 'Да' : 'Не' }}</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-secondary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-sticky-note mr-1"></i> Бележки и коментари
                    </h3>
                </div>
                <div class="card-body">
                    @if($invoice->note)
                        <div class="mb-2">
                            <strong>Бележки:</strong>
                            <p class="mb-0">{!! nl2br(e($invoice->note)) !!}</p>
                        </div>
                    @endif
                    @if($invoice->zeroexplain)
                        <div>
                            <strong>Обяснение за нулева ставка:</strong>
                            <p class="mb-0">{{ $invoice->zeroexplain }}</p>
                        </div>
                    @endif
                    @if(!$invoice->note && !$invoice->zeroexplain)
                        <p class="text-muted mb-0">Няма въведени бележки.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- СИСТЕМНА ИНФОРМАЦИЯ --}}
    <div class="card card-light card-outline">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-history mr-1"></i> Системна информация
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <small class="text-muted d-block">Създадена на: {{ $invoice->created_at->format('d.m.Y H:i:s') }}</small>
                    <small class="text-muted d-block">Последна промяна: {{ $invoice->updated_at->format('d.m.Y H:i:s') }}</small>
                </div>
                <div class="col-md-6">
                    <small class="text-muted d-block">Валутен курс: 1 € = {{ number_format($rate, 5, ',', ' ') }} лв</small>
                    @if($invoice->sale_type > 0)
                        <small class="text-muted d-block">Тип продажба: {{ $invoice->sale_type }}</small>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop