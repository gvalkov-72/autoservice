@extends('adminlte::page')

@section('title', 'Нова доставка')

@section('content_header')
    <h1>Нова доставка</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.stock.store-purchase') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Артикул</label>
                    <select name="product_id" class="form-control select2" required>
                        <option value="">Изберете артикул</option>
                        @foreach($products as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Количество</label>
                    <input type="number" name="quantity" class="form-control" min="1" step="1" required>
                </div>
                <div class="form-group">
                    <label>Цена на доставка (за бр.)</label>
                    <input type="number" name="cost_price" class="form-control" min="0" step="0.01">
                </div>
                <div class="form-group">
                    <label>Бележки</label>
                    <textarea name="notes" class="form-control" rows="3"></textarea>
                </div>
                <button type="submit" class="btn btn-success">Запази доставка</button>
                <a href="{{ route('admin.stock.index') }}" class="btn btn-secondary">Отказ</a>
            </form>
        </div>
    </div>
@stop