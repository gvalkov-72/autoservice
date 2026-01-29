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
                                    <input type="text" class="form-control border-success display-field" 
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
                                    <input type="text" class="form-control border-info display-field" 
                                           id="phone" name="phone" value="{{ old('phone') }}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- СЕКЦИЯ ЗА ИЗБОР НА АВТОМОБИЛ -->
                    <div class="form-group mt-3" id="vehicle-selection-group" style="display: none;">
                        <label for="vehicle_id" class="font-weight-bold text-primary">
                            <i class="fas fa-car mr-1"></i>Избор на автомобил <span id="vehicle-count" class="text-muted"></span>
                            <button type="button" class="btn btn-link btn-sm p-0 ml-2" id="switch-to-new-vehicle" style="display: none;">
                                <small><i class="fas fa-exchange-alt"></i> Добави нов</small>
                            </button>
                        </label>
                        <div class="input-group input-group-sm">
                            <select class="form-control form-control-lg border-primary vehicle-select" 
                                    id="vehicle_id" name="vehicle_id">
                                <option value="">-- Изберете автомобил --</option>
                            </select>
                            <div class="input-group-append">
                                <button type="button" class="btn btn-outline-primary" id="refresh-vehicles" title="Обнови списъка">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            </div>
                        </div>
                        <small class="text-muted">Изберете автомобил от списъка или добавете нов</small>
                    </div>

                    <!-- СЕКЦИЯ ЗА ДОБАВЯНЕ НА НОВ АВТОМОБИЛ -->
                    <div class="form-group mt-3" id="new-vehicle-group" style="display: none;">
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <button type="button" class="close" id="cancel-new-vehicle">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h5><i class="fas fa-car mr-1"></i>Добавяне на нов автомобил</h5>
                            <small>Попълнете данните на новия автомобил</small>
                        </div>
                    </div>

                    <!-- СЕКЦИЯ С ДЕТАЙЛИ ЗА АВТОМОБИЛА -->
                    <div class="row mt-3" id="vehicle-details" style="display: none;">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="vehicle" class="font-weight-bold">
                                    <i class="fas fa-car mr-1"></i>Автомобил
                                    <span class="vehicle-mode-badge badge badge-success ml-1" id="vehicle-mode">избран</span>
                                </label>
                                <input type="text" class="form-control form-control-sm vehicle-field" 
                                       id="vehicle" name="vehicle" 
                                       placeholder="Марка и модел..." 
                                       data-original-name="vehicle">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="plate_number" class="font-weight-bold">
                                    <i class="fas fa-tag mr-1"></i>Рег. номер
                                </label>
                                <input type="text" class="form-control form-control-sm vehicle-field text-uppercase" 
                                       id="plate_number" name="plate_number" 
                                       placeholder="AB 1234 CD"
                                       data-original-name="plate_number">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="chassis_number" class="font-weight-bold">
                                    <i class="fas fa-barcode mr-1"></i>VIN номер
                                </label>
                                <input type="text" class="form-control form-control-sm vehicle-field" 
                                       id="chassis_number" name="chassis_number"
                                       placeholder="VIN/Номер на рама"
                                       data-original-name="chassis_number">
                            </div>
                        </div>
                    </div>

                    <!-- БУТОН ЗА ДОБАВЯНЕ НА НОВ АВТОМОБИЛ -->
                    <div class="form-group mt-2" id="add-vehicle-button" style="display: none;">
                        <button type="button" class="btn btn-outline-warning btn-sm" id="add-new-vehicle-btn">
                            <i class="fas fa-plus-circle mr-1"></i> Добавете нов автомобил
                        </button>
                        <small class="text-muted ml-2">Ако желаният автомобил не е в списъка</small>
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
            <!-- Поле за код с автокомплит -->
            <div class="position-relative">
                <input type="text" class="form-control form-control-sm border-0 item-code-search" 
                       placeholder="Въведете код..." autocomplete="off">
                <input type="hidden" class="item-product-id" name="items[__INDEX__][product_id]">
                <input type="hidden" class="item-is-new-product" name="items[__INDEX__][is_new_product]" value="0">
                <input type="text" class="form-control form-control-sm border-0 item-code d-none" 
                       name="items[__INDEX__][item_code]" placeholder="КОД" readonly>
                <div class="product-results position-absolute w-100 bg-white border mt-1" style="display:none; z-index:1050; max-height:200px; overflow-y:auto;"></div>
            </div>
        </td>
        <td class="align-middle">
            <!-- Поле за име с автокомплит -->
            <div class="position-relative">
                <input type="text" class="form-control form-control-sm border-0 item-name-search" 
                       placeholder="Търсене на артикул..." autocomplete="off">
                <input type="text" class="form-control form-control-sm border-0 item-name d-none" 
                       name="items[__INDEX__][item_name]" placeholder="Описание..." required readonly>
                <div class="product-results position-absolute w-100 bg-white border mt-1" style="display:none; z-index:1050; max-height:200px; overflow-y:auto;"></div>
            </div>
        </td>
        <td class="align-middle">
            <select class="form-control form-control-sm border-0 item-measure" name="items[__INDEX__][item_measure]">
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
                   name="items[__INDEX__][quantity]">
        </td>
        <td class="align-middle">
            <div class="input-group input-group-sm">
                <input type="number" step="0.01" min="0" value="0"
                       class="form-control border-0 text-right item-price" 
                       name="items[__INDEX__][price_each]">
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

<!-- Hidden полета за нов автомобил -->
<input type="hidden" id="is_new_vehicle" name="is_new_vehicle" value="0">
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
    
    /* Стилове за автокомплит */
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
        cursor: pointer !important;
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
    
    /* Поля за display (readonly) */
    .display-field {
        background-color: #f8f9fa !important;
        cursor: text !important;
        user-select: text !important;
    }
    
    .display-field:hover {
        background-color: #e9ecef !important;
    }
    
    /* Select dropdown - ръчичка */
    .vehicle-select {
        cursor: pointer !important;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 0.75rem center;
        background-size: 16px 12px;
        padding-right: 2.5rem !important;
    }
    
    .vehicle-select option {
        cursor: pointer !important;
        padding: 8px !important;
    }
    
    /* Стилове за режим на нов автомобил */
    .vehicle-field {
        transition: all 0.3s;
    }
    
    .vehicle-field[readonly] {
        background-color: #f8f9fa !important;
        border-color: #28a745 !important;
    }
    
    .vehicle-field:not([readonly]) {
        background-color: white !important;
        border-color: #ffc107 !important;
    }
    
    /* Бейдж за режим */
    .vehicle-mode-badge {
        font-size: 0.7em;
        vertical-align: middle;
    }
    
    /* Анимации */
    .btn:hover, .btn-tool:hover {
        transform: translateY(-1px);
        transition: transform 0.2s;
    }
    
    /* Стилове за disabled елементи */
    .form-control:disabled {
        cursor: not-allowed !important;
        opacity: 0.6;
    }
    
    /* Ново: стилове за валидация */
    .is-invalid {
        border-color: #dc3545 !important;
        background-color: #fff5f5 !important;
    }
    
    .item-name:focus {
        border-color: #80bdff !important;
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
    }
    
    .border-0.is-invalid {
        border: 1px solid #dc3545 !important;
    }
    
    /* СТИЛОВЕ ЗА АВТОКОМПЛИТ НА АРТИКУЛИ */
    .product-results {
        border: 1px solid #007bff;
        border-radius: 0.25rem;
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,.15);
        z-index: 1050;
    }
    
    .product-option {
        cursor: pointer;
        transition: all 0.2s;
        padding: 8px 12px;
        border-bottom: 1px solid #dee2e6;
    }
    
    .product-option:last-child {
        border-bottom: none;
    }
    
    .product-option:hover {
        background-color: #007bff;
        color: white;
    }
    
    .product-option:hover .badge-light {
        background-color: #e9ecef !important;
        color: #495057 !important;
    }
    
    /* Показване/скриване на полета */
    .d-none {
        display: none !important;
    }
    
    /* Курсор за редактируеми полета */
    .item-code:not([readonly]), .item-name:not([readonly]) {
        cursor: text !important;
        background-color: #fff !important;
        border: 1px solid #ffc107 !important;
    }
    
    /* ПРЕМАХВАНЕ НА СТРЕЛКИТЕ В ЧИСЛОВИ ПОЛЕТА */
    /* Chrome, Safari, Edge, Opera */
    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    
    /* Firefox */
    input[type=number] {
        -moz-appearance: textfield;
        appearance: textfield;
    }
    
    /* Универсален стил за всички браузъри */
    input[type="number"] {
        appearance: textfield;
        -webkit-appearance: textfield;
        -moz-appearance: textfield;
    }
    
    /* Допълнителни стилове за числови полета */
    .form-control[type="number"] {
        padding-right: 0.75rem !important;
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
    let productSearchTimer = null;
    let isNewVehicleMode = false;
    
    // ============================================================================
    // ОСНОВНИ ФУНКЦИИ
    // ============================================================================
    
    function toBgn(eur) {
        return (eur * BGN_TO_EUR_RATE).toFixed(2).replace('.', ',');
    }
    
    function formatNumber(num) {
        return num.toFixed(2).replace('.', ',');
    }
    
    function updateTotals() {
        let itemsTotal = 0;
        let serviceAmount = parseFloat($('#service_amount').val()) || 0;
        
        $('.item-row').each(function() {
            const quantity = parseFloat($(this).find('.item-quantity').val()) || 0;
            const price = parseFloat($(this).find('.item-price').val()) || 0;
            const rowTotal = quantity * price;
            itemsTotal += rowTotal;
        });
        
        const grandTotal = itemsTotal + serviceAmount;
        
        $('#items_total').text(formatNumber(itemsTotal) + ' €');
        $('#service_display').text(formatNumber(serviceAmount) + ' €');
        $('#grand_total').text(formatNumber(grandTotal) + ' €');
        
        if (SHOW_BGN) {
            $('#items_total_bgn').text(toBgn(itemsTotal) + ' лв');
            $('#service_display_bgn').text(toBgn(serviceAmount) + ' лв');
            $('#grand_total_bgn').text(toBgn(grandTotal) + ' лв');
            $('#service_amount_bgn').text(toBgn(serviceAmount) + ' лв');
        }
    }
    
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
    
    // ============================================================================
    // ТЪРСЕНЕ И ИЗБОР НА КЛИЕНТ
    // ============================================================================
    
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
                        results.show();
                    }
                }
            });
        }, 300);
    }
    
    function selectCustomer(customer) {
        $('#customer_id').val(customer.id);
        $('#client_name').val(customer.name);
        $('#phone').val(customer.phone || '');
        $('#client_search').val(customer.name);
        $('#client-results').hide();
        
        // Ресет на автомобилната секция
        isNewVehicleMode = false;
        $('#is_new_vehicle').val('0');
        $('#vehicle_id').val('');
        $('#vehicle').val('');
        $('#plate_number').val('');
        $('#chassis_number').val('');
        $('#vehicle-details').hide();
        $('#new-vehicle-group').hide();
        $('#switch-to-new-vehicle').hide();
        
        loadCustomerVehicles(customer.id);
    }
    
    function clearCustomer() {
        $('#customer_id').val('');
        $('#client_name').val('');
        $('#phone').val('');
        $('#client_search').val('');
        $('#client-results').hide();
        
        $('#vehicle-selection-group').hide();
        $('#vehicle-details').hide();
        $('#new-vehicle-group').hide();
        $('#add-vehicle-button').hide();
        $('#vehicle_id').html('<option value="">-- Първо изберете клиент --</option>');
        $('#vehicle').val('');
        $('#plate_number').val('');
        $('#chassis_number').val('');
        $('#vehicle-count').text('');
    }
    
    // ============================================================================
    // УПРАВЛЕНИЕ НА АВТОМОБИЛИ
    // ============================================================================
    
    function loadCustomerVehicles(customerId) {
        if (!customerId) {
            $('#vehicle-selection-group').hide();
            $('#vehicle-details').hide();
            return;
        }
        
        $.ajax({
            url: `/admin/customers/${customerId}/vehicles`,
            method: 'GET',
            dataType: 'json',
            beforeSend: function() {
                $('#vehicle_id').html('<option value="">Зареждане на автомобили...</option>');
                $('#vehicle-count').text('(зареждане...)');
            },
            success: function(response) {
                if (response.success && response.vehicles && response.vehicles.length > 0) {
                    let options = '<option value="">-- Изберете автомобил --</option>';
                    
                    response.vehicles.forEach(function(vehicle) {
                        let displayText = vehicle.vehicle || 'Без модел';
                        if (vehicle.plate_number) {
                            displayText += ` (${vehicle.plate_number})`;
                        }
                        
                        options += `<option value="${vehicle.id}" 
                            data-model="${vehicle.vehicle || ''}"
                            data-plate="${vehicle.plate_number || ''}"
                            data-vin="${vehicle.chassis_number || ''}"
                            data-mileage="${vehicle.last_mileage || ''}">
                            ${displayText}
                        </option>`;
                    });
                    
                    $('#vehicle_id').html(options);
                    $('#vehicle-selection-group').show();
                    $('#vehicle-count').text(`(${response.vehicles.length} автомобила)`);
                    $('#add-vehicle-button').show();
                    
                } else {
                    $('#vehicle_id').html('<option value="">Няма регистрирани автомобили</option>');
                    $('#vehicle-selection-group').show();
                    $('#vehicle-count').text('(няма автомобили)');
                    $('#add-vehicle-button').show();
                }
            },
            error: function() {
                $('#vehicle_id').html('<option value="">Грешка при зареждане</option>');
                $('#vehicle-count').text('(грешка)');
                $('#vehicle-selection-group').show();
                $('#add-vehicle-button').show();
            }
        });
    }
    
    function switchToNewVehicleMode() {
        isNewVehicleMode = true;
        $('#is_new_vehicle').val('1');
        
        // Променяме интерфейса
        $('#vehicle-selection-group').hide();
        $('#new-vehicle-group').show();
        $('#add-vehicle-button').hide();
        $('#switch-to-new-vehicle').hide();
        
        // Променяме полетата за автомобил
        $('.vehicle-field').prop('readonly', false)
            .removeClass('display-field')
            .css('border-color', '#ffc107');
        
        $('#vehicle-mode').text('нов').removeClass('badge-success').addClass('badge-warning');
        $('#vehicle-details').show();
        
        // Изчистваме select за автомобили
        $('#vehicle_id').val('');
        
        // Фокус върху първото поле
        setTimeout(() => $('#vehicle').focus(), 100);
    }
    
    function switchToSelectVehicleMode() {
        isNewVehicleMode = false;
        $('#is_new_vehicle').val('0');
        
        // Връщаме интерфейса
        $('#vehicle-selection-group').show();
        $('#new-vehicle-group').hide();
        $('#add-vehicle-button').show();
        
        // Променяме полетата за автомобил обратно на readonly
        $('.vehicle-field').prop('readonly', true)
            .addClass('display-field')
            .css('border-color', '#28a745');
        
        $('#vehicle-mode').text('избран').removeClass('badge-warning').addClass('badge-success');
        
        // Изчистваме полетата
        if (!$('#vehicle_id').val()) {
            $('#vehicle').val('');
            $('#plate_number').val('');
            $('#chassis_number').val('');
            $('#vehicle-details').hide();
        }
    }
    
    // ============================================================================
    // ТЪРСЕНЕ НА АРТИКУЛИ (PRODUCTS) - НОВА ФУНКЦИОНАЛНОСТ
    // ============================================================================
    
    function searchProducts(query, row, fieldType = 'name') {
        if (query.length < 2) {
            row.find('.product-results').hide();
            return;
        }
        
        clearTimeout(productSearchTimer);
        productSearchTimer = setTimeout(function() {
            $.ajax({
                url: "{{ route('admin.products.search') }}",
                method: 'GET',
                data: { q: query },
                dataType: 'json',
                success: function(products) {
                    const results = row.find('.product-results');
                    results.empty();
                    
                    if (products && products.length > 0) {
                        products.forEach(function(product) {
                            const option = $('<div class="product-option p-2 border-bottom cursor-pointer"></div>');
                            option.html(`
                                <div class="font-weight-bold">${product.name}</div>
                                <div class="small">
                                    <span class="badge badge-light mr-2">${product.code || 'Без код'}</span>
                                    <span class="text-muted">${product.uom || 'бр.'} | ${product.price} €</span>
                                </div>
                            `);
                            
                            option.data('product', product);
                            option.on('click', function() {
                                selectProduct(product, row);
                                results.hide();
                            });
                            
                            option.on('mouseenter', function() {
                                $(this).addClass('bg-primary text-white');
                            }).on('mouseleave', function() {
                                $(this).removeClass('bg-primary text-white');
                            });
                            
                            results.append(option);
                        });
                        results.show();
                    } else {
                        results.hide();
                    }
                },
                error: function() {
                    console.log('Грешка при търсене на артикули');
                    row.find('.product-results').hide();
                }
            });
        }, 300);
    }
    
    function selectProduct(product, row) {
        // Маркираме, че това е съществуващ продукт
        row.find('.item-product-id').val(product.id);
        row.find('.item-is-new-product').val('0');
        
        // Попълваме полетата
        row.find('.item-code-search').val(product.code || '');
        row.find('.item-code').val(product.code || '').removeClass('d-none');
        row.find('.item-name-search').val(product.name);
        row.find('.item-name').val(product.name).removeClass('d-none');
        row.find('.item-measure').val(product.uom || 'бр.');
        row.find('.item-price').val(product.price || 0);
        
        // Скриваме полетата за търсене
        row.find('.item-code-search').addClass('d-none');
        row.find('.item-name-search').addClass('d-none');
        
        // Обновяваме сумата
        updateRowTotal(row);
        updateTotals();
    }
    
    function enableManualProductEntry(row) {
        // Показваме полетата за ръчно въвеждане
        row.find('.item-code-search').addClass('d-none');
        row.find('.item-name-search').addClass('d-none');
        row.find('.item-code').removeClass('d-none').prop('readonly', false);
        row.find('.item-name').removeClass('d-none').prop('readonly', false);
        
        // Маркираме като нов продукт
        row.find('.item-is-new-product').val('1');
        row.find('.item-product-id').val('');
        
        // Фокус върху полето за име
        setTimeout(() => row.find('.item-name').focus(), 100);
    }
    
    // ============================================================================
    // УПРАВЛЕНИЕ НА АРТИКУЛИТЕ
    // ============================================================================
    
    $('#add-item').on('click', function() {
        const template = document.getElementById('item-template');
        const clone = template.content.cloneNode(true);
        
        // Заменяме __INDEX__ с текущия itemCounter
        const currentIndex = itemCounter;
        $(clone).find('input, select').each(function() {
            if (this.name) {
                this.name = this.name.replace(/__INDEX__/g, currentIndex);
            }
        });
        
        // Визуален номер (започва от 1)
        $(clone).find('.item-index').text(currentIndex + 1);
        
        $('#items-container').append(clone);
        $('#no-items-message').hide();
        
        const newRow = $('#items-container').find('.item-row').last();
        initRowEvents(newRow);
        newRow.find('.item-name-search').focus();
        updateTotals();
        
        // Увеличаваме брояча
        itemCounter++;
    });
    
    function initRowEvents(row) {
        // Промяна на количество и цена
        row.find('.item-quantity, .item-price').on('input', function() {
            updateRowTotal(row);
            updateTotals();
        });
        
        // Премахване на артикул
        row.find('.remove-item').on('click', function() {
            if (confirm('Премахване на артикула?')) {
                row.remove();
                reindexAllItems();
                updateTotals();
                if ($('.item-row').length === 0) {
                    $('#no-items-message').show();
                }
            }
        });
        
        // Автокомплит за код на продукт
        row.find('.item-code-search').on('input', function() {
            const query = $(this).val();
            searchProducts(query, row, 'code');
        });
        
        // Автокомплит за име на продукт
        row.find('.item-name-search').on('input', function() {
            const query = $(this).val();
            searchProducts(query, row, 'name');
        });
        
        // При фокус в полета за търсене - показваме резултати, ако има
        row.find('.item-code-search, .item-name-search').on('focus', function() {
            const query = $(this).val();
            if (query.length >= 2) {
                searchProducts(query, row);
            }
        });
        
        // При натискане на Enter в полето за име - позволяваме ръчно въвеждане
        row.find('.item-name-search').on('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                if ($(this).val().trim() !== '') {
                    enableManualProductEntry(row);
                }
            }
        });
        
        // Клас за ръчно въвеждане
        row.find('.item-code, .item-name').on('dblclick', function() {
            if (confirm('Искате ли да редактирате този артикул ръчно?')) {
                enableManualProductEntry(row);
            }
        });
        
        // При загуба на фокус, ако имаме въведено име, маркираме като нов продукт
        row.find('.item-name-search').on('blur', function() {
            const name = $(this).val().trim();
            if (name !== '' && row.find('.item-product-id').val() === '') {
                // Ако имаме въведено име, но не сме избрали от списъка
                setTimeout(() => {
                    if (row.find('.item-product-id').val() === '') {
                        enableManualProductEntry(row);
                        row.find('.item-name').val(name);
                    }
                }, 200);
            }
        });
        
        // Слушател за външен клик за скриване на резултатите
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.item-code-search, .item-name-search, .product-results').length) {
                row.find('.product-results').hide();
            }
        });
    }
    
    function reindexAllItems() {
        $('.item-row').each(function(index) {
            // Визуален номер
            $(this).find('.item-index').text(index + 1);
            
            // Заменяме всички имена на полетата
            $(this).find('input, select').each(function() {
                const field = $(this);
                const oldName = field.attr('name');
                if (oldName) {
                    // Намираме текущия индекс в името
                    const match = oldName.match(/items\[(\d+)\]/);
                    if (match) {
                        const newName = oldName.replace(/items\[\d+\]/, `items[${index}]`);
                        field.attr('name', newName);
                    }
                }
            });
        });
        
        // Обновяваме брояча
        itemCounter = $('.item-row').length;
    }
    
    // ============================================================================
    // СЛУШАТЕЛИ ЗА СЪБИТИЯ
    // ============================================================================
    
    // Клиенти
    $('#client_search').on('input', function() {
        searchClients($(this).val());
    });
    
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#client_search, #client-results').length) {
            $('#client-results').hide();
        }
    });
    
    $('#clear-client').on('click', function(e) {
        e.preventDefault();
        clearCustomer();
    });
    
    $('#client_search').on('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const firstOption = $('.client-option:first');
            if (firstOption.length) firstOption.click();
        }
        if (e.key === 'Escape') $('#client-results').hide();
    });
    
    // Автомобили
    $(document).on('change', '#vehicle_id', function() {
        const selectedOption = $(this).find('option:selected');
        const vehicleId = $(this).val();
        
        if (vehicleId) {
            // Попълваме полетата
            $('#vehicle').val(selectedOption.data('model') || '');
            $('#plate_number').val(selectedOption.data('plate') || '');
            $('#chassis_number').val(selectedOption.data('vin') || '');
            
            if (selectedOption.data('mileage')) {
                $('#mileage').val(selectedOption.data('mileage'));
            }
            
            // Променяме интерфейса
            $('.vehicle-field').prop('readonly', true).addClass('display-field');
            $('#vehicle-details').show();
            $('#switch-to-new-vehicle').show();
            isNewVehicleMode = false;
            $('#is_new_vehicle').val('0');
            
        } else {
            $('#vehicle-details').hide();
            $('#switch-to-new-vehicle').hide();
        }
    });
    
    // Добавяне на нов автомобил
    $('#add-new-vehicle-btn, #switch-to-new-vehicle').on('click', function() {
        switchToNewVehicleMode();
    });
    
    $('#cancel-new-vehicle').on('click', function() {
        if (confirm('Отказ от добавяне на нов автомобил?')) {
            switchToSelectVehicleMode();
        }
    });
    
    // При започване на писане в полета за автомобил
    $(document).on('focus', '.vehicle-field', function() {
        if (!isNewVehicleMode && $(this).prop('readonly')) {
            if (confirm('Искате ли да добавите нов автомобил?')) {
                switchToNewVehicleMode();
            }
        }
    });
    
    $('#refresh-vehicles').on('click', function() {
        const customerId = $('#customer_id').val();
        if (customerId) {
            loadCustomerVehicles(customerId);
        } else {
            alert('Първо изберете клиент!');
        }
    });
    
    // ============================================================================
    // ВАЛИДАЦИЯ И ИНИЦИАЛИЗАЦИЯ
    // ============================================================================
    
    $('#work-order-form').on('submit', function(e) {
        let valid = true;
        const errors = [];
        
        if (!$('#client_name').val().trim()) {
            errors.push('Изберете клиент!');
            valid = false;
        }
        
        const vehicle = $('#vehicle').val().trim();
        const plateNumber = $('#plate_number').val().trim();
        
        if (!vehicle || !plateNumber) {
            errors.push('Попълнете автомобил и регистрационен номер!');
            valid = false;
        } else if (!isNewVehicleMode && !$('#vehicle_id').val()) {
            errors.push('Изберете автомобил от списъка или добавете нов!');
            valid = false;
        }
        
        // Проверка дали има поне един артикул с попълнено име
        let hasValidItems = false;
        $('.item-name').each(function() {
            if ($(this).val().trim() !== '') {
                hasValidItems = true;
            }
        });
        
        if (!hasValidItems && parseFloat($('#service_amount').val()) === 0) {
            errors.push('Добавете поне един артикул с описание или стойност на труда!');
            valid = false;
        }
        
        // Проверка за корекни данни в артикули
        $('.item-row').each(function(index) {
            const itemName = $(this).find('.item-name').val().trim();
            const itemQuantity = parseFloat($(this).find('.item-quantity').val()) || 0;
            
            if (itemName && itemQuantity === 0) {
                errors.push(`Артикул "${itemName.substring(0, 30)}..." има количество 0`);
                $(this).find('.item-quantity').addClass('is-invalid');
                valid = false;
            }
        });
        
        if (!valid) {
            e.preventDefault();
            alert('Моля, коригирайте следните грешки:\n\n' + errors.join('\n'));
            return false;
        }
        
        // Показване на индикатор за зареждане
        $(this).find('button[type="submit"]')
            .html('<i class="fas fa-spinner fa-spin mr-2"></i> Запазване...')
            .prop('disabled', true);
    });
    
    // ============================================================================
    // ИНИЦИАЛИЗАЦИЯ
    // ============================================================================
    
    function init() {
        updateTotals();
        
        // Добавяме автоматично първи артикул
        $('#add-item').click();
        
        // Слушател за стойност на труда
        $('#service_amount').on('input', updateTotals);
        
        // Форматиране на числата при излизане от полето
        $('body').on('blur', 'input[type="number"]', function() {
            const value = parseFloat($(this).val());
            if (!isNaN(value)) {
                $(this).val(value.toFixed(2));
            }
        });
        
        // Фокус върху търсенето на клиент
        setTimeout(() => $('#client_search').focus(), 100);
    }
    
    init();
});
</script>
@stop