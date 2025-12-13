@extends('adminlte::page')

@section('title', 'Клиенти')

@section('content_header')
    <h1>Клиенти</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <a href="{{ route('admin.customers.create') }}" class="btn btn-success btn-sm float-right">Добави клиент</a>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Име</th>
                        <th>Тип</th>
                        <th>Телефон</th>
                        <th>Имейл</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($customers as $c)
                        <tr>
                            <td>{{ $c->id }}</td>
                            <td>{{ $c->name }}</td>
                            <td>{{ $c->type == 'company' ? 'Фирма' : 'Физ. лице' }}</td>
                            <td>{{ $c->phone }}</td>
                            <td>{{ $c->email }}</td>
                            <td>
                                <a href="{{ route('admin.customers.show', $c) }}" class="btn btn-sm btn-info">Детайли</a>
                                <a href="{{ route('admin.customers.edit', $c) }}" class="btn btn-sm btn-primary">Редактирай</a>
                                <form action="{{ route('admin.customers.destroy', $c) }}" method="POST" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Изтриване?')">Изтрий</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $customers->links() }}
        </div>
    </div>
@stop