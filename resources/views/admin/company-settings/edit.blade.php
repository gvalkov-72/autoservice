@extends('adminlte::page')

@section('title', 'Редактирай данни на автосервиза')

@section('content_header')
    <h1>Редактирай данни на автосервиза</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.company-settings.update', $companySetting) }}" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Име на фирмата *</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $companySetting->name) }}" required>
                        </div>
                        <div class="form-group">
                            <label>МОЛ</label>
                            <input type="text" name="contact_person" class="form-control" value="{{ old('contact_person', $companySetting->contact_person) }}">
                        </div>
                        <div class="form-group">
                            <label>Град</label>
                            <input type="text" name="city" class="form-control" value="{{ old('city', $companySetting->city) }}">
                        </div>
                        <div class="form-group">
                            <label>Адрес</label>
                            <input type="text" name="address" class="form-control" value="{{ old('address', $companySetting->address) }}">
                        </div>
                        <div class="form-group">
                            <label>ЕИК / ЕГН / ЗДДС номер</label>
                            <input type="text" name="vat_number" class="form-control" value="{{ old('vat_number', $companySetting->vat_number) }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Телефон</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $companySetting->phone) }}">
                        </div>
                        <div class="form-group">
                            <label>Имейл</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $companySetting->email) }}">
                        </div>
                        <div class="form-group">
                            <label>IBAN</label>
                            <input type="text" name="iban" class="form-control" value="{{ old('iban', $companySetting->iban) }}">
                        </div>
                        <div class="form-group">
                            <label>Име на банката</label>
                            <input type="text" name="bank_name" class="form-control" value="{{ old('bank_name', $companySetting->bank_name) }}">
                        </div>
                        <div class="form-group">
                            <label>BIC код</label>
                            <input type="text" name="bic" class="form-control" value="{{ old('bic', $companySetting->bic) }}">
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Уебсайт</label>
                            <input type="url" name="website" class="form-control" value="{{ old('website', $companySetting->website) }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Лого</label>
                            <input type="file" name="logo" class="form-control-file">
                            @if($companySetting->logo_path)
                                <div class="mt-2">
                                    <small>Текущо лого:</small><br>
                                    <img src="{{ Storage::url($companySetting->logo_path) }}" alt="Лого" style="max-height: 50px;">
                                </div>
                            @endif
                            <small class="form-text text-muted">JPEG, PNG, JPG, GIF. Макс. 2MB</small>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Текст за фактури (футър)</label>
                    <textarea name="invoice_footer" class="form-control" rows="3">{{ old('invoice_footer', $companySetting->invoice_footer) }}</textarea>
                </div>
                
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" name="is_active" id="is_active" value="1" 
                               class="custom-control-input" {{ old('is_active', $companySetting->is_active) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="is_active">
                            Направи тези данни активни
                        </label>
                        <small class="form-text text-muted">
                            Ако активирате тази опция, всички други настройки ще бъдат деактивирани.
                        </small>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-success">Запази промените</button>
                <a href="{{ route('admin.company-settings.index') }}" class="btn btn-secondary">Отказ</a>
            </form>
        </div>
    </div>
@stop