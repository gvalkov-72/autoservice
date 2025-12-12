@extends('adminlte::page')

@section('title', 'Нова поръчка')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="text-primary"><i class="fas fa-file-invoice mr-2"></i>Нова поръчка</h1>
        <a href="{{ route('admin.work-orders.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left mr-1"></i> Назад
        </a>
    </div>
@stop

@section('content')
    <div class="card border-primary">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><i class="fas fa-search mr-2"></i>Бързо търсене</h4>
        </div>
        <div class="card-body bg-light">
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <label class="font-weight-bold">Бързо търсене на клиент или автомобил:</label>
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-white">
                                    <i class="fas fa-search text-primary"></i>
                                </span>
                            </div>
                            <input type="text" id="globalSearch" class="form-control form-control-sm" 
                                   placeholder="Въведете име на клиент, телефон, имейл или регистрационен номер...">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div id="searchResults" class="list-group mt-2" style="display: none; max-height: 300px; overflow-y: auto;">
                            <!-- Резултатите ще се появят тук -->
                        </div>
                    </div>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <div class="alert alert-info w-100 mb-0 py-2">
                        <small class="d-block">
                            <i class="fas fa-info-circle mr-1"></i>
                            Търсете по: <strong>име</strong>, <strong>телефон</strong>, 
                            <strong>имейл</strong> или <strong>регистрационен номер</strong>.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-success mt-3">
        <div class="card-header bg-success text-white">
            <h4 class="mb-0"><i class="fas fa-user-circle mr-2"></i>Информация за клиент и автомобил</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.work-orders.store') }}" method="POST" id="orderForm">
                @csrf
                
                {{-- Основна информация --}}
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="font-weight-bold text-primary">Клиент <span class="text-danger">*</span></label>
                            <select name="customer_id" id="customer_id" class="form-control form-control-sm select2" 
                                    data-placeholder="Изберете клиент" required>
                                <option value=""></option>
                                @foreach($customers as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                            <div id="customerInfo" class="mt-1 p-2 bg-light rounded" style="display: none;">
                                <small class="text-muted">
                                    <i class="fas fa-user text-primary mr-1"></i>
                                    <span id="customerName"></span><br>
                                    <i class="fas fa-phone text-primary mr-1"></i>
                                    <span id="customerPhone"></span><br>
                                    <i class="fas fa-envelope text-primary mr-1"></i>
                                    <span id="customerEmail"></span>
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="font-weight-bold text-primary">Автомобил <span class="text-danger">*</span></label>
                            <select name="vehicle_id" id="vehicle_id" class="form-control form-control-sm select2" required
                                    data-placeholder="Изберете автомобил"
                                    data-mileages="{{ $vehiclesForMileage->pluck('mileage', 'id')->toJson() }}">
                                <option value=""></option>
                            </select>
                            <div id="vehicleInfo" class="mt-1 p-2 bg-light rounded" style="display: none;">
                                <small class="text-muted">
                                    <i class="fas fa-car text-primary mr-1"></i>
                                    <span id="vehicleMakeModel"></span><br>
                                    <i class="fas fa-hashtag text-primary mr-1"></i>
                                    Рег. номер: <span id="vehiclePlate"></span><br>
                                    <i class="fas fa-calendar text-primary mr-1"></i>
                                    Година: <span id="vehicleYear"></span><br>
                                    <i class="fas fa-gas-pump text-primary mr-1"></i>
                                    Двигател: <span id="vehicleEngine"></span>
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="font-weight-bold text-primary">Механик</label>
                            <select name="assigned_to" class="form-control form-control-sm select2">
                                <option value=""></option>
                                @foreach($mechanics as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Незадължително</small>
                        </div>
                    </div>
                </div>

                {{-- Детайли за поръчката --}}
                <div class="card border-warning mt-3">
                    <div class="card-header bg-warning">
                        <h5 class="mb-0 text-dark"><i class="fas fa-clipboard-list mr-2"></i>Детайли на поръчката</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="font-weight-bold text-dark">Статус <span class="text-danger">*</span></label>
                                    <select name="status" class="form-control form-control-sm" required>
                                        <option value="draft">📝 Чернова</option>
                                        <option value="open" selected>🔓 Отворена</option>
                                        <option value="in_progress">⚙️ В прогрес</option>
                                        <option value="completed">✅ Завършена</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="font-weight-bold text-dark">Дата приемане</label>
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light">
                                                <i class="fas fa-calendar-alt text-warning"></i>
                                            </span>
                                        </div>
                                        <input type="datetime-local" name="received_at" class="form-control" 
                                               value="{{ now()->format('Y-m-d\TH:i') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="font-weight-bold text-dark">Пробег (km)</label>
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light">
                                                <i class="fas fa-tachometer-alt text-warning"></i>
                                            </span>
                                        </div>
                                        <input type="number" name="km_on_receive" class="form-control" 
                                               min="0" placeholder="0" id="vehicle_mileage">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="font-weight-bold text-dark">Очаквана дата</label>
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light">
                                                <i class="fas fa-calendar-check text-warning"></i>
                                            </span>
                                        </div>
                                        <input type="date" name="estimated_completion" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-2">
                            <label class="font-weight-bold text-dark">
                                <i class="fas fa-sticky-note mr-1"></i>Бележки
                            </label>
                            <textarea name="notes" class="form-control form-control-sm" rows="2" 
                                      placeholder="Допълнителни бележки за поръчката..."></textarea>
                        </div>
                    </div>
                </div>

                {{-- Динамична таблица с позиции --}}
                <div class="card border-info mt-3">
                    <div class="card-header bg-info text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-list-alt mr-2"></i>Позиции в поръчката
                            <span class="badge badge-light ml-2" id="itemsCount">0 позиции</span>
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover table-bordered" id="itemsTable">
                                <thead class="bg-light">
                                    <tr>
                                        <th width="5%">№</th>
                                        <th width="25%">Продукт/Услуга</th>
                                        <th width="25%">Описание</th>
                                        <th width="10%">Кол-во</th>
                                        <th width="10%">Цена без ДДС</th>
                                        <th width="10%">ДДС %</th>
                                        <th width="10%">Общо</th>
                                        <th width="5%"></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot class="bg-light">
                                    <tr>
                                        <td colspan="7" class="text-right font-weight-bold">Общо позиции:</td>
                                        <td class="font-weight-bold" id="itemsCountFooter">0</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="d-flex flex-wrap gap-2 mt-2">
                            <button type="button" id="addProductRow" class="btn btn-success btn-sm">
                                <i class="fas fa-box mr-1"></i> Добави продукт
                            </button>
                            <button type="button" id="addServiceRow" class="btn btn-primary btn-sm">
                                <i class="fas fa-tools mr-1"></i> Добави услуга
                            </button>
                            <button type="button" id="addQuickService" class="btn btn-outline-info btn-sm">
                                <i class="fas fa-bolt mr-1"></i> Бърза услуга
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Суми --}}
                <div class="row mt-3">
                    <div class="col-md-5 offset-md-7">
                        <div class="card border-success">
                            <div class="card-header bg-success text-white py-1">
                                <h5 class="mb-0"><i class="fas fa-calculator mr-2"></i>Общо суми</h5>
                            </div>
                            <div class="card-body py-2">
                                <table class="table table-sm table-borderless mb-0">
                                    <tr>
                                        <th class="text-right py-1">Общо без ДДС:</th>
                                        <td class="text-right py-1">
                                            <span class="font-weight-bold text-primary" id="totalWithoutVat">0.00</span> лв.
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-right py-1">ДДС:</th>
                                        <td class="text-right py-1">
                                            <span class="font-weight-bold text-warning" id="totalVat">0.00</span> лв.
                                        </td>
                                    </tr>
                                    <tr class="border-top">
                                        <th class="text-right py-1 font-weight-bold">Общо с ДДС:</th>
                                        <td class="text-right py-1">
                                            <span class="h5 font-weight-bold text-success" id="grandTotal">0.00</span> лв.
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Бутони за действие --}}
                <div class="mt-3 d-flex justify-content-end gap-2">
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="fas fa-save mr-1"></i> Запази поръчка
                    </button>
                    <button type="submit" name="action" value="save_and_print" class="btn btn-primary btn-sm">
                        <i class="fas fa-print mr-1"></i> Запази и отпечатай
                    </button>
                    <a href="{{ route('admin.work-orders.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-times mr-1"></i> Отказ
                    </a>
                </div>
            </form>
        </div>
    </div>
@stop

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-theme/0.1.0-beta.10/select2-bootstrap.min.css">
    <style>
        /* По-малки Select2 контроли */
        .select2-container--bootstrap .select2-selection {
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            height: calc(1.5em + 0.5rem + 2px);
            font-size: 0.875rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }
        
        .select2-container--bootstrap .select2-selection--single .select2-selection__rendered {
            line-height: calc(1.5em + 0.5rem);
            padding-left: 0.375rem;
        }
        
        .select2-container--bootstrap .select2-selection:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
        
        .select2-container--bootstrap .select2-selection__arrow {
            height: calc(1.5em + 0.5rem);
        }
        
        /* По-малки форми */
        .form-control, .form-control-sm {
            font-size: 0.875rem;
            padding: 0.25rem 0.5rem;
            height: calc(1.5em + 0.5rem + 2px);
        }
        
        .form-group label {
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        
        /* По-малки картони */
        .card {
            margin-bottom: 1rem;
        }
        
        .card-header {
            border-radius: 0.25rem 0.25rem 0 0 !important;
            padding: 0.5rem 0.75rem;
            font-size: 0.95rem;
        }
        
        .card-body {
            padding: 0.75rem;
        }
        
        /* По-малка таблица */
        .table-sm {
            font-size: 0.85rem;
        }
        
        .table-sm th,
        .table-sm td {
            padding: 0.3rem;
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.05);
        }
        
        /* По-малки бутони */
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            line-height: 1.5;
            border-radius: 0.2rem;
        }
        
        /* По-малки инпут групи */
        .input-group-sm > .form-control,
        .input-group-sm > .input-group-prepend > .input-group-text,
        .input-group-sm > .input-group-append > .input-group-text {
            height: calc(1.5em + 0.5rem + 2px);
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        
        .input-group-text {
            background-color: #f8f9fa;
            border: 1px solid #ced4da;
            font-size: 0.875rem;
        }
        
        /* По-малки badge */
        .badge {
            font-size: 0.7em;
            padding: 0.25em 0.5em;
        }
        
        /* Автодопълване резултати */
        .list-group-item {
            padding: 0.375rem 0.75rem;
            font-size: 0.85rem;
        }
        
        .list-group-item:hover {
            background-color: #007bff !important;
            color: white !important;
            cursor: pointer;
        }
        
        .list-group-item .customer-badge {
            font-size: 0.7em;
        }
        
        .search-highlight {
            background-color: #fff3cd;
            font-weight: bold;
        }
        
        /* Информационни панели */
        #customerInfo, #vehicleInfo {
            border-left: 3px solid #007bff;
            padding: 0.5rem;
            font-size: 0.8rem;
            margin-top: 0.25rem;
        }
        
        /* Разстояния между редовете */
        .row {
            margin-bottom: 0.5rem;
        }
        
        .form-group {
            margin-bottom: 0.75rem;
        }
        
        /* Стилове за различни типове редове */
        .product-row {
            background-color: rgba(40, 167, 69, 0.05);
        }
        
        .service-row {
            background-color: rgba(23, 162, 184, 0.05);
        }
        
        .quick-service-row {
            background-color: rgba(255, 193, 7, 0.05);
        }
        
        /* AdminLTE подобрения */
        .card.border-primary .card-header {
            background: linear-gradient(45deg, #007bff, #6610f2);
        }
        
        .card.border-success .card-header {
            background: linear-gradient(45deg, #28a745, #20c997);
        }
        
        .card.border-warning .card-header {
            background: linear-gradient(45deg, #ffc107, #fd7e14);
        }
        
        .card.border-info .card-header {
            background: linear-gradient(45deg, #17a2b8, #20c997);
        }
        
        /* По-малки иконки */
        .fas, .fa {
            font-size: 0.9em;
        }
    </style>
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/i18n/bg.js"></script>
    <script>
        $(function () {
            // Инициализация на Select2
            $('.select2').select2({
                theme: 'bootstrap',
                language: 'bg',
                width: '100%',
                allowClear: true
            });

            // АВТОМАТИЧНО ПОПЪЛВАНЕ НА ПРОБЕГА ПРИ ИЗБОР НА АВТОМОБИЛ
            const vehicleSelect = document.getElementById('vehicle_id');
            const mileageInput = document.getElementById('vehicle_mileage');
            
            if (vehicleSelect && mileageInput) {
                // Вземаме данните за пробега от data attribute
                const vehiclesData = JSON.parse(vehicleSelect.getAttribute('data-mileages') || '{}');
                
                // Слушател за промяна на избора на автомобил
                $(vehicleSelect).on('change', function() {
                    const vehicleId = this.value;
                    if (vehicleId && vehiclesData[vehicleId]) {
                        // Попълване на полето за пробег
                        mileageInput.value = vehiclesData[vehicleId];
                        
                        // Тригер за валидация, ако е необходимо
                        $(mileageInput).trigger('input');
                    }
                });
            }

            // Глобално търсене
            let searchTimeout;
            $('#globalSearch').on('input', function() {
                clearTimeout(searchTimeout);
                const query = $(this).val().trim();
                
                if (query.length < 2) {
                    $('#searchResults').hide().empty();
                    return;
                }
                
                searchTimeout = setTimeout(function() {
                    performGlobalSearch(query);
                }, 300);
            });

            // Изчистване на търсенето
            $('#clearSearch').click(function() {
                $('#globalSearch').val('');
                $('#searchResults').hide().empty();
            });

            // Затваряне на резултатите при клик извън тях
            $(document).click(function(e) {
                if (!$(e.target).closest('#globalSearch, #searchResults').length) {
                    $('#searchResults').hide();
                }
            });

            // Зареждане на автомобили при избор на клиент
            $('#customer_id').change(function () {
                const customerId = $(this).val();
                loadCustomerVehicles(customerId);
                
                // Ако има избран клиент, покажи информацията му
                if (customerId) {
                    loadCustomerInfo(customerId);
                } else {
                    $('#customerInfo').hide();
                }
            });

            // Зареждане на информация при избор на автомобил
            $('#vehicle_id').change(function () {
                const vehicleId = $(this).val();
                if (vehicleId) {
                    loadVehicleInfo(vehicleId);
                } else {
                    $('#vehicleInfo').hide();
                }
            });
        });

        // Функция за глобално търсене
        function performGlobalSearch(query) {
            $.ajax({
                url: "/admin/api/search/customer-vehicle",
                method: 'GET',
                data: { q: query },
                beforeSend: function() {
                    $('#searchResults').html(`
                        <div class="list-group-item">
                            <div class="d-flex justify-content-center">
                                <div class="spinner-border spinner-border-sm text-primary mr-2"></div>
                                <span>Търсене...</span>
                            </div>
                        </div>
                    `).show();
                },
                success: function(data) {
                    if (data.customers.length === 0 && data.vehicles.length === 0) {
                        $('#searchResults').html(`
                            <div class="list-group-item text-muted">
                                <i class="fas fa-search mr-2"></i>Няма намерени резултати
                            </div>
                        `).show();
                        return;
                    }

                    let html = '';
                    
                    // Групиране на резултатите
                    if (data.customers && data.customers.length > 0) {
                        html += `<div class="list-group-item list-group-item-secondary font-weight-bold">Клиенти</div>`;
                        data.customers.forEach(customer => {
                            html += `
                                <div class="list-group-item list-group-item-action" 
                                     data-type="customer" 
                                     data-id="${customer.id}"
                                     data-name="${customer.name}"
                                     data-phone="${customer.phone || ''}"
                                     data-email="${customer.email || ''}"
                                     data-address="${customer.address || ''}">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fas fa-user text-primary mr-2"></i>
                                            <strong>${highlightText(customer.name, query)}</strong>
                                        </div>
                                        <span class="badge badge-primary customer-badge">Клиент</span>
                                    </div>
                                    ${customer.phone ? `<small class="text-muted d-block mt-1"><i class="fas fa-phone mr-1"></i>${highlightText(customer.phone, query)}</small>` : ''}
                                    ${customer.email ? `<small class="text-muted d-block"><i class="fas fa-envelope mr-1"></i>${highlightText(customer.email, query)}</small>` : ''}
                                    <small class="text-muted d-block">${customer.vehicles_count || 0} автомобила</small>
                                </div>
                            `;
                        });
                    }

                    if (data.vehicles && data.vehicles.length > 0) {
                        html += `<div class="list-group-item list-group-item-secondary font-weight-bold">Автомобили</div>`;
                        data.vehicles.forEach(vehicle => {
                            html += `
                                <div class="list-group-item list-group-item-action" 
                                     data-type="vehicle" 
                                     data-id="${vehicle.id}"
                                     data-customer-id="${vehicle.customer_id}"
                                     data-plate="${vehicle.plate}"
                                     data-make="${vehicle.make || ''}"
                                     data-model="${vehicle.model || ''}"
                                     data-year="${vehicle.year || ''}"
                                     data-engine="${vehicle.engine || ''}"
                                     data-vin="${vehicle.vin || ''}"
                                     data-mileage="${vehicle.mileage || ''}">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fas fa-car text-success mr-2"></i>
                                            <strong>${highlightText(vehicle.plate, query)}</strong>
                                        </div>
                                        <span class="badge badge-success customer-badge">Автомобил</span>
                                    </div>
                                    <small class="text-muted d-block mt-1">
                                        ${vehicle.make || ''} ${vehicle.model || ''} ${vehicle.year ? `(${vehicle.year})` : ''}
                                    </small>
                                    <small class="text-muted d-block">
                                        <i class="fas fa-user mr-1"></i>${vehicle.customer?.name || 'Няма клиент'}
                                    </small>
                                </div>
                            `;
                        });
                    }

                    $('#searchResults').html(html).show();
                    
                    // Добавяне на event listener за избор на резултат
                    $('.list-group-item[data-type]').click(function() {
                        selectSearchResult($(this));
                    });
                },
                error: function() {
                    $('#searchResults').html(`
                        <div class="list-group-item text-danger">
                            <i class="fas fa-exclamation-circle mr-2"></i>Грешка при търсенето
                        </div>
                    `).show();
                }
            });
        }

        // Функция за осветяване на текста при търсене
        function highlightText(text, query) {
            if (!query || !text) return text;
            const regex = new RegExp(`(${escapeRegExp(query)})`, 'gi');
            return text.toString().replace(regex, '<span class="search-highlight">$1</span>');
        }

        function escapeRegExp(string) {
            return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        }

        // Функция за избор на резултат от търсенето
        function selectSearchResult($element) {
            const type = $element.data('type');
            
            if (type === 'customer') {
                // Попълване на клиент
                $('#customer_id').val($element.data('id')).trigger('change');
                $('#customerInfo').html(`
                    <small class="text-muted">
                        <i class="fas fa-user text-primary mr-1"></i>
                        <strong>${$element.data('name')}</strong><br>
                        ${$element.data('phone') ? `<i class="fas fa-phone text-primary mr-1"></i>${$element.data('phone')}<br>` : ''}
                        ${$element.data('email') ? `<i class="fas fa-envelope text-primary mr-1"></i>${$element.data('email')}` : ''}
                    </small>
                `).show();
                
                // Зареждане на автомобилите на клиента
                loadCustomerVehicles($element.data('id'));
                
            } else if (type === 'vehicle') {
                // Попълване на автомобил и неговия клиент
                $('#vehicle_id').val($element.data('id')).trigger('change');
                
                // Попълване на информация за автомобила
                $('#vehicleInfo').html(`
                    <small class="text-muted">
                        <i class="fas fa-car text-primary mr-1"></i>
                        <strong>${$element.data('make')} ${$element.data('model')}</strong><br>
                        <i class="fas fa-hashtag text-primary mr-1"></i>
                        Рег. номер: <strong>${$element.data('plate')}</strong><br>
                        ${$element.data('year') ? `<i class="fas fa-calendar text-primary mr-1"></i>Година: ${$element.data('year')}<br>` : ''}
                        ${$element.data('engine') ? `<i class="fas fa-gas-pump text-primary mr-1"></i>Двигател: ${$element.data('engine')}<br>` : ''}
                        ${$element.data('mileage') ? `<i class="fas fa-tachometer-alt text-primary mr-1"></i>Пробег: ${$element.data('mileage')} км` : ''}
                    </small>
                `).show();
                
                // Попълване на пробега
                if ($element.data('mileage')) {
                    $('#vehicle_mileage').val($element.data('mileage'));
                }
                
                // Попълване на клиента
                if ($element.data('customerId')) {
                    $('#customer_id').val($element.data('customerId')).trigger('change');
                }
            }
            
            // Скриване на резултатите и изчистване на търсенето
            $('#searchResults').hide().empty();
            $('#globalSearch').val('');
        }

        // Функция за зареждане на автомобилите на клиент
        function loadCustomerVehicles(customerId) {
            if (!customerId) {
                $('#vehicle_id').html('<option value=""></option>').prop('disabled', true).trigger('change');
                return;
            }
            
            $('#vehicle_id').html('<option value="">Зареждане...</option>').prop('disabled', true);
            
            $.get("/admin/api/customer-vehicles/" + customerId, function (data) {
                let html = '<option value=""></option>';
                if (data.length > 0) {
                    $.each(data, function (i, v) {
                        html += `<option value="${v.id}">${v.plate} - ${v.make} ${v.model} (${v.year || '?'})</option>`;
                    });
                } else {
                    html = '<option value="">Няма регистрирани автомобили</option>';
                }
                $('#vehicle_id').html(html).prop('disabled', false).trigger('change');
            }).fail(function() {
                $('#vehicle_id').html('<option value="">Грешка при зареждане</option>').prop('disabled', false);
            });
        }

        // Функция за зареждане на информация за клиент
        function loadCustomerInfo(customerId) {
            $.get("/admin/api/customer-info/" + customerId, function (data) {
                if (data) {
                    $('#customerInfo').html(`
                        <small class="text-muted">
                            <i class="fas fa-user text-primary mr-1"></i>
                            <strong>${data.name}</strong><br>
                            ${data.phone ? `<i class="fas fa-phone text-primary mr-1"></i>${data.phone}<br>` : ''}
                            ${data.email ? `<i class="fas fa-envelope text-primary mr-1"></i>${data.email}<br>` : ''}
                            ${data.address ? `<i class="fas fa-map-marker-alt text-primary mr-1"></i>${data.address}` : ''}
                        </small>
                    `).show();
                }
            }).fail(function() {
                $('#customerInfo').hide();
            });
        }

        // Функция за зареждане на информация за автомобил
        function loadVehicleInfo(vehicleId) {
            $.get("/admin/api/vehicle-info/" + vehicleId, function (data) {
                if (data) {
                    $('#vehicleInfo').html(`
                        <small class="text-muted">
                            <i class="fas fa-car text-primary mr-1"></i>
                            <strong>${data.make} ${data.model}</strong><br>
                            <i class="fas fa-hashtag text-primary mr-1"></i>
                            Рег. номер: <strong>${data.plate}</strong><br>
                            ${data.year ? `<i class="fas fa-calendar text-primary mr-1"></i>Година: ${data.year}<br>` : ''}
                            ${data.engine ? `<i class="fas fa-gas-pump text-primary mr-1"></i>Двигател: ${data.engine}<br>` : ''}
                            ${data.vin ? `<i class="fas fa-barcode text-primary mr-1"></i>VIN: ${data.vin}<br>` : ''}
                            ${data.mileage ? `<i class="fas fa-tachometer-alt text-primary mr-1"></i>Пробег: ${data.mileage} км` : ''}
                        </small>
                    `).show();
                    
                    // Попълване на пробега в формата
                    if (data.mileage) {
                        $('#vehicle_mileage').val(data.mileage);
                    }
                }
            }).fail(function() {
                $('#vehicleInfo').hide();
            });
        }

        // =============== Функции за таблицата с позиции ===============
        let rowIdx = 0;

        function calcLine(row) {
            const qty   = parseFloat(row.find('.qty').val()) || 0;
            const price = parseFloat(row.find('.price').val()) || 0;
            const vat   = parseFloat(row.find('.vat').val()) || 0;
            const line  = qty * price;
            const vatAm = line * vat / 100;
            row.find('.lineTotal').text((line + vatAm).toFixed(2));
            calcTotals();
            updateItemsCount();
        }

        function calcTotals() {
            let totalWithout = 0, totalVat = 0;
            $('#itemsTable tbody tr').each(function () {
                const qty   = parseFloat($(this).find('.qty').val()) || 0;
                const price = parseFloat($(this).find('.price').val()) || 0;
                const vat   = parseFloat($(this).find('.vat').val()) || 0;
                const line  = qty * price;
                totalWithout += line;
                totalVat     += line * vat / 100;
            });
            $('#totalWithoutVat').text(totalWithout.toFixed(2));
            $('#totalVat').text(totalVat.toFixed(2));
            $('#grandTotal').text((totalWithout + totalVat).toFixed(2));
        }

        function updateItemsCount() {
            const count = $('#itemsTable tbody tr').length;
            $('#itemsCount').text(count + ' позиции');
            $('#itemsCountFooter').text(count);
        }

        function addRow(rowType = 'product', predefinedItem = null) {
            rowIdx++;
            const isService = rowType === 'service';
            const isQuickService = rowType === 'quick_service';
            const rowClass = isService ? 'service-row' : (isQuickService ? 'quick-service-row' : 'product-row');
            
            let productOptions = '<option value=""></option>';
            let placeholder = 'Изберете продукт';
            
            if (isService || isQuickService) {
                // Добави услуги
                placeholder = 'Изберете услуга';
                @if(isset($services) && $services->count())
                    @foreach($services as $service)
                        productOptions += `
                            <option value="service_{{ $service->id }}" 
                                    data-price="{{ $service->price }}" 
                                    data-vat="{{ $service->vat_percent }}"
                                    data-type="service">
                                {{ $service->code }} - {{ $service->name }}
                            </option>`;
                    @endforeach
                @endif
            } else {
                // Добави продукти
                @if(isset($products) && $products->count())
                    @foreach($products as $product)
                        productOptions += `
                            <option value="product_{{ $product->id }}" 
                                    data-price="{{ $product->price }}" 
                                    data-vat="{{ $product->vat_percent }}"
                                    data-type="product">
                                {{ $product->sku }} - {{ $product->name }}
                            </option>`;
                    @endforeach
                @endif
            }

            const html = `
                <tr id="R${rowIdx}" class="${rowClass}">
                    <td class="align-middle">${rowIdx}</td>
                    <td>
                        <select name="items[${rowIdx}][product_id]" class="form-control form-control-sm product-select" 
                                style="width:100%" data-row-type="${rowType}">
                            ${productOptions}
                        </select>
                        <input type="hidden" name="items[${rowIdx}][item_type]" value="${isService || isQuickService ? 'service' : 'product'}">
                    </td>
                    <td>
                        <input type="text" name="items[${rowIdx}][description]" 
                               class="form-control form-control-sm" placeholder="Описание" required>
                    </td>
                    <td>
                        <input type="number" name="items[${rowIdx}][quantity]" 
                               class="form-control form-control-sm qty" min="0.01" step="0.01" value="1" required>
                    </td>
                    <td>
                        <input type="number" name="items[${rowIdx}][unit_price]" 
                               class="form-control form-control-sm price" min="0.01" step="0.01" required>
                    </td>
                    <td>
                        <input type="number" name="items[${rowIdx}][vat_percent]" 
                               class="form-control form-control-sm vat" min="0" max="100" step="0.01" value="20" required>
                    </td>
                    <td class="align-middle lineTotal font-weight-bold">0.00</td>
                    <td class="align-middle">
                        <button type="button" class="btn btn-sm btn-danger removeRow" title="Премахни">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>`;
            
            $('#itemsTable tbody').append(html);
            
            // Инициализация на Select2
            $(`#R${rowIdx} .product-select`).select2({
                theme: 'bootstrap',
                language: 'bg',
                placeholder: placeholder,
                allowClear: true,
                width: '100%'
            });
            
            // Ако има predefined елемент, избери го
            if (predefinedItem) {
                $(`#R${rowIdx} .product-select`).val(predefinedItem.id).trigger('change');
                $(`#R${rowIdx} .price`).val(predefinedItem.price);
                $(`#R${rowIdx} .vat`).val(predefinedItem.vat_percent || 20);
                $(`#R${rowIdx} input[name*="description"]`).val(predefinedItem.name);
                calcLine($(`#R${rowIdx}`));
            }
            
            updateItemsCount();
        }

        // Event Listeners за таблицата
        $(document)
            .on('click', '#addProductRow', () => addRow('product'))
            .on('click', '#addServiceRow', () => addRow('service'))
            .on('click', '#addQuickService', () => {
                addRow('service', {
                    id: 'quick_service',
                    name: 'Бърз ремонт',
                    price: 50,
                    vat_percent: 20
                });
            })
            .on('click', '.removeRow', function () {
                $(this).closest('tr').remove();
                calcTotals();
                updateItemsCount();
                renumberRows();
            })
            .on('change keyup', '.qty, .price, .vat', function () {
                calcLine($(this).closest('tr'));
            })
            .on('change', '.product-select', function () {
                const option = $(this).find(':selected');
                const row = $(this).closest('tr');
                const price = option.data('price');
                const vat = option.data('vat');
                const type = option.data('type');
                
                if (price) row.find('.price').val(price);
                if (vat) row.find('.vat').val(vat);
                
                if (option.text() && !row.find('input[name*="description"]').val()) {
                    row.find('input[name*="description"]').val(option.text().split(' - ')[1] || option.text());
                }
                
                // Обновяване на типа на артикула
                row.find('input[name*="item_type"]').val(type);
                
                calcLine(row);
            });

        // Преномериране на редове след изтриване
        function renumberRows() {
            $('#itemsTable tbody tr').each(function(index) {
                $(this).find('td:first').text(index + 1);
                $(this).attr('id', 'R' + (index + 1));
                // Обновяване на имената на полетата
                $(this).find('[name*="items"]').each(function() {
                    const name = $(this).attr('name');
                    $(this).attr('name', name.replace(/items\[\d+\]/, `items[${index + 1}]`));
                });
            });
            rowIdx = $('#itemsTable tbody tr').length;
        }

        // Добавяне на първи ред при зареждане
        $(document).ready(function() {
            addRow('product');
        });
    </script>
@endpush