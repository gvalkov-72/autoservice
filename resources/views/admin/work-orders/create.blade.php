@extends('adminlte::page')

@section('title', 'Нова поръчка')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-plus-circle text-primary mr-2"></i>Нова поръчка</h1>
        <a href="{{ route('admin.work-orders.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left mr-1"></i> Назад
        </a>
    </div>
@stop

@section('content')
@php
    $rate = 1.95583;
    $showBgn = now()->lte('2026-01-31');
    
    function toBgn($amountEur, $rate = 1.95583, $decimals = 2) {
        return number_format($amountEur * $rate, $decimals, ',', ' ');
    }
    
    function formatEur($amountEur, $decimals = 2) {
        return number_format($amountEur, $decimals, ',', ' ');
    }
@endphp

<form action="{{ route('admin.work-orders.store') }}" method="POST" id="work-order-form">
    @csrf
    
    <div class="row">
        <!-- Основна информация -->
        <div class="col-lg-8">
            <div class="card card-outline card-primary">
                <div class="card-header border-bottom-0">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle mr-1"></i>
                        Основна информация
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="row">
                        <!-- Клиент -->
                        <div class="col-md-6">
                            <div class="form-group" style="position: relative;">
                                <label for="client_search" class="font-weight-bold text-primary">
                                    <i class="fas fa-user mr-1"></i>Клиент *
                                </label>
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control form-control-lg border-primary" 
                                           id="client_search" name="client_search" 
                                           placeholder="Име или телефон..." autocomplete="off">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-outline-primary" id="clear-client">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                <input type="hidden" id="customer_id" name="customer_id">
                                <div class="input-group mt-2">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light">
                                            <i class="fas fa-user-check"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="form-control border-success bg-light" 
                                           id="client_name" name="client_name" 
                                           value="{{ old('client_name') }}" required readonly>
                                </div>
                                <div id="client-results" style="display: none; position: absolute; z-index: 1000; width: 100%; max-height: 250px; overflow-y: auto; background: white; border: 1px solid #007bff; border-radius: 0.375rem; box-shadow: 0 0.5rem 1rem rgba(0,0,0,.15);"></div>
                                <small class="text-muted mt-1">
                                    <i class="fas fa-info-circle"></i> Въведете поне 2 символа за търсене
                                </small>
                                @error('client_name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <!-- Контакти -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="phone" class="font-weight-bold">
                                    <i class="fas fa-phone mr-1"></i>Телефон
                                </label>
                                <div class="input-group input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light">
                                            <i class="fas fa-phone-alt"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="form-control border-info bg-light" 
                                           id="phone" name="phone" value="{{ old('phone') }}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <!-- Автомобил -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="vehicle" class="font-weight-bold">
                                    <i class="fas fa-car mr-1"></i>Автомобил
                                </label>
                                <input type="text" class="form-control form-control-sm" 
                                       id="vehicle" name="vehicle" value="{{ old('vehicle') }}"
                                       placeholder="Марка и модел...">
                            </div>
                        </div>

                        <!-- Рег. номер -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="plate_number" class="font-weight-bold">
                                    <i class="fas fa-tag mr-1"></i>Рег. номер
                                </label>
                                <input type="text" class="form-control form-control-sm text-uppercase" 
                                       id="plate_number" name="plate_number" 
                                       value="{{ old('plate_number') }}" placeholder="AB 1234 CD">
                            </div>
                        </div>

                        <!-- VIN -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="chassis_number" class="font-weight-bold">
                                    <i class="fas fa-barcode mr-1"></i>VIN номер
                                </label>
                                <input type="text" class="form-control form-control-sm" 
                                       id="chassis_number" name="chassis_number" 
                                       value="{{ old('chassis_number') }}">
                            </div>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <!-- Дата -->
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="order_date" class="font-weight-bold">
                                    <i class="fas fa-calendar-alt mr-1"></i>Дата
                                </label>
                                <input type="date" class="form-control form-control-sm" 
                                       id="order_date" name="order_date" 
                                       value="{{ old('order_date', date('Y-m-d')) }}">
                            </div>
                        </div>

                        <!-- Пробег -->
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="mileage" class="font-weight-bold">
                                    <i class="fas fa-tachometer-alt mr-1"></i>Пробег (км)
                                </label>
                                <input type="number" class="form-control form-control-sm" 
                                       id="mileage" name="mileage" value="{{ old('mileage') }}" min="0">
                            </div>
                        </div>

                        <!-- Механик -->
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="mechanic_code" class="font-weight-bold">
                                    <i class="fas fa-tools mr-1"></i>Механик
                                </label>
                                <input type="number" class="form-control form-control-sm" 
                                       id="mechanic_code" name="mechanic_code" 
                                       value="{{ old('mechanic_code') }}">
                            </div>
                        </div>

                        <!-- Създадена от -->
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="created_by" class="font-weight-bold">
                                    <i class="fas fa-user-edit mr-1"></i>Създадена от
                                </label>
                                <input type="text" class="form-control form-control-sm" 
                                       id="created_by" name="created_by" 
                                       value="{{ old('created_by', auth()->user()->name) }}">
                            </div>
                        </div>
                    </div>

                    <!-- Бележки -->
                    <div class="form-group mt-3">
                        <label for="note" class="font-weight-bold">
                            <i class="fas fa-sticky-note mr-1"></i>Бележки
                        </label>
                        <textarea class="form-control form-control-sm" 
                                  id="note" name="note" rows="2" 
                                  placeholder="Допълнителни бележки...">{{ old('note') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Финансово обобщение -->
        <div class="col-lg-4">
            <div class="card card-outline card-success">
                <div class="card-header border-bottom-0">
                    <h3 class="card-title">
                        <i class="fas fa-calculator mr-1"></i>
                        Финансово обобщение
                    </h3>
                </div>
                <div class="card-body pt-0">
                    <!-- Стойност на труда -->
                    <div class="form-group">
                        <label for="service_amount" class="font-weight-bold">
                            <i class="fas fa-hard-hat mr-1"></i>Стойност на труда
                        </label>
                        <div class="input-group input-group-sm">
                            <input type="number" step="0.01" min="0" 
                                   class="form-control form-control-lg border-success text-right" 
                                   id="service_amount" name="service_amount" 
                                   value="{{ old('service_amount', 0) }}">
                            <div class="input-group-append">
                                <span class="input-group-text bg-success text-white">€</span>
                            </div>
                        </div>
                        @if($showBgn)
                        <small class="text-muted ml-2">
                            ≈ <span id="service_amount_bgn">0,00</span> лв
                        </small>
                        @endif
                    </div>

                    <hr class="my-3">

                    <!-- Обобщение -->
                    <div class="summary-box p-3 bg-light rounded">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Артикули:</span>
                            <strong>
                                <span id="items_total">0,00</span> €
                                @if($showBgn)
                                <br><small class="text-muted" id="items_total_bgn">0,00 лв</small>
                                @endif
                            </strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Труд:</span>
                            <strong>
                                <span id="service_display">0,00</span> €
                                @if($showBgn)
                                <br><small class="text-muted" id="service_display_bgn">0,00 лв</small>
                                @endif
                            </strong>
                        </div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between">
                            <h5 class="mb-0">ОБЩО:</h5>
                            <h4 class="mb-0 text-success">
                                <span id="grand_total">0,00</span> €
                                @if($showBgn)
                                <br><small class="text-muted" id="grand_total_bgn">0,00 лв</small>
                                @endif
                            </h4>
                        </div>
                    </div>

                    <!-- Информация -->
                    <div class="alert alert-info mt-3 py-2" role="alert">
                        <small>
                            <i class="fas fa-info-circle mr-1"></i>
                            Сумите се съхраняват в <strong>евро</strong> в базата данни.
                            @if($showBgn)
                            <br>Показването в лева е активна до 31.01.2026 г.
                            @endif
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Артикули и услуги -->
    <div class="row mt-3">
        <div class="col-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title d-flex justify-content-between align-items-center">
                        <span>
                            <i class="fas fa-boxes mr-1"></i>
                            Артикули и услуги
                        </span>
                        <button type="button" class="btn btn-success btn-sm" id="add-item">
                            <i class="fas fa-plus-circle mr-1"></i> Добави артикул
                        </button>
                    </h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="items-table">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width: 40px">#</th>
                                    <th style="width: 100px">Код</th>
                                    <th>Описание</th>
                                    <th style="width: 80px">Мярка</th>
                                    <th style="width: 100px">Количество</th>
                                    <th style="width: 120px">Ед. цена</th>
                                    <th style="width: 120px">Сума</th>
                                    <th style="width: 50px"></th>
                                </tr>
                            </thead>
                            <tbody id="items-container">
                                <!-- Динамично ще се добавят редове тук -->
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="text-center text-muted py-4" id="no-items-message">
                        <i class="fas fa-inbox fa-2x mb-2"></i><br>
                        Все още няма добавени артикули.<br>
                        <small>Натиснете "Добави артикул", за да започнете.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Бутони за действие -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card card-outline">
                <div class="card-body text-center py-3">
                    <button type="submit" class="btn btn-primary btn-lg px-5">
                        <i class="fas fa-save mr-2"></i> Запази поръчката
                    </button>
                    <a href="{{ route('admin.work-orders.index') }}" class="btn btn-outline-secondary btn-lg px-5 ml-2">
                        <i class="fas fa-times mr-2"></i> Отказ
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Шаблон за ред с артикул -->
<template id="item-template">
    <tr class="item-row">
        <td class="align-middle text-center item-index font-weight-bold">1</td>
        <td class="align-middle">
            <input type="text" class="form-control form-control-sm border-0 item-code" 
                   name="items[INDEX][item_code]" placeholder="КОД">
        </td>
        <td class="align-middle">
            <input type="text" class="form-control form-control-sm border-0 item-name" 
                   name="items[INDEX][item_name]" placeholder="Въведете описание..." required>
        </td>
        <td class="align-middle">
            <select class="form-control form-control-sm border-0 item-measure" name="items[INDEX][item_measure]">
                <option value="бр.">бр.</option>
                <option value="кг">кг</option>
                <option value="л">л</option>
                <option value="м">м</option>
                <option value="ч">ч</option>
                <option value="услуга">услуга</option>
            </select>
        </td>
        <td class="align-middle">
            <input type="number" step="0.01" min="0" value="1"
                   class="form-control form-control-sm border-0 text-right item-quantity" 
                   name="items[INDEX][quantity]">
        </td>
        <td class="align-middle">
            <div class="input-group input-group-sm">
                <input type="number" step="0.01" min="0" value="0"
                       class="form-control border-0 text-right item-price" 
                       name="items[INDEX][price_each]">
                <div class="input-group-append">
                    <span class="input-group-text bg-transparent">€</span>
                </div>
            </div>
            @if($showBgn)
            <small class="text-muted item-price-bgn">0,00 лв</small>
            @endif
        </td>
        <td class="align-middle">
            <div class="font-weight-bold item-total">0,00 €</div>
            @if($showBgn)
            <small class="text-muted item-total-bgn">0,00 лв</small>
            @endif
        </td>
        <td class="align-middle text-center">
            <button type="button" class="btn btn-danger btn-sm remove-item" title="Премахни">
                <i class="fas fa-trash-alt"></i>
            </button>
        </td>
    </tr>
</template>
@stop

@section('css')
<style>
    .card-header {
        background: linear-gradient(120deg, #f8f9fa 0%, #e9ecef 100%);
    }
    .card-outline {
        border-top: 3px solid #007bff;
    }
    .card-outline.card-success {
        border-top-color: #28a745;
    }
    .summary-box {
        border: 1px dashed #28a745;
        background-color: #f8fff9 !important;
    }
    .item-row:hover {
        background-color: #f8f9fa !important;
    }
    .item-row td {
        padding: 8px !important;
        vertical-align: middle !important;
    }
    .item-row input, .item-row select {
        background-color: transparent !important;
        border: 1px solid transparent !important;
        transition: border 0.3s;
    }
    .item-row input:focus, .item-row select:focus {
        border: 1px solid #80bdff !important;
        background-color: white !important;
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
    }
    .item-quantity, .item-price {
        text-align: right;
    }
    #no-items-message {
        display: block;
    }
    #items-container:has(tr) ~ #no-items-message {
        display: none;
    }
    
    /* КРИТИЧНО: Оправени стилове за автокомплит */
    #client-results {
        display: none;
        position: absolute;
        z-index: 9999;
        width: 100%;
        max-height: 250px;
        overflow-y: auto;
        background: white;
        border: 1px solid #007bff;
        border-radius: 0.375rem;
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,.15);
        margin-top: 5px;
    }
    
    .client-option {
        padding: 10px 15px;
        cursor: pointer;
        border-bottom: 1px solid #dee2e6;
        transition: all 0.2s;
    }
    
    .client-option:last-child {
        border-bottom: none;
    }
    
    .client-option:hover {
        background-color: #007bff !important;
        color: white !important;
    }
    
    .client-option:hover .client-phone {
        color: #e9ecef !important;
    }
    
    .client-name {
        font-weight: 600;
        color: #495057;
        margin-bottom: 2px;
    }
    
    .client-phone {
        color: #6c757d;
        font-size: 0.85em;
    }
    
    .form-control:read-only {
        background-color: #f8f9fa;
        cursor: not-allowed;
    }
    
    label {
        font-size: 0.9rem;
        margin-bottom: 0.3rem;
    }
</style>
@stop

@section('js')
<script>
$(document).ready(function() {
    const BGN_TO_EUR_RATE = {{ $rate }};
    const SHOW_BGN = {{ $showBgn ? 'true' : 'false' }};
    let itemCounter = 0;
    let searchTimer = null;
    
    // Функция за конвертиране ОТ евро КЪМ левове
    function toBgn(eur) {
        return (eur * BGN_TO_EUR_RATE).toFixed(2).replace('.', ',');
    }
    
    // Функция за форматиране на число
    function formatNumber(num) {
        return num.toFixed(2).replace('.', ',');
    }
    
    // Функция за преизчисляване на общите суми
    function updateTotals() {
        let itemsTotal = 0;
        let serviceAmount = parseFloat($('#service_amount').val()) || 0;
        
        // Сумиране на артикулите
        $('.item-row').each(function() {
            const quantity = parseFloat($(this).find('.item-quantity').val()) || 0;
            const price = parseFloat($(this).find('.item-price').val()) || 0;
            const rowTotal = quantity * price;
            itemsTotal += rowTotal;
        });
        
        // Общо
        const grandTotal = itemsTotal + serviceAmount;
        
        // Показване на сумите в евро
        $('#items_total').text(formatNumber(itemsTotal) + ' €');
        $('#service_display').text(formatNumber(serviceAmount) + ' €');
        $('#grand_total').text(formatNumber(grandTotal) + ' €');
        
        // Показване на сумите в левове (ако трябва)
        if (SHOW_BGN) {
            $('#items_total_bgn').text(toBgn(itemsTotal) + ' лв');
            $('#service_display_bgn').text(toBgn(serviceAmount) + ' лв');
            $('#grand_total_bgn').text(toBgn(grandTotal) + ' лв');
            $('#service_amount_bgn').text(toBgn(serviceAmount) + ' лв');
        }
    }
    
    // Функция за преизчисляване на сумата на ред
    function updateRowTotal(row) {
        const quantity = parseFloat(row.find('.item-quantity').val()) || 0;
        const price = parseFloat(row.find('.item-price').val()) || 0;
        const rowTotal = quantity * price;
        
        row.find('.item-total').text(formatNumber(rowTotal) + ' €');
        if (SHOW_BGN) {
            row.find('.item-total-bgn').text(toBgn(rowTotal) + ' лв');
            row.find('.item-price-bgn').text(toBgn(price) + ' лв');
        }
    }
    
    // Търсене на клиенти
    function searchClients(query) {
        if (query.length < 2) {
            $('#client-results').hide();
            return;
        }
        
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function() {
            $.ajax({
                url: "{{ route('admin.customers.search') }}",
                method: 'GET',
                data: { q: query },
                dataType: 'json',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data) {
                    const results = $('#client-results');
                    results.empty();
                    
                    if (data && data.length > 0) {
                        data.forEach(function(customer) {
                            const option = $('<div class="client-option"></div>');
                            option.html(`
                                <div class="client-name">${customer.name}</div>
                                <div class="client-phone">${customer.phone || 'Няма телефон'}</div>
                            `);
                            
                            option.data('customer', customer);
                            option.on('click', function() {
                                selectCustomer(customer);
                            });
                            
                            results.append(option);
                        });
                        
                        // Показваме резултатите
                        results.show();
                    } else {
                        results.hide();
                    }
                },
                error: function() {
                    $('#client-results').hide();
                }
            });
        }, 300);
    }
    
    // Избор на клиент
    function selectCustomer(customer) {
        $('#customer_id').val(customer.id);
        $('#client_name').val(customer.name);
        $('#phone').val(customer.phone || '');
        $('#client_search').val(customer.name);
        $('#client-results').hide();
        
        // Фокус в следващото поле
        setTimeout(() => $('#vehicle').focus(), 100);
    }
    
    // Изчистване на избрания клиент
    function clearCustomer() {
        $('#customer_id').val('');
        $('#client_name').val('');
        $('#phone').val('');
        $('#client_search').val('');
        $('#client_search').focus();
        $('#client-results').hide();
    }
    
    // Добавяне на нов артикул
    $('#add-item').on('click', function() {
        const template = document.getElementById('item-template');
        const clone = template.content.cloneNode(true);
        itemCounter++;
        
        // Замяна на INDEX с пореден номер
        $(clone).find('input, select').each(function() {
            this.name = this.name.replace(/INDEX/g, itemCounter);
        });
        
        $(clone).find('.item-index').text(itemCounter);
        
        $('#items-container').append(clone);
        $('#no-items-message').hide();
        
        // Инициализиране на събития за новия ред
        const newRow = $('#items-container').find('.item-row').last();
        initRowEvents(newRow);
        
        // Фокус върху полето за описание
        newRow.find('.item-name').focus();
        
        updateTotals();
    });
    
    // Инициализиране на събития за ред
    function initRowEvents(row) {
        row.find('.item-quantity, .item-price').on('input', function() {
            updateRowTotal(row);
            updateTotals();
        });
        
        row.find('.remove-item').on('click', function() {
            if (confirm('Сигурни ли сте, че искате да премахнете този артикул?')) {
                row.remove();
                updateItemIndexes();
                updateTotals();
                
                // Ако няма артикули, показваме съобщението
                if ($('.item-row').length === 0) {
                    $('#no-items-message').show();
                }
            }
        });
    }
    
    // Обновяване на номерата на редовете
    function updateItemIndexes() {
        $('.item-row').each(function(index) {
            $(this).find('.item-index').text(index + 1);
            
            // Обновяване на имената на полетата с новия индекс
            const newIndex = index + 1;
            $(this).find('input, select').each(function() {
                const name = this.name;
                const newName = name.replace(/items\[\d+\]/, `items[${newIndex}]`);
                this.name = newName;
            });
        });
        itemCounter = $('.item-row').length;
    }
    
    // Инициализиране при зареждане на страницата
    function init() {
        updateTotals();
        
        // Добавяме един празен ред по подразбиране
        $('#add-item').click();
        
        // Събития за автокомплит на клиенти
        $('#client_search').on('input', function() {
            searchClients($(this).val());
        });
        
        $('#client_search').on('focus', function() {
            if ($(this).val().length >= 2) {
                $('#client-results').show();
            }
        });
        
        // Скриване при клик извън
        $(document).on('click', function(e) {
            if (!$(e.target).closest('#client_search, #client-results').length) {
                $('#client-results').hide();
            }
        });
        
        $('#clear-client').on('click', function(e) {
            e.preventDefault();
            clearCustomer();
        });
        
        // Автоматично избиране на клиент при натискане на Enter
        $('#client_search').on('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const firstOption = $('.client-option:first');
                if (firstOption.length) {
                    firstOption.click();
                }
            }
            
            if (e.key === 'Escape') {
                $('#client-results').hide();
            }
        });
        
        // Събитие за промяна на стойността на труда
        $('#service_amount').on('input', updateTotals);
        
        // Автоматично форматиране на числата
        $('body').on('blur', 'input[type="number"]', function() {
            const value = parseFloat($(this).val());
            if (!isNaN(value)) {
                $(this).val(value.toFixed(2));
            }
        });
        
        // Фокус в полето за търсене на клиент
        setTimeout(() => {
            $('#client_search').focus();
        }, 100);
    }
    
    // Валидация на формата преди изпращане
    $('#work-order-form').on('submit', function(e) {
        let valid = true;
        const errors = [];
        
        // Проверка за задължително име на клиент
        if (!$('#client_name').val().trim()) {
            errors.push('Моля, изберете клиент от списъка!');
            $('#client_search').focus();
            valid = false;
        }
        
        // Проверка за валидни суми (не отрицателни)
        $('.item-price, .item-quantity, #service_amount').each(function() {
            const value = parseFloat($(this).val());
            if (value < 0) {
                errors.push('Стойностите не могат да бъдат отрицателни!');
                $(this).focus();
                valid = false;
                return false;
            }
        });
        
        // Проверка за поне един артикул с описание
        let hasValidItem = false;
        $('.item-name').each(function() {
            if ($(this).val().trim()) {
                hasValidItem = true;
                return false;
            }
        });
        
        if (!hasValidItem) {
            errors.push('Моля, добавете поне един артикул или услуга!');
            valid = false;
        }
        
        if (!valid) {
            e.preventDefault();
            let errorMessage = 'Моля, коригирайте следните грешки:\n\n';
            errors.forEach((error, index) => {
                errorMessage += `${index + 1}. ${error}\n`;
            });
            alert(errorMessage);
        } else {
            // Показване на съобщение за успешно запазване
            $(this).find('button[type="submit"]')
                .html('<i class="fas fa-spinner fa-spin mr-2"></i> Запазване...')
                .prop('disabled', true);
        }
    });
    
    // Инициализация
    init();
});
</script>
@stop