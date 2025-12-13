@extends('adminlte::page')

@section('title', 'Месечен отчет')

@section('content_header')
    <h1>Месечен отчет</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <form method="GET" class="form-inline">
                <label for="month" class="mr-2">Месец:</label>
                <input type="month" name="month" id="month" value="{{ $month }}" class="form-control mr-2">
                <button type="submit" class="btn btn-primary">Филтрирай</button>
            </form>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <tr>
                    <th>Брой фактури</th>
                    <td>{{ $invoices->count() }}</td>
                </tr>
                <tr>
                    <th>Общо приходи (с ДДС)</th>
                    <td>{{ number_format($totalSales, 2) }} лв.</td>
                </tr>
                <tr>
                    <th>ДДС</th>
                    <td>{{ number_format($vatTotal, 2) }} лв.</td>
                </tr>
            </table>
        </div>
    </div>
@stop