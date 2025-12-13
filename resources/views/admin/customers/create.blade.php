@extends('adminlte::page')

@section('title', 'Добави клиент')

@section('content_header')
    <h1>Добави клиент</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.customers.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Тип</label>
                            <select name="type" class="form-control" required>
                                <option value="individual">Физическо лице</option>
                                <option value="company">Фирма</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Име / Фирма</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>ДДС №</label>
                            <input type="text" name="vat_number" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Контактно лице</label>
                            <input type="text" name="contact_person" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Телефон</label>
                            <input type="text" name="phone" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Имейл</label>
                            <input type="email" name="email" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Адрес</label>
                            <input type="text" name="address" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Град</label>
                            <input type="text" name="city" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Бележки</label>
                    <textarea name="notes" class="form-control" rows="3"></textarea>
                </div>
                <button type="submit" class="btn btn-success">Запази</button>
                <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary">Отказ</a>
            </form>
        </div>
    </div>
@stop