@extends('adminlte::page')

@section('title', 'Редактирай автомобил')

@section('content_header')
    <h1>Редактирай автомобил</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.vehicles.update', $vehicle) }}" method="POST">
                @csrf @method('PUT')
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Собственик</label>
                            <select name="customer_id" class="form-control select2" required>
                                @foreach($customers as $id => $name)
                                    <option value="{{ $id }}" {{ $vehicle->customer_id == $id ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>VIN</label>
                            <input type="text" name="vin" class="form-control" value="{{ old('vin', $vehicle->vin) }}" required>
                        </div>
                        <div class="form-group">
                            <label>Рег. №</label>
                            <input type="text" name="plate" class="form-control" value="{{ old('plate', $vehicle->plate) }}" required>
                        </div>
                        <div class="form-group">
                            <label>Марка</label>
                            <input type="text" name="make" class="form-control" value="{{ old('make', $vehicle->make) }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Модел</label>
                            <input type="text" name="model" class="form-control" value="{{ old('model', $vehicle->model) }}" required>
                        </div>
                        <div class="form-group">
                            <label>Година</label>
                            <input type="number" name="year" class="form-control" value="{{ old('year', $vehicle->year) }}" min="1900" max="{{ date('Y') }}">
                        </div>
                        <div class="form-group">
                            <label>Пробег</label>
                            <input type="number" name="mileage" class="form-control" value="{{ old('mileage', $vehicle->mileage) }}" min="0">
                        </div>
                        <div class="form-group">
                            <label>ДК №</label>
                            <input type="text" name="dk_no" class="form-control" value="{{ old('dk_no', $vehicle->dk_no) }}">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Бележки</label>
                    <textarea name="notes" class="form-control" rows="3">{{ old('notes', $vehicle->notes) }}</textarea>
                </div>
                <button type="submit" class="btn btn-success">Запази</button>
                <a href="{{ route('admin.vehicles.index') }}" class="btn btn-secondary">Отказ</a>
            </form>
        </div>
    </div>
@stop