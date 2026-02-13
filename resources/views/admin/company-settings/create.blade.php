@extends('adminlte::page')

@section('title', 'Нови фирмени данни')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Нови фирмени данни</h1>
        <a href="{{ route('admin.company-settings.index') }}" class="btn btn-default btn-sm">
            <i class="fas fa-arrow-left"></i> Назад
        </a>
    </div>
@stop

@section('content')
    <form action="{{ route('admin.company-settings.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- ОСНОВНА ИНФОРМАЦИЯ --}}
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">Основна информация</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <x-adminlte-input name="name" label="Име на фирма *" 
                            value="{{ html_entity_decode(old('name'), ENT_QUOTES, 'UTF-8') }}"
                            placeholder="ООД &quot;Автосервиз&quot;" required />
                    </div>
                    <div class="col-md-3">
                        <x-adminlte-input name="city" label="Град"
                            value="{{ html_entity_decode(old('city'), ENT_QUOTES, 'UTF-8') }}"
                            placeholder="София" />
                    </div>
                    <div class="col-md-3">
                        <x-adminlte-input name="address" label="Адрес"
                            value="{{ html_entity_decode(old('address'), ENT_QUOTES, 'UTF-8') }}"
                            placeholder="ул. България 1" />
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <x-adminlte-input name="vat_number" label="ЕИК/БУЛСТАТ"
                            value="{{ html_entity_decode(old('vat_number'), ENT_QUOTES, 'UTF-8') }}"
                            placeholder="123456789" />
                    </div>
                    <div class="col-md-4">
                        <x-adminlte-input name="contact_person" label="МОЛ / Лице за контакт"
                            value="{{ html_entity_decode(old('contact_person'), ENT_QUOTES, 'UTF-8') }}"
                            placeholder="Иван Иванов" />
                    </div>
                    <div class="col-md-4">
                        <x-adminlte-input-switch name="is_active" label="Активен профил"
                            :checked="old('is_active', true)" />
                    </div>
                </div>
            </div>
        </div>

        {{-- КОНТАКТИ --}}
        <div class="card card-info card-outline">
            <div class="card-header">
                <h3 class="card-title">Контакти</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <x-adminlte-input name="phone" label="Телефон"
                            value="{{ html_entity_decode(old('phone'), ENT_QUOTES, 'UTF-8') }}"
                            placeholder="+359 2 123 456" />
                    </div>
                    <div class="col-md-4">
                        <x-adminlte-input name="email" label="E-mail" type="email"
                            value="{{ html_entity_decode(old('email'), ENT_QUOTES, 'UTF-8') }}"
                            placeholder="office@autoservice.bg" />
                    </div>
                    <div class="col-md-4">
                        <x-adminlte-input name="website" label="Уебсайт"
                            value="{{ html_entity_decode(old('website'), ENT_QUOTES, 'UTF-8') }}"
                            placeholder="https://autoservice.bg" />
                    </div>
                </div>
            </div>
        </div>

        {{-- БАНКОВИ ДАННИ --}}
        <div class="card card-success card-outline">
            <div class="card-header">
                <h3 class="card-title">Банкови данни</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-5">
                        <x-adminlte-input name="iban" label="IBAN"
                            value="{{ html_entity_decode(old('iban'), ENT_QUOTES, 'UTF-8') }}"
                            placeholder="BG80 BNBG 9661 1020 3456 78" />
                    </div>
                    <div class="col-md-4">
                        <x-adminlte-input name="bank_name" label="Банка"
                            value="{{ html_entity_decode(old('bank_name'), ENT_QUOTES, 'UTF-8') }}"
                            placeholder="БНБ / Уникредит Булбанк ..." />
                    </div>
                    <div class="col-md-3">
                        <x-adminlte-input name="bic" label="BIC / SWIFT"
                            value="{{ html_entity_decode(old('bic'), ENT_QUOTES, 'UTF-8') }}"
                            placeholder="BNBGBGSF" />
                    </div>
                </div>
            </div>
        </div>

        {{-- ЛОГО --}}
        <div class="card card-secondary card-outline">
            <div class="card-header">
                <h3 class="card-title">Лого</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="logo">Лого (JPEG, PNG, JPG, GIF, макс. 2MB)</label>
                            <div class="custom-file">
                                <input type="file" name="logo" class="custom-file-input" id="logo" accept="image/*">
                                <label class="custom-file-label" for="logo">Избери файл...</label>
                            </div>
                            @error('logo')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div id="logo-preview-container" style="display: none;">
                            <label>Преглед:</label>
                            <div>
                                <img id="logo-preview" src="#" alt="Лого преглед" 
                                     style="max-height: 100px; max-width: 200px; border: 1px solid #ddd; padding: 5px; border-radius: 4px;">
                            </div>
                        </div>
                    </div>
                </div>
                <p class="text-muted small mb-0">Препоръчителен размер: 200x100 пиксела.</p>
            </div>
        </div>

        {{-- ДОПЪЛНИТЕЛНИ НАСТРОЙКИ --}}
        <div class="card card-secondary card-outline">
            <div class="card-header">
                <h3 class="card-title">Допълнително</h3>
            </div>
            <div class="card-body">
                <x-adminlte-textarea name="invoice_footer" label="Текст в долния колонтитул на фактура"
                    placeholder="Благодарим Ви за доверието! …">{{ old('invoice_footer') }}</x-adminlte-textarea>
                <p class="text-muted small mb-0">Този текст ще се показва в долната част на всички фактури.</p>
            </div>
        </div>

        {{-- БУТОНИ --}}
        <div class="row mb-4">
            <div class="col-12 text-right">
                <x-adminlte-button type="submit" label="Запази фирмените данни" theme="success" icon="fas fa-save" />
                <a href="{{ route('admin.company-settings.index') }}" class="btn btn-default ml-2">Отказ</a>
            </div>
        </div>
    </form>
@stop

@section('js')
    <script>
        $(function() {
            // Bootstrap Custom File Input – показва името на избрания файл
            bsCustomFileInput.init();

            // Преглед на логото преди качване
            function readLogo(input) {
                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#logo-preview-container').show();
                        $('#logo-preview').attr('src', e.target.result);
                    };
                    reader.readAsDataURL(input.files[0]);
                }
            }

            $('#logo').on('change', function() {
                readLogo(this);
            });
        });
    </script>
@stop

@section('css')
    {{-- Bootstrap Custom File Input --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.min.css">
@stop