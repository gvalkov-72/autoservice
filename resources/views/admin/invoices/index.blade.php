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
    </style>

    <div class="row">
        <div class="col-12">
            <div class="card card-primary card-outline">

                <div class="card-header border-bottom-0 pb-0">
                    <div class="d-flex justify-content-between align-items-center">

                        <!-- ТЪРСЕНЕ -->
                        <div class="input-group input-group-sm" style="width: 300px;">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                            </div>
                            <input type="text" id="quickSearch" class="form-control"
                                placeholder="Търсене по № фактура, клиент, сума...">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                                    <i class="fas fa-times"></i>
                                </button>
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
                                    <option value="active">Активни</option>
                                    <option value="inactive">Неактивни</option>
                                </select>
                            </div>

                            <div class="input-group input-group-sm" style="width:200px;">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-credit-card"></i></span>
                                </div>
                                <select id="filterPayment" class="form-control">
                                    <option value="">Всички плащания</option>
                                    <option value="paid">Платени</option>
                                    <option value="unpaid">Неплатени</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th width="30" class="text-center">
                                    <input type="checkbox" id="checkAll">
                                </th>
                                <th width="60" class="text-center">ID</th>
                                <th>Фактура</th>
                                <th>Клиент</th>
                                <th width="120">Сума</th>
                                <th width="120">Плащане</th>
                                <th width="120">Статус</th>
                                <th width="140" class="text-center">Действия</th>
                            </tr>
                        </thead>
                        <tbody id="invoicesTableBody">
                            @foreach ($invoices as $invoice)
                                <tr data-status="{{ $invoice->is_active ? 'active' : 'inactive' }}"
                                    data-payment="{{ $invoice->payment_status }}">
                                    <td class="text-center">
                                        <input type="checkbox" class="row-check" value="{{ $invoice->id }}">
                                    </td>
                                    <td class="text-center">#{{ $invoice->id }}</td>
                                    <td>{{ $invoice->invoice_number }}</td>
                                    <td>{{ $invoice->customer?->name }}</td>
                                    <td>{{ number_format($invoice->total_amount, 2) }} лв</td>
                                    <td>
                                        <span
                                            class="badge badge-{{ $invoice->payment_status === 'paid' ? 'success' : 'warning' }}">
                                            {{ $invoice->payment_status === 'paid' ? 'Платена' : 'Неплатена' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $invoice->is_active ? 'success' : 'secondary' }}">
                                            {{ $invoice->is_active ? 'Активна' : 'Неактивна' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.invoices.show', $invoice) }}" class="btn btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.invoices.edit', $invoice) }}" class="btn btn-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
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
                            Показване {{ $invoices->firstItem() }} – {{ $invoices->lastItem() }}
                            от {{ $invoices->total() }} фактури
                        </small>
                    </div>
                    <div class="float-right">
                        {{ $invoices->links('pagination::bootstrap-4') }}
                    </div>
                </div>

            </div>
        </div>
    </div>
@stop

@push('js')
    <script>
        $(document).ready(function() {
            const rows = $('#invoicesTableBody tr');

            function applyFilters() {
                const search = $('#quickSearch').val().toLowerCase();
                const status = $('#filterStatus').val();
                const payment = $('#filterPayment').val();

                rows.each(function() {
                    const row = $(this);
                    let show = true;

                    if (status && row.data('status') !== status) show = false;
                    if (payment && row.data('payment') !== payment) show = false;
                    if (search && !row.text().toLowerCase().includes(search)) show = false;

                    row.toggle(show);
                });
            }

            $('#quickSearch, #filterStatus, #filterPayment').on('keyup change', applyFilters);
            $('#clearSearch').click(function() {
                $('#quickSearch').val('');
                applyFilters();
            });

            $('#checkAll').on('change', function() {
                $('.row-check').prop('checked', this.checked);
            });

            $('#bulkPdf').on('click', function(e) {
                e.preventDefault();
                let ids = $('.row-check:checked').map(function() {
                    return this.value;
                }).get();

                if (!ids.length) {
                    alert('Моля, изберете поне една фактура.');
                    return;
                }

                // Отваряне в нов прозорец
                window.open(
                    "{{ route('admin.invoices.export.pdf') }}?ids=" + ids.join(','),
                    '_blank'
                );
            });

            applyFilters();
        });
    </script>
@endpush
