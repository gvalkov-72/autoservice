@extends('adminlte::page')

@section('title', 'Фирмени данни')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Фирмени данни</h1>
        <a href="{{ route('admin.company-settings.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Нови фирмени данни
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
                        <i class="fas fa-building mr-1"></i> Списък фирмени профили
                    </h3>
                    <div class="d-flex align-items-center ml-2">
                        <span class="mr-2 font-weight-normal" style="font-size:0.75rem;">Търсене:</span>
                        <input type="text" name="search" value="{{ $search ?? '' }}"
                               class="form-control form-control-sm" style="width:280px;"
                               placeholder="име, град, ЕИК, телефон, имейл...">
                    </div>
                </div>

                {{-- Филтри (няма сложни филтри, само бутон за изчистване) --}}
                <div class="d-flex align-items-center border-left pl-3">
                    <a href="{{ route('admin.company-settings.index') }}" class="btn btn-default btn-sm" title="Изчисти търсенето">
                        <i class="fas fa-times"></i> Изчисти
                    </a>
                </div>
            </div>
        </div>

        {{-- ТАБЛИЦА --}}
        <div class="card-body table-responsive p-0">
            <table class="table table-hover table-striped mb-0">
                <thead>
                    <tr>
                        <th style="width:60px;">ID</th>
                        <th>Фирма</th>
                        <th>Град / Адрес</th>
                        <th>ЕИК / ДДС</th>
                        <th>Контакт</th>
                        <th style="width:100px;">Статус</th>
                        <th style="width:140px;" class="text-center">Действия</th>
                    </tr>
                </thead>
                <tbody id="company-settings-body">
                    @include('admin.company-settings.partials.rows', ['companySettings' => $companySettings])
                </tbody>
            </table>
        </div>

        {{-- PAGINATION / INFO --}}
        @if ($companySettings instanceof \Illuminate\Pagination\LengthAwarePaginator && $companySettings->hasPages())
            <div class="card-footer clearfix" id="pagination-wrapper">
                <div class="float-left">
                    <small class="text-muted">
                        Показване на {{ $companySettings->firstItem() }} до {{ $companySettings->lastItem() }}
                        от общо {{ $companySettings->total() }} резултата
                    </small>
                </div>
                <div class="float-right">
                    {{ $companySettings->appends(request()->except('page'))->links('pagination::bootstrap-4') }}
                </div>
            </div>
        @else
            <div class="card-footer" id="results-info">
                <small class="text-muted">
                    Показване на всички {{ $companySettings->count() }} фирмени профила
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
                    $('#company-settings-body').after(
                        '<div class="card-footer" id="live-search-info">' +
                        '<small class="text-muted">Намерени ' + total + ' фирмени профила (live търсене)</small>' +
                        '</div>'
                    );
                } else {
                    $('#company-settings-body').after(
                        '<div class="card-footer" id="live-search-info">' +
                        '<small class="text-muted">Няма намерени фирмени профили</small>' +
                        '</div>'
                    );
                }
            }

            function performSearch() {
                const search = $('input[name="search"]').val().trim();

                // Ако няма criteria → презареждане на нормалния index
                if (search.length === 0) {
                    removeLiveSearchInfo();
                    window.location = "{{ route('admin.company-settings.index') }}";
                    return;
                }

                $.get("{{ route('admin.company-settings.live-search') }}", {
                        search: search
                    })
                    .done(function(response) {
                        $('#company-settings-body').html(response.html);
                        showResultInfo(response.total);
                    })
                    .fail(function() {
                        $('#company-settings-body').html(
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
                    window.location = "{{ route('admin.company-settings.index') }}";
                }
            });

            $('input[name="search"]').focus();

            $(document).on('submit', 'form[method="DELETE"]', function(e) {
                if (!confirm('Сигурни ли сте, че искате да изтриете този фирмен профил?')) {
                    e.preventDefault();
                    return false;
                }
                return true;
            });
        });
    </script>
@stop