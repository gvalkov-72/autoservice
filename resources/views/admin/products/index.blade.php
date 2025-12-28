@extends('adminlte::page')

@section('title', 'Продукти')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0">
            <i class="fas fa-cubes text-primary mr-2"></i>Управление на продукти
        </h1>
        <div>
            <!-- Групов експорт -->
            <div class="btn-group mr-2" role="group">
                <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-toggle="dropdown"
                    aria-expanded="false">
                    <i class="fas fa-file-export mr-1"></i>Експорт
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="{{ route('admin.products.export.all') }}?format=pdf">
                        <i class="fas fa-file-pdf text-danger mr-1"></i>PDF (всички)
                    </a>
                    <a class="dropdown-item" href="{{ route('admin.products.export.all') }}?format=excel">
                        <i class="fas fa-file-excel text-success mr-1"></i>Excel (всички)
                    </a>
                </div>
            </div>

            <!-- Импорт -->
            <a href="{{ route('admin.products.import') }}" class="btn btn-sm btn-outline-info mr-2"
                title="Импорт от Excel/CSV">
                <i class="fas fa-file-import mr-1"></i>Импорт
            </a>

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

                        <!-- Филтър по складова наличност -->
                        <div class="input-group input-group-sm" style="width: 200px;">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-box"></i>
                                </span>
                            </div>
                            <select id="filterStock" class="form-control">
                                <option value="">Всички наличности</option>
                                <option value="low">Ниски наличности</option>
                                <option value="out">Изчерпани</option>
                                <option value="normal">Нормални</option>
                            </select>
                        </div>

                        <!-- Филтър по тип (продукт/услуга) -->
                        <div class="input-group input-group-sm" style="width: 200px;">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-tag"></i>
                                </span>
                            </div>
                            <select id="filterType" class="form-control">
                                <option value="">Всички типове</option>
                                <option value="product">Продукти</option>
                                <option value="service">Услуги</option>
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
                                placeholder="Търсене по име, код, PLU, баркод...">
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
                <form id="bulkActionForm" action="{{ route('admin.products.bulk.actions') }}" method="POST" class="mb-0">
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
                                    <h6 class="dropdown-header">Склад</h6>
                                    <button type="button" class="dropdown-item bulk-action-item" data-action="update_stock">
                                        <i class="fas fa-boxes text-info mr-2"></i>Обнови наличност
                                    </button>

                                    <div class="dropdown-divider"></div>
                                    <h6 class="dropdown-header">Експорт</h6>
                                    <button type="button" class="dropdown-item bulk-action-item" data-action="export">
                                        <i class="fas fa-file-export text-primary mr-2"></i>Експортирай избраните
                                    </button>

                                    <div class="dropdown-divider"></div>
                                    <h6 class="dropdown-header">Опасно</h6>
                                    <button type="button" class="dropdown-item bulk-action-item text-danger"
                                            data-action="delete">
                                        <i class="fas fa-trash-alt mr-2"></i>Изтрий избраните
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
                                    <th width="100">Кодове</th>
                                    <th width="100">Наличност</th>
                                    <th width="100">Цена</th>
                                    <th width="100">Тип</th>
                                    <th width="100">Статус</th>
                                    <th width="140" class="text-center">Действия</th>
                                </tr>
                            </thead>
                            <tbody id="productsTableBody">
                                @foreach ($products as $product)
                                    @php
                                        // Определяне на класовете за статус на наличност
                                        $stockStatus = 'normal';
                                        $stockClass = 'success';
                                        if ($product->quantity <= 0) {
                                            $stockStatus = 'out';
                                            $stockClass = 'danger';
                                        } elseif ($product->min_stock > 0 && $product->quantity <= $product->min_stock) {
                                            $stockStatus = 'low';
                                            $stockClass = 'warning';
                                        }
                                    @endphp
                                    <tr data-status="{{ $product->is_active ? 'active' : 'inactive' }}"
                                        data-stock="{{ $stockStatus }}"
                                        data-type="{{ $product->is_service ? 'service' : 'product' }}">
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
                                                    @if($product->is_service)
                                                        <span class="badge bg-secondary p-2" title="Услуга">
                                                            <i class="fas fa-concierge-bell"></i>
                                                        </span>
                                                    @else
                                                        <span class="badge bg-primary p-2" title="Продукт">
                                                            <i class="fas fa-cube"></i>
                                                        </span>
                                                    @endif
                                                </div>
                                                <div>
                                                    <div class="font-weight-bold">{{ $product->name }}</div>
                                                    <small class="text-muted">{{ \Illuminate\Support\Str::limit($product->description, 50) }}</small>
                                                    @if($product->location)
                                                        <div class="text-xs">
                                                            <i class="fas fa-map-marker-alt text-muted"></i>
                                                            {{ $product->location }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                @if($product->code)
                                                    <div class="small">
                                                        <span class="text-muted">Код:</span>
                                                        <strong>{{ $product->code }}</strong>
                                                    </div>
                                                @endif
                                                @if($product->plu)
                                                    <div class="small">
                                                        <span class="text-muted">PLU:</span>
                                                        <strong>{{ $product->plu }}</strong>
                                                    </div>
                                                @endif
                                                @if($product->barcode)
                                                    <div class="small">
                                                        <span class="text-muted">Баркод:</span>
                                                        <code>{{ $product->barcode }}</code>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="mr-2">
                                                    <span class="badge bg-{{ $stockClass }}">
                                                        {{ number_format($product->quantity, 2) }} {{ $product->unit_of_measure }}
                                                    </span>
                                                </div>
                                                @if($product->min_stock > 0 && $stockStatus === 'low')
                                                    <small class="text-warning">
                                                        <i class="fas fa-exclamation-triangle"></i>
                                                        мин: {{ $product->min_stock }}
                                                    </small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="font-weight-bold text-primary">
                                                {{ number_format($product->price, 2) }} лв.
                                            </div>
                                            @if($product->cost_price > 0)
                                                <small class="text-muted">
                                                    ст-ст: {{ number_format($product->cost_price, 2) }} лв.
                                                </small>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($product->is_service)
                                                <span class="badge bg-secondary">Услуга</span>
                                            @else
                                                <span class="badge bg-primary">Продукт</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($product->is_active)
                                                <span class="badge bg-success">Активен</span>
                                            @else
                                                <span class="badge bg-secondary">Неактивен</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('admin.products.show', $product) }}"
                                                   class="btn btn-info"
                                                   title="Преглед">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.products.edit', $product) }}"
                                                   class="btn btn-warning"
                                                   title="Редактиране">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @if(!$product->is_service)
                                                <a href="{{ route('admin.products.barcode', $product) }}"
                                                   class="btn btn-dark"
                                                   title="Баркод">
                                                    <i class="fas fa-barcode"></i>
                                                </a>
                                                @endif
                                                <button type="button"
                                                        class="btn btn-danger"
                                                        onclick="confirmDelete({{ $product->id }}, '{{ $product->name }}')"
                                                        title="Изтриване">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Пагинация (на отделен ред както при клиентите) -->
                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted">
                                    Показване на <strong>{{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }}</strong>
                                    от <strong>{{ $products->total() }}</strong> продукти
                                </span>
                            </div>
                            <div>
                                {{ $products->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .table td, .table th {
            vertical-align: middle;
        }
        .badge.bg-primary.p-2,
        .badge.bg-secondary.p-2 {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 5px;
        }
        .btn-group-sm>.btn, .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            line-height: 1.5;
            border-radius: 0.2rem;
        }
        /* Стилове за пагинация */
        .pagination {
            margin-bottom: 0;
        }
        .page-link {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Глобални променливи
            let selectedProducts = [];

            // =====================
            // Bulk Selection Logic
            // =====================

            // Мастер чекбокс
            $('#masterCheckbox').on('change', function() {
                const isChecked = $(this).prop('checked');
                $('.product-checkbox').prop('checked', isChecked);

                if (isChecked) {
                    selectedProducts = $('.product-checkbox').map(function() {
                        return $(this).val();
                    }).get();
                } else {
                    selectedProducts = [];
                }

                updateBulkActionUI();
            });

            // Индивидуални чекбокси
            $(document).on('change', '.product-checkbox', function() {
                const productId = $(this).val();

                if ($(this).prop('checked')) {
                    if (!selectedProducts.includes(productId)) {
                        selectedProducts.push(productId);
                    }
                } else {
                    selectedProducts = selectedProducts.filter(id => id != productId);
                    $('#masterCheckbox').prop('checked', false);
                }

                updateBulkActionUI();
            });

            // Обновяване на UI за bulk действия
            function updateBulkActionUI() {
                const count = selectedProducts.length;
                $('#selectedCount').text(count);
                $('#selectedIdsInput').val(JSON.stringify(selectedProducts));

                if (count > 0) {
                    $('#bulkActionHeader').removeClass('d-none');
                    $('#bulkActionButton').prop('disabled', false);
                } else {
                    $('#bulkActionHeader').addClass('d-none');
                    $('#bulkActionButton').prop('disabled', true);
                }
            }

            // Изчистване на избора
            $('#clearSelection').on('click', function() {
                selectedProducts = [];
                $('.product-checkbox, #masterCheckbox').prop('checked', false);
                updateBulkActionUI();
            });

            // Bulk действия
            $('.bulk-action-item').on('click', function() {
                const action = $(this).data('action');

                if (selectedProducts.length === 0) {
                    alert('Моля, изберете поне един продукт!');
                    return;
                }

                if (action === 'delete') {
                    if (!confirm(`Сигурни ли сте, че искате да изтриете ${selectedProducts.length} продукт(а)?`)) {
                        return;
                    }
                }

                if (action === 'export') {
                    // Експорт на избраните продукти
                    window.location.href = '{{ route("admin.products.export.selected") }}' +
                        '?ids=' + selectedProducts.join(',') + '&format=excel';
                    return;
                }

                $('#bulkActionInput').val(action);
                $('#bulkActionForm').submit();
            });

            // =====================
            // Филтриране
            // =====================

            // Филтри по статус, наличност и тип
            $('#filterStatus, #filterStock, #filterType').on('change', function() {
                applyFilters();
            });

            // Бързо търсене
            $('#quickSearch').on('keyup', function() {
                applyFilters();
            });

            // Изчистване на търсенето
            $('#clearSearch').on('click', function() {
                $('#quickSearch').val('');
                applyFilters();
            });

            // Прилагане на филтрите
            function applyFilters() {
                const searchTerm = $('#quickSearch').val().toLowerCase();
                const statusFilter = $('#filterStatus').val();
                const stockFilter = $('#filterStock').val();
                const typeFilter = $('#filterType').val();

                $('tbody tr').each(function() {
                    const row = $(this);
                    const status = row.data('status');
                    const stock = row.data('stock');
                    const type = row.data('type');

                    // Проверка на филтрите
                    let showRow = true;

                    if (statusFilter && status !== statusFilter) {
                        showRow = false;
                    }

                    if (stockFilter && stock !== stockFilter) {
                        showRow = false;
                    }

                    if (typeFilter && type !== typeFilter) {
                        showRow = false;
                    }

                    // Проверка на търсенето
                    if (searchTerm && showRow) {
                        const rowText = row.text().toLowerCase();
                        showRow = rowText.includes(searchTerm);
                    }

                    row.toggle(showRow);
                });
            }

            // =====================
            // Функция за потвърждение на изтриване
            // =====================
            window.confirmDelete = function(productId, productName) {
                if (confirm(`Сигурни ли сте, че искате да изтриете продукт "${productName}"?`)) {
                    // Създаване на форма за изтриване
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/admin/products/${productId}`;
                    
                    // Добавяне на CSRF токен
                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';
                    form.appendChild(csrfToken);
                    
                    // Добавяне на методно поле за DELETE
                    const methodField = document.createElement('input');
                    methodField.type = 'hidden';
                    methodField.name = '_method';
                    methodField.value = 'DELETE';
                    form.appendChild(methodField);
                    
                    // Добавяне на формата към документа и изпращане
                    document.body.appendChild(form);
                    form.submit();
                }
            };

            // =====================
            // Инициализация
            // =====================

            // Показване на flash съобщения
            @if(session('success'))
                const successHtml = `
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle mr-2"></i>
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert">
                            <span>&times;</span>
                        </button>
                    </div>
                `;
                $('.content-header').after(successHtml);
                
                // Автоматично скриване на alert след 5 секунди
                setTimeout(() => {
                    $('.alert').alert('close');
                }, 5000);
            @endif

            @if(session('error'))
                const errorHtml = `
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert">
                            <span>&times;</span>
                        </button>
                    </div>
                `;
                $('.content-header').after(errorHtml);
                
                // Автоматично скриване на alert след 5 секунди
                setTimeout(() => {
                    $('.alert').alert('close');
                }, 5000);
            @endif
        });
    </script>
@stop