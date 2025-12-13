@extends('adminlte::page')

@section('title', 'Редактирай клиент')

@section('content_header')
    <h1>Редактирай клиент</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.customers.update', $customer) }}" method="POST">
                @csrf @method('PUT')
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Тип</label>
                            <select name="type" class="form-control" required>
                                <option value="individual" {{ $customer->type == 'individual' ? 'selected' : '' }}>Физическо лице</option>
                                <option value="company" {{ $customer->type == 'company' ? 'selected' : '' }}>Фирма</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Име / Фирма</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $customer->name) }}" required>
                        </div>
                        <div class="form-group">
                            <label>ДДС №</label>
                            <input type="text" name="vat_number" class="form-control" value="{{ old('vat_number', $customer->vat_number) }}">
                        </div>
                        <div class="form-group">
                            <label>Контактно лице</label>
                            <input type="text" name="contact_person" class="form-control" value="{{ old('contact_person', $customer->contact_person) }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Телефон</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $customer->phone) }}">
                        </div>
                        <div class="form-group">
                            <label>Имейл</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $customer->email) }}">
                        </div>
                        <div class="form-group">
                            <label>Адрес</label>
                            <input type="text" name="address" class="form-control" value="{{ old('address', $customer->address) }}">
                        </div>
                        <div class="form-group">
                            <label>Град</label>
                            <input type="text" name="city" class="form-control" value="{{ old('city', $customer->city) }}">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Бележки</label>
                    <textarea name="notes" class="form-control" rows="3">{{ old('notes', $customer->notes) }}</textarea>
                </div>
                <button type="submit" class="btn btn-success">Запази</button>
                <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary">Отказ</a>
            </form>
        </div>
    </div>
@stop