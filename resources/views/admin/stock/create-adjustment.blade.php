@extends('adminlte::page')

@section('title', 'Корекция на наличност')

@section('content_header')
    <h1>Корекция на наличност</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.stock.store-adjustment') }}" method="POST">
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
                    <label>Промяна (бр.)</label>
                    <input type="number" name="quantity" class="form-control" step="1" required>
                    <small class="form-text text-muted">Положително = добавяне, Отрицателно = намаляване</small>
                </div>
                <div class="form-group">
                    <label>Причина / Бележки <span class="text-danger">*</span></label>
                    <textarea name="notes" class="form-control" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn btn-warning">Запази корекция</button>
                <a href="{{ route('admin.stock.index') }}" class="btn btn-secondary">Отказ</a>
            </form>
        </div>
    </div>
@stop