@extends('adminlte::page')

@section('title', 'Добави автомобил')

@section('content_header')
    <h1>Добави автомобил</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.vehicles.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Собственик</label>
                            <select name="customer_id" class="form-control select2" required>
                                <option value="">Изберете клиент</option>
                                @foreach($customers as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>VIN</label>
                            <input type="text" name="vin" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Рег. №</label>
                            <input type="text" name="plate" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Марка</label>
                            <input type="text" name="make" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Модел</label>
                            <input type="text" name="model" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Година</label>
                            <input type="number" name="year" class="form-control" min="1900" max="{{ date('Y') }}">
                        </div>
                        <div class="form-group">
                            <label>Пробег</label>
                            <input type="number" name="mileage" class="form-control" min="0">
                        </div>
                        <div class="form-group">
                            <label>ДК №</label>
                            <input type="text" name="dk_no" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Бележки</label>
                    <textarea name="notes" class="form-control" rows="3"></textarea>
                </div>
                <button type="submit" class="btn btn-success">Запази</button>
                <a href="{{ route('admin.vehicles.index') }}" class="btn btn-secondary">Отказ</a>
            </form>
        </div>
    </div>
@stop