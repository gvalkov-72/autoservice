@extends('adminlte::page')

@section('title', 'Фирмени данни: ' . $companySetting->name)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>
            <i class="fas fa-building mr-2"></i> {!! $companySetting->name !!}
            @if($companySetting->is_active)
                <span class="badge badge-success ml-2">Активен профил</span>
            @else
                <span class="badge badge-secondary ml-2">Неактивен профил</span>
            @endif
        </h1>
        <div>
            <a href="{{ route('admin.company-settings.print', $companySetting->id) }}" class="btn btn-default btn-sm" target="_blank">
                <i class="fas fa-print"></i> Печат
            </a>
            <a href="{{ route('admin.company-settings.pdf', $companySetting->id) }}" class="btn btn-danger btn-sm" target="_blank">
                <i class="fas fa-file-pdf"></i> PDF
            </a>
            <a href="{{ route('admin.company-settings.edit', $companySetting->id) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit"></i> Редактиране
            </a>
            <a href="{{ route('admin.company-settings.index') }}" class="btn btn-default btn-sm ml-2">
                <i class="fas fa-arrow-left"></i> Назад
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        {{-- ЛЯВА КОЛОНА: ЛОГО И ОСНОВНА ИНФОРМАЦИЯ --}}
        <div class="col-md-4">
            {{-- ЛОГО --}}
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-image mr-1"></i> Лого
                    </h3>
                </div>
                <div class="card-body text-center">
                    @if($companySetting->logo_path)
                        <img src="{{ $companySetting->logo_url }}" alt="Лого на {{ $companySetting->name }}" 
                             style="max-height: 150px; max-width: 100%; border: 1px solid #ddd; padding: 10px; border-radius: 4px; background: #fff;">
                        <div class="mt-3">
                            <a href="{{ $companySetting->logo_url }}" download class="btn btn-sm btn-default">
                                <i class="fas fa-download"></i> Изтегли
                            </a>
                        </div>
                    @else
                        <div style="padding: 30px 20px; background: #f9f9f9; border-radius: 4px;">
                            <i class="fas fa-building fa-4x text-muted"></i>
                            <p class="text-muted mt-3 mb-0">Няма качено лого</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- АКТИВЕН ПРОФИЛ --}}
            <div class="card card-success card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-check-circle mr-1"></i> Статус
                    </h3>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th style="width: 120px;">Статус:</th>
                            <td>
                                @if($companySetting->is_active)
                                    <span class="badge badge-success">Активен</span>
                                    <small class="text-muted d-block mt-1">Този профил се използва в документите.</small>
                                @else
                                    <span class="badge badge-secondary">Неактивен</span>
                                    <small class="text-muted d-block mt-1">Неактивният профил не се визуализира във фактури и документи.</small>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        {{-- ДЯСНА КОЛОНА: ДАННИ --}}
        <div class="col-md-8">
            {{-- ОСНОВНА ИНФОРМАЦИЯ --}}
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle mr-1"></i> Основна информация
                    </h3>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th style="width: 150px;">Име на фирма:</th>
                            <td><strong>{!! $companySetting->name !!}</strong></td>
                        </tr>
                        <tr>
                            <th>Град:</th>
                            <td>{{ $companySetting->city ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th>Адрес:</th>
                            <td>{{ $companySetting->address ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th>ЕИК/БУЛСТАТ:</th>
                            <td>{{ $companySetting->vat_number ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th>ДДС номер:</th>
                            <td>
                                @if($companySetting->vat_number)
                                    BG{{ $companySetting->vat_number }}
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>МОЛ / Лице за контакт:</th>
                            <td>{!! $companySetting->contact_person ?? '—' !!}</td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- КОНТАКТИ --}}
            <div class="card card-info card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-address-book mr-1"></i> Контакти
                    </h3>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th style="width: 150px;">Телефон:</th>
                            <td>
                                @if($companySetting->phone)
                                    <a href="tel:{{ $companySetting->phone }}">{{ $companySetting->phone }}</a>
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>E-mail:</th>
                            <td>
                                @if($companySetting->email)
                                    <a href="mailto:{{ $companySetting->email }}">{{ $companySetting->email }}</a>
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Уебсайт:</th>
                            <td>
                                @if($companySetting->website)
                                    <a href="{{ $companySetting->website }}" target="_blank">{{ $companySetting->website }}</a>
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- БАНКОВИ ДАННИ --}}
            <div class="card card-success card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-university mr-1"></i> Банкови данни
                    </h3>
                </div>
                <div class="card-body">
                    @if($companySetting->iban || $companySetting->bank_name || $companySetting->bic)
                        <table class="table table-sm table-borderless">
                            <tr>
                                <th style="width: 150px;">IBAN:</th>
                                <td>{{ $companySetting->iban ?? '—' }}</td>
                            </tr>
                            <tr>
                                <th>Банка:</th>
                                <td>{{ $companySetting->bank_name ?? '—' }}</td>
                            </tr>
                            <tr>
                                <th>BIC/SWIFT:</th>
                                <td>{{ $companySetting->bic ?? '—' }}</td>
                            </tr>
                        </table>
                    @else
                        <p class="text-muted mb-0">Няма въведени банкови данни.</p>
                    @endif
                </div>
            </div>

            {{-- ДОПЪЛНИТЕЛНА ИНФОРМАЦИЯ --}}
            <div class="card card-secondary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-sticky-note mr-1"></i> Допълнително
                    </h3>
                </div>
                <div class="card-body">
                    @if($companySetting->invoice_footer)
                        <div class="mb-2">
                            <strong>Текст в долния колонтитул на фактура:</strong>
                            <p class="mb-0 mt-1 p-2 bg-light rounded">{!! nl2br(e($companySetting->invoice_footer)) !!}</p>
                        </div>
                    @else
                        <p class="text-muted mb-0">Няма въведен текст за колонтитул.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- СИСТЕМНА ИНФОРМАЦИЯ --}}
    <div class="card card-light card-outline">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-history mr-1"></i> Системна информация
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <small class="text-muted d-block">Създаден на: {{ $companySetting->created_at->format('d.m.Y H:i:s') }}</small>
                    <small class="text-muted d-block">Последна промяна: {{ $companySetting->updated_at->format('d.m.Y H:i:s') }}</small>
                </div>
                <div class="col-md-6 text-right">
                    <small class="text-muted d-block">ID: {{ $companySetting->id }}</small>
                </div>
            </div>
        </div>
    </div>
@stop