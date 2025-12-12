@extends('adminlte::page')

@section('title', 'Права')

@section('content_header')
    <h1>Права</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <a href="{{ route('admin.permissions.create') }}" class="btn btn-success btn-sm float-right">Добави</a>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Име</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($permissions as $p)
                        <tr>
                            <td>{{ $p->id }}</td>
                            <td>{{ $p->name }}</td>
                            <td>
                                <a href="{{ route('admin.permissions.edit', $p) }}" class="btn btn-sm btn-primary">Редактирай</a>
                                <form action="{{ route('admin.permissions.destroy', $p) }}" method="POST" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Изтриване?')">Изтрий</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $permissions->links() }}
        </div>
    </div>
@stop