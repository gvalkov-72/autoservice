@extends('adminlte::page')

@section('title', 'Добави продукт')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0">
            <i class="fas fa-plus-circle text-success mr-2"></i>Добави нов продукт
        </h1>
        <div class="btn-group">
            <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Назад
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <div class="card">
                <div class="card-header bg-light">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-box mr-2"></i>Информация за продукта
                    </h3>
                </div>
                <form action="{{ route('admin.products.store') }}" method="POST" id="productForm">
                    @csrf
                    
                    <div class="card-body">
                        <!-- Табове за различни секции -->
                        <ul class="nav nav-tabs mb-4" id="productTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="basic-tab" data-toggle="tab" href="#basic" role="tab">
                                    <i class="fas fa-info-circle mr-2"></i>Основни данни
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="pricing-tab" data-toggle="tab" href="#pricing" role="tab">
                                    <i class="fas fa-tag mr-2"></i>Цени и ДДС
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="inventory-tab" data-toggle="tab" href="#inventory" role="tab">
                                    <i class="fas fa-warehouse mr-2"></i>Склад и инвентар
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="codes-tab" data-toggle="tab" href="#codes" role="tab">
                                    <i class="fas fa-barcode mr-2"></i>Кодове и баркод
                                </a>
                            </li>
                        </ul>
                        
                        <div class="tab-content" id="productTabsContent">
                            <!-- Таб Основни данни -->
                            <div class="tab-pane fade show active" id="basic" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="font-weight-bold">Име на продукта *</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <i class="fas fa-box"></i>
                                                    </span>
                                                </div>
                                                <input type="text" name="name" 
                                                       class="form-control @error('name') is-invalid @enderror" 
                                                       value="{{ old('name') }}" 
                                                       placeholder="Например: Диск спирачен преден" required>
                                            </div>
                                            @error('name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="font-weight-bold">Бранд/Марка</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <i class="fas fa-tag"></i>
                                                    </span>
                                                </div>
                                                <input type="text" name="brand" 
                                                       class="form-control @error('brand') is-invalid @enderror" 
                                                       value="{{ old('brand') }}" 
                                                       placeholder="Например: Bosch, Valeo">
                                            </div>
                                            @error('brand') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label>Описание</label>
                                    <textarea name="description" rows="3"
                                              class="form-control @error('description') is-invalid @enderror"
                                              placeholder="Подробно описание на продукта...">{{ old('description') }}</textarea>
                                    @error('description') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Мерна единица *</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <i class="fas fa-balance-scale"></i>
                                                    </span>
                                                </div>
                                                <select name="unit" class="form-control @error('unit') is-invalid @enderror" required>
                                                    <option value="">Изберете...</option>
                                                    <option value="бр." {{ old('unit') == 'бр.' ? 'selected' : '' }}>бр. (брой)</option>
                                                    <option value="кг" {{ old('unit') == 'кг' ? 'selected' : '' }}>кг (килограм)</option>
                                                    <option value="л" {{ old('unit') == 'л' ? 'selected' : '' }}>л (литър)</option>
                                                    <option value="м" {{ old('unit') == 'м' ? 'selected' : '' }}>м (метър)</option>
                                                    <option value="компл." {{ old('unit') == 'компл.' ? 'selected' : '' }}>компл. (комплект)</option>
                                                    <option value="оп." {{ old('unit') == 'оп.' ? 'selected' : '' }}>оп. (операция)</option>
                                                </select>
                                            </div>
                                            @error('unit') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Код на мерна единица</label>
                                            <input type="text" name="uom_code" 
                                                   class="form-control @error('uom_code') is-invalid @enderror" 
                                                   value="{{ old('uom_code') }}" 
                                                   placeholder="Например: PCE, KG, L">
                                            @error('uom_code') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Тип продукт</label>
                                            <div class="custom-control custom-switch mt-2">
                                                <input type="checkbox" name="is_service" 
                                                       class="custom-control-input @error('is_service') is-invalid @enderror" 
                                                       value="1" id="is_service" {{ old('is_service') ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="is_service">
                                                    Това е услуга (не се проследява инвентар)
                                                </label>
                                            </div>
                                            @error('is_service') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Статус</label>
                                            <div class="custom-control custom-switch mt-2">
                                                <input type="checkbox" name="is_active" 
                                                       class="custom-control-input @error('is_active') is-invalid @enderror" 
                                                       value="1" id="is_active" {{ old('is_active', true) ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="is_active">
                                                    Активен продукт
                                                </label>
                                            </div>
                                            @error('is_active') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Таб Цени и ДДС -->
                            <div class="tab-pane fade" id="pricing" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="font-weight-bold">Продажна цена *</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <i class="fas fa-money-bill-wave"></i>
                                                    </span>
                                                </div>
                                                <input type="number" name="price" step="0.01" min="0"
                                                       class="form-control @error('price') is-invalid @enderror" 
                                                       value="{{ old('price') }}" required>
                                                <div class="input-group-append">
                                                    <span class="input-group-text">лв.</span>
                                                </div>
                                            </div>
                                            @error('price') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="font-weight-bold">Себестойност</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <i class="fas fa-money-bill"></i>
                                                    </span>
                                                </div>
                                                <input type="number" name="cost_price" step="0.01" min="0"
                                                       class="form-control @error('cost_price') is-invalid @enderror" 
                                                       value="{{ old('cost_price') }}">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">лв.</span>
                                                </div>
                                            </div>
                                            @error('cost_price') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                            <small class="text-muted">За покупна цена или себестойност на производство</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>ДДС процент</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <i class="fas fa-percentage"></i>
                                                    </span>
                                                </div>
                                                <input type="number" name="vat_percent" step="0.01" min="0" max="100"
                                                       class="form-control @error('vat_percent') is-invalid @enderror" 
                                                       value="{{ old('vat_percent', 20) }}">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </div>
                                            @error('vat_percent') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Цена с ДДС</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <i class="fas fa-calculator"></i>
                                                    </span>
                                                </div>
                                                <input type="text" id="price_with_vat" class="form-control" readonly>
                                            </div>
                                            <small class="text-muted">Автоматично изчисление</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Последна покупна цена</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <i class="fas fa-shopping-cart"></i>
                                                    </span>
                                                </div>
                                                <input type="number" name="last_purchase_price" step="0.01" min="0"
                                                       class="form-control @error('last_purchase_price') is-invalid @enderror" 
                                                       value="{{ old('last_purchase_price') }}">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">лв.</span>
                                                </div>
                                            </div>
                                            @error('last_purchase_price') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Дата на последна покупка</label>
                                            <input type="date" name="last_purchase_date" 
                                                   class="form-control @error('last_purchase_date') is-invalid @enderror" 
                                                   value="{{ old('last_purchase_date') }}">
                                            @error('last_purchase_date') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Таб Склад и инвентар -->
                            <div class="tab-pane fade" id="inventory" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="font-weight-bold">Проследяване на инвентар</label>
                                            <div class="custom-control custom-switch mt-2">
                                                <input type="checkbox" name="track_inventory" 
                                                       class="custom-control-input @error('track_inventory') is-invalid @enderror" 
                                                       value="1" id="track_inventory" {{ old('track_inventory', true) ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="track_inventory">
                                                    Проследявай наличността
                                                </label>
                                            </div>
                                            @error('track_inventory') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle mr-2"></i>
                                            <strong>Забележка:</strong> Ако продуктът е услуга, инвентарът автоматично се изключва.
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row" id="inventoryFields">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Наличност</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <i class="fas fa-boxes"></i>
                                                    </span>
                                                </div>
                                                <input type="number" name="stock_quantity" min="0"
                                                       class="form-control @error('stock_quantity') is-invalid @enderror" 
                                                       value="{{ old('stock_quantity', 0) }}">
                                            </div>
                                            @error('stock_quantity') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Минимална наличност</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <i class="fas fa-exclamation-triangle"></i>
                                                    </span>
                                                </div>
                                                <input type="number" name="min_stock_level" min="0"
                                                       class="form-control @error('min_stock_level') is-invalid @enderror" 
                                                       value="{{ old('min_stock_level', 0) }}">
                                            </div>
                                            @error('min_stock_level') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                            <small class="text-muted">Предупреждение при достигане</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Ниво за повторна поръчка</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <i class="fas fa-sync-alt"></i>
                                                    </span>
                                                </div>
                                                <input type="number" name="reorder_level" min="0"
                                                       class="form-control @error('reorder_level') is-invalid @enderror" 
                                                       value="{{ old('reorder_level', 0) }}">
                                            </div>
                                            @error('reorder_level') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Време за доставка (дни)</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <i class="fas fa-clock"></i>
                                                    </span>
                                                </div>
                                                <input type="number" name="lead_time_days" min="0"
                                                       class="form-control @error('lead_time_days') is-invalid @enderror" 
                                                       value="{{ old('lead_time_days') }}">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">дни</span>
                                                </div>
                                            </div>
                                            @error('lead_time_days') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Местоположение в склад</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <i class="fas fa-map-marker-alt"></i>
                                                    </span>
                                                </div>
                                                <input type="text" name="location" 
                                                       class="form-control @error('location') is-invalid @enderror" 
                                                       value="{{ old('location') }}" 
                                                       placeholder="Например: Ред А, Стелаж 3">
                                            </div>
                                            @error('location') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Таб Кодове и баркод -->
                            <div class="tab-pane fade" id="codes" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="font-weight-bold">SKU код *</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <i class="fas fa-hashtag"></i>
                                                    </span>
                                                </div>
                                                <input type="text" name="sku" 
                                                       class="form-control @error('sku') is-invalid @enderror" 
                                                       value="{{ old('sku') }}" required
                                                       placeholder="Уникален идентификатор">
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-outline-secondary" id="generateSku">
                                                        <i class="fas fa-random"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            @error('sku') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                            <small class="text-muted">Стойността по подразбиране ще се използва и за баркод</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="font-weight-bold">Баркод</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <i class="fas fa-barcode"></i>
                                                    </span>
                                                </div>
                                                <input type="text" name="barcode" 
                                                       class="form-control @error('barcode') is-invalid @enderror" 
                                                       value="{{ old('barcode') }}"
                                                       placeholder="Баркод за сканиране">
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-outline-secondary" id="generateBarcode">
                                                        <i class="fas fa-barcode"></i> Генерирай
                                                    </button>
                                                </div>
                                            </div>
                                            @error('barcode') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                            <small class="text-muted">Оставете празно за автоматично генериране от SKU</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row mt-4">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Номер на продукта</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <i class="fas fa-sort-numeric-up"></i>
                                                    </span>
                                                </div>
                                                <input type="text" name="product_number" 
                                                       class="form-control @error('product_number') is-invalid @enderror" 
                                                       value="{{ old('product_number') }}"
                                                       placeholder="Вътрешен номер">
                                            </div>
                                            @error('product_number') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Старо ID (от Access)</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <i class="fas fa-database"></i>
                                                    </span>
                                                </div>
                                                <input type="text" name="old_id" 
                                                       class="form-control @error('old_id') is-invalid @enderror" 
                                                       value="{{ old('old_id') }}"
                                                       placeholder="PLU код от старата система">
                                            </div>
                                            @error('old_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                            <small class="text-muted">За запазване на връзка с предишна система</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Код на доставчика</label>
                                            <input type="text" name="supplier_code" 
                                                   class="form-control @error('supplier_code') is-invalid @enderror" 
                                                   value="{{ old('supplier_code') }}"
                                                   placeholder="Код от доставчика">
                                            @error('supplier_code') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Тест на баркод</label>
                                            <div class="border p-3 text-center" id="barcodePreview">
                                                <small class="text-muted">Баркод ще се генерира след запазване</small>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-info mt-2 w-100" id="testBarcodeBtn">
                                                <i class="fas fa-qrcode mr-1"></i> Тест на баркод сканиране
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-white">
                        <div class="d-flex justify-content-between">
                            <div>
                                <button type="submit" class="btn btn-success px-4">
                                    <i class="fas fa-save mr-2"></i> Запази продукта
                                </button>
                                <button type="reset" class="btn btn-outline-secondary">
                                    <i class="fas fa-undo mr-2"></i> Изчисти формата
                                </button>
                            </div>
                            <div>
                                <a href="{{ route('admin.products.index') }}" class="btn btn-outline-danger">
                                    <i class="fas fa-times mr-2"></i> Отказ
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Бързи съвети -->
            <div class="card mt-3">
                <div class="card-header bg-light">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-lightbulb mr-2"></i>Бързи съвети
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="alert alert-light">
                                <h6><i class="fas fa-barcode text-primary mr-2"></i>Баркод</h6>
                                <small>SKU кодът автоматично става баркод. Можете да го промените или да генерирате нов.</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="alert alert-light">
                                <h6><i class="fas fa-box text-success mr-2"></i>Инвентар</h6>
                                <small>За услуги изключете проследяването на инвентар, за да не се показват като стоки.</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="alert alert-light">
                                <h6><i class="fas fa-database text-warning mr-2"></i>Старо ID</h6>
                                <small>Ако импортирате от стара система, запазете старото ID за проследяване.</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@push('css')
<style>
    .nav-tabs .nav-link {
        border: none;
        color: #6c757d;
        font-weight: 500;
        padding: 0.75rem 1.25rem;
    }
    
    .nav-tabs .nav-link.active {
        color: #007bff;
        border-bottom: 3px solid #007bff;
        background: transparent;
    }
    
    .tab-content {
        padding: 1rem 0;
    }
    
    .custom-switch .custom-control-label::before {
        background-color: #adb5bd;
    }
    
    .custom-switch .custom-control-input:checked ~ .custom-control-label::before {
        background-color: #28a745;
    }
    
    #barcodePreview {
        min-height: 80px;
        background: #f8f9fa;
        border-radius: 4px;
    }
    
    .input-group-text {
        background-color: #f8f9fa;
        border-right: 0;
    }
</style>
@endpush

@push('js')
<script>
    $(document).ready(function() {
        // Табове
        $('#productTabs a').on('click', function (e) {
            e.preventDefault();
            $(this).tab('show');
        });
        
        // Генериране на SKU
        $('#generateSku').click(function() {
            const name = $('input[name="name"]').val();
            if (name) {
                // Генериране на SKU от името
                const sku = 'SKU_' + Math.random().toString(36).substr(2, 9).toUpperCase();
                $('input[name="sku"]').val(sku);
                
                // Копиране в баркод, ако е празно
                if (!$('input[name="barcode"]').val()) {
                    $('input[name="barcode"]').val(sku);
                }
            } else {
                alert('Моля, въведете име на продукта първо!');
            }
        });
        
        // Генериране на баркод
        $('#generateBarcode').click(function() {
            const sku = $('input[name="sku"]').val();
            if (sku) {
                $('input[name="barcode"]').val(sku);
            } else {
                alert('Моля, въведете или генерирайте SKU първо!');
            }
        });
        
        // Тест на баркод
        $('#testBarcodeBtn').click(function() {
            const barcode = $('input[name="barcode"]').val();
            const sku = $('input[name="sku"]').val();
            
            const codeToShow = barcode || sku;
            
            if (codeToShow) {
                $('#barcodePreview').html(`
                    <div class="font-weight-bold mb-1">${codeToShow}</div>
                    <div class="text-success">
                        <i class="fas fa-check-circle mr-1"></i>Баркодът е готов за сканиране
                    </div>
                    <small class="text-muted">Сканирайте с баркод четец за тест</small>
                `);
            } else {
                $('#barcodePreview').html(`
                    <div class="text-danger">
                        <i class="fas fa-exclamation-triangle mr-1"></i>Няма баркод за тест
                    </div>
                `);
            }
        });
        
        // Изчисляване на цена с ДДС
        function calculatePriceWithVat() {
            const price = parseFloat($('input[name="price"]').val()) || 0;
            const vatPercent = parseFloat($('input[name="vat_percent"]').val()) || 20;
            const priceWithVat = price * (1 + (vatPercent / 100));
            
            $('#price_with_vat').val(priceWithVat.toFixed(2) + ' лв.');
        }
        
        $('input[name="price"], input[name="vat_percent"]').on('input', calculatePriceWithVat);
        calculatePriceWithVat(); // Изчисляване при зареждане
        
        // Управление на инвентарните полета
        function toggleInventoryFields() {
            const isService = $('#is_service').is(':checked');
            const trackInventory = $('#track_inventory').is(':checked');
            
            if (isService || !trackInventory) {
                $('#inventoryFields').hide();
                $('#track_inventory').prop('checked', false);
            } else {
                $('#inventoryFields').show();
            }
        }
        
        $('#is_service, #track_inventory').change(toggleInventoryFields);
        toggleInventoryFields(); // Извикване при зареждане
        
        // Автоматично попълване на product_number от SKU
        $('input[name="sku"]').on('blur', function() {
            const sku = $(this).val();
            const productNumberInput = $('input[name="product_number"]');
            
            if (sku && !productNumberInput.val()) {
                productNumberInput.val(sku);
            }
        });
        
        // Валидация на формата
        $('#productForm').submit(function(e) {
            const sku = $('input[name="sku"]').val();
            const barcode = $('input[name="barcode"]').val();
            
            // Ако няма баркод, копираме SKU
            if (sku && !barcode) {
                $('input[name="barcode"]').val(sku);
            }
            
            return true;
        });
    });
</script>
@endpush