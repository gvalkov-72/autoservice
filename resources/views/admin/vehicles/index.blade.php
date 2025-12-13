@extends('adminlte::page')

@section('title', 'Автомобили')

@section('content_header')
    <h1>Автомобили</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <a href="{{ route('admin.vehicles.create') }}" class="btn btn-success btn-sm float-right">Добави автомобил</a>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Собственик</th>
                        <th>Рег. №</th>
                        <th>VIN</th>
                        <th>Марка / Модел</th>
                        <th>Година</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($vehicles as $v)
                        <tr>
                            <td>{{ $v->id }}</td>
                            <td>{{ $v->customer->name }}</td>
                            <td>{{ $v->plate }}</td>
                            <td>{{ $v->vin }}</td>
                            <td>{{ $v->make }} / {{ $v->model }}</td>
                            <td>{{ $v->year }}</td>
                            <td>
                                <a href="{{ route('admin.vehicles.show', $v) }}" class="btn btn-sm btn-info">Детайли</a>
                                <a href="{{ route('admin.vehicles.edit', $v) }}" class="btn btn-sm btn-primary">Редактирай</a>
                                <form action="{{ route('admin.vehicles.destroy', $v) }}" method="POST" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Изтриване?')">Изтрий</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $vehicles->links() }}
        </div>
    </div>
@stop