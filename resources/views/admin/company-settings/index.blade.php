@extends('adminlte::page')

@section('title', 'Данни на Автосервиза')

@section('content_header')
    <h1>Данни на Автосервиза</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <a href="{{ route('admin.company-settings.create') }}" class="btn btn-success btn-sm float-right">Добави данни</a>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Име на фирма</th>
                        <th>Град</th>
                        <th>ЕИК</th>
                        <th>МОЛ</th>
                        <th>Статус</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($companySettings as $setting)
                        <tr>
                            <td>{{ $setting->id }}</td>
                            <td>{{ $setting->name }}</td>
                            <td>{{ $setting->city }}</td>
                            <td>{{ $setting->vat_number }}</td>
                            <td>{{ $setting->contact_person }}</td>
                            <td>
                                @if($setting->is_active)
                                    <span class="badge badge-success">Активен</span>
                                @else
                                    <span class="badge badge-secondary">Неактивен</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.company-settings.show', $setting) }}" class="btn btn-sm btn-info">Детайли</a>
                                <a href="{{ route('admin.company-settings.edit', $setting) }}" class="btn btn-sm btn-primary">Редактирай</a>
                                @if(!$setting->is_active)
                                <form action="{{ route('admin.company-settings.destroy', $setting) }}" method="POST" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Сигурни ли сте?')">Изтрий</button>
                                </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $companySettings->links() }}
        </div>
    </div>
@stop