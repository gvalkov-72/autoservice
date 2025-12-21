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
                        <!-- Основна информация -->
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">Основна информация</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Тип *</label>
                                    <select name="type" class="form-control @error('type') is-invalid @enderror" required>
                                        <option value="">Изберете тип...</option>
                                        <option value="customer" {{ old('type') == 'customer' ? 'selected' : '' }}>Клиент</option>
                                        <option value="supplier" {{ old('type') == 'supplier' ? 'selected' : '' }}>Доставчик</option>
                                        <option value="both" {{ old('type') == 'both' ? 'selected' : '' }}>Клиент и Доставчик</option>
                                    </select>
                                    @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                
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
                                            <label>ДДС номер</label>
                                            <input type="text" name="vat_number" 
                                                   class="form-control @error('vat_number') is-invalid @enderror" 
                                                   value="{{ old('vat_number') }}" 
                                                   placeholder="BG123456789">
                                            @error('vat_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
                                    <label>Контактно лице</label>
                                    <input type="text" name="contact_person" 
                                           class="form-control @error('contact_person') is-invalid @enderror" 
                                           value="{{ old('contact_person') }}" 
                                           placeholder="Име на контактно лице">
                                    @error('contact_person') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Съдебен регистър (част)</label>
                                            <input type="text" name="court_registration" 
                                                   class="form-control @error('court_registration') is-invalid @enderror" 
                                                   value="{{ old('court_registration') }}">
                                            @error('court_registration') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Буква (Булстат)</label>
                                            <input type="text" name="bulstat_letter" 
                                                   class="form-control @error('bulstat_letter') is-invalid @enderror" 
                                                   value="{{ old('bulstat_letter') }}" 
                                                   maxlength="1" placeholder="А, Б, В...">
                                            @error('bulstat_letter') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Контактна информация -->
                        <div class="card card-info card-outline mt-3">
                            <div class="card-header">
                                <h3 class="card-title">Контактна информация</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Телефон</label>
                                    <input type="text" name="phone" 
                                           class="form-control @error('phone') is-invalid @enderror" 
                                           value="{{ old('phone') }}">
                                    @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                
                                <div class="form-group">
                                    <label>Факс</label>
                                    <input type="text" name="fax" 
                                           class="form-control @error('fax') is-invalid @enderror" 
                                           value="{{ old('fax') }}">
                                    @error('fax') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
                    </div>
                    
                    <!-- Дясна колона -->
                    <div class="col-md-6">
                        <!-- Адресна информация -->
                        <div class="card card-success card-outline">
                            <div class="card-header">
                                <h3 class="card-title">Адресна информация</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Основен адрес (комбиниран)</label>
                                    <textarea name="address" 
                                              class="form-control @error('address') is-invalid @enderror" 
                                              rows="2">{{ old('address') }}</textarea>
                                    @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    <small class="form-text text-muted">Ще се попълни автоматично от полетата по-долу, ако е оставено празно</small>
                                </div>
                                
                                <div class="form-group">
                                    <label>Адрес ред 1</label>
                                    <input type="text" name="address_line1" 
                                           class="form-control @error('address_line1') is-invalid @enderror" 
                                           value="{{ old('address_line1') }}">
                                    @error('address_line1') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                
                                <div class="form-group">
                                    <label>Адрес ред 2</label>
                                    <input type="text" name="address_line2" 
                                           class="form-control @error('address_line2') is-invalid @enderror" 
                                           value="{{ old('address_line2') }}">
                                    @error('address_line2') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                
                                <div class="form-group">
                                    <label>Град</label>
                                    <input type="text" name="city" 
                                           class="form-control @error('city') is-invalid @enderror" 
                                           value="{{ old('city') }}">
                                    @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
                                            <input type="checkbox" name="include_in_reports" 
                                                   class="form-check-input @error('include_in_reports') is-invalid @enderror" 
                                                   value="1" id="include_in_reports" {{ old('include_in_reports', true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="include_in_reports">Включване в справки</label>
                                            @error('include_in_reports') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group mt-3">
                                    <label>Старо ID (от Access системата)</label>
                                    <input type="text" name="old_system_id" 
                                           class="form-control @error('old_system_id') is-invalid @enderror" 
                                           value="{{ old('old_system_id') }}" 
                                           placeholder="Номер от старата система">
                                    @error('old_system_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Запази клиент
                        </button>
                        <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Отказ
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop