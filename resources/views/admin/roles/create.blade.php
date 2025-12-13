@extends('adminlte::page')

@section('title', 'Добави роля')

@section('content_header')
    <h1>Добави роля</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.roles.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Име на роля</label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Права</label>
                    <div class="row">
                        @foreach($permissions as $id => $name)
                            <div class="col-md-3 col-sm-4">
                                <div class="icheck-primary d-inline">
                                    <input type="checkbox" id="perm{{ $id }}" name="permissions[]" value="{{ $id }}">
                                    <label for="perm{{ $id }}" class="font-weight-normal">{{ $name }}</label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <button type="submit" class="btn btn-success">Запази</button>
                <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">Отказ</a>
            </form>
        </div>
    </div>
@stop