@extends('adminlte::page')

@section('title', 'Нов автомобил')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Нов автомобил</h1>
        <div>
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
                    <h3 class="card-title">Данни за автомобила</h3>
                </div>
                
                <form action="{{ route('admin.vehicles.store') }}" method="POST" id="vehicle-form">
                    @csrf
                    
                    <div class="card-body">
                        {{-- ИЗБОР НА КЛИЕНТ --}}
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5><i class="fas fa-user mr-2"></i>Избор на клиент</h5>
                                <hr>
                                
                                <div class="form-group">
                                    <label for="customer_id">Клиент *</label>
                                    <select class="form-control @error('customer_id') is-invalid @enderror" 
                                            id="customer_id" name="customer_id" required>
                                        <option value="">Изберете клиент</option>
                                        @foreach($customers as $cust)
                                            <option value="{{ $cust->id }}" 
                                                {{ old('customer_id', $customer ? $customer->id : '') == $cust->id ? 'selected' : '' }}>
                                                {{ $cust->name }}
                                                @if($cust->phone)
                                                    ({{ $cust->phone }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('customer_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        {{-- ОСНОВНА ИНФОРМАЦИЯ ЗА АВТОМОБИЛА --}}
                        <div class="row">
                            <div class="col-md-6">
                                <h5><i class="fas fa-car mr-2"></i>Основни данни</h5>
                                <hr>
                                
                                <div class="form-group">
                                    <label for="vehicle">Марка и модел</label>
                                    <input type="text" class="form-control @error('vehicle') is-invalid @enderror" 
                                           id="vehicle" name="vehicle" value="{{ old('vehicle') }}"
                                           placeholder="Пример: Volkswagen Golf 2.0 TDI">
                                    @error('vehicle')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                
                                <div class="form-group">
                                    <label for="plate_number">Регистрационен номер *</label>
                                    <input type="text" class="form-control @error('plate_number') is-invalid @enderror text-uppercase" 
                                           id="plate_number" name="plate_number" value="{{ old('plate_number') }}" required
                                           placeholder="Пример: AB 1234 CD">
                                    @error('plate_number')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                
                                <div class="form-group">
                                    <label for="chassis_number">VIN номер / Номер на рама</label>
                                    <input type="text" class="form-control @error('chassis_number') is-invalid @enderror" 
                                           id="chassis_number" name="chassis_number" value="{{ old('chassis_number') }}"
                                           placeholder="17-цифрен VIN номер">
                                    @error('chassis_number')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <h5><i class="fas fa-cogs mr-2"></i>Допълнителни данни</h5>
                                <hr>
                                
                                <div class="form-group">
                                    <label for="last_mileage">Последен известен пробег (км)</label>
                                    <input type="number" class="form-control @error('last_mileage') is-invalid @enderror" 
                                           id="last_mileage" name="last_mileage" value="{{ old('last_mileage') }}"
                                           placeholder="Въведете километри" min="0">
                                    @error('last_mileage')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                
                                <div class="form-group">
                                    <label for="notes">Бележки</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                                              id="notes" name="notes" rows="5">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                
                                <div class="form-check mt-3">
                                    <input type="checkbox" class="form-check-input" 
                                           id="is_active" name="is_active" value="1" 
                                           {{ old('is_active', 1) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">Активен автомобил</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Запази автомобил
                        </button>
                        <a href="{{ route('admin.vehicles.index') }}" class="btn btn-secondary">Отказ</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        $(function() {
            // Автоматично форматиране на регистрационния номер
            $('#plate_number').on('blur', function() {
                let plate = $(this).val().toUpperCase().replace(/\s+/g, ' ');
                $(this).val(plate);
            });
            
            // Автоматично форматиране на VIN номера
            $('#chassis_number').on('blur', function() {
                let vin = $(this).val().toUpperCase();
                $(this).val(vin);
            });
            
            // Валидация на формата
            $('#vehicle-form').on('submit', function() {
                const plateNumber = $('#plate_number').val().trim();
                if (!plateNumber) {
                    alert('Моля, въведете регистрационен номер!');
                    $('#plate_number').focus();
                    return false;
                }
                
                const customerId = $('#customer_id').val();
                if (!customerId) {
                    alert('Моля, изберете клиент!');
                    $('#customer_id').focus();
                    return false;
                }
                
                return true;
            });
        });
    </script>
@stop