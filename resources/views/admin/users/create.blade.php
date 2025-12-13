@extends('adminlte::page')

@section('title', 'Добави потребител')

@section('content_header')
    <h1>Добави потребител</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Име</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Имейл</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Парола</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Повтори парола</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Роли</label>
                    <div class="row">
                        @foreach($roles as $id => $name)
                            <div class="col-md-3 col-sm-4">
                                <div class="icheck-primary d-inline">
                                    <input type="checkbox" id="role{{ $id }}" name="roles[]" value="{{ $id }}">
                                    <label for="role{{ $id }}" class="font-weight-normal">{{ $name }}</label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <button type="submit" class="btn btn-success">Запази</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Отказ</a>
            </form>
        </div>
    </div>
@stop