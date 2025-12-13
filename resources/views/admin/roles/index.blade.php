@extends('adminlte::page')

@section('title', 'Роли')

@section('content_header')
    <h1>Роли</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <a href="{{ route('admin.roles.create') }}" class="btn btn-success btn-sm float-right">Добави</a>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Име</th>
                        <th>Права</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($roles as $r)
                        <tr>
                            <td>{{ $r->id }}</td>
                            <td>{{ $r->name }}</td>
                            <td>{{ $r->permissions->pluck('name')->join(', ') }}</td>
                            <td>
                                <a href="{{ route('admin.roles.edit', $r) }}" class="btn btn-sm btn-primary">Редактирай</a>
                                <form action="{{ route('admin.roles.destroy', $r) }}" method="POST" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Изтриване?')">Изтрий</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $roles->links() }}
        </div>
    </div>
@stop