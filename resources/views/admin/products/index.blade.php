@extends('adminlte::page')

@section('title', 'Продукти')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0">
            <i class="fas fa-boxes text-primary mr-2"></i>Управление на продукти
        </h1>
        <div>
            <!-- Групов експорт -->
            <div class="btn-group mr-2" role="group">
                <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-toggle="dropdown"
                    aria-expanded="false">
                    <i class="fas fa-file-export mr-1"></i>Експорт
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="{{ route('admin.products.export.all', ['format' => 'pdf']) }}">
                        <i class="fas fa-file-pdf text-danger mr-1"></i>PDF (всички)
                    </a>
                    <a class="dropdown-item" href="{{ route('admin.products.export.all', ['format' => 'excel']) }}">
                        <i class="fas fa-file-excel text-success mr-1"></i>Excel (всички)
                    </a>
                    <a class="dropdown-item" href="{{ route('admin.products.export.all', ['format' => 'csv']) }}">
                        <i class="fas fa-file-csv text-info mr-1"></i>CSV (всички)
                    </a>
                </div>
            </div>

            <!-- Нов продукт -->
            <a href="{{ route('admin.products.create') }}" class="btn btn-success">
                <i class="fas fa-plus mr-1"></i>Нов продукт
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-primary card-outline">
                <div class="card-header border-bottom-0 pb-0">
                    <h3 class="card-title">
                        <i class="fas fa-list mr-1"></i>
                        Списък на продукти
                    </h3>

                    <!-- Филтри и търсене -->
                    <div class="card-tools d-flex align-items-center" style="gap: 10px;">
                        <!-- Филтър по тип продукт -->
                        <div class="input-group input-group-sm" style="width: 200px;">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-filter"></i>
                                </span>
                            </div>
                            <select id="filterProductType" class="form-control">
                                <option value="">Всички типове</option>
                                <option value="product">Стоки</option>
                                <option value="service">Услуги</option>
                            </select>
                        </div>

                        <!-- Филтър по активност -->
                        <div class="input-group input-group-sm" style="width: 180px;">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-check-circle"></i>
                                </span>
                            </div>
                            <select id="filterStatus" class="form-control">
                                <option value="">Всички статуси</option>
                                <option value="active">Активни</option>
                                <option value="inactive">Неактивни</option>
                            </select>
                        </div>

                        <!-- Филтър по наличност -->
                        <div class="input-group input-group-sm" style="width: 200px;">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-box"></i>
                                </span>
                            </div>
                            <select id="filterStockStatus" class="form-control">
                                <option value="">Всякаква наличност</option>
                                <option value="in_stock">В наличност</option>
                                <option value="low">Ниски наличности</option>
                                <option value="out">Изчерпани</option>
                            </select>
                        </div>

                        <!-- Търсачка -->
                        <div class="input-group input-group-sm" style="width: 300px;">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                            </div>
                            <input type="text" id="quickSearch" class="form-control"
                                placeholder="Търсене по име, SKU, баркод...">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" id="clearSearch"
                                    title="Изчисти търсене">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bulk Action Form -->
                <form id="bulkActionForm" action="{{ route('admin.products.bulk-action') }}" method="POST" class="mb-0">
                    @csrf
                    <input type="hidden" name="action" id="bulkActionInput">
                    <input type="hidden" name="selected_ids" id="selectedIdsInput">

                    <div class="card-header bg-light py-2 d-none" id="bulkActionHeader">
                        <div class="d-flex align-items-center">
                            <div class="custom-control custom-checkbox mr-3">
                                <input type="checkbox" class="custom-control-input" id="selectAllCheckbox">
                                <label class="custom-control-label" for="selectAllCheckbox">
                                    <span id="selectedCount">0</span> избрани
                                </label>
                            </div>

                            <div class="btn-group btn-group-sm mr-2">
                                <button type="button" class="btn btn-outline-secondary dropdown-toggle"
                                    data-toggle="dropdown" id="bulkActionButton" disabled>
                                    Групово действие
                                </button>
                                <div class="dropdown-menu">
                                    <h6 class="dropdown-header">Статус</h6>
                                    <button type="button" class="dropdown-item bulk-action-item" data-action="activate">
                                        <i class="fas fa-check text-success mr-2"></i>Активирай
                                    </button>
                                    <button type="button" class="dropdown-item bulk-action-item"
                                        data-action="deactivate">
                                        <i class="fas fa-times text-danger mr-2"></i>Деактивирай
                                    </button>

                                    <div class="dropdown-divider"></div>
                                    <h6 class="dropdown-header">Тип</h6>
                                    <button type="button" class="dropdown-item bulk-action-item" data-action="mark_as_product">
                                        <i class="fas fa-box text-info mr-2"></i>Маркирай като стока
                                    </button>
                                    <button type="button" class="dropdown-item bulk-action-item" data-action="mark_as_service">
                                        <i class="fas fa-concierge-bell text-warning mr-2"></i>Маркирай като услуга
                                    </button>

                                    <div class="dropdown-divider"></div>
                                    <h6 class="dropdown-header">Инвентар</h6>
                                    <button type="button" class="dropdown-item bulk-action-item"
                                        data-action="enable_inventory">
                                        <i class="fas fa-toggle-on text-primary mr-2"></i>Включи инвентар
                                    </button>
                                    <button type="button" class="dropdown-item bulk-action-item"
                                        data-action="disable_inventory">
                                        <i class="fas fa-toggle-off text-secondary mr-2"></i>Изключи инвентар
                                    </button>

                                    <div class="dropdown-divider"></div>
                                    <h6 class="dropdown-header">Баркод</h6>
                                    <button type="button" class="dropdown-item bulk-action-item" data-action="generate_barcodes">
                                        <i class="fas fa-barcode text-dark mr-2"></i>Генерирай баркодове
                                    </button>

                                    <div class="dropdown-divider"></div>
                                    <h6 class="dropdown-header">Експорт</h6>
                                    <button type="button" class="dropdown-item bulk-action-item" data-action="export">
                                        <i class="fas fa-file-export text-primary mr-2"></i>Експортирай избраните
                                    </button>
                                </div>
                            </div>

                            <button type="button" class="btn btn-sm btn-outline-danger" id="clearSelection">
                                <i class="fas fa-times mr-1"></i>Изчисти избор
                            </button>
                        </div>
                    </div>

                    <div class="card-body">
                        <table class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th width="50" class="text-center">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="masterCheckbox">
                                            <label class="custom-control-label" for="masterCheckbox"></label>
                                        </div>
                                    </th>
                                    <th width="60" class="text-center">ID</th>
                                    <th>Продукт</th>
                                    <th width="120">Кодове</th>
                                    <th width="100">Наличност</th>
                                    <th width="100">Цена</th>
                                    <th width="100">Статус</th>
                                    <th width="140" class="text-center">Действия</th>
                                </tr>
                            </thead>
                            <tbody id="productsTableBody">
                                @foreach ($products as $product)
                                    <tr data-product-type="{{ $product->is_service ? 'service' : 'product' }}"
                                        data-status="{{ $product->is_active ? 'active' : 'inactive' }}"
                                        data-stock-status="{{ $product->is_out_of_stock ? 'out' : ($product->is_low_stock ? 'low' : 'in_stock') }}">
                                        <td class="text-center">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input product-checkbox"
                                                    id="product_{{ $product->id }}" value="{{ $product->id }}">
                                                <label class="custom-control-label"
                                                    for="product_{{ $product->id }}"></label>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-light text-dark">#{{ $product->id }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex">
                                                <div class="mr-2">
                                                    @if ($product->is_service)
                                                        <span class="badge bg-warning p-2" title="Услуга">
                                                            <i class="fas fa-concierge-bell"></i>
                                                        </span>
                                                    @else
                                                        <span class="badge bg-info p-2" title="Стокa">
                                                            <i class="fas fa-box"></i>
                                                        </span>
                                                    @endif
                                                </div>
                                                <div>
                                                    <a href="{{ route('admin.products.show', $product) }}"
                                                        class="text-dark font-weight-normal product-name"
                                                        style="font-size: 0.95em; text-decoration: none;">
                                                        {{ $product->name }}
                                                    </a>
                                                    @if ($product->brand)
                                                        <div>
                                                            <small class="text-muted">
                                                                <i class="fas fa-tag fa-xs mr-1"></i>{{ $product->brand }}
                                                            </small>
                                                        </div>
                                                    @endif
                                                    @if ($product->description)
                                                        <div>
                                                            <small class="text-muted">
                                                                <i class="fas fa-info-circle fa-xs mr-1"></i>
                                                                {{ Str::limit($product->description, 50) }}
                                                            </small>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        @if($product->old_id)
                                                            <small class="text-muted">
                                                                <i class="fas fa-database fa-xs mr-1"></i>Старо ID: {{ $product->old_id }}
                                                            </small>
                                                        @endif
                                                        @if($product->product_number)
                                                            <small class="text-muted ml-2">
                                                                <i class="fas fa-hashtag fa-xs mr-1"></i>Номер: {{ $product->product_number }}
                                                            </small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="mb-1">
                                                <strong>SKU:</strong>
                                                <span class="badge badge-light">{{ $product->sku }}</span>
                                            </div>
                                            @if ($product->barcode)
                                                <div class="mb-1">
                                                    <strong>Баркод:</strong>
                                                    <span class="badge badge-secondary">{{ $product->barcode }}</span>
                                                </div>
                                            @endif
                                            <div>
                                                <strong>М.ед:</strong>
                                                <span class="badge badge-info">{{ $product->unit }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            @if ($product->track_inventory)
                                                <div class="text-center">
                                                    <span class="h5">{{ $product->stock_quantity }}</span>
                                                    <div>
                                                        @if ($product->is_out_of_stock)
                                                            <span class="badge badge-danger">Изчерпано</span>
                                                        @elseif ($product->is_low_stock)
                                                            <span class="badge badge-warning">Ниско</span>
                                                        @else
                                                            <span class="badge badge-success">Налично</span>
                                                        @endif
                                                    </div>
                                                    @if ($product->min_stock_level > 0)
                                                        <small class="text-muted">мин: {{ $product->min_stock_level }}</small>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="badge badge-secondary">Без инвентар</span>
                                            @endif
                                        </td>
                                        <td class="text-right">
                                            <div class="font-weight-bold">{{ number_format($product->price, 2) }} лв.</div>
                                            @if ($product->cost_price)
                                                <small class="text-muted">
                                                    себест: {{ number_format($product->cost_price, 2) }} лв.
                                                </small>
                                                <br>
                                                <small class="text-success">
                                                    марж: {{ number_format($product->profit_margin, 1) }}%
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($product->is_active)
                                                <span class="badge badge-success">Активен</span>
                                            @else
                                                <span class="badge badge-secondary">Неактивен</span>
                                            @endif
                                            <br>
                                            @if (!$product->track_inventory)
                                                <small class="text-muted">
                                                    <i class="fas fa-ban fa-xs"></i> Без проследяване
                                                </small>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('admin.products.show', $product) }}"
                                                    class="btn btn-info" title="Преглед">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.products.edit', $product) }}"
                                                    class="btn btn-primary" title="Редактирай">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @if ($product->barcode)
                                                    <a href="{{ route('admin.products.print-barcode', $product) }}"
                                                       class="btn btn-dark" title="Печат на баркод">
                                                        <i class="fas fa-barcode"></i>
                                                    </a>
                                                @endif
                                                <form action="{{ route('admin.products.destroy', $product) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-danger"
                                                        onclick="return confirm('Сигурни ли сте, че искате да деактивирате този продукт?')"
                                                        title="Деактивирай">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="card-footer clearfix">
                        <div class="float-left">
                            <small class="text-muted">
                                Показване на {{ $products->firstItem() }} до {{ $products->lastItem() }} 
                                от общо {{ $products->total() }} продукта
                            </small>
                        </div>
                        <div class="float-right">
                            {{ $products->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@push('js')
<script>
    $(document).ready(function () {
        // Променливи за състояние
        let selectedProducts = [];
        const tableBody = $('#productsTableBody');
        const rows = tableBody.find('tr');

        // ========== ФУНКЦИИ ЗА ФИЛТРАЦИЯ ==========
        function applyFilters() {
            const productType = $('#filterProductType').val();
            const status = $('#filterStatus').val();
            const stockStatus = $('#filterStockStatus').val();
            const searchTerm = $('#quickSearch').val().toLowerCase();

            rows.each(function () {
                const row = $(this);
                const rowProductType = row.data('product-type');
                const rowStatus = row.data('status');
                const rowStockStatus = row.data('stock-status');
                const rowText = row.text().toLowerCase();

                let showRow = true;

                // Филтър по тип продукт
                if (productType && rowProductType !== productType) {
                    showRow = false;
                }

                // Филтър по статус
                if (status && rowStatus !== status) {
                    showRow = false;
                }

                // Филтър по наличност
                if (stockStatus && rowStockStatus !== stockStatus) {
                    showRow = false;
                }

                // Филтър по търсене
                if (searchTerm && !rowText.includes(searchTerm)) {
                    showRow = false;
                }

                row.toggle(showRow);
            });
        }

        // Слушатели за филтрите
        $('#filterProductType, #filterStatus, #filterStockStatus, #quickSearch').on('change keyup', applyFilters);

        // Изчистване на търсенето
        $('#clearSearch').click(function () {
            $('#quickSearch').val('');
            applyFilters();
        });

        // ========== ФУНКЦИИ ЗА ГРУПОВ ИЗБОР ==========
        function updateBulkActionUI() {
            const count = selectedProducts.length;
            const bulkHeader = $('#bulkActionHeader');
            const bulkButton = $('#bulkActionButton');
            const selectedCountSpan = $('#selectedCount');

            selectedCountSpan.text(count);

            if (count > 0) {
                bulkHeader.removeClass('d-none');
                bulkButton.prop('disabled', false);
            } else {
                bulkHeader.addClass('d-none');
                bulkButton.prop('disabled', true);
            }
        }

        // Избор/отмяна на всички
        $('#masterCheckbox, #selectAllCheckbox').change(function () {
            const isChecked = $(this).is(':checked');
            $('.product-checkbox').prop('checked', isChecked).trigger('change');
        });

        // Избор на отделен продукт
        $('.product-checkbox').change(function () {
            const productId = $(this).val();
            const isChecked = $(this).is(':checked');

            if (isChecked) {
                if (!selectedProducts.includes(productId)) {
                    selectedProducts.push(productId);
                }
            } else {
                selectedProducts = selectedProducts.filter(id => id != productId);
            }

            updateBulkActionUI();
            updateSelectedIdsInput();
        });

        // Изчистване на избора
        $('#clearSelection').click(function () {
            selectedProducts = [];
            $('.product-checkbox').prop('checked', false);
            updateBulkActionUI();
            updateSelectedIdsInput();
        });

        // Актуализиране на скритото поле за избрани ID
        function updateSelectedIdsInput() {
            $('#selectedIdsInput').val(JSON.stringify(selectedProducts));
        }

        // Групово действие
        $('.bulk-action-item').click(function () {
            const action = $(this).data('action');
            
            if (selectedProducts.length === 0) {
                alert('Моля, изберете поне един продукт!');
                return;
            }

            if (action === 'export' || action === 'generate_barcodes') {
                // Специален случай за експорт и баркодове
                if (action === 'generate_barcodes') {
                    if (!confirm(`Сигурни ли сте, че искате да генерирате баркодове за ${selectedProducts.length} продукт(а)?`)) {
                        return;
                    }
                }
                $('#bulkActionInput').val(action);
                $('#bulkActionForm').submit();
                return;
            }

            // Потвърждение за действия, които променят данни
            if (['activate', 'deactivate', 'mark_as_product', 'mark_as_service', 'enable_inventory', 'disable_inventory'].includes(action)) {
                if (!confirm(`Сигурни ли сте, че искате да изпълните това действие върху ${selectedProducts.length} продукт(а)?`)) {
                    return;
                }
            }

            $('#bulkActionInput').val(action);
            $('#bulkActionForm').submit();
        });

        // ========== ИНИЦИАЛИЗАЦИЯ ==========
        updateBulkActionUI();
        applyFilters(); // Прилага филтрите при зареждане на страницата
    });
</script>
@endpush