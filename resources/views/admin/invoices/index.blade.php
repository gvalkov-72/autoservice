@extends('adminlte::page')

@section('title', 'Фактури')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0">
            <i class="fas fa-file-invoice-dollar text-primary mr-2"></i>Управление на фактури
        </h1>
        <div>
            <!-- Групов експорт -->
            <div class="btn-group mr-2" role="group">
                <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-toggle="dropdown">
                    <i class="fas fa-file-export mr-1"></i>Експорт
                </button>
                <div class="dropdown-menu" style="font-size:0.85rem; min-width:180px;">
                    <a class="dropdown-item" href="{{ route('admin.invoices.export.pdf') }}">
                        <i class="fas fa-file-pdf text-danger mr-1"></i>PDF (всички)
                    </a>
                    <a class="dropdown-item" href="#" id="bulkPdf">
                        <i class="fas fa-file-pdf text-danger mr-1"></i>PDF (избрани)
                    </a>
                </div>
            </div>

            <a href="{{ route('admin.invoices.create') }}" class="btn btn-success">
                <i class="fas fa-plus mr-1"></i>Нова фактура
            </a>
        </div>
    </div>
@stop

@section('content')

    <style>
        .table {
            font-size: 0.8rem !important;
        }

        .table .fas,
        .table .badge .fas {
            font-size: 0.7rem !important;
        }

        .table thead th {
            font-size: 0.85rem !important;
            padding: 0.4rem 0.5rem !important;
        }

        .table tbody td {
            padding: 0.3rem 0.5rem !important;
        }

        .table .badge {
            font-size: 0.7rem !important;
            padding: 0.2rem 0.4rem !important;
        }

        .btn-group-sm>.btn,
        .btn-sm {
            padding: 0.15rem 0.35rem !important;
            font-size: 0.75rem !important;
        }
        
        .loading-spinner {
            display: none;
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
        }
        
        #searchInputGroup {
            position: relative;
        }
        
        .search-hint {
            font-size: 0.7rem;
            color: #6c757d;
            margin-top: 2px;
        }
    </style>

    <div class="row">
        <div class="col-12">
            <div class="card card-primary card-outline">

                <div class="card-header border-bottom-0 pb-0">
                    <div class="d-flex justify-content-between align-items-center">

                        <!-- ТЪРСЕНЕ -->
                        <div style="width: 300px;">
                            <div class="input-group input-group-sm" id="searchInputGroup">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                </div>
                                <input type="text" id="quickSearch" class="form-control"
                                    placeholder="Търсене по № фактура, клиент, сума..."
                                    value="{{ request('search') }}" autocomplete="off"
                                    title="Търси записи, които започват с въведения текст">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <div class="loading-spinner" id="searchLoading">
                                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                                        <span class="sr-only">Зареждане...</span>
                                    </div>
                                </div>
                            </div>
                            <div class="search-hint">
                                <i class="fas fa-info-circle mr-1"></i>Търси записи, които <strong>започват</strong> с въведения текст
                            </div>
                        </div>

                        <!-- ФИЛТРИ -->
                        <div class="card-tools d-flex align-items-center" style="gap:10px;">
                            <div class="input-group input-group-sm" style="width:180px;">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-check-circle"></i></span>
                                </div>
                                <select id="filterStatus" class="form-control">
                                    <option value="">Всички статуси</option>
                                    <option value="active" {{ request('is_active_filter') == 'active' ? 'selected' : '' }}>Активни</option>
                                    <option value="inactive" {{ request('is_active_filter') == 'inactive' ? 'selected' : '' }}>Неактивни</option>
                                </select>
                            </div>

                            <div class="input-group input-group-sm" style="width:200px;">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-credit-card"></i></span>
                                </div>
                                <select id="filterPayment" class="form-control">
                                    <option value="">Всички плащания</option>
                                    <option value="paid" {{ request('payment_filter') == 'paid' ? 'selected' : '' }}>Платени</option>
                                    <option value="unpaid" {{ request('payment_filter') == 'unpaid' ? 'selected' : '' }}>Неплатени</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body" id="invoicesTableContainer">
                    @include('admin.invoices.partials.table', ['invoices' => $invoices])
                </div>

            </div>
        </div>
    </div>
@stop

@push('js')
<script>
$(document).ready(function() {
    console.log('Invoice search starting...');
    
    // Проверка дали jQuery работи
    console.log('jQuery version:', $().jquery);
    
    // Настройваме CSRF токен за всички AJAX заявки
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest'
        }
    });

    // Проста функция за AJAX търсене
    function performSearch() {
        console.log('performSearch called');
        
        const searchValue = $('#quickSearch').val();
        const statusValue = $('#filterStatus').val();
        const paymentValue = $('#filterPayment').val();
        
        console.log('Search params:', {
            search: searchValue,
            is_active_filter: statusValue,
            payment_filter: paymentValue
        });
        
        // Показваме loading спинър
        $('#searchLoading').show();
        
        // Подготвяме данните за заявката
        let data = {};
        
        if (searchValue) {
            data.search = searchValue;
        }
        
        if (statusValue) {
            data.is_active_filter = statusValue;
        }
        
        if (paymentValue) {
            data.payment_filter = paymentValue;
        }
        
        // Винаги добавяме ajax параметър
        data.ajax = true;
        
        console.log('Sending AJAX with data:', data);
        
        $.ajax({
            url: '{{ route("admin.invoices.index") }}',
            type: 'GET',
            data: data,
            success: function(response) {
                console.log('AJAX success, response received');
                $('#invoicesTableContainer').html(response);
                console.log('Table updated');
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error Details:');
                console.error('Status:', status);
                console.error('Error:', error);
                console.error('XHR Response:', xhr.responseText);
                console.error('XHR Status:', xhr.status);
                alert('Възникна грешка при търсенето. Проверете конзолата за повече информация.');
            },
            complete: function() {
                $('#searchLoading').hide();
                console.log('AJAX complete');
            }
        });
    }

    // Таймер за дебонс
    let searchTimeout;
    
    // Автоматично търсене при писане
    $('#quickSearch').on('input', function() {
        console.log('Input detected in search field');
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            performSearch();
        }, 300); // 300ms забавяне
    });

    // При промяна на филтрите
    $('#filterStatus, #filterPayment').on('change', function() {
        console.log('Filter changed:', this.id);
        performSearch();
    });

    // Изчистване на търсенето
    $('#clearSearch').click(function() {
        console.log('Clear search clicked');
        $('#quickSearch').val('');
        performSearch();
    });

    // Пагинация с AJAX
    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();
        console.log('Pagination clicked');
        
        const url = $(this).attr('href');
        $('#searchLoading').show();
        
        $.ajax({
            url: url,
            type: 'GET',
            data: { 
                ajax: true,
                search: $('#quickSearch').val(),
                is_active_filter: $('#filterStatus').val(),
                payment_filter: $('#filterPayment').val()
            },
            success: function(response) {
                $('#invoicesTableContainer').html(response);
            },
            error: function(xhr, status, error) {
                console.error('Pagination AJAX Error:', status, error);
                alert('Възникна грешка при зареждането на страницата.');
            },
            complete: function() {
                $('#searchLoading').hide();
            }
        });
    });

    // Check all checkbox
    $(document).on('change', '#checkAll', function() {
        $('.row-check').prop('checked', this.checked);
    });

    // Bulk PDF export
    $(document).on('click', '#bulkPdf', function(e) {
        e.preventDefault();
        let ids = $('.row-check:checked').map(function() {
            return this.value;
        }).get();

        if (!ids.length) {
            alert('Моля, изберете поне една фактура.');
            return;
        }

        window.open(
            "{{ route('admin.invoices.export.pdf') }}?ids=" + ids.join(','),
            '_blank'
        );
    });

    console.log('Invoice search initialized');
});
</script>
@endpush