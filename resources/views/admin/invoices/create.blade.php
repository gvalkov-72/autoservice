@extends('adminlte::page')

@section('title', 'Нова фактура')

@section('content_header')
    <h1>Нова фактура</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.invoices.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Клиент</label>
                            <select name="customer_id" class="form-control select2" required>
                                <option value="">Изберете клиент</option>
                                @foreach(\App\Models\Customer::pluck('name', 'id') as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Поръчка (по избор)</label>
                            <select name="work_order_id" class="form-control">
                                <option value="">Без поръчка</option>
                                @foreach(\App\Models\WorkOrder::doesntHave('invoices')->get() as $wo)
                                    <option value="{{ $wo->id }}">{{ $wo->number }} - {{ $wo->customer->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Дата на издаване</label>
                            <input type="date" name="issue_date" class="form-control" value="{{ today()->format('Y-m-d') }}" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Дата на падеж</label>
                            <input type="date" name="due_date" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Начин на плащане</label>
                            <input type="text" name="payment_method" class="form-control" placeholder="В брой / По банка">
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-success">Създай фактура</button>
                <a href="{{ route('admin.invoices.index') }}" class="btn btn-secondary">Отказ</a>
            </form>
        </div>
    </div>
@stop