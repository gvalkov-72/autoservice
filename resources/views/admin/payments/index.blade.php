@extends('adminlte::page')

@section('title', 'Плащания')

@section('content_header')
    <h1>Плащания</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <a href="{{ route('admin.payments.create') }}" class="btn btn-success btn-sm float-right">Ново плащане</a>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Фактура</th>
                        <th>Клиент</th>
                        <th>Сума</th>
                        <th>Метод</th>
                        <th>Дата</th>
                        <th>Касов бон</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payments as $p)
                        <tr>
                            <td>{{ $p->id }}</td>
                            <td>{{ $p->invoice->number }}</td>
                            <td>{{ $p->invoice->customer->name }}</td>
                            <td>{{ number_format($p->amount, 2) }} лв.</td>
                            <td>{{ $p->method }}</td>
                            <td>{{ $p->paid_at->format('d.m.Y') }}</td>
                            <td>{{ $p->reference ?? '-' }}</td>
                            <td>
                                <a href="{{ route('admin.payments.pdf', $p) }}" class="btn btn-sm btn-info" target="_blank">PDF</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $payments->links() }}
        </div>
    </div>
@stop