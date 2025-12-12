@extends('adminlte::page')

@section('title', 'Фактури')

@section('content_header')
    <h1>Фактури</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <a href="{{ route('admin.invoices.create') }}" class="btn btn-success btn-sm float-right">Нова фактура</a>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Номер</th>
                        <th>Клиент</th>
                        <th>Дата на издаване</th>
                        <th>Сума</th>
                        <th>Статус</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoices as $inv)
                        <tr>
                            <td>{{ $inv->number }}</td>
                            <td>{{ $inv->customer->name }}</td>
                            <td>{{ $inv->issue_date ? \Carbon\Carbon::parse($inv->issue_date)->format('d.m.Y') : '-' }}</td>
                            <td>{{ number_format($inv->grand_total, 2) }} лв.</td>
                            <td>{{ $inv->status }}</td>
                            <td>
                                <a href="{{ route('admin.invoices.show', $inv) }}" class="btn btn-sm btn-info">Преглед</a>
                                <a href="#" class="btn btn-sm btn-secondary">PDF</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $invoices->links() }}
        </div>
    </div>
@stop