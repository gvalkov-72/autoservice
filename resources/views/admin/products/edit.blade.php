@extends('adminlte::page')

@section('title', 'Редактирай артикул')

@section('content_header')
    <h1>Редактирай артикул</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.products.update', $product) }}" method="POST">
                @csrf @method('PUT')
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Код (SKU)</label>
                            <input type="text" name="sku" class="form-control"
                                   value="{{ old('sku', $product->sku) }}" required>
                        </div>

                        <div class="form-group">
                            <label>Наименование</label>
                            <input type="text" name="name" class="form-control"
                                   value="{{ old('name', $product->name) }}" required>
                        </div>

                        <div class="form-group">
                            <label>Марка</label>
                            <input type="text" name="brand" class="form-control"
                                   value="{{ old('brand', $product->brand) }}">
                        </div>

                        <div class="form-group">
                            <label>Описание</label>
                            <textarea name="description" class="form-control" rows="3">{{ old('description', $product->description) }}</textarea>
                        </div>
                    </div>

                    <div class="col-md-6">

                        <div class="form-group">
                            <label>Мерна единица</label>
                            <input type="text" name="unit" class="form-control"
                                   value="{{ old('unit', $product->unit) }}">
                        </div>

                        <div class="form-group">
                            <label>Цена (без ДДС)</label>
                            <input type="number" name="price" class="form-control" step="0.01" min="0"
                                   value="{{ old('price', number_format($product->price, 2, '.', '')) }}"
                                   required>
                        </div>

                        <div class="form-group">
                            <label>ДДС %</label>
                            <input type="number" name="vat_percent" class="form-control" step="0.01" min="0" max="100"
                                   value="{{ old('vat_percent', $product->vat_percent) }}">
                        </div>

                        <div class="form-group">
                            <label>Наличност</label>
                            <input type="number" name="stock_quantity" class="form-control" min="0"
                                   value="{{ old('stock_quantity', $product->stock_quantity) }}">
                        </div>

                        <div class="form-group">
                            <label>Мин. наличност</label>
                            <input type="number" name="min_stock_level" class="form-control" min="0"
                                   value="{{ old('min_stock_level', $product->min_stock_level) }}">
                        </div>

                        <div class="form-group">
                            <label>Местоположение</label>
                            <input type="text" name="location" class="form-control"
                                   value="{{ old('location', $product->location) }}">
                        </div>

                    </div>
                </div>

                <button type="submit" class="btn btn-success">Запази</button>
                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Отказ</a>
            </form>
        </div>
    </div>
@stop
