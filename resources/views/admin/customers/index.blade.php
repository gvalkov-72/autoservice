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
                        <!-- Филтър по тип клиент -->
                        <div class="input-group input-group-sm" style="width: 200px;">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-filter"></i>
                                </span>
                            </div>
                            <select id="filterCustomerType" class="form-control">
                                <option value="">Всички типове</option>
                                <option value="customer">Само клиенти</option>
                                <option value="supplier">Само доставчици</option>
                                <option value="both">Клиенти и доставчици</option>
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

                        <!-- Филтър по включване в бюлетин -->
                        <div class="input-group input-group-sm" style="width: 200px;">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-envelope"></i>
                                </span>
                            </div>
                            <select id="filterMailing" class="form-control">
                                <option value="">Всички за бюлетин</option>
                                <option value="included">Включени в бюлетин</option>
                                <option value="excluded">Изключени от бюлетин</option>
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
                                placeholder="Търсене по име, телефон, имейл, номер...">
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
                                    <h6 class="dropdown-header">Тип</h6>
                                    <button type="button" class="dropdown-item bulk-action-item" data-action="make_customer">
                                        <i class="fas fa-user text-info mr-2"></i>Маркирай като клиент
                                    </button>
                                    <button type="button" class="dropdown-item bulk-action-item" data-action="make_supplier">
                                        <i class="fas fa-truck text-warning mr-2"></i>Маркирай като доставчик
                                    </button>

                                    <div class="dropdown-divider"></div>
                                    <h6 class="dropdown-header">Бюлетин</h6>
                                    <button type="button" class="dropdown-item bulk-action-item"
                                        data-action="include_in_mailing">
                                        <i class="fas fa-envelope text-primary mr-2"></i>Включи в бюлетин
                                    </button>
                                    <button type="button" class="dropdown-item bulk-action-item"
                                        data-action="exclude_from_mailing">
                                        <i class="fas fa-envelope-slash text-secondary mr-2"></i>Изключи от бюлетин
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
                                    <th width="150">Контакти</th>
                                    <th width="100">Тип</th>
                                    <th width="100">Статус</th>
                                    <th width="140" class="text-center">Действия</th>
                                </tr>
                            </thead>
                            <tbody id="customersTableBody">
                                @foreach ($customers as $customer)
                                    <tr data-customer-type="{{ $customer->is_customer && $customer->is_supplier ? 'both' : ($customer->is_customer ? 'customer' : 'supplier') }}"
                                        data-status="{{ $customer->is_active ? 'active' : 'inactive' }}"
                                        data-mailing="{{ $customer->include_in_mailing ? 'included' : 'excluded' }}">
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
                                                    @if ($customer->is_customer && $customer->is_supplier)
                                                        <span class="badge bg-primary p-2" title="Клиент и доставчик">
                                                            <i class="fas fa-user-tie"></i>
                                                        </span>
                                                    @elseif($customer->is_supplier)
                                                        <span class="badge bg-warning p-2" title="Доставчик">
                                                            <i class="fas fa-truck"></i>
                                                        </span>
                                                    @else
                                                        <span class="badge bg-info p-2" title="Клиент">
                                                            <i class="fas fa-user"></i>
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
                                                    @if ($customer->mol)
                                                        <div>
                                                            <small class="text-muted">
                                                                <i class="fas fa-id-card fa-xs mr-1"></i>МОЛ: {{ $customer->mol }}
                                                            </small>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        @if($customer->old_id)
                                                            <small class="text-muted">
                                                                <i class="fas fa-database fa-xs mr-1"></i>Старо ID: {{ $customer->old_id }}
                                                            </small>
                                                        @endif
                                                        @if($customer->customer_number && $customer->customer_number != $customer->old_id)
                                                            <small class="text-muted ml-2">
                                                                <i class="fas fa-hashtag fa-xs mr-1"></i>Номер: {{ $customer->customer_number }}
                                                            </small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if ($customer->phone)
                                                <div>
                                                    <i class="fas fa-phone fa-xs text-muted mr-1"></i>
                                                    <small>{{ $customer->phone }}</small>
                                                </div>
                                            @endif
                                            @if ($customer->email)
                                                <div>
                                                    <i class="fas fa-envelope fa-xs text-muted mr-1"></i>
                                                    <small>{{ $customer->email }}</small>
                                                </div>
                                            @endif
                                            @if ($customer->tax_number)
                                                <div>
                                                    <i class="fas fa-file-invoice-dollar fa-xs text-muted mr-1"></i>
                                                    <small>ДДС: {{ $customer->tax_number }}</small>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                @if ($customer->is_customer)
                                                    <span class="badge badge-info mb-1">Клиент</span>
                                                @endif
                                                @if ($customer->is_supplier)
                                                    <span class="badge badge-warning">Доставчик</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            @if ($customer->is_active)
                                                <span class="badge badge-success">Активен</span>
                                            @else
                                                <span class="badge badge-secondary">Неактивен</span>
                                            @endif
                                            <br>
                                            @if ($customer->include_in_mailing)
                                                <small class="text-muted">
                                                    <i class="fas fa-envelope fa-xs"></i> В бюлетин
                                                </small>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('admin.customers.show', $customer) }}"
                                                    class="btn btn-info" title="Преглед">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.customers.edit', $customer) }}"
                                                    class="btn btn-primary" title="Редактирай">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.customers.destroy', $customer) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-danger"
                                                        onclick="return confirm('Сигурни ли сте, че искате да деактивирате този клиент?')"
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
                                Показване на {{ $customers->firstItem() }} до {{ $customers->lastItem() }} 
                                от общо {{ $customers->total() }} клиенти
                            </small>
                        </div>
                        <div class="float-right">
                            {{ $customers->links('pagination::bootstrap-4') }}
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
        let selectedCustomers = [];
        const tableBody = $('#customersTableBody');
        const rows = tableBody.find('tr');

        // ========== ФУНКЦИИ ЗА ФИЛТРАЦИЯ ==========
        function applyFilters() {
            const customerType = $('#filterCustomerType').val();
            const status = $('#filterStatus').val();
            const mailing = $('#filterMailing').val();
            const searchTerm = $('#quickSearch').val().toLowerCase();

            rows.each(function () {
                const row = $(this);
                const rowCustomerType = row.data('customer-type');
                const rowStatus = row.data('status');
                const rowMailing = row.data('mailing');
                const rowText = row.text().toLowerCase();

                let showRow = true;

                // Филтър по тип клиент
                if (customerType) {
                    if (customerType === 'both' && rowCustomerType !== 'both') {
                        showRow = false;
                    } else if (customerType === 'customer' && !rowCustomerType.includes('customer')) {
                        showRow = false;
                    } else if (customerType === 'supplier' && !rowCustomerType.includes('supplier')) {
                        showRow = false;
                    }
                }

                // Филтър по статус
                if (status && rowStatus !== status) {
                    showRow = false;
                }

                // Филтър по бюлетин
                if (mailing && rowMailing !== mailing) {
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
        $('#filterCustomerType, #filterStatus, #filterMailing, #quickSearch').on('change keyup', applyFilters);

        // Изчистване на търсенето
        $('#clearSearch').click(function () {
            $('#quickSearch').val('');
            applyFilters();
        });

        // ========== ФУНКЦИИ ЗА ГРУПОВ ИЗБОР ==========
        function updateBulkActionUI() {
            const count = selectedCustomers.length;
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
            $('.customer-checkbox').prop('checked', isChecked).trigger('change');
        });

        // Избор на отделен клиент
        $('.customer-checkbox').change(function () {
            const customerId = $(this).val();
            const isChecked = $(this).is(':checked');

            if (isChecked) {
                if (!selectedCustomers.includes(customerId)) {
                    selectedCustomers.push(customerId);
                }
            } else {
                selectedCustomers = selectedCustomers.filter(id => id != customerId);
            }

            updateBulkActionUI();
            updateSelectedIdsInput();
        });

        // Изчистване на избора
        $('#clearSelection').click(function () {
            selectedCustomers = [];
            $('.customer-checkbox').prop('checked', false);
            updateBulkActionUI();
            updateSelectedIdsInput();
        });

        // Актуализиране на скритото поле за избрани ID
        function updateSelectedIdsInput() {
            $('#selectedIdsInput').val(JSON.stringify(selectedCustomers));
        }

        // Групово действие
        $('.bulk-action-item').click(function () {
            const action = $(this).data('action');
            
            if (selectedCustomers.length === 0) {
                alert('Моля, изберете поне един клиент!');
                return;
            }

            if (action === 'export') {
                // Специален случай за експорт
                exportSelectedCustomers();
                return;
            }

            // Потвърждение за действия, които променят данни
            if (['activate', 'deactivate', 'make_customer', 'make_supplier', 'include_in_mailing', 'exclude_from_mailing'].includes(action)) {
                if (!confirm(`Сигурни ли сте, че искате да изпълните това действие върху ${selectedCustomers.length} клиент(а)?`)) {
                    return;
                }
            }

            $('#bulkActionInput').val(action);
            $('#bulkActionForm').submit();
        });

        // Функция за експорт на избрани клиенти
        function exportSelectedCustomers() {
            const ids = selectedCustomers.join(',');
            window.location.href = `{{ route('admin.customers.export.all') }}?selected_ids=${ids}`;
        }

        // ========== ИНИЦИАЛИЗАЦИЯ ==========
        updateBulkActionUI();
        applyFilters(); // Прилага филтрите при зареждане на страницата
    });
</script>
@endpush