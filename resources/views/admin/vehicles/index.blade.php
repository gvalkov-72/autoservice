@extends('adminlte::page')

@section('title', 'Превозни средства')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1 class="m-0">
        <i class="fas fa-car text-primary mr-2"></i>Управление на превозни средства
    </h1>
    <a href="{{ route('admin.vehicles.create') }}" class="btn btn-success">
        <i class="fas fa-plus mr-1"></i>Нов превозно средство
    </a>
</div>
@stop

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-primary card-outline">
            <div class="card-header border-bottom-0 pb-0">
                <h3 class="card-title">
                    <i class="fas fa-list mr-1"></i>
                    Списък на превозни средства
                </h3>
                <div class="card-tools" style="position: absolute; left: 50%; transform: translateX(-50%);">
                    <div class="input-group input-group-sm" style="width: 300px;">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                        </div>
                        <input type="text" 
                               id="quickSearch" 
                               class="form-control" 
                               placeholder="Търсене по регистрационен номер...">
                    </div>
                </div>
            </div>

            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th width="60" class="text-center">ID</th>
                            <th>Превозно средство</th>
                            <th width="120">Марка/Модел</th>
                            <th width="150">Клиент</th>
                            <th width="100">Статус</th>
                            <th width="140" class="text-center">Действия</th>
                        </tr>
                    </thead>
                    <tbody id="vehiclesTableBody">
                        @foreach($vehicles as $vehicle)
                        <tr>
                            <td class="text-center">
                                <span class="badge bg-light text-dark">#{{ $vehicle->id }}</span>
                            </td>
                            <td>
                                <div class="d-flex">
                                    <div class="mr-2">
                                        @if($vehicle->is_active)
                                        <span class="badge bg-success p-2">
                                            <i class="fas fa-car"></i>
                                        </span>
                                        @else
                                        <span class="badge bg-secondary p-2">
                                            <i class="fas fa-car"></i>
                                        </span>
                                        @endif
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.vehicles.show', $vehicle) }}" 
                                           class="text-dark font-weight-normal vehicle-name" 
                                           style="font-size: 0.95em; text-decoration: none;">
                                            <strong>{{ $vehicle->plate }}</strong>
                                        </a>
                                        @if($vehicle->vin)
                                        <div>
                                            <small class="text-muted">
                                                <i class="fas fa-barcode fa-xs mr-1"></i>VIN: {{ $vehicle->vin }}
                                            </small>
                                        </div>
                                        @endif
                                        @if($vehicle->old_system_id)
                                        <div>
                                            <small class="text-muted">
                                                <i class="fas fa-database fa-xs mr-1"></i>Старо ID: {{ $vehicle->old_system_id }}
                                            </small>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <span class="badge bg-info">{{ $vehicle->make }}</span>
                                    @if($vehicle->model)
                                    <span class="badge bg-warning">{{ $vehicle->model }}</span>
                                    @endif
                                </div>
                                @if($vehicle->year)
                                <small class="text-muted">
                                    <i class="fas fa-calendar-alt fa-xs mr-1"></i>{{ $vehicle->year }} г.
                                </small>
                                @endif
                            </td>
                            <td>
                                @if($vehicle->customer)
                                <div class="mb-1">
                                    <i class="fas fa-user text-success mr-1"></i>
                                    <small>{{ $vehicle->customer->name }}</small>
                                </div>
                                @if($vehicle->customer->phone)
                                <div>
                                    <i class="fas fa-phone text-primary mr-1"></i>
                                    <small>{{ $vehicle->customer->phone }}</small>
                                </div>
                                @endif
                                @else
                                <span class="text-muted">Без клиент</span>
                                @endif
                            </td>
                            <td>
                                @if($vehicle->is_active)
                                <span class="badge bg-success">
                                    <i class="fas fa-check mr-1"></i>Активен
                                </span>
                                @else
                                <span class="badge bg-secondary">
                                    <i class="fas fa-times mr-1"></i>Неактивен
                                </span>
                                @endif
                                @if($vehicle->mileage)
                                <br>
                                <small class="text-muted mt-1">
                                    <i class="fas fa-tachometer-alt fa-xs mr-1"></i>{{ number_format($vehicle->mileage, 0) }} км
                                </small>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="{{ route('admin.vehicles.show', $vehicle) }}" 
                                       class="btn btn-sm btn-info" 
                                       title="Преглед">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.vehicles.edit', $vehicle) }}" 
                                       class="btn btn-sm btn-primary" 
                                       title="Редактирай">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.vehicles.destroy', $vehicle) }}" 
                                          method="POST" 
                                          class="d-inline"
                                          onsubmit="return confirm('Сигурни ли сте, че искате да деактивирате това превозно средство?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-sm btn-danger" 
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

                @if($vehicles->isEmpty())
                <div class="text-center py-4">
                    <i class="fas fa-car fa-2x text-muted mb-3"></i>
                    <h5 class="text-muted">Няма намерени превозни средства</h5>
                    <p class="text-muted mb-0">Създайте първото превозно средство</p>
                    <a href="{{ route('admin.vehicles.create') }}" class="btn btn-primary mt-3">
                        <i class="fas fa-plus mr-1"></i>Добави превозно средство
                    </a>
                </div>
                @endif
            </div>

            @if($vehicles->hasPages())
            <div class="card-footer clearfix">
                <div class="float-right">
                    {{ $vehicles->links() }}
                </div>
            </div>
            @endif
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
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
}

@media (max-width: 768px) {
    .card-header .card-tools {
        position: relative;
        left: 0;
        transform: none;
        margin-top: 10px;
        width: 100%;
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

/* Стилове за пагинация - оправяне на огромните стрелки */
.pagination {
    margin: 0;
    padding: 0;
}

.pagination .page-link {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
    line-height: 1.5;
    border-radius: 0.2rem;
    min-width: 32px;
    text-align: center;
}

.pagination .page-item {
    margin: 0 2px;
}

.pagination .page-item:first-child .page-link,
.pagination .page-item:last-child .page-link {
    border-radius: 0.2rem;
}

/* По-малки иконки в пагинацията */
.pagination .page-link i {
    font-size: 0.75rem;
}

/* Стилове за адаптивна пагинация на малки екрани */
@media (max-width: 576px) {
    .pagination {
        flex-wrap: wrap;
        justify-content: center;
    }
    
    .pagination .page-item {
        margin: 2px;
    }
    
    .pagination .page-link {
        padding: 0.2rem 0.4rem;
        font-size: 0.8rem;
        min-width: 28px;
    }
    
    /* Скриване на текст и показване само на иконки за предишна/следваща */
    .pagination .page-item:first-child .page-link span:not(.sr-only),
    .pagination .page-item:last-child .page-link span:not(.sr-only) {
        display: none;
    }
    
    .pagination .page-item:first-child .page-link i,
    .pagination .page-item:last-child .page-link i {
        margin: 0;
        font-size: 0.8rem;
    }
}
</style>
@endpush

@push('js')
<script>
$(document).ready(function() {
    // Съхраняваме оригиналните редове
    const originalRows = $('#vehiclesTableBody tr').toArray();
    const originalVehicles = [];

    // Подготвяме данните
    originalRows.forEach(row => {
        const plateElement = $(row).find('.vehicle-name strong');
        originalVehicles.push({
            element: row,
            plate: plateElement.text().toLowerCase(),
            originalPlate: plateElement.text()
        });
    });

    // Функция за търсене по "започва с"
    function searchVehicles(searchText) {
        const searchLower = searchText.toLowerCase().trim();
        const tbody = $('#vehiclesTableBody');

        // Ако няма текст, показваме всички
        if (searchText.length === 0) {
            tbody.empty();
            originalRows.forEach(row => {
                tbody.append(row);
            });
            return;
        }

        // Филтрираме по "започва с"
        const filtered = originalVehicles.filter(vehicle => {
            return vehicle.plate.startsWith(searchLower);
        });

        // Изчистваме и показваме резултатите
        tbody.empty();

        if (filtered.length > 0) {
            filtered.forEach(vehicle => {
                const newRow = $(vehicle.element).clone();

                // Подсветяваме съвпадението
                if (searchLower.length > 0) {
                    const plateElement = newRow.find('.vehicle-name strong');
                    const originalText = vehicle.originalPlate;
                    const highlightedText = 
                        '<span class="highlight">' + 
                        originalText.substring(0, searchLower.length) + 
                        '</span>' + 
                        originalText.substring(searchLower.length);
                    plateElement.html(highlightedText);
                }

                tbody.append(newRow);
            });
        } else {
            tbody.html(`
                <tr>
                    <td colspan="6" class="text-center py-4">
                        <i class="fas fa-search fa-2x text-muted mb-3"></i>
                        <h5 class="text-muted">Няма намерени резултати</h5>
                        <p class="text-muted mb-0">Не бяха намерени превозни средства, чиито регистрационни номера започват с ${searchText}</p>
                    </td>
                </tr>
            `);
        }
    }

    // Слушател за търсене
    $('#quickSearch').on('input', function() {
        const searchValue = $(this).val();

        // Използваме debounce за по-добро представяне
        clearTimeout(window.searchTimer);
        window.searchTimer = setTimeout(() => {
            searchVehicles(searchValue);
        }, 200);
    });

    // Enter в полето за търсене
    $('#quickSearch').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            searchVehicles($(this).val());
        }
    });

    // Ctrl+F за фокус в търсачката
    $(document).on('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
            e.preventDefault();
            $('#quickSearch').focus();
        }
    });
});
</script>
@endpush