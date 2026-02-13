@extends('adminlte::page')

@section('title', 'Преглед на автомобил: ' . $vehicle->plate_number)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Преглед на автомобил: {{ $vehicle->plate_number }}</h1>
        <div>
            <a href="{{ route('admin.vehicles.edit', $vehicle->id) }}" class="btn btn-warning btn-sm mr-2">
                <i class="fas fa-edit"></i> Редактирай
            </a>
            <a href="{{ route('admin.vehicles.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Назад
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-car mr-2"></i>Информация за автомобил
                        <span class="badge {{ $vehicle->is_active ? 'bg-success' : 'bg-secondary' }} ml-2">
                            {{ $vehicle->is_active ? 'АКТИВЕН' : 'НЕАКТИВЕН' }}
                        </span>
                    </h3>
                </div>
                
                <div class="card-body">
                    {{-- ОСНОВНА ИНФОРМАЦИЯ --}}
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5><i class="fas fa-info-circle mr-2"></i>Основни данни</h5>
                            <hr>
                            
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <th style="width: 180px;">Рег. номер:</th>
                                    <td><strong>{{ $vehicle->plate_number }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Марка/Модел:</th>
                                    <td>{{ $vehicle->vehicle ?: '—' }}</td>
                                </tr>
                                <tr>
                                    <th>VIN номер:</th>
                                    <td>
                                        @if($vehicle->chassis_number)
                                            <span class="font-monospace">{{ $vehicle->chassis_number }}</span>
                                        @else
                                            —
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Пробег:</th>
                                    <td>
                                        @if($vehicle->last_mileage)
                                            {{ number_format($vehicle->last_mileage, 0, ',', ' ') }} км
                                        @else
                                            —
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Създаден на:</th>
                                    <td>{{ $vehicle->created_at->format('d.m.Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Последна промяна:</th>
                                    <td>{{ $vehicle->updated_at->format('d.m.Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            <h5><i class="fas fa-user mr-2"></i>Собственик</h5>
                            <hr>
                            
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <th style="width: 180px;">Клиент:</th>
                                    <td>
                                        @if($vehicle->customer)
                                            <a href="{{ route('admin.customers.show', $vehicle->customer_id) }}">
                                                <strong>{{ $vehicle->customer->name }}</strong>
                                            </a>
                                        @else
                                            —
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Телефон:</th>
                                    <td>
                                        @if($vehicle->customer && $vehicle->customer->phone)
                                            {{ $vehicle->customer->phone }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Адрес:</th>
                                    <td>
                                        @if($vehicle->customer && $vehicle->customer->address)
                                            {{ $vehicle->customer->address }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>ДДС номер:</th>
                                    <td>
                                        @if($vehicle->customer && $vehicle->customer->tax_number)
                                            {{ $vehicle->customer->tax_number }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    {{-- БЕЛЕЖКИ --}}
                    @if($vehicle->notes)
                    <div class="row">
                        <div class="col-md-12">
                            <h5><i class="fas fa-sticky-note mr-2"></i>Бележки</h5>
                            <hr>
                            <div class="p-3 bg-light rounded">
                                {!! nl2br(e($vehicle->notes)) !!}
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
                
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <div>
                            <a href="{{ route('admin.vehicles.edit', $vehicle->id) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Редактирай
                            </a>
                            <a href="{{ route('admin.vehicles.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Назад към списъка
                            </a>
                        </div>
                        
                        <div>
                            <button type="button" class="btn btn-danger" 
                                    onclick="if(confirm('Сигурни ли сте, че искате да изтриете този автомобил?')) {
                                        document.getElementById('delete-form').submit();
                                    }">
                                <i class="fas fa-trash"></i> Изтрий автомобил
                            </button>
                        </div>
                    </div>
                </div>
                
                {{-- Скрыта форма за изтриване --}}
                <form id="delete-form" action="{{ route('admin.vehicles.destroy', $vehicle->id) }}" 
                      method="POST" style="display: none;">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    </div>
@stop