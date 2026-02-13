@extends('adminlte::page')

@section('title', 'Редактиране на клиент')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Редактиране: {!! $customer->name !!}</h1>
        <a href="{{ route('admin.customers.index') }}" class="btn btn-default btn-sm">
            <i class="fas fa-arrow-left"></i> Назад
        </a>
    </div>
@stop

@section('content')
    <form action="{{ route('admin.customers.update', $customer->id) }}" method="POST">
        @csrf
        @method('PUT')
        {{-- Основни данни --}}
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">Основни данни</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <x-adminlte-input name="old_id" label="Стар ID (old_id)" 
                            value="{{ html_entity_decode(old('old_id', $customer->old_id), ENT_QUOTES, 'UTF-8') }}" 
                            placeholder="—" readonly />
                    </div>
                    <div class="col-md-4">
                        <x-adminlte-input name="customer_number" label="Клиентски номер" 
                            value="{{ html_entity_decode(old('customer_number', $customer->customer_number), ENT_QUOTES, 'UTF-8') }}" placeholder="..." />
                    </div>
                    <div class="col-md-4">
                        <x-adminlte-input name="name" label="Име *" 
                            value="{{ html_entity_decode(old('name', $customer->name), ENT_QUOTES, 'UTF-8') }}" 
                            placeholder="Име на клиент/фирма" required />
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <x-adminlte-input name="email" label="E-mail" type="email" 
                            value="{{ html_entity_decode(old('email', $customer->email), ENT_QUOTES, 'UTF-8') }}" placeholder="office@example.com" />
                    </div>
                    <div class="col-md-4">
                        <x-adminlte-input name="phone" label="Телефон" 
                            value="{{ html_entity_decode(old('phone', $customer->phone), ENT_QUOTES, 'UTF-8') }}" placeholder="+359..." />
                    </div>
                    <div class="col-md-4">
                        <x-adminlte-input name="fax" label="Факс" 
                            value="{{ html_entity_decode(old('fax', $customer->fax), ENT_QUOTES, 'UTF-8') }}" placeholder="..." />
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <x-adminlte-input name="address" label="Адрес (ул./бул.)" 
                            value="{{ html_entity_decode(old('address', $customer->address), ENT_QUOTES, 'UTF-8') }}" placeholder="..." />
                    </div>
                    <div class="col-md-6">
                        <x-adminlte-input name="address_2" label="Адрес ред 2" 
                            value="{{ html_entity_decode(old('address_2', $customer->address_2), ENT_QUOTES, 'UTF-8') }}" placeholder="..." />
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <x-adminlte-input name="res_address_1" label="Жилищен адрес ред 1" 
                            value="{{ html_entity_decode(old('res_address_1', $customer->res_address_1), ENT_QUOTES, 'UTF-8') }}" placeholder="..." />
                    </div>
                    <div class="col-md-6">
                        <x-adminlte-input name="res_address_2" label="Жилищен адрес ред 2" 
                            value="{{ html_entity_decode(old('res_address_2', $customer->res_address_2), ENT_QUOTES, 'UTF-8') }}" placeholder="..." />
                    </div>
                </div>
            </div>
        </div>

        {{-- Данъчни и контакт --}}
        <div class="card card-secondary card-outline">
            <div class="card-header">
                <h3 class="card-title">Данъчни данни & Контакт</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <x-adminlte-input name="mol" label="МОЛ" 
                            value="{{ html_entity_decode(old('mol', $customer->mol), ENT_QUOTES, 'UTF-8') }}" placeholder="..." />
                    </div>
                    <div class="col-md-4">
                        <x-adminlte-input name="contact_person" label="Лице за контакт" 
                            value="{{ html_entity_decode(old('contact_person', $customer->contact_person), ENT_QUOTES, 'UTF-8') }}" placeholder="..." />
                    </div>
                    <div class="col-md-4">
                        <x-adminlte-input name="tax_number" label="ДДС номер" 
                            value="{{ html_entity_decode(old('tax_number', $customer->tax_number), ENT_QUOTES, 'UTF-8') }}" placeholder="BG..." />
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <x-adminlte-input name="bulstat" label="БУЛСТАТ/ЕИК" 
                            value="{{ html_entity_decode(old('bulstat', $customer->bulstat), ENT_QUOTES, 'UTF-8') }}" placeholder="..." />
                    </div>
                    <div class="col-md-4">
                        <x-adminlte-input name="bulstat_letter" label="БУЛСТАТ писмо" 
                            value="{{ html_entity_decode(old('bulstat_letter', $customer->bulstat_letter), ENT_QUOTES, 'UTF-8') }}" placeholder="..." />
                    </div>
                    <div class="col-md-4">
                        <x-adminlte-input name="doc_type" label="Тип документ" 
                            value="{{ html_entity_decode(old('doc_type', $customer->doc_type), ENT_QUOTES, 'UTF-8') }}" placeholder="..." />
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <x-adminlte-input name="receiver" label="Получател" 
                            value="{{ html_entity_decode(old('receiver', $customer->receiver), ENT_QUOTES, 'UTF-8') }}" placeholder="..." />
                    </div>
                    <div class="col-md-8">
                        <x-adminlte-textarea name="receiver_details" label="Детайли за получател" rows="2" placeholder="...">{{ html_entity_decode(old('receiver_details', $customer->receiver_details), ENT_QUOTES, 'UTF-8') }}</x-adminlte-textarea>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <x-adminlte-input name="eidate" label="Дата ЕИ" type="date" 
                            value="{{ old('eidate', optional($customer->eidate)->format('Y-m-d')) }}" />
                    </div>
                    <div class="col-md-4">
                        <x-adminlte-input name="partida" label="Partida" 
                            value="{{ html_entity_decode(old('partida', $customer->partida), ENT_QUOTES, 'UTF-8') }}" placeholder="..." />
                    </div>
                </div>
            </div>
        </div>

        {{-- Бележки и настройки --}}
        <div class="card card-secondary card-outline">
            <div class="card-header">
                <h3 class="card-title">Допълнително</h3>
            </div>
            <div class="card-body">
                <x-adminlte-textarea name="notes" label="Бележки" rows="3" placeholder="Вътрешни бележки...">{{ html_entity_decode(old('notes', $customer->notes), ENT_QUOTES, 'UTF-8') }}</x-adminlte-textarea>

                <div class="row">
                    <div class="col-md-4">
                        <x-adminlte-input-switch name="include_in_mailing" label="Включване в имейл групи" 
                            :checked="old('include_in_mailing', $customer->include_in_mailing)" />
                    </div>
                    <div class="col-md-4">
                        <x-adminlte-input-switch name="is_active" label="Активен" 
                            :checked="old('is_active', $customer->is_active)" />
                    </div>
                    <div class="col-md-4">
                        <x-adminlte-input-switch name="is_customer" label="Клиент" 
                            :checked="old('is_customer', $customer->is_customer)" />
                    </div>
                    <div class="col-md-4">
                        <x-adminlte-input-switch name="is_supplier" label="Доставчик" 
                            :checked="old('is_supplier', $customer->is_supplier)" />
                    </div>
                </div>
            </div>
        </div>

        {{-- АВТОМОБИЛИ --}}
        <div class="card card-info card-outline" id="vehicles-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-car"></i> Автомобили
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-sm btn-success" id="add-vehicle-row">
                        <i class="fas fa-plus"></i> Добави автомобил
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm table-bordered mb-0" id="vehicles-table">
                    <thead>
                        <tr>
                            <th>Марка/Модел</th>
                            <th style="width:140px;">Рег. номер</th>
                            <th style="width:170px;">VIN/Рама</th>
                            <th style="width:100px;">Пробег (км)</th>
                            <th>Бележки</th>
                            <th style="width:60px;">Актив.</th>
                            <th style="width:50px;"></th>
                        </tr>
                    </thead>
                    <tbody id="vehicles-container">
                        @foreach($customer->vehicles as $vehicle)
                            <tr class="vehicle-row">
                                <td>
                                    <input type="text" name="vehicles[][vehicle]" class="form-control form-control-sm" placeholder="Марка/модел" value="{{ html_entity_decode($vehicle->vehicle, ENT_QUOTES, 'UTF-8') }}">
                                    <input type="hidden" name="vehicles[][id]" value="{{ $vehicle->id }}">
                                </td>
                                <td>
                                    <input type="text" name="vehicles[][plate_number]" class="form-control form-control-sm" placeholder="Рег. номер" value="{{ html_entity_decode($vehicle->plate_number, ENT_QUOTES, 'UTF-8') }}">
                                </td>
                                <td>
                                    <input type="text" name="vehicles[][chassis_number]" class="form-control form-control-sm" placeholder="VIN/Рама" value="{{ html_entity_decode($vehicle->chassis_number, ENT_QUOTES, 'UTF-8') }}">
                                </td>
                                <td>
                                    <input type="number" name="vehicles[][last_mileage]" class="form-control form-control-sm" placeholder="км" value="{{ $vehicle->last_mileage }}">
                                </td>
                                <td>
                                    <input type="text" name="vehicles[][notes]" class="form-control form-control-sm" placeholder="Бележки" value="{{ html_entity_decode($vehicle->notes, ENT_QUOTES, 'UTF-8') }}">
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" name="vehicles[][is_active]" class="form-check-input" style="margin-left:0;" value="1" {{ $vehicle->is_active ? 'checked' : '' }}>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-danger remove-vehicle-row" title="Премахни">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <small class="text-muted">Добавете/редактирайте автомобили. Премахването ще изтрие автомобила при запис.</small>
            </div>
        </div>

        {{-- БУТОНИ --}}
        <div class="row mb-4">
            <div class="col-12 text-right">
                <x-adminlte-button type="submit" label="Запази промените" theme="success" icon="fas fa-save" />
                <a href="{{ route('admin.customers.index') }}" class="btn btn-default ml-2">Отказ</a>
            </div>
        </div>
    </form>
@stop

@section('js')
    <script>
        $(function() {
            const vehicleRowTemplate = `
                <tr class="vehicle-row">
                    <td>
                        <input type="text" name="vehicles[][vehicle]" class="form-control form-control-sm" placeholder="Марка/модел" value="">
                    </td>
                    <td>
                        <input type="text" name="vehicles[][plate_number]" class="form-control form-control-sm" placeholder="Рег. номер" value="">
                    </td>
                    <td>
                        <input type="text" name="vehicles[][chassis_number]" class="form-control form-control-sm" placeholder="VIN/Рама" value="">
                    </td>
                    <td>
                        <input type="number" name="vehicles[][last_mileage]" class="form-control form-control-sm" placeholder="км" value="">
                    </td>
                    <td>
                        <input type="text" name="vehicles[][notes]" class="form-control form-control-sm" placeholder="Бележки" value="">
                    </td>
                    <td class="text-center">
                        <input type="checkbox" name="vehicles[][is_active]" class="form-check-input" style="margin-left:0;" checked value="1">
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-danger remove-vehicle-row" title="Премахни">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;

            $('#add-vehicle-row').on('click', function() {
                $('#vehicles-container').append(vehicleRowTemplate);
            });

            $(document).on('click', '.remove-vehicle-row', function() {
                const row = $(this).closest('tr');
                if (row.find('input[name*="[id]"]').length) {
                    if (row.find('input[name*="[_delete]"]').length === 0) {
                        row.append('<input type="hidden" name="vehicles[][_delete]" value="1">');
                    }
                    row.hide();
                } else {
                    row.remove();
                }
            });
        });
    </script>
@stop