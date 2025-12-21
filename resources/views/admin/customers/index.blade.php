@extends('adminlte::page')

@section('title', 'Клиенти')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0">
            <i class="fas fa-users text-primary mr-2"></i>Управление на клиенти
        </h1>
        <div>
            <!-- Групов експорт -->
            <div class="btn-group mr-2" role="group">
                <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-toggle="dropdown"
                    aria-expanded="false">
                    <i class="fas fa-file-export mr-1"></i>Експорт
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="{{ route('admin.customers.export.all', ['format' => 'pdf']) }}">
                        <i class="fas fa-file-pdf text-danger mr-1"></i>PDF (всички)
                    </a>
                    <a class="dropdown-item" href="{{ route('admin.customers.export.all', ['format' => 'excel']) }}">
                        <i class="fas fa-file-excel text-success mr-1"></i>Excel (всички)
                    </a>
                    <a class="dropdown-item" href="{{ route('admin.customers.export.all', ['format' => 'csv']) }}">
                        <i class="fas fa-file-csv text-info mr-1"></i>CSV (всички)
                    </a>
                </div>
            </div>

            <!-- Импорт -->
            <a href="{{ route('admin.customers.import') }}" class="btn btn-sm btn-outline-info mr-2"
                title="Импорт от Excel/CSV">
                <i class="fas fa-file-import mr-1"></i>Импорт
            </a>

            <!-- Нов клиент -->
            <a href="{{ route('admin.customers.create') }}" class="btn btn-success">
                <i class="fas fa-plus mr-1"></i>Нов клиент
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
                        Списък на клиенти
                    </h3>

                    <!-- Филтри и търсене -->
                    <div class="card-tools d-flex align-items-center" style="gap: 10px;">
                        <!-- Фильтри -->
                        <div class="input-group input-group-sm" style="width: 200px;">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-filter"></i>
                                </span>
                            </div>
                            <select id="filterType" class="form-control">
                                <option value="">Всички типове</option>
                                <option value="customer">Клиенти</option>
                                <option value="supplier">Доставчици</option>
                                <option value="both">И двата</option>
                            </select>
                        </div>

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

                        <!-- Търсачка -->
                        <div class="input-group input-group-sm" style="width: 300px;">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                            </div>
                            <input type="text" id="quickSearch" class="form-control"
                                placeholder="Търсене по име, телефон, имейл...">
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
                <form id="bulkActionForm" action="{{ route('admin.customers.bulk-action') }}" method="POST" class="mb-0">
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
                                    <h6 class="dropdown-header">Справки</h6>
                                    <button type="button" class="dropdown-item bulk-action-item"
                                        data-action="include_in_reports">
                                        <i class="fas fa-chart-bar text-info mr-2"></i>Включи в справки
                                    </button>
                                    <button type="button" class="dropdown-item bulk-action-item"
                                        data-action="exclude_from_reports">
                                        <i class="fas fa-ban text-warning mr-2"></i>Изключи от справки
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
                                    <th>Клиент</th>
                                    <th width="120">Тип</th>
                                    <th width="150">Контакти</th>
                                    <th width="100">Статус</th>
                                    <th width="140" class="text-center">Действия</th>
                                </tr>
                            </thead>
                            <tbody id="customersTableBody">
                                @foreach ($customers as $customer)
                                    <tr data-type="{{ $customer->type }}"
                                        data-status="{{ $customer->is_active ? 'active' : 'inactive' }}">
                                        <td class="text-center">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input customer-checkbox"
                                                    id="customer_{{ $customer->id }}" value="{{ $customer->id }}">
                                                <label class="custom-control-label"
                                                    for="customer_{{ $customer->id }}"></label>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-light text-dark">#{{ $customer->id }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex">
                                                <div class="mr-2">
                                                    @if ($customer->type == 'customer')
                                                        <span class="badge bg-info p-2">
                                                            <i class="fas fa-user"></i>
                                                        </span>
                                                    @elseif($customer->type == 'supplier')
                                                        <span class="badge bg-warning p-2">
                                                            <i class="fas fa-truck"></i>
                                                        </span>
                                                    @else
                                                        <span class="badge bg-primary p-2">
                                                            <i class="fas fa-user-tie"></i>
                                                        </span>
                                                    @endif
                                                </div>
                                                <div>
                                                    <a href="{{ route('admin.customers.show', $customer) }}"
                                                        class="text-dark font-weight-normal customer-name"
                                                        style="font-size: 0.95em; text-decoration: none;">
                                                        {{ $customer->name }}
                                                    </a>
                                                    @if ($customer->contact_person)
                                                        <div>
                                                            <small class="text-muted">
                                                                <i
                                                                    class="fas fa-user-circle fa-xs mr-1"></i>{{ $customer->contact_person }}
                                                            </small>
                                                        </div>
                                                    @endif
                                                    @if ($customer->old_system_id)
                                                        <div>
                                                            <small class="text-muted">
                                                                <i class="fas fa-database fa-xs mr-1"></i>Старо ID:
                                                                {{ $customer->old_system_id }}
                                                            </small>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if ($customer->type == 'customer')
                                                <span class="badge bg-info">Клиент</span>
                                            @elseif($customer->type == 'supplier')
                                                <span class="badge bg-warning">Доставчик</span>
                                            @else
                                                <span class="badge bg-primary">И двата</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($customer->phone)
                                                <div class="mb-1">
                                                    <i class="fas fa-phone text-success mr-1"></i>
                                                    <small>{{ $customer->phone }}</small>
                                                </div>
                                            @endif
                                            @if ($customer->email)
                                                <div>
                                                    <i class="fas fa-envelope text-primary mr-1"></i>
                                                    <small class="customer-email">{{ $customer->email }}</small>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($customer->is_active)
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check mr-1"></i>Активен
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">
                                                    <i class="fas fa-times mr-1"></i>Неактивен
                                                </span>
                                            @endif
                                            @if ($customer->include_in_reports)
                                                <br><small class="badge bg-info mt-1">В справки</small>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('admin.customers.show', $customer) }}"
                                                    class="btn btn-info" title="Преглед">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.customers.edit', $customer) }}"
                                                    class="btn btn-primary" title="Редактирай">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="{{ route('admin.customers.export.pdf', $customer) }}"
                                                    class="btn btn-danger" title="PDF">
                                                    <i class="fas fa-file-pdf"></i>
                                                </a>
                                                <form action="{{ route('admin.customers.destroy', $customer) }}"
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('Сигурни ли сте, че искате да деактивирате този клиент?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-danger" title="Деактивирай">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        @if ($customers->isEmpty())
                            <div class="text-center py-4">
                                <i class="fas fa-users fa-2x text-muted mb-3"></i>
                                <h5 class="text-muted">Няма намерени клиенти</h5>
                                <p class="text-muted mb-0">Създайте първия клиент или импортирайте от файл</p>
                                <div class="mt-3">
                                    <a href="{{ route('admin.customers.create') }}" class="btn btn-primary mr-2">
                                        <i class="fas fa-plus mr-1"></i>Добави клиент
                                    </a>
                                    <a href="{{ route('admin.customers.import') }}" class="btn btn-info">
                                        <i class="fas fa-file-import mr-1"></i>Импортирай
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>

                    @if($customers->hasPages())
<div class="card-footer clearfix">
    <div class="float-right">
        <nav aria-label="Пагинация">
            <ul class="pagination pagination-sm m-0">
                {{-- Previous Page Link --}}
                @if ($customers->onFirstPage())
                    <li class="page-item disabled">
                        <span class="page-link">
                            <i class="fas fa-chevron-left mr-1"></i> Предишна
                        </span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $customers->previousPageUrl() }}" rel="prev">
                            <i class="fas fa-chevron-left mr-1"></i> Предишна
                        </a>
                    </li>
                @endif

                {{-- Pagination Elements with Ellipsis --}}
                @php
                    $current = $customers->currentPage();
                    $last = $customers->lastPage();
                    $window = 2; // Брой страници преди и след текущата
                    
                    // Определяме кои страници да покажем
                    $start = max(1, $current - $window);
                    $end = min($last, $current + $window);
                @endphp

                {{-- Първа страница винаги --}}
                @if ($start > 1)
                    <li class="page-item">
                        <a class="page-link" href="{{ $customers->url(1) }}">1</a>
                    </li>
                    @if ($start > 2)
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    @endif
                @endif

                {{-- Страници в прозореца --}}
                @for ($page = $start; $page <= $end; $page++)
                    @if ($page == $current)
                        <li class="page-item active">
                            <span class="page-link">{{ $page }}</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $customers->url($page) }}">{{ $page }}</a>
                        </li>
                    @endif
                @endfor

                {{-- Последна страница --}}
                @if ($end < $last)
                    @if ($end < $last - 1)
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    @endif
                    <li class="page-item">
                        <a class="page-link" href="{{ $customers->url($last) }}">{{ $last }}</a>
                    </li>
                @endif

                {{-- Next Page Link --}}
                @if ($customers->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $customers->nextPageUrl() }}" rel="next">
                            Следваща <i class="fas fa-chevron-right ml-1"></i>
                        </a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <span class="page-link">
                            Следваща <i class="fas fa-chevron-right ml-1"></i>
                        </span>
                    </li>
                @endif
            </ul>
        </nav>
    </div>
</div>
@endif
                </form>
            </div>
        </div>
    </div>
@stop

@push('css')
    <style>
        .card.card-primary.card-outline {
            border-top: 3px solid #007bff;
        }

        .card-header .card-tools {
            position: relative;
            left: 0;
            transform: none;
        }

        @media (max-width: 768px) {
            .card-header .card-tools {
                flex-direction: column;
                gap: 10px;
            }

            .card-header .card-tools .input-group {
                width: 100% !important;
            }
        }

        /* Стил за подсветка на търсения текст */
        .highlight {
            background-color: #ffeb3b;
            color: #000;
            font-weight: bold;
            padding: 1px 2px;
            border-radius: 2px;
        }

        /* Стил за избрани редове */
        tr.selected {
            background-color: rgba(0, 123, 255, 0.1) !important;
        }

        tr.selected td {
            border-color: rgba(0, 123, 255, 0.3) !important;
        }

        /* Bulk action header */
        #bulkActionHeader {
            transition: all 0.3s ease;
            border-bottom: 2px solid #007bff;
        }
    </style>
@endpush

@push('js')
    <script>
        $(document).ready(function() {
            // --- ГЛОБАЛНИ ПРОМЕНЛИВИ ---
            let originalRows = $('#customersTableBody tr').toArray();
            let selectedCustomers = new Set();

            // --- ФУНКЦИИ ЗА ТЪРСЕНЕ И ФИЛТРИРАНЕ ---
            function performSearchAndFilter() {
                const searchText = $('#quickSearch').val().toLowerCase().trim();
                const filterType = $('#filterType').val();
                const filterStatus = $('#filterStatus').val();

                const tbody = $('#customersTableBody');
                let visibleCount = 0;

                originalRows.forEach(row => {
                    const $row = $(row);
                    const name = $row.find('.customer-name').text().toLowerCase();
                    const email = $row.find('.customer-email').text().toLowerCase();
                    const rowType = $row.data('type');
                    const rowStatus = $row.data('status');

                    let matchesSearch = true;
                    let matchesFilter = true;

                    // Проверка за търсене
                    if (searchText.length > 0) {
                        matchesSearch = name.includes(searchText) || email.includes(searchText);
                    }

                    // Проверка за филтри
                    if (filterType && rowType !== filterType) {
                        matchesFilter = false;
                    }
                    if (filterStatus && rowStatus !== filterStatus) {
                        matchesFilter = false;
                    }

                    // Показване/скриване на реда
                    if (matchesSearch && matchesFilter) {
                        $row.show();
                        visibleCount++;

                        // Подсветяване на текста за търсене
                        if (searchText.length > 0) {
                            highlightText($row.find('.customer-name'), searchText);
                            highlightText($row.find('.customer-email'), searchText);
                        } else {
                            removeHighlight($row.find('.customer-name'));
                            removeHighlight($row.find('.customer-email'));
                        }
                    } else {
                        $row.hide();
                    }
                });

                // Показване на съобщение ако няма резултати
                if (visibleCount === 0) {
                    tbody.append(`
                <tr id="noResultsRow">
                    <td colspan="7" class="text-center py-4">
                        <i class="fas fa-search fa-2x text-muted mb-3"></i>
                        <h5 class="text-muted">Няма намерени резултати</h5>
                        <p class="text-muted mb-0">Не бяха намерени клиенти, отговарящи на критериите</p>
                    </td>
                </tr>
            `);
                } else {
                    $('#noResultsRow').remove();
                }
            }

            function highlightText($element, searchText) {
                const text = $element.text();
                const regex = new RegExp(`(${searchText})`, 'gi');
                const highlighted = text.replace(regex, '<span class="highlight">$1</span>');
                $element.html(highlighted);
            }

            function removeHighlight($element) {
                const text = $element.text();
                $element.text(text);
            }

            // --- ФУНКЦИИ ЗА ГРУПОВИ ДЕЙСТВИЯ ---
            function updateBulkActionHeader() {
                const count = selectedCustomers.size;
                const $header = $('#bulkActionHeader');
                const $selectedCount = $('#selectedCount');
                const $bulkActionButton = $('#bulkActionButton');

                $selectedCount.text(count);

                if (count > 0) {
                    $header.removeClass('d-none');
                    $bulkActionButton.prop('disabled', false);
                    $bulkActionButton.text(`Групово действие (${count})`);

                    // Маркиране на избраните редове
                    $('tr').removeClass('selected');
                    selectedCustomers.forEach(id => {
                        $(`#customer_${id}`).closest('tr').addClass('selected');
                    });
                } else {
                    $header.addClass('d-none');
                    $bulkActionButton.prop('disabled', true);
                    $bulkActionButton.text('Групово действие');
                    $('tr').removeClass('selected');
                }

                // Обновяване на главния чекбокс
                updateMasterCheckbox();
            }

            function updateMasterCheckbox() {
                const visibleRows = $('#customersTableBody tr:visible').not('#noResultsRow');
                const checkedVisibleRows = visibleRows.find('.customer-checkbox:checked').length;

                const $masterCheckbox = $('#masterCheckbox');
                const $selectAllCheckbox = $('#selectAllCheckbox');

                if (visibleRows.length === 0) {
                    $masterCheckbox.prop('checked', false);
                    $masterCheckbox.prop('indeterminate', false);
                    $selectAllCheckbox.prop('checked', false);
                    $selectAllCheckbox.prop('indeterminate', false);
                } else if (checkedVisibleRows === visibleRows.length) {
                    $masterCheckbox.prop('checked', true);
                    $masterCheckbox.prop('indeterminate', false);
                    $selectAllCheckbox.prop('checked', true);
                    $selectAllCheckbox.prop('indeterminate', false);
                } else if (checkedVisibleRows > 0) {
                    $masterCheckbox.prop('checked', false);
                    $masterCheckbox.prop('indeterminate', true);
                    $selectAllCheckbox.prop('checked', false);
                    $selectAllCheckbox.prop('indeterminate', true);
                } else {
                    $masterCheckbox.prop('checked', false);
                    $masterCheckbox.prop('indeterminate', false);
                    $selectAllCheckbox.prop('checked', false);
                    $selectAllCheckbox.prop('indeterminate', false);
                }
            }

            function performBulkAction(action) {
                if (selectedCustomers.size === 0) {
                    alert('Моля, изберете поне един клиент за действие.');
                    return;
                }

                if (action === 'export') {
                    // Експорт на избраните клиенти
                    const idsArray = Array.from(selectedCustomers);
                    const exportUrl =
                        `{{ route('admin.customers.export.all') }}?selected_ids=${idsArray.join(',')}`;
                    window.location.href = exportUrl;
                    return;
                }

                // Потвърждение за други действия
                let confirmMessage = '';
                switch (action) {
                    case 'activate':
                        confirmMessage =
                            `Сигурни ли сте, че искате да активирате ${selectedCustomers.size} клиента?`;
                        break;
                    case 'deactivate':
                        confirmMessage =
                            `Сигурни ли сте, че искате да деактивирате ${selectedCustomers.size} клиента?`;
                        break;
                    case 'include_in_reports':
                        confirmMessage =
                            `Сигурни ли сте, че искате да включите ${selectedCustomers.size} клиента в справки?`;
                        break;
                    case 'exclude_from_reports':
                        confirmMessage =
                            `Сигурни ли сте, че искате да изключите ${selectedCustomers.size} клиента от справки?`;
                        break;
                }

                if (!confirm(confirmMessage)) {
                    return;
                }

                // Изпращане на формата
                $('#selectedIdsInput').val(Array.from(selectedCustomers).join(','));
                $('#bulkActionInput').val(action);
                $('#bulkActionForm').submit();
            }

            // --- СЛУШАТЕЛИ НА СЪБИТИЯ ---
            // Търсене и филтри
            $('#quickSearch').on('input', function() {
                clearTimeout(window.searchTimer);
                window.searchTimer = setTimeout(() => {
                    performSearchAndFilter();
                    updateMasterCheckbox();
                }, 300);
            });

            $('#filterType, #filterStatus').on('change', function() {
                performSearchAndFilter();
                updateMasterCheckbox();
            });

            $('#clearSearch').on('click', function() {
                $('#quickSearch').val('');
                $('#filterType').val('');
                $('#filterStatus').val('');
                performSearchAndFilter();
                updateMasterCheckbox();
            });

            // Групово селектиране
            $('#masterCheckbox').on('change', function() {
                const isChecked = $(this).prop('checked');
                const visibleRows = $('#customersTableBody tr:visible').not('#noResultsRow');

                visibleRows.each(function() {
                    const checkbox = $(this).find('.customer-checkbox');
                    const customerId = checkbox.val();

                    if (isChecked) {
                        checkbox.prop('checked', true);
                        selectedCustomers.add(customerId);
                    } else {
                        checkbox.prop('checked', false);
                        selectedCustomers.delete(customerId);
                    }
                });

                updateBulkActionHeader();
            });

            $('#selectAllCheckbox').on('change', function() {
                const isChecked = $(this).prop('checked');
                const allRows = $('#customersTableBody tr').not('#noResultsRow');

                allRows.each(function() {
                    const checkbox = $(this).find('.customer-checkbox');
                    const customerId = checkbox.val();

                    if (isChecked) {
                        checkbox.prop('checked', true);
                        selectedCustomers.add(customerId);
                    } else {
                        checkbox.prop('checked', false);
                        selectedCustomers.delete(customerId);
                    }
                });

                updateBulkActionHeader();
            });

            $(document).on('change', '.customer-checkbox', function() {
                const customerId = $(this).val();

                if ($(this).prop('checked')) {
                    selectedCustomers.add(customerId);
                } else {
                    selectedCustomers.delete(customerId);
                }

                updateBulkActionHeader();
            });

            // Групово действие
            $('.bulk-action-item').on('click', function() {
                const action = $(this).data('action');
                performBulkAction(action);
            });

            $('#clearSelection').on('click', function() {
                $('.customer-checkbox').prop('checked', false);
                selectedCustomers.clear();
                updateBulkActionHeader();
            });

            // Глобални клавишни комбинации
            $(document).on('keydown', function(e) {
                // Ctrl+F за фокус в търсачката
                if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
                    e.preventDefault();
                    $('#quickSearch').focus();
                }

                // Ctrl+A за селектиране на всички видими
                if ((e.ctrlKey || e.metaKey) && e.key === 'a') {
                    e.preventDefault();
                    $('#masterCheckbox').prop('checked', true).trigger('change');
                }

                // Escape за изчистване на селекцията
                if (e.key === 'Escape') {
                    selectedCustomers.clear();
                    $('.customer-checkbox').prop('checked', false);
                    updateBulkActionHeader();
                }
            });

            // Инициализация
            performSearchAndFilter();
        });
    </script>
@endpush
