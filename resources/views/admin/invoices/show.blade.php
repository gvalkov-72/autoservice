@extends('adminlte::page')

@section('title', 'Преглед на фактура')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0">
            <i class="fas fa-file-invoice text-primary mr-2"></i>Фактура № {{ $invoice->invoice_number }}
        </h1>
        <div class="btn-group">
            <a href="{{ route('admin.invoices.edit', $invoice) }}" class="btn btn-primary">
                <i class="fas fa-edit mr-1"></i>Редактирай
            </a>
            <a href="{{ route('admin.invoices.pdf', $invoice) }}" class="btn btn-success" target="_blank">
                <i class="fas fa-file-pdf mr-1"></i>PDF
            </a>
            <a href="{{ route('admin.invoices.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i>Назад
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Информация за фактурата</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card card-light">
                                <div class="card-header">
                                    <h3 class="card-title">Данни за клиента</h3>
                                </div>
                                <div class="card-body">
                                    <p><strong>Клиент:</strong> {{ $invoice->customer->name ?? 'Няма клиент' }}</p>
                                    <p><strong>Телефон:</strong> {{ $invoice->customer->phone ?? '-' }}</p>
                                    <p><strong>Имейл:</strong> {{ $invoice->customer->email ?? '-' }}</p>
                                    <p><strong>Адрес:</strong> {{ $invoice->customer->address ?? '-' }}</p>
                                    <p><strong>ЕИК:</strong> {{ $invoice->customer->eik ?? '-' }}</p>
                                    <p><strong>ДДС №:</strong> {{ $invoice->customer->vat_number ?? '-' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card card-light">
                                <div class="card-header">
                                    <h3 class="card-title">Данни за фактурата</h3>
                                </div>
                                <div class="card-body">
                                    <p><strong>Номер на фактура:</strong> {{ $invoice->invoice_number }}</p>
                                    <p><strong>Дата на издаване:</strong>
                                        {{ $invoice->issue_date ? \Carbon\Carbon::parse($invoice->issue_date)->format('d.m.Y') : '-' }}
                                    </p>
                                    <p><strong>Дата на падеж:</strong>
                                        {{ $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('d.m.Y') : '-' }}
                                    </p>
                                    <p><strong>Дата на данъчно събитие:</strong>
                                        {{ $invoice->tax_event_date ? \Carbon\Carbon::parse($invoice->tax_event_date)->format('d.m.Y') : '-' }}
                                    </p>
                                    <p><strong>Статус на плащане:</strong>
                                        <span
                                            class="badge badge-{{ $invoice->payment_status === 'paid' ? 'success' : 'warning' }}">
                                            {{ $invoice->payment_status === 'paid' ? 'Платена' : 'Неплатена' }}
                                        </span>
                                    </p>
                                    <p><strong>Статус:</strong>
                                        <span class="badge badge-{{ $invoice->is_active ? 'success' : 'secondary' }}">
                                            {{ $invoice->is_active ? 'Активна' : 'Неактивна' }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card card-light">
                                <div class="card-header">
                                    <h3 class="card-title">Финансова информация</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="info-box bg-light">
                                                <div class="info-box-content">
                                                    <span class="info-box-text text-center">Общо без ДДС</span>
                                                    <span
                                                        class="info-box-number text-center display-6">{{ number_format($invoice->net_total ?? 0, 2) }}
                                                        лв</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="info-box bg-light">
                                                <div class="info-box-content">
                                                    <span class="info-box-text text-center">ДДС
                                                        ({{ $invoice->vat_rate ?? 0 }}%)</span>
                                                    <span
                                                        class="info-box-number text-center display-6">{{ number_format($invoice->vat_amount ?? 0, 2) }}
                                                        лв</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="info-box bg-success">
                                                <div class="info-box-content">
                                                    <span class="info-box-text text-center text-white">Обща сума</span>
                                                    <span
                                                        class="info-box-number text-center display-6 text-white">{{ number_format($invoice->grand_total ?? 0, 2) }}
                                                        лв</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <h4 class="mb-3">
                        <i class="fas fa-list mr-2"></i>Позиции
                    </h4>

                    @if ($invoice->items && count($invoice->items) > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover">
                                <thead class="thead-dark">
                                    <tr>
                                        <th width="40">№</th>
                                        <th>Описание</th>
                                        <th width="100">Мярка</th>
                                        <th width="100">Количество</th>
                                        <th width="120">Ед. цена</th>
                                        <th width="100">Отстъпка %</th>
                                        <th width="120">Отстъпка ст-т</th>
                                        <th width="120">Стойност</th>
                                        <th width="80">ДДС %</th>
                                        <th width="120">ДДС ст-т</th>
                                        <th width="140">Общо</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $totalSubtotal = 0;
                                        $totalVatAmount = 0;
                                        $totalGrandTotal = 0;
                                    @endphp

                                    @foreach ($invoice->items as $index => $item)
                                        @php
                                            $quantity = $item->quantity ?? 1;
                                            $unitPrice = $item->unit_price ?? 0;
                                            $discountPercent = $item->discount_percent ?? 0;
                                            $discountAmount = $quantity * $unitPrice * ($discountPercent / 100);
                                            $subtotal = $quantity * $unitPrice - $discountAmount;
                                            $vatPercent = $item->vat_percent ?? 20;
                                            $vatAmount = $subtotal * ($vatPercent / 100);
                                            $totalWithVat = $subtotal + $vatAmount;

                                            $totalSubtotal += $subtotal;
                                            $totalVatAmount += $vatAmount;
                                            $totalGrandTotal += $totalWithVat;
                                        @endphp
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td>{{ $item->description }}</td>
                                            <td class="text-center">{{ $item->unit ?? 'бр.' }}</td>
                                            <td class="text-right">{{ number_format($quantity, 2) }}</td>
                                            <td class="text-right">{{ number_format($unitPrice, 2) }} лв</td>
                                            <td class="text-right">{{ number_format($discountPercent, 2) }}%</td>
                                            <td class="text-right">{{ number_format($discountAmount, 2) }} лв</td>
                                            <td class="text-right">{{ number_format($subtotal, 2) }} лв</td>
                                            <td class="text-right">{{ number_format($vatPercent, 2) }}%</td>
                                            <td class="text-right">{{ number_format($vatAmount, 2) }} лв</td>
                                            <td class="text-right font-weight-bold">{{ number_format($totalWithVat, 2) }}
                                                лв</td>
                                        </tr>
                                    @endforeach

                                    <tr class="table-success font-weight-bold">
                                        <td colspan="7" class="text-right">Общо:</td>
                                        <td class="text-right">{{ number_format($totalSubtotal, 2) }} лв</td>
                                        <td class="text-right">-</td>
                                        <td class="text-right">{{ number_format($totalVatAmount, 2) }} лв</td>
                                        <td class="text-right">{{ number_format($totalGrandTotal, 2) }} лв</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle mr-2"></i>Няма добавени позиции към тази фактура.
                        </div>
                    @endif

                    @if ($invoice->notes)
                        <div class="mt-4">
                            <h5><i class="fas fa-sticky-note mr-2"></i>Бележки</h5>
                            <div class="alert alert-info">
                                {{ $invoice->notes }}
                            </div>
                        </div>
                    @endif
                </div>

                {{-- PAYMENTS --}}
                <div class="card card-outline card-success mt-4">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-money-bill-wave"></i> Плащания по фактурата
                        </h3>
                    </div>

                    <div class="card-body p-0">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Дата</th>
                                    <th>Метод</th>
                                    <th>Банка</th>
                                    <th class="text-end">Сума</th>
                                    <th>Референция</th>
                                    <th class="text-center">Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($invoice->payments as $payment)
                                    <tr>
                                        <td>{{ $payment->paid_at->format('d.m.Y') }}</td>
                                        <td>{{ $payment->method?->name }}</td>
                                        <td>{{ $payment->bank?->name ?? '—' }}</td>
                                        <td class="text-end text-success fw-bold">
                                            {{ number_format($payment->amount, 2, ',', ' ') }} лв.
                                        </td>
                                        <td>{{ $payment->reference }}</td>
                                        <td class="text-center">
                                            <form method="POST" action="{{ route('admin.payments.destroy', $payment) }}"
                                                onsubmit="return confirm('Сигурни ли сте, че искате да изтриете това плащане?')">
                                                @csrf
                                                @method('DELETE')

                                                <button class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">
                                            Няма въведени плащания
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="card-footer text-end">
                        <strong>Платено:</strong>
                        {{ number_format($invoice->paid_amount, 2, ',', ' ') }} лв.
                        &nbsp; | &nbsp;
                        <strong>Остава:</strong>
                        {{ number_format($invoice->remaining_amount, 2, ',', ' ') }} лв.
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@push('css')
<style>
    .info-box {
        border-radius: 8px;
        padding: 15px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
    }
    .info-box:hover { transform: translateY(-5px); }
</style>
@endpush
