@extends('adminlte::page')

@section('title', 'Добави артикул')

@section('content_header')
    <h1>Добави артикул</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.products.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Код (SKU)</label>
                            <input type="text" name="sku" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Наименование</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Марка</label>
                            <input type="text" name="brand" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Описание</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Мерна единица</label>
                            <input type="text" name="unit" class="form-control" value="бр.">
                        </div>
                        <div class="form-group">
                            <label>Цена (без ДДС)</label>
                            <input type="number" name="price" class="form-control" step="0.01" min="0" required>
                        </div>
                        <div class="form-group">
                            <label>ДДС %</label>
                            <input type="number" name="vat_percent" class="form-control" value="20" step="0.01" min="0" max="100">
                        </div>
                        <div class="form-group">
                            <label>Наличност</label>
                            <input type="number" name="stock_quantity" class="form-control" value="0" min="0">
                        </div>
                        <div class="form-group">
                            <label>Мин. наличност</label>
                            <input type="number" name="min_stock_level" class="form-control" value="0" min="0">
                        </div>
                        <div class="form-group">
                            <label>Местоположение</label>
                            <input type="text" name="location" class="form-control">
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-success">Запази</button>
                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Отказ</a>
            </form>
        </div>
    </div>
@stop