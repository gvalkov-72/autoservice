@extends('adminlte::page')

@section('title', 'Услуги')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Услуги</h1>
        <a href="{{ route('admin.services.create') }}" class="btn btn-success">
            <i class="fas fa-plus mr-1"></i> Нова услуга
        </a>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Код</th>
                            <th>Име</th>
                            <th>Категория</th>
                            <th>Цена</th>
                            <th>ДДС</th>
                            <th>Продължителност</th>
                            <th>Статус</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($services as $service)
                            <tr>
                                <td>
                                    <strong>{{ $service->code }}</strong>
                                </td>
                                <td>{{ $service->name }}</td>
                                <td>
                                    @if($service->category)
                                        <span class="badge badge-info">{{ $service->category->name }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ number_format($service->price, 2) }} лв.</strong>
                                </td>
                                <td>{{ $service->vat_percent }}%</td>
                                <td>
                                    @if($service->duration_minutes)
                                        {{ $service->duration_minutes }} мин.
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($service->is_active)
                                        <span class="badge badge-success">Активна</span>
                                    @else
                                        <span class="badge badge-secondary">Неактивна</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.services.show', $service) }}" 
                                           class="btn btn-info" title="Преглед">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.services.edit', $service) }}" 
                                           class="btn btn-warning" title="Редактирай">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.services.destroy', $service) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" 
                                                    title="Изтрий"
                                                    onclick="return confirm('Сигурни ли сте?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3">
                {{ $services->links() }}
            </div>
        </div>
    </div>
@stop