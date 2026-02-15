@extends('adminlte::page')

@section('title', 'Фактури')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Фактури</h1>
        <a href="{{ route('admin.invoices.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Нова фактура
        </a>
    </div>
@stop

@section('content')
    <div class="card card-primary card-outline">
        <div class="card-header">
            <div class="d-flex align-items-center justify-content-between flex-wrap">
                {{-- Заглавие и Live търсене --}}
                <div class="d-flex align-items-center">
                    <h3 class="card-title mb-0 mr-4">
                        <i class="fas fa-file-invoice mr-1"></i> Списък фактури
                    </h3>
                    <div class="d-flex align-items-center ml-2">
                        <span class="mr-2 font-weight-normal" style="font-size:0.75rem;">Търсене:</span>
                        <input type="text" name="search" value="{{ $search ?? '' }}"
                               class="form-control form-control-sm" style="width:280px;"
                               placeholder="№ фактура, клиент, телефон...">
                    </div>
                </div>

                {{-- Филтри --}}
                <div class="d-flex align-items-center border-left pl-3">
                    {{-- Дата от/до --}}
                    <div class="d-flex align-items-center mr-3">
                        <small class="text-muted mr-1">От:</small>
                        <input type="date" name="date_from" id="date_from" class="form-control form-control-sm"
                               style="width:130px;" value="{{ $date_from ?? '' }}">
                    </div>
                    <div class="d-flex align-items-center mr-3">
                        <small class="text-muted mr-1">До:</small>
                        <input type="date" name="date_to" id="date_to" class="form-control form-control-sm"
                               style="width:130px;" value="{{ $date_to ?? '' }}">
                    </div>

                    {{-- Статус плащане --}}
                    <div class="d-flex align-items-center mr-3">
                        <small class="text-muted mr-1">Платени:</small>
                        <select name="paid" id="paid" class="form-control form-control-sm" style="width:100px;">
                            <option value="">Всички</option>
                            <option value="1" {{ request('paid') === '1' ? 'selected' : '' }}>Платени</option>
                            <option value="0" {{ request('paid') === '0' ? 'selected' : '' }}>Неплатени</option>
                        </select>
                    </div>

                    {{-- Анулирани --}}
                    <div class="d-flex align-items-center mr-3">
                        <small class="text-muted mr-1">Анулирани:</small>
                        <select name="is_void" id="is_void" class="form-control form-control-sm" style="width:100px;">
                            <option value="">Всички</option>
                            <option value="1" {{ request('is_void') === '1' ? 'selected' : '' }}>Анулирани</option>
                            <option value="0" {{ request('is_void') === '0' ? 'selected' : '' }}>Активни</option>
                        </select>
                    </div>

                    <a href="{{ route('admin.invoices.index') }}" class="btn btn-default btn-sm" title="Изчисти филтри">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </div>
        </div>

        {{-- ТАБЛИЦА --}}
        <div class="card-body table-responsive p-0">
            <table class="table table-hover table-striped mb-0">
                <thead>
                    <tr>
                        <th style="width:70px;">№</th>
                        <th>Дата</th>
                        <th>Клиент</th>
                        <th>Работна поръчка</th>
                        <th>Тип</th>
                        <th>Падеж</th>
                        <th class="text-right">Сума (€)</th>
                        <th class="text-right">Сума (лв)</th>
                        <th style="width:100px;">Статус</th>
                        <th style="width:140px;" class="text-center">Действия</th>
                    </tr>
                </thead>
                <tbody id="invoices-body">
                    @include('admin.invoices.partials.rows', ['invoices' => $invoices])
                </tbody>
            </table>
        </div>

        {{-- PAGINATION / INFO --}}
        @if ($invoices instanceof \Illuminate\Pagination\LengthAwarePaginator && $invoices->hasPages())
            <div class="card-footer clearfix" id="pagination-wrapper">
                <div class="float-left">
                    <small class="text-muted">
                        Показване на {{ $invoices->firstItem() }} до {{ $invoices->lastItem() }}
                        от общо {{ $invoices->total() }} фактури
                    </small>
                </div>
                <div class="float-right">
                    {{ $invoices->appends(request()->except('page'))->links('pagination::bootstrap-4') }}
                </div>
            </div>
        @else
            <div class="card-footer" id="results-info">
                <small class="text-muted">
                    Показване на всички {{ $invoices->count() }} фактури
                </small>
            </div>
        @endif
    </div>
@stop

@section('js')
    <script>
        $(function() {
            let timer = null;

            function removeLiveSearchInfo() {
                $('#live-search-info, #live-search-pagination').remove();
            }

            function showResultInfo(total) {
                removeLiveSearchInfo();
                $('#pagination-wrapper, #results-info').hide();

                if (total > 0) {
                    $('#invoices-body').after(
                        '<div class="card-footer" id="live-search-info">' +
                        '<small class="text-muted">Намерени ' + total + ' фактури (live търсене)</small>' +
                        '</div>'
                    );
                } else {
                    $('#invoices-body').after(
                        '<div class="card-footer" id="live-search-info">' +
                        '<small class="text-muted">Няма намерени фактури</small>' +
                        '</div>'
                    );
                }
            }

            function performSearch() {
                const search = $('input[name="search"]').val().trim();
                const dateFrom = $('#date_from').val();
                const dateTo = $('#date_to').val();
                const paid = $('#paid').val();
                const isVoid = $('#is_void').val();

                // Ако няма criteria → презареждане на нормалния index
                if (search.length === 0 && !dateFrom && !dateTo && !paid && !isVoid) {
                    removeLiveSearchInfo();
                    window.location = "{{ route('admin.invoices.index') }}";
                    return;
                }

                $.get("{{ route('admin.invoices.live-search') }}", {
                        search: search,
                        date_from: dateFrom,
                        date_to: dateTo,
                        paid: paid,
                        is_void: isVoid
                    })
                    .done(function(response) {
                        $('#invoices-body').html(response.html);
                        showResultInfo(response.total);
                    })
                    .fail(function() {
                        $('#invoices-body').html(
                            '<tr><td colspan="9" class="text-center text-danger py-4">Грешка при търсенето</td></tr>'
                        );
                        removeLiveSearchInfo();
                        $('#pagination-wrapper, #results-info').hide();
                    });
            }

            $('input[name="search"]').on('keyup', function() {
                clearTimeout(timer);
                timer = setTimeout(performSearch, 300);
            });

            $('input[name="search"]').on('input', function() {
                if ($(this).val().trim() === '') {
                    if (!$('#date_from').val() && !$('#date_to').val() && !$('#paid').val() && !$('#is_void').val()) {
                        window.location = "{{ route('admin.invoices.index') }}";
                    } else {
                        performSearch();
                    }
                }
            });

            $('#date_from, #date_to, #paid, #is_void').on('change', function() {
                performSearch();
            });

            $('input[name="search"]').focus();

            $(document).on('submit', 'form[method="DELETE"]', function(e) {
                if (!confirm('Сигурни ли сте, че искате да изтриете тази фактура?')) {
                    e.preventDefault();
                    return false;
                }
                return true;
            });
        });
    </script>
@stop