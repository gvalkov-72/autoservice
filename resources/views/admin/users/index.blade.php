@extends('adminlte::page')

@section('title', 'Потребители')

@section('content_header')
    <h1>Потребители</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <a href="{{ route('admin.users.create') }}" class="btn btn-success btn-sm float-right">Добави</a>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Име</th>
                        <th>Имейл</th>
                        <th>Роли</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $u)
                        <tr>
                            <td>{{ $u->id }}</td>
                            <td>{{ $u->name }}</td>
                            <td>{{ $u->email }}</td>
                            <td>{{ $u->roles->pluck('name')->join(', ') }}</td>
                            <td>
                                <a href="{{ route('admin.users.edit', $u) }}" class="btn btn-sm btn-primary">Редактирай</a>
                                <form action="{{ route('admin.users.destroy', $u) }}" method="POST" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Изтриване?')">Изтрий</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $users->links() }}
        </div>
    </div>
@stop