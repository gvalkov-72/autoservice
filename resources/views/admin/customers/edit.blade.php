@extends('adminlte::page')

@section('title', 'Редактиране на клиент')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0">
            <i class="fas fa-user-edit text-primary mr-2"></i>Редактиране на клиент
        </h1>
        <div class="btn-group">
            <a href="{{ route('admin.customers.show', $customer) }}" class="btn btn-sm btn-outline-info">
                <i class="fas fa-eye mr-1"></i> Преглед
            </a>
            <a href="{{ route('admin.customers.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Назад
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-light">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-user-circle mr-2"></i>Основни данни
                    </h3>
                </div>
                <form action="{{ route('admin.customers.update', $customer) }}" method="POST">
                    @csrf @method('PUT')
                    
                    <div class="card-body">
                        <!-- ID секция -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="font-weight-bold">ID от стара система</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light">
                                                <i class="fas fa-database text-muted"></i>
                                            </span>
                                        </div>
                                        <input type="text" name="old_id" 
                                               class="form-control @error('old_id') is-invalid @enderror" 
                                               value="{{ old('old_id', $customer->old_id) }}"
                                               placeholder="Старо ID">
                                    </div>
                                    @error('old_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                    <small class="text-muted">За връзка с предишна система</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="font-weight-bold">Вътрешен номер</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light">
                                                <i class="fas fa-hashtag text-muted"></i>
                                            </span>
                                        </div>
                                        <input type="text" name="customer_number" 
                                               class="form-control @error('customer_number') is-invalid @enderror" 
                                               value="{{ old('customer_number', $customer->customer_number) }}"
                                               placeholder="Номер на клиент">
                                    </div>
                                    @error('customer_number') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="font-weight-bold">Статус</label>
                                    <div class="custom-control custom-switch mt-2">
                                        <input type="checkbox" name="is_active" 
                                               class="custom-control-input @error('is_active') is-invalid @enderror" 
                                               value="1" id="is_active" 
                                               {{ old('is_active', $customer->is_active) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="is_active">
                                            {{ $customer->is_active ? 'Активен' : 'Неактивен' }}
                                        </label>
                                    </div>
                                    @error('is_active') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Основна информация -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Име / Фирма *</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="fas fa-building"></i>
                                            </span>
                                        </div>
                                        <input type="text" name="name" 
                                               class="form-control @error('name') is-invalid @enderror" 
                                               value="{{ old('name', $customer->name) }}" required>
                                    </div>
                                    @error('name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Контактно лице</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="fas fa-user"></i>
                                            </span>
                                        </div>
                                        <input type="text" name="contact_person" 
                                               class="form-control @error('contact_person') is-invalid @enderror" 
                                               value="{{ old('contact_person', $customer->contact_person) }}">
                                    </div>
                                    @error('contact_person') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Юридически данни -->
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>МОЛ</label>
                                    <input type="text" name="mol" 
                                           class="form-control @error('mol') is-invalid @enderror" 
                                           value="{{ old('mol', $customer->mol) }}">
                                    @error('mol') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Данъчен номер</label>
                                    <input type="text" name="tax_number" 
                                           class="form-control @error('tax_number') is-invalid @enderror" 
                                           value="{{ old('tax_number', $customer->tax_number) }}">
                                    @error('tax_number') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Булстат</label>
                                    <input type="text" name="bulstat" 
                                           class="form-control @error('bulstat') is-invalid @enderror" 
                                           value="{{ old('bulstat', $customer->bulstat) }}">
                                    @error('bulstat') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Контакти -->
                        <h5 class="mt-4 mb-3 border-bottom pb-2">
                            <i class="fas fa-address-book mr-2"></i>Контакти
                        </h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Телефон</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="fas fa-phone"></i>
                                            </span>
                                        </div>
                                        <input type="text" name="phone" 
                                               class="form-control @error('phone') is-invalid @enderror" 
                                               value="{{ old('phone', $customer->phone) }}">
                                    </div>
                                    @error('phone') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Имейл</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="fas fa-envelope"></i>
                                            </span>
                                        </div>
                                        <input type="email" name="email" 
                                               class="form-control @error('email') is-invalid @enderror" 
                                               value="{{ old('email', $customer->email) }}">
                                    </div>
                                    @error('email') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Адреси -->
                        <h5 class="mt-4 mb-3 border-bottom pb-2">
                            <i class="fas fa-map-marker-alt mr-2"></i>Адреси
                        </h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Основен адрес</label>
                                    <textarea name="address" rows="2"
                                              class="form-control @error('address') is-invalid @enderror">{{ old('address', $customer->address) }}</textarea>
                                    @error('address') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Допълнителен адрес</label>
                                    <textarea name="address_2" rows="2"
                                              class="form-control @error('address_2') is-invalid @enderror">{{ old('address_2', $customer->address_2) }}</textarea>
                                    @error('address_2') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Тип клиент и флагове -->
                        <h5 class="mt-4 mb-3 border-bottom pb-2">
                            <i class="fas fa-tags mr-2"></i>Настройки
                        </h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Тип</label>
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" name="is_customer" 
                                                       class="custom-control-input @error('is_customer') is-invalid @enderror" 
                                                       value="1" id="is_customer" 
                                                       {{ old('is_customer', $customer->is_customer) ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="is_customer">
                                                    <i class="fas fa-user mr-1"></i> Клиент
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" name="is_supplier" 
                                                       class="custom-control-input @error('is_supplier') is-invalid @enderror" 
                                                       value="1" id="is_supplier" 
                                                       {{ old('is_supplier', $customer->is_supplier) ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="is_supplier">
                                                    <i class="fas fa-truck mr-1"></i> Доставчик
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    @error('is_customer') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                    @error('is_supplier') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Бюлетин</label>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" name="include_in_mailing" 
                                               class="custom-control-input @error('include_in_mailing') is-invalid @enderror" 
                                               value="1" id="include_in_mailing" 
                                               {{ old('include_in_mailing', $customer->include_in_mailing) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="include_in_mailing">
                                            {{ $customer->include_in_mailing ? 'Включен' : 'Изключен' }}
                                        </label>
                                    </div>
                                    @error('include_in_mailing') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Бележки -->
                        <div class="form-group mt-4">
                            <label>Бележки</label>
                            <textarea name="notes" rows="4"
                                      class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $customer->notes) }}</textarea>
                            @error('notes') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="card-footer bg-white">
                        <div class="d-flex justify-content-between">
                            <div>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fas fa-save mr-2"></i> Запази промените
                                </button>
                                <button type="reset" class="btn btn-outline-secondary">
                                    <i class="fas fa-undo mr-2"></i> Възстанови
                                </button>
                            </div>
                            <div>
                                <span class="text-muted mr-3">
                                    <i class="fas fa-info-circle mr-1"></i> Последна промяна: 
                                    {{ $customer->updated_at->format('d.m.Y H:i') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Дясна колона - информация -->
        <div class="col-lg-4">
            <!-- Информация за клиента -->
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle mr-2"></i>Информация
                    </h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between">
                            <span>Вътрешен ID</span>
                            <strong>#{{ $customer->id }}</strong>
                        </div>
                        <div class="list-group-item d-flex justify-content-between">
                            <span>Създаден на</span>
                            <span>{{ $customer->created_at->format('d.m.Y') }}</span>
                        </div>
                        <div class="list-group-item">
                            <small class="text-muted">
                                <i class="fas fa-car mr-1"></i> Автомобили: 
                                <span class="badge badge-info">{{ $customer->vehicles()->count() }}</span>
                            </small>
                        </div>
                        <div class="list-group-item">
                            <small class="text-muted">
                                <i class="fas fa-wrench mr-1"></i> Поръчки: 
                                <span class="badge badge-info">{{ $customer->workOrders()->count() }}</span>
                            </small>
                        </div>
                        <div class="list-group-item">
                            <small class="text-muted">
                                <i class="fas fa-file-invoice mr-1"></i> Фактури: 
                                <span class="badge badge-info">{{ $customer->invoices()->count() }}</span>
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Бързи действия -->
            <div class="card mt-3">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-bolt mr-2"></i>Бързи действия
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.vehicles.create', ['customer_id' => $customer->id]) }}" 
                           class="btn btn-outline-success btn-sm">
                            <i class="fas fa-car mr-2"></i>Добави автомобил
                        </a>
                        <a href="{{ route('admin.work-orders.create', ['customer_id' => $customer->id]) }}" 
                           class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-wrench mr-2"></i>Нова поръчка
                        </a>
                        <a href="{{ route('admin.invoices.create', ['customer_id' => $customer->id]) }}" 
                           class="btn btn-outline-info btn-sm">
                            <i class="fas fa-file-invoice mr-2"></i>Нова фактура
                        </a>
                    </div>
                </div>
            </div>

            <!-- Допълнителни данни (само ако има такива) -->
            @if($customer->receiver || $customer->receiver_details || $customer->eidale)
            <div class="card mt-3">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-ellipsis-h mr-2"></i>Допълнителни данни
                    </h5>
                </div>
                <div class="card-body">
                    @if($customer->receiver)
                        <div class="mb-2">
                            <small class="text-muted">Получател:</small>
                            <div>{{ $customer->receiver }}</div>
                        </div>
                    @endif
                    @if($customer->receiver_details)
                        <div class="mb-2">
                            <small class="text-muted">Детайли за получателя:</small>
                            <div class="small">{{ $customer->receiver_details }}</div>
                        </div>
                    @endif
                    @if($customer->eidale)
                        <div class="mb-2">
                            <small class="text-muted">ЕИДАЛЕ код:</small>
                            <div>{{ $customer->eidale }}</div>
                        </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
@stop

@push('css')
<style>
    .form-group label {
        font-weight: 600;
        color: #495057;
    }
    .input-group-text {
        background-color: #f8f9fa;
        border-right: 0;
    }
    .card-header {
        border-bottom: 1px solid rgba(0,0,0,.125);
    }
    .list-group-item {
        border: none;
        padding: 0.5rem 0;
    }
    .custom-switch .custom-control-label::before {
        background-color: #adb5bd;
    }
    .custom-switch .custom-control-input:checked ~ .custom-control-label::before {
        background-color: #28a745;
    }
</style>
@endpush

@push('js')
<script>
    $(document).ready(function() {
        // Автоматично генериране на customer_number от old_id
        $('input[name="old_id"]').on('blur', function() {
            const oldId = $(this).val().trim();
            const customerNumberInput = $('input[name="customer_number"]');
            
            if (oldId && !customerNumberInput.val().trim()) {
                customerNumberInput.val(oldId);
            }
        });

        // Динамична промяна на етикета на статуса
        $('#is_active').change(function() {
            const label = $(this).next('.custom-control-label');
            label.text(this.checked ? 'Активен' : 'Неактивен');
        });

        // Динамична промяна на етикета на бюлетина
        $('#include_in_mailing').change(function() {
            const label = $(this).next('.custom-control-label');
            label.text(this.checked ? 'Включен' : 'Изключен');
        });

        // Валидация на телефонен номер
        $('input[name="phone"]').on('input', function() {
            this.value = this.value.replace(/[^\d\s\+\(\)\-]/g, '');
        });

        // Валидация на булстат (само цифри)
        $('input[name="bulstat"]').on('input', function() {
            this.value = this.value.replace(/[^\d]/g, '');
        });

        // Показване на допълнителни полета при нужда
        $('#showAdvanced').click(function(e) {
            e.preventDefault();
            $('#advancedFields').toggleClass('d-none');
            $(this).find('i').toggleClass('fa-chevron-down fa-chevron-up');
        });
    });
</script>
@endpush