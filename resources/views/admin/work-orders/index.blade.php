@extends('adminlte::page')

@section('title', 'Работни поръчки')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Работни поръчки</h1>
        <a href="{{ route('admin.work-orders.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Нова поръчка
        </a>
    </div>
@stop

@section('content')
    @php
        $rate = 1.95583;
        $showBgn = now()->lte('2026-01-31');

        function toBgn($amountEur, $rate = 1.95583, $decimals = 2)
        {
            return number_format($amountEur * $rate, $decimals, ',', ' ');
        }

        function formatEur($amountEur, $decimals = 2)
        {
            return number_format($amountEur, $decimals, ',', ' ');
        }
    @endphp
    <div class="card card-primary card-outline">
        {{-- ЗАГЛАВИЕ, SEARCH И ФИЛТЪРИ В ЕДНО --}}
        <div class="card-header">
            <div class="d-flex align-items-center justify-content-between">
                {{-- Заглавие --}}
                <div class="d-flex align-items-center">
                    <h3 class="card-title mb-0 mr-4">
                        <i class="fas fa-clipboard-list mr-1"></i>
                        Списък с поръчки
                    </h3>

                    {{-- Live Search с надпис --}}
                    <div class="d-flex align-items-center ml-2">
                        <span class="mr-2 font-weight-normal" style="font-size: 0.75rem;">Търсене на поръчка:</span>
                        <input type="text" name="search" value="{{ $search ?? '' }}"
                            class="form-control form-control-sm" style="width: 300px;"
                            placeholder="клиент, кола, рег.№, телефон...">
                    </div>
                </div>

                {{-- Филтър по дати --}}
                <div class="d-flex align-items-center border-left pl-3">
                    <div class="d-flex align-items-center mr-2">
                        <small class="text-muted mr-1">От:</small>
                        <input type="date" name="date_from" id="date_from" class="form-control form-control-sm"
                            style="width: 130px;" value="{{ $dateFrom ?? '' }}">
                    </div>

                    <div class="d-flex align-items-center mr-2">
                        <small class="text-muted mr-1">До:</small>
                        <input type="date" name="date_to" id="date_to" class="form-control form-control-sm"
                            style="width: 130px;" value="{{ $dateTo ?? '' }}">
                    </div>

                    <button id="apply-date-filter" class="btn btn-primary btn-sm mr-2">
                        <i class="fas fa-filter"></i>
                    </button>

                    <a href="{{ route('admin.work-orders.index') }}" class="btn btn-default btn-sm" title="Изчисти филтър">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </div>
        </div>

        {{-- ТАБЛИЦА С ПОРЪЧКИ --}}
        <div class="card-body table-responsive p-0">
            <table class="table table-hover table-striped mb-0">
                <thead>
                    <tr>
                        <th style="width:70px">#</th>
                        <th>Клиент - <small class="text-muted">(тел. номер)</small></th>
                        <th>Автомобил - <small class="text-muted">(рег.№ / VIN)</small></th>
                        <th>Дата</th>
                        <th class="text-right">Общо</th>
                        <th style="width:90px"></th>
                    </tr>
                </thead>

                <tbody id="work-orders-body">
                    @forelse($workOrders as $wo)
                        <tr>
                            <td>{{ $wo->old_id }}</td>
                            <td>
                                <strong>{{ $wo->client_name ?: '—' }}</strong><br>
                                <small class="text-muted">{{ $wo->phone }}</small>
                            </td>
                            <td>
                                <div class="font-weight-bold">{{ $wo->vehicle ?: '—' }}</div>
                                <div class="text-muted">
                                    <small>
                                        @if ($wo->plate_number)
                                            <i class="fas fa-car mr-1"></i>{{ $wo->plate_number }}
                                        @endif
                                        @if ($wo->chassis_number)
                                            @if ($wo->plate_number)
                                                •
                                            @endif
                                            <i class="fas fa-id-card mr-1"></i>{{ $wo->chassis_number }}
                                        @endif
                                        @if (!$wo->plate_number && !$wo->chassis_number)
                                            —
                                        @endif
                                    </small>
                                </div>
                            </td>
                            <td>{{ $wo->order_date?->format('d.m.Y') }}</td>
                            <td class="text-right font-weight-bold">
                                <div>{{ formatEur($wo->total) }} €</div>
                                @if ($showBgn)
                                    <small class="text-muted">{{ toBgn($wo->total, $rate) }} лв</small>
                                @endif
                            </td>
                            <td class="text-right">
                                <a href="{{ route('admin.work-orders.show', $wo->id) }}"
                                    class="btn btn-sm btn-outline-primary" title="Преглед">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                Няма намерени поръчки
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if ($needsPagination)
            <div class="card-footer clearfix" id="pagination-wrapper">
                <div class="float-left">
                    <small class="text-muted">
                        Показване на {{ $workOrders->firstItem() }} до {{ $workOrders->lastItem() }}
                        от общо {{ $workOrders->total() }} поръчки
                    </small>
                </div>
                <div class="float-right">
                    {{ $workOrders->appends(request()->except('page'))->links('pagination::bootstrap-4') }}
                </div>
            </div>
        @else
            <div class="card-footer" id="results-info">
                <small class="text-muted">
                    Показване на всички {{ count($workOrders) }} поръчки
                </small>
            </div>
        @endif
    </div>
@stop

@section('js')
    <script>
        $(function() {
            let timer = null;

            // Функция за премахване на стария live search резултат
            function removePreviousResultInfo() {
                $('#live-search-info').remove();
                $('#live-search-pagination').remove();
            }

            // Функция за показване на информация за резултатите
            function showResultInfo(total, isLiveSearch = false) {
                removePreviousResultInfo();

                if (total > 0) {
                    $('#pagination-wrapper, #results-info').hide();
                    let infoText = 'Намерени ' + total + ' поръчки';
                    if (isLiveSearch) infoText += ' (live search)';

                    $('#work-orders-body').after(
                        '<div class="card-footer" id="live-search-info">' +
                        '<small class="text-muted">' + infoText + '</small>' +
                        '</div>'
                    );
                } else {
                    $('#pagination-wrapper, #results-info').hide();
                    $('#work-orders-body').after(
                        '<div class="card-footer" id="live-search-info">' +
                        '<small class="text-muted">Няма намерени поръчки</small>' +
                        '</div>'
                    );
                }
            }

            // Функция за live search с дати
            function performSearch() {
                const searchValue = $('input[name="search"]').val().trim();
                const dateFrom = $('#date_from').val();
                const dateTo = $('#date_to').val();

                // Ако полето се изчисти и няма дати → връщаме нормалния index
                if (searchValue.length === 0 && !dateFrom && !dateTo) {
                    removePreviousResultInfo();
                    window.location = "{{ route('admin.work-orders.index') }}";
                    return;
                }

                // Изпращаме заявка с всички параметри
                $.get("{{ route('admin.work-orders.search') }}", {
                        q: searchValue,
                        date_from: dateFrom,
                        date_to: dateTo
                    })
                    .done(function(response) {
                        $('#work-orders-body').html(response.html);
                        showResultInfo(response.total, true);
                    })
                    .fail(function() {
                        $('#work-orders-body').html(
                            '<tr><td colspan="6" class="text-center text-danger py-4">Грешка при търсенето</td></tr>'
                        );
                        removePreviousResultInfo();
                        $('#pagination-wrapper, #results-info').hide();
                    });
            }

            // Live search за текстово поле
            $('input[name="search"]').on('keyup', function() {
                clearTimeout(timer);
                timer = setTimeout(performSearch, 300);
            });

            // Бутон за филтриране по дати
            $('#apply-date-filter').on('click', function() {
                const dateFrom = $('#date_from').val();
                const dateTo = $('#date_to').val();

                if (dateFrom && dateTo && dateFrom > dateTo) {
                    alert('"От дата" не може да бъде по-късно от "До дата"');
                    return;
                }

                // Изпращаме заявка с датите
                const searchValue = $('input[name="search"]').val().trim();
                $.get("{{ route('admin.work-orders.search') }}", {
                        q: searchValue,
                        date_from: dateFrom,
                        date_to: dateTo
                    })
                    .done(function(response) {
                        $('#work-orders-body').html(response.html);
                        showResultInfo(response.total, false);

                        // Активираме текстовото търсене, ако има стойност
                        if (searchValue) {
                            $('input[name="search"]').focus();
                        }
                    });
            });

            // При изчистване на текстовото поле
            $('input[name="search"]').on('input', function() {
                if ($(this).val().trim() === '') {
                    removePreviousResultInfo();
                }
            });

            // Фокус в текстовото поле
            $('input[name="search"]').focus();
        });
    </script>
@stop
