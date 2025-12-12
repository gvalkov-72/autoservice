@extends('adminlte::page')

@section('title', 'Наличности')

@section('content_header')
    <h1>Наличности</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Ниски наличности</h3>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead>
                            <tr><th>Код</th><th>Артикул</th><th>Наличност</th></tr>
                        </thead>
                        <tbody>
                            @foreach($lowStock as $p)
                                <tr>
                                    <td>{{ $p->sku }}</td>
                                    <td>{{ $p->name }}</td>
                                    <td><span class="badge badge-warning">{{ $p->stock_quantity }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Бързи операции</h3>
                </div>
                <div class="card-body">
                    <a href="{{ route('admin.stock.create-purchase') }}" class="btn btn-success btn-block">Нова доставка</a>
                    <a href="{{ route('admin.stock.create-adjustment') }}" class="btn btn-warning btn-block">Корекция на наличност</a>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header">
            <h3 class="card-title">История на движенията</h3>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Артикул</th>
                        <th>Промяна</th>
                        <th>Тип</th>
                        <th>От</th>
                        <th>Дата</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($movements as $m)
                        <tr>
                            <td>{{ $m->id }}</td>
                            <td>{{ $m->product->name }}</td>
                            <td>{{ $m->change }}</td>
                            <td>{{ $m->type }}</td>
                            <td>{{ $m->creator->name }}</td>
                            <td>{{ $m->created_at->format('d.m.Y H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $movements->links() }}
        </div>
    </div>
@stop