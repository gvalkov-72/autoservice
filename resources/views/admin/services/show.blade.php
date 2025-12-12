@extends('adminlte::page')

@section('title', 'Услуга: ' . $service->name)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Услуга: {{ $service->name }}</h1>
        <div>
            <a href="{{ route('admin.services.edit', $service) }}" class="btn btn-warning">
                <i class="fas fa-edit mr-1"></i> Редактирай
            </a>
            <a href="{{ route('admin.services.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Назад
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Основна информация</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Код:</th>
                            <td><strong>{{ $service->code }}</strong></td>
                        </tr>
                        <tr>
                            <th>Име:</th>
                            <td>{{ $service->name }}</td>
                        </tr>
                        <tr>
                            <th>Категория:</th>
                            <td>
                                @if($service->category)
                                    <span class="badge badge-info">{{ $service->category->name }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Описание:</th>
                            <td>{{ $service->description ?: '-' }}</td>
                        </tr>
                        <tr>
                            <th>Цена (без ДДС):</th>
                            <td><strong>{{ number_format($service->price, 2) }} лв.</strong></td>
                        </tr>
                        <tr>
                            <th>ДДС:</th>
                            <td>{{ $service->vat_percent }}%</td>
                        </tr>
                        <tr>
                            <th>Цена с ДДС:</th>
                            <td><strong class="text-success">{{ number_format($service->price_with_vat, 2) }} лв.</strong></td>
                        </tr>
                        <tr>
                            <th>Продължителност:</th>
                            <td>
                                @if($service->duration_minutes)
                                    {{ $service->duration_minutes }} минути
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Статус:</th>
                            <td>
                                @if($service->is_active)
                                    <span class="badge badge-success">Активна</span>
                                @else
                                    <span class="badge badge-secondary">Неактивна</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            
            @if($service->notes)
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">Бележки</h3>
                </div>
                <div class="card-body">
                    {{ $service->notes }}
                </div>
            </div>
            @endif
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Детайли</h3>
                </div>
                <div class="card-body">
                    <div class="info-box">
                        <span class="info-box-icon bg-info">
                            <i class="fas fa-calendar-alt"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Създадена на</span>
                            <span class="info-box-number">
                                {{ $service->created_at->format('d.m.Y H:i') }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="info-box mt-2">
                        <span class="info-box-icon bg-success">
                            <i class="fas fa-sync-alt"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Последна промяна</span>
                            <span class="info-box-number">
                                {{ $service->updated_at->format('d.m.Y H:i') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">Действия</h3>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.services.edit', $service) }}" 
                           class="btn btn-warning btn-block">
                            <i class="fas fa-edit mr-1"></i> Редактирай услуга
                        </a>
                        
                        <form action="{{ route('admin.services.destroy', $service) }}" 
                              method="POST" class="d-grid">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-block"
                                    onclick="return confirm('Сигурни ли сте, че искате да изтриете тази услуга?')">
                                <i class="fas fa-trash mr-1"></i> Изтрий услуга
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop