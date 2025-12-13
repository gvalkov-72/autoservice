@extends('adminlte::page')

@section('title', 'Ново плащане')

@section('content_header')
    <h1>Ново плащане</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.payments.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Фактура</label>
                    <select name="invoice_id" class="form-control select2" required>
                        <option value="">Изберете фактура</option>
                        @foreach($invoices as $id => $number)
                            <option value="{{ $id }}" {{ old('invoice_id') == $id ? 'selected' : '' }}>{{ $number }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Сума (лв.)</label>
                    <input type="number" name="amount" class="form-control" min="0.01" step="0.01" required>
                </div>
                <div class="form-group">
                    <label>Начин на плащане</label>
                    <select name="method" class="form-control" required>
                        <option value="cash">В брой</option>
                        <option value="card">Карта</option>
                        <option value="bank">По банка</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Дата на плащане</label>
                    <input type="date" name="paid_at" class="form-control" value="{{ today()->format('Y-m-d') }}" required>
                </div>
                <div class="form-group">
                    <label>Касов бон / реф.</label>
                    <input type="text" name="reference" class="form-control" maxlength="50">
                </div>
                <button type="submit" class="btn btn-success">Запази плащане</button>
                <a href="{{ route('admin.payments.index') }}" class="btn btn-secondary">Отказ</a>
            </form>
        </div>
    </div>
@stop