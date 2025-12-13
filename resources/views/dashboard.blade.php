@extends('adminlte::page')

@section('title', 'Начало – Автосервиз')

@section('content_header')
    <h1>Дашборд</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ \App\Models\Customer::count() }}</h3>
                    <p>Клиенти</p>
                </div>
                <div class="icon"><i class="fas fa-users"></i></div>
                <a href="{{ route('admin.customers.index') }}" class="small-box-footer">Повече <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ \App\Models\Vehicle::count() }}</h3>
                    <p>Автомобили</p>
                </div>
                <div class="icon"><i class="fas fa-car"></i></div>
                <a href="{{ route('admin.vehicles.index') }}" class="small-box-footer">Повече <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ \App\Models\Product::count() }}</h3>
                    <p>Артикули</p>
                </div>
                <div class="icon"><i class="fas fa-cubes"></i></div>
                <a href="{{ route('admin.products.index') }}" class="small-box-footer">Повече <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ \App\Models\WorkOrder::count() }}</h3>
                    <p>Активни поръчки</p>
                </div>
                <div class="icon"><i class="fas fa-clipboard-list"></i></div>
                <a href="{{ route('admin.work-orders.index') }}" class="small-box-footer">Повече <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Последни поръчки</h3>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>№</th>
                                <th>Клиент</th>
                                <th>Статус</th>
                                <th>Дата</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(\App\Models\WorkOrder::with('customer')->latest()->limit(5)->get() as $wo)
                                <tr>
                                    <td>{{ $wo->number }}</td>
                                    <td>{{ $wo->customer->name }}</td>
                                    <td>{{ $wo->status }}</td>
                                    <td>{{ $wo->created_at->format('d.m.Y') }}</td>
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
                    <h3 class="card-title">Ниски наличности</h3>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Код</th>
                                <th>Артикул</th>
                                <th>Наличност</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(\App\Models\Product::whereRaw('stock_quantity <= min_stock_level')->limit(5)->get() as $p)
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
    </div>
@stop