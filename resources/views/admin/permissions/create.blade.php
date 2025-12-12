@extends('adminlte::page')

@section('title', 'Добави право')

@section('content_header')
    <h1>Добави право</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.permissions.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Име на право</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-success">Запази</button>
                <a href="{{ route('admin.permissions.index') }}" class="btn btn-secondary">Отказ</a>
            </form>
        </div>
    </div>
@stop