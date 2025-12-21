@extends('adminlte::page')

@section('title', 'Добави превозно средство')

@section('content_header')
    <h1>Добави превозно средство</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.vehicles.store') }}" method="POST">
                @csrf

                <div class="row">
                    <!-- Лява колона -->
                    <div class="col-md-6">
                        <!-- Основна информация -->
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">Основна информация</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Клиент *</label>
                                    <select name="customer_id" class="form-control select2 @error('customer_id') is-invalid @enderror" required style="width: 100%;">
                                        <option value="">Изберете клиент...</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                                {{ $customer->name }} 
                                                @if($customer->phone)
                                                    ({{ $customer->phone }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('customer_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Регистрационен номер *</label>
                                            <input type="text" name="plate" 
                                                   class="form-control @error('plate') is-invalid @enderror" 
                                                   value="{{ old('plate') }}" 
                                                   placeholder="CA1234BC" required>
                                            @error('plate') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>ДК номер</label>
                                            <input type="text" name="dk_no" 
                                                   class="form-control @error('dk_no') is-invalid @enderror" 
                                                   value="{{ old('dk_no') }}" 
                                                   placeholder="ДК номер">
                                            @error('dk_no') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Марка *</label>
                                            <input type="text" name="make" 
                                                   class="form-control @error('make') is-invalid @enderror" 
                                                   value="{{ old('make') }}" 
                                                   placeholder="Напр. BMW, Mercedes" required>
                                            @error('make') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Модел *</label>
                                            <input type="text" name="model" 
                                                   class="form-control @error('model') is-invalid @enderror" 
                                                   value="{{ old('model') }}" 
                                                   placeholder="Напр. X5, C220" required>
                                            @error('model') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Година</label>
                                            <input type="number" name="year" 
                                                   class="form-control @error('year') is-invalid @enderror" 
                                                   value="{{ old('year') }}" 
                                                   min="1900" max="{{ date('Y') }}"
                                                   placeholder="Напр. 2020">
                                            @error('year') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Пробег (км)</label>
                                            <input type="number" name="mileage" 
                                                   class="form-control @error('mileage') is-invalid @enderror" 
                                                   value="{{ old('mileage') }}" 
                                                   min="0"
                                                   placeholder="Напр. 150000">
                                            @error('mileage') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Идентификационни номера -->
                        <div class="card card-info card-outline mt-3">
                            <div class="card-header">
                                <h3 class="card-title">Идентификационни номера</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label>VIN номер</label>
                                    <input type="text" name="vin" 
                                           class="form-control @error('vin') is-invalid @enderror" 
                                           value="{{ old('vin') }}"
                                           placeholder="17-цифрен VIN номер">
                                    @error('vin') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="form-group">
                                    <label>Шаси номер</label>
                                    <input type="text" name="chassis" 
                                           class="form-control @error('chassis') is-invalid @enderror" 
                                           value="{{ old('chassis') }}">
                                    @error('chassis') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="form-group">
                                    <label>Код на монитора</label>
                                    <input type="text" name="monitor_code" 
                                           class="form-control @error('monitor_code') is-invalid @enderror" 
                                           value="{{ old('monitor_code') }}">
                                    @error('monitor_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Дясна колона -->
                    <div class="col-md-6">
                        <!-- Метаданни от старата система -->
                        <div class="card card-success card-outline">
                            <div class="card-header">
                                <h3 class="card-title">Метаданни от стара система</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Поръчка (от Access)</label>
                                    <input type="text" name="order_reference" 
                                           class="form-control @error('order_reference') is-invalid @enderror" 
                                           value="{{ old('order_reference') }}">
                                    @error('order_reference') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Дата на поръчка (PODate)</label>
                                            <input type="date" name="po_date" 
                                                   class="form-control @error('po_date') is-invalid @enderror" 
                                                   value="{{ old('po_date') }}">
                                            @error('po_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Автор (Author)</label>
                                            <input type="text" name="author" 
                                                   class="form-control @error('author') is-invalid @enderror" 
                                                   value="{{ old('author') }}">
                                            @error('author') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Старо ID (от Access)</label>
                                            <input type="text" name="old_system_id" 
                                                   class="form-control @error('old_system_id') is-invalid @enderror" 
                                                   value="{{ old('old_system_id') }}">
                                            @error('old_system_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Импортна партида</label>
                                            <input type="text" name="import_batch" 
                                                   class="form-control @error('import_batch') is-invalid @enderror" 
                                                   value="{{ old('import_batch') }}">
                                            @error('import_batch') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Допълнителна информация -->
                        <div class="card card-warning card-outline mt-3">
                            <div class="card-header">
                                <h3 class="card-title">Допълнителна информация</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Бележки</label>
                                    <textarea name="notes" 
                                              class="form-control @error('notes') is-invalid @enderror" 
                                              rows="4">{{ old('notes') }}</textarea>
                                    @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-check">
                                            <input type="checkbox" name="is_active" 
                                                   class="form-check-input @error('is_active') is-invalid @enderror" 
                                                   value="1" id="is_active" {{ old('is_active', true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_active">Активно превозно средство</label>
                                            @error('is_active') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Бутони за действие -->
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body text-center">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fas fa-save mr-2"></i>Запази превозно средство
                                </button>
                                <a href="{{ route('admin.vehicles.index') }}" class="btn btn-secondary btn-lg ml-2">
                                    <i class="fas fa-times mr-2"></i>Отказ
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop

@push('css')
<style>
.select2-container--default .select2-selection--single {
    border: 1px solid #ced4da;
    border-radius: .25rem;
    height: calc(2.25rem + 2px);
    padding: .375rem .75rem;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 1.5;
    padding-left: 0;
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: calc(2.25rem + 2px);
}
</style>
@endpush

@push('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // Инициализиране на Select2 за избор на клиент
    $('.select2').select2({
        placeholder: "Изберете клиент...",
        allowClear: true,
        language: {
            noResults: function() {
                return "Няма намерени клиенти";
            }
        }
    });

    // Автоматично главни букви за регистрационния номер
    $('input[name="plate"]').on('blur', function() {
        $(this).val($(this).val().toUpperCase());
    });

    // Автоматично главни букви за VIN
    $('input[name="vin"]').on('blur', function() {
        $(this).val($(this).val().toUpperCase());
    });

    // Автоматично главни букви за шаси
    $('input[name="chassis"]').on('blur', function() {
        $(this).val($(this).val().toUpperCase());
    });

    // Автоматично главна буква за марка
    $('input[name="make"]').on('blur', function() {
        var value = $(this).val();
        if (value) {
            $(this).val(value.charAt(0).toUpperCase() + value.slice(1));
        }
    });

    // Автоматично главна буква за модел
    $('input[name="model"]').on('blur', function() {
        var value = $(this).val();
        if (value) {
            $(this).val(value.charAt(0).toUpperCase() + value.slice(1));
        }
    });
});
</script>
@endpush