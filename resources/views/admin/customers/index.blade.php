@extends('adminlte::page')

@section('title', 'Клиенти')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Клиенти</h1>
        <a href="{{ route('admin.customers.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Нов клиент
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
                        <i class="fas fa-users mr-1"></i> Списък клиенти
                    </h3>
                    <div class="d-flex align-items-center ml-2">
                        <span class="mr-2 font-weight-normal" style="font-size:0.75rem;">Търсене:</span>
                        <input type="text" name="search" value="{{ $search ?? '' }}"
                               class="form-control form-control-sm" style="width:320px;"
                               placeholder="име, телефон, e-mail, клиентски №, БУЛСТАТ...">
                    </div>
                </div>

                {{-- Филтри --}}
                <div class="d-flex align-items-center border-left pl-3">
                    <div class="d-flex align-items-center mr-3">
                        <small class="text-muted mr-1">Активност:</small>
                        <select name="is_active" id="is_active" class="form-control form-control-sm" style="width:120px;">
                            <option value="">Всички</option>
                            <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Активни</option>
                            <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Неактивни</option>
                        </select>
                    </div>
                    <div class="d-flex align-items-center mr-3">
                        <small class="text-muted mr-1">Тип:</small>
                        <select name="type" id="type" class="form-control form-control-sm" style="width:120px;">
                            <option value="">Всички</option>
                            <option value="customer" {{ request('type') === 'customer' ? 'selected' : '' }}>Клиенти</option>
                            <option value="supplier" {{ request('type') === 'supplier' ? 'selected' : '' }}>Доставчици</option>
                        </select>
                    </div>
                    <a href="{{ route('admin.customers.index') }}" class="btn btn-default btn-sm" title="Изчисти филтри">
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
                        <th style="width:60px;">#</th>
                        <th>Клиент / Доставчик</th>
                        <th>Данъчни данни</th>
                        <th>Адрес</th>
                        <th>Автомобили</th>
                        <th style="width:110px;">Статус</th>
                        <th style="width:140px;" class="text-center">Действия</th>
                    </tr>
                </thead>
                <tbody id="customers-body">
                    @include('admin.customers.partials.rows', ['customers' => $customers])
                </tbody>
            </table>
        </div>

        {{-- PAGINATION / INFO --}}
        @if ($customers instanceof \Illuminate\Pagination\LengthAwarePaginator && $customers->hasPages())
            <div class="card-footer clearfix" id="pagination-wrapper">
                <div class="float-left">
                    <small class="text-muted">
                        Показване на {{ $customers->firstItem() }} до {{ $customers->lastItem() }}
                        от общо {{ $customers->total() }} резултата
                    </small>
                </div>
                <div class="float-right">
                    {{ $customers->appends(request()->except('page'))->links('pagination::bootstrap-4') }}
                </div>
            </div>
        @else
            <div class="card-footer" id="results-info">
                <small class="text-muted">
                    Показване на всички {{ $customers->count() }} клиенти
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
                    $('#customers-body').after(
                        '<div class="card-footer" id="live-search-info">' +
                        '<small class="text-muted">Намерени ' + total + ' клиенти (live търсене)</small>' +
                        '</div>'
                    );
                } else {
                    $('#customers-body').after(
                        '<div class="card-footer" id="live-search-info">' +
                        '<small class="text-muted">Няма намерени клиенти</small>' +
                        '</div>'
                    );
                }
            }

            function performSearch() {
                const search = $('input[name="search"]').val().trim();
                const isActive = $('#is_active').val();
                const type = $('#type').val();

                if (search.length === 0 && !isActive && !type) {
                    removeLiveSearchInfo();
                    window.location = "{{ route('admin.customers.index') }}";
                    return;
                }

                $.get("{{ route('admin.customers.live-search') }}", {
                        search: search,
                        is_active: isActive,
                        type: type
                    })
                    .done(function(response) {
                        $('#customers-body').html(response.html);
                        showResultInfo(response.total);
                    })
                    .fail(function() {
                        $('#customers-body').html(
                            '<tr><td colspan="7" class="text-center text-danger py-4">Грешка при търсенето</td></tr>'
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
                    if (!$('#is_active').val() && !$('#type').val()) {
                        window.location = "{{ route('admin.customers.index') }}";
                    } else {
                        performSearch();
                    }
                }
            });

            $('#is_active, #type').on('change', function() {
                performSearch();
            });

            $('input[name="search"]').focus();

            $(document).on('submit', 'form[method="DELETE"]', function(e) {
                if (!confirm('Сигурни ли сте, че искате да изтриете този клиент?')) {
                    e.preventDefault();
                    return false;
                }
                return true;
            });
        });
    </script>
@stop