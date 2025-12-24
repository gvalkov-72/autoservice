@extends('adminlte::page')

@section('title', 'Добави клиент')

@section('content_header')
    <h1>Добави клиент</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.customers.store') }}" method="POST">
                @csrf

                <div class="row">
                    <!-- Лява колона -->
                    <div class="col-md-6">
                        <!-- Поля за миграция -->
                        <div class="card card-secondary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">Данни за миграция (Access)</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Старо ID (от Access)</label>
                                            <input type="text" name="old_id" 
                                                   class="form-control @error('old_id') is-invalid @enderror" 
                                                   value="{{ old('old_id') }}" 
                                                   placeholder="Оригинален номер от старата система">
                                            @error('old_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            <small class="form-text text-muted">Запазете връзката със старата система</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Номер на клиент</label>
                                            <input type="text" name="customer_number" 
                                                   class="form-control @error('customer_number') is-invalid @enderror" 
                                                   value="{{ old('customer_number') }}" 
                                                   placeholder="Вътрешен номер на клиента">
                                            @error('customer_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            <small class="form-text text-muted">Автоматично ще се попълни със старото ID, ако е празно</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Основна информация -->
                        <div class="card card-primary card-outline mt-3">
                            <div class="card-header">
                                <h3 class="card-title">Основна информация</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Име / Фирма *</label>
                                    <input type="text" name="name" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           value="{{ old('name') }}" 
                                           placeholder="Име на физическо лице или фирма" required>
                                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Контактно лице</label>
                                            <input type="text" name="contact_person" 
                                                   class="form-control @error('contact_person') is-invalid @enderror" 
                                                   value="{{ old('contact_person') }}" 
                                                   placeholder="Име на контактно лице">
                                            @error('contact_person') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>МОЛ</label>
                                            <input type="text" name="mol" 
                                                   class="form-control @error('mol') is-invalid @enderror" 
                                                   value="{{ old('mol') }}" 
                                                   placeholder="Материално отговорно лице">
                                            @error('mol') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Данъчен номер (ДДС)</label>
                                            <input type="text" name="tax_number" 
                                                   class="form-control @error('tax_number') is-invalid @enderror" 
                                                   value="{{ old('tax_number') }}" 
                                                   placeholder="BG123456789">
                                            @error('tax_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Булстат</label>
                                            <input type="text" name="bulstat" 
                                                   class="form-control @error('bulstat') is-invalid @enderror" 
                                                   value="{{ old('bulstat') }}" 
                                                   placeholder="123456789" maxlength="13">
                                            @error('bulstat') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Вид документ</label>
                                    <input type="text" name="doc_type" 
                                           class="form-control @error('doc_type') is-invalid @enderror" 
                                           value="{{ old('doc_type') }}" 
                                           placeholder="Тип на документа за самоличност">
                                    @error('doc_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Допълнителни полета от Access -->
                        <div class="card card-dark card-outline mt-3">
                            <div class="card-header">
                                <h3 class="card-title">Допълнителни полета (Access)</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>ЕИДАЛЕ код</label>
                                            <input type="text" name="eidale" 
                                                   class="form-control @error('eidale') is-invalid @enderror" 
                                                   value="{{ old('eidale') }}">
                                            @error('eidale') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Партида</label>
                                            <input type="text" name="partida" 
                                                   class="form-control @error('partida') is-invalid @enderror" 
                                                   value="{{ old('partida') }}">
                                            @error('partida') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Буква (Булстат)</label>
                                    <input type="text" name="bulsial_letter" 
                                           class="form-control @error('bulsial_letter') is-invalid @enderror" 
                                           value="{{ old('bulsial_letter') }}" 
                                           maxlength="10" placeholder="Допълнителна буква към булстат">
                                    @error('bulsial_letter') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Дясна колона -->
                    <div class="col-md-6">
                        <!-- Контактна информация -->
                        <div class="card card-info card-outline">
                            <div class="card-header">
                                <h3 class="card-title">Контактна информация</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Телефон</label>
                                            <input type="text" name="phone" 
                                                   class="form-control @error('phone') is-invalid @enderror" 
                                                   value="{{ old('phone') }}">
                                            @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Факс</label>
                                            <input type="text" name="fax" 
                                                   class="form-control @error('fax') is-invalid @enderror" 
                                                   value="{{ old('fax') }}">
                                            @error('fax') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Имейл</label>
                                    <input type="email" name="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           value="{{ old('email') }}">
                                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Адресна информация -->
                        <div class="card card-success card-outline mt-3">
                            <div class="card-header">
                                <h3 class="card-title">Адресна информация</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Основен адрес (ред 1) *</label>
                                    <input type="text" name="address" 
                                           class="form-control @error('address') is-invalid @enderror" 
                                           value="{{ old('address') }}" 
                                           placeholder="ул./бул./ж.к., №, ет., ап.">
                                    @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="form-group">
                                    <label>Основен адрес (ред 2)</label>
                                    <input type="text" name="address_2" 
                                           class="form-control @error('address_2') is-invalid @enderror" 
                                           value="{{ old('address_2') }}" 
                                           placeholder="Допълнителна информация за адреса">
                                    @error('address_2') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Резервен адрес (ред 1)</label>
                                            <input type="text" name="res_address_1" 
                                                   class="form-control @error('res_address_1') is-invalid @enderror" 
                                                   value="{{ old('res_address_1') }}">
                                            @error('res_address_1') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Резервен адрес (ред 2)</label>
                                            <input type="text" name="res_address_2" 
                                                   class="form-control @error('res_address_2') is-invalid @enderror" 
                                                   value="{{ old('res_address_2') }}">
                                            @error('res_address_2') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Получател информация -->
                        <div class="card card-warning card-outline mt-3">
                            <div class="card-header">
                                <h3 class="card-title">Информация за получател</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Получател</label>
                                    <input type="text" name="receiver" 
                                           class="form-control @error('receiver') is-invalid @enderror" 
                                           value="{{ old('receiver') }}" 
                                           placeholder="Име на получателя">
                                    @error('receiver') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="form-group">
                                    <label>Детайли за получателя</label>
                                    <textarea name="receiver_details" 
                                              class="form-control @error('receiver_details') is-invalid @enderror" 
                                              rows="3">{{ old('receiver_details') }}</textarea>
                                    @error('receiver_details') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Флагове и бележки -->
                        <div class="card card-danger card-outline mt-3">
                            <div class="card-header">
                                <h3 class="card-title">Флагове и бележки</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input type="checkbox" name="is_active" 
                                                   class="form-check-input @error('is_active') is-invalid @enderror" 
                                                   value="1" id="is_active" {{ old('is_active', true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_active">Активен клиент</label>
                                            @error('is_active') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input type="checkbox" name="is_customer" 
                                                   class="form-check-input @error('is_customer') is-invalid @enderror" 
                                                   value="1" id="is_customer" {{ old('is_customer', true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_customer">Клиент</label>
                                            @error('is_customer') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input type="checkbox" name="is_supplier" 
                                                   class="form-check-input @error('is_supplier') is-invalid @enderror" 
                                                   value="1" id="is_supplier" {{ old('is_supplier', false) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_supplier">Доставчик</label>
                                            @error('is_supplier') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input type="checkbox" name="include_in_mailing" 
                                                   class="form-check-input @error('include_in_mailing') is-invalid @enderror" 
                                                   value="1" id="include_in_mailing" {{ old('include_in_mailing', true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="include_in_mailing">Включване в бюлетин</label>
                                            @error('include_in_mailing') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mt-3">
                                    <label>Бележки</label>
                                    <textarea name="notes" 
                                              class="form-control @error('notes') is-invalid @enderror" 
                                              rows="4">{{ old('notes') }}</textarea>
                                    @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save mr-1"></i> Запази клиент
                    </button>
                    <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times mr-1"></i> Отказ
                    </a>
                </div>
            </form>
        </div>
    </div>
@stop

@push('css')
<style>
    .card-outline {
        border-top: 3px solid;
    }
    .card-secondary.card-outline {
        border-top-color: #6c757d;
    }
    .card-primary.card-outline {
        border-top-color: #007bff;
    }
    .card-info.card-outline {
        border-top-color: #17a2b8;
    }
    .card-success.card-outline {
        border-top-color: #28a745;
    }
    .card-warning.card-outline {
        border-top-color: #ffc107;
    }
    .card-danger.card-outline {
        border-top-color: #dc3545;
    }
    .card-dark.card-outline {
        border-top-color: #343a40;
    }
</style>
@endpush

@push('js')
<script>
    $(document).ready(function() {
        // Автоматично попълване на customer_number, ако old_id е попълнено, а customer_number е празно
        $('input[name="old_id"]').on('blur', function() {
            const oldId = $(this).val();
            const customerNumberInput = $('input[name="customer_number"]');
            
            if (oldId && !customerNumberInput.val()) {
                customerNumberInput.val(oldId);
            }
        });

        // Валидация на булстат (само цифри)
        $('input[name="bulstat"]').on('input', function() {
            this.value = this.value.replace(/[^\d]/g, '');
        });

        // Валидация на телефон/факс
        $('input[name="phone"], input[name="fax"]').on('input', function() {
            this.value = this.value.replace(/[^\d\s\+\(\)\-]/g, '');
        });
    });
</script>
@endpush