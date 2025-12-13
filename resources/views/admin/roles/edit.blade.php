@extends('adminlte::page')

@section('title', 'Редактирай роля')

@section('content_header')
    <h1>Редактирай роля</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.roles.update', $role) }}" method="POST">
                @csrf @method('PUT')
                <div class="form-group">
                    <label>Име на роля</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $role->name) }}" required>
                </div>

                <div class="form-group">
                    <label>Права</label>
                    <div class="row">
                        @foreach($permissions as $id => $name)
                            <div class="col-md-3 col-sm-4">
                                <div class="icheck-primary d-inline">
                                    <input type="checkbox" id="perm{{ $id }}" name="permissions[]" value="{{ $id }}"
                                        {{ $role->permissions->contains($id) ? 'checked' : '' }}>
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