@extends('adminlte::page')

@section('title', 'Превозни средства')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Превозни средства</h1>
        <a href="{{ route('admin.vehicles.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Нов автомобил
        </a>
    </div>
@stop

@section('content')
    <div class="card card-primary card-outline">
        {{-- ЗАГЛАВИЕ И ТЪРСЕНЕ --}}
        <div class="card-header">
            <div class="d-flex align-items-center justify-content-between">
                {{-- Заглавие --}}
                <div class="d-flex align-items-center">
                    <h3 class="card-title mb-0 mr-4">
                        <i class="fas fa-car mr-1"></i>
                        Списък с автомобили
                    </h3>

                    {{-- Live Search --}}
                    <div class="d-flex align-items-center ml-2">
                        <span class="mr-2 font-weight-normal" style="font-size: 0.75rem;">Търсене на автомобил:</span>
                        <input type="text" name="search" value="{{ $search ?? '' }}"
                            class="form-control form-control-sm" style="width: 300px;"
                            placeholder="марка, рег.№, VIN...">
                    </div>
                </div>

                {{-- Филтри --}}
                <div class="d-flex align-items-center border-left pl-3">
                    <div class="dropdown mr-2">
                        <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" 
                                id="filterDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-filter"></i> Филтър
                        </button>
                        <div class="dropdown-menu" aria-labelledby="filterDropdown">
                            <a class="dropdown-item filter-option" href="#" data-type="all">Всички</a>
                            <a class="dropdown-item filter-option" href="#" data-type="active">Активни</a>
                            <a class="dropdown-item filter-option" href="#" data-type="inactive">Неактивни</a>
                        </div>
                    </div>
                    
                    {{-- Филтър по клиент --}}
                    <div class="dropdown mr-2">
                        <button class="btn btn-info btn-sm dropdown-toggle" type="button" 
                                id="customerFilterDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-user"></i> Клиент
                        </button>
                        <div class="dropdown-menu" aria-labelledby="customerFilterDropdown" style="max-height: 300px; overflow-y: auto;">
                            <a class="dropdown-item customer-filter" href="#" data-customer-id="">Всички клиенти</a>
                            @foreach($customers as $customer)
                                <a class="dropdown-item customer-filter" href="#" data-customer-id="{{ $customer->id }}">
                                    {{ $customer->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <a href="{{ route('admin.vehicles.index') }}" class="btn btn-default btn-sm" title="Изчисти филтър">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </div>
        </div>

        {{-- ТАБЛИЦА С АВТОМОБИЛИ --}}
        <div class="card-body table-responsive p-0">
            <table class="table table-hover table-striped mb-0">
                <thead>
                    <tr>
                        <th style="width:70px">ID</th>
                        <th>Автомобил</th>
                        <th>Клиент</th>
                        <th>Рег. номер</th>
                        <th>VIN номер</th>
                        <th>Пробег</th>
                        <th>Статус</th>
                        <th style="width:150px" class="text-center">Действия</th>
                    </tr>
                </thead>

                <tbody id="vehicles-body">
                    @forelse($vehicles as $vehicle)
                        <tr>
                            <td>{{ $vehicle->id }}</td>
                            <td>
                                <strong>{{ $vehicle->vehicle ?: '—' }}</strong>
                                @if($vehicle->notes)
                                    <br><small class="text-muted">{{ Str::limit($vehicle->notes, 50) }}</small>
                                @endif
                            </td>
                            <td>
                                @if($vehicle->customer)
                                    <a href="{{ route('admin.customers.show', $vehicle->customer_id) }}">
                                        {{ $vehicle->customer->name }}
                                    </a>
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                @if($vehicle->plate_number)
                                    <span class="badge badge-dark">{{ $vehicle->plate_number }}</span>
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                @if($vehicle->chassis_number)
                                    <small class="text-muted font-monospace">{{ $vehicle->chassis_number }}</small>
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                @if($vehicle->last_mileage)
                                    {{ number_format($vehicle->last_mileage, 0, ',', ' ') }} км
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $vehicle->is_active ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $vehicle->is_active ? 'Активен' : 'Неактивен' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('admin.vehicles.show', $vehicle->id) }}"
                                        class="btn btn-outline-primary" title="Преглед">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.vehicles.edit', $vehicle->id) }}"
                                        class="btn btn-outline-warning" title="Редактиране">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('admin.work-orders.create', ['vehicle_id' => $vehicle->id]) }}"
                                        class="btn btn-outline-info" title="Нова поръчка">
                                        <i class="fas fa-clipboard-list"></i>
                                    </a>
                                    <form action="{{ route('admin.vehicles.destroy', $vehicle->id) }}" 
                                          method="POST" 
                                          class="d-inline"
                                          onsubmit="return confirm('Сигурни ли сте, че искате да изтриете автомобил {{ $vehicle->plate_number }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Изтриване">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                Няма намерени автомобили
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if ($vehicles->hasPages())
            <div class="card-footer clearfix">
                <div class="float-left">
                    <small class="text-muted">
                        Показване на {{ $vehicles->firstItem() }} до {{ $vehicles->lastItem() }}
                        от общо {{ $vehicles->total() }} автомобила
                    </small>
                </div>
                <div class="float-right">
                    {{ $vehicles->appends(request()->except('page'))->links('pagination::bootstrap-4') }}
                </div>
            </div>
        @else
            <div class="card-footer">
                <small class="text-muted">
                    Показване на всички {{ count($vehicles) }} автомобила
                </small>
            </div>
        @endif
    </div>
@stop

@section('js')
    <script>
        $(function() {
            let timer = null;

            // Функция за live search
            function performSearch() {
                const searchValue = $('input[name="search"]').val().trim();
                
                if (searchValue.length === 0) {
                    window.location = "{{ route('admin.vehicles.index') }}";
                    return;
                }

                $.get("{{ route('admin.vehicles.search') }}", { q: searchValue })
                    .done(function(response) {
                        $('#vehicles-body').html(response.html);
                    })
                    .fail(function() {
                        $('#vehicles-body').html(
                            '<tr><td colspan="8" class="text-center text-danger py-4">Грешка при търсенето</td></tr>'
                        );
                    });
            }

            // Live search за текстово поле
            $('input[name="search"]').on('keyup', function() {
                clearTimeout(timer);
                timer = setTimeout(performSearch, 300);
            });

            // Филтриране по тип
            $('.filter-option').on('click', function(e) {
                e.preventDefault();
                const filterType = $(this).data('type');
                
                let url = "{{ route('admin.vehicles.index') }}";
                if (filterType !== 'all') {
                    url += '?is_active=' + (filterType === 'active' ? '1' : '0');
                }
                
                window.location = url;
            });

            // Филтриране по клиент
            $('.customer-filter').on('click', function(e) {
                e.preventDefault();
                const customerId = $(this).data('customer-id');
                
                let url = "{{ route('admin.vehicles.index') }}";
                if (customerId) {
                    url += '?customer_id=' + customerId;
                }
                
                window.location = url;
            });

            // Фокус в текстовото поле
            $('input[name="search"]').focus();
        });
    </script>
@stop