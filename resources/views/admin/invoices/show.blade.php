@extends('adminlte::page')

@section('title', 'Преглед на фактура')

@section('content_header')
    <h1>Фактура № {{ $invoice->number }}</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <p><strong>Клиент:</strong> {{ $invoice->customer->name }}</p>
                    <p><strong>Дата на издаване:</strong>
                        {{ $invoice->issue_date ? \Carbon\Carbon::parse($invoice->issue_date)->format('d.m.Y') : '-' }}</p>
                    <p><strong>Падеж:</strong>
                        {{ $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('d.m.Y') : '-' }}</p>
                </div>
                <div class="col-md-4">
                    <p><strong>Поръчка:</strong> {{ $invoice->workOrder->number ?? '-' }}</p>
                    <p><strong>Начин на плащане:</strong> {{ $invoice->payment_method ?? '-' }}</p>
                    <p><strong>Статус:</strong> {{ $invoice->status }}</p>
                </div>
                <div class="col-md-4">
                    <p><strong>Общо без ДДС:</strong> {{ number_format($invoice->subtotal, 2) }} лв.</p>
                    <p><strong>ДДС:</strong> {{ number_format($invoice->vat_total, 2) }} лв.</p>
                    <p><strong>Общо:</strong> {{ number_format($invoice->grand_total, 2) }} лв.</p>
                </div>
            </div>
            <hr>
            <h4>Позиции</h4>
            <table class="table table-sm table-bordered">
                <thead>
                    <tr>
                        <th>№</th>
                        <th>Описание</th>
                        <th>Количество</th>
                        <th>Цена (без ДДС)</th>
                        <th>ДДС %</th>
                        <th>Общо</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($invoice->items as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->description }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ number_format($item->unit_price, 2) }} лв.</td>
                            <td>{{ $item->vat_percent }} %</td>
                            <td>{{ number_format($item->line_total, 2) }} лв.</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            <a href="{{ route('admin.invoices.edit', $invoice) }}" class="btn btn-primary">Редактирай</a>
            <a href="#" class="btn btn-secondary">PDF</a>
            <a href="{{ route('admin.invoices.index') }}" class="btn btn-secondary">Назад</a>
        </div>
    </div>
@stop
