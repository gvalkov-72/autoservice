@extends('adminlte::page')

@section('title', 'Клиенти')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1 class="m-0">
        <i class="fas fa-users text-primary mr-2"></i>Управление на клиенти
    </h1>
    <a href="{{ route('admin.customers.create') }}" class="btn btn-success">
        <i class="fas fa-plus mr-1"></i>Нов клиент
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
                    Списък на клиенти
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
                               placeholder="Търсене по име на клиент...">
                    </div>
                </div>
            </div>
            
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th width="60" class="text-center">ID</th>
                            <th>Клиент</th>
                            <th width="120">Тип</th>
                            <th width="150">Контакти</th>
                            <th width="100">Статус</th>
                            <th width="140" class="text-center">Действия</th>
                        </tr>
                    </thead>
                    <tbody id="customersTableBody">
                        @foreach($customers as $customer)
                        <tr>
                            <td class="text-center">
                                <span class="badge bg-light text-dark">#{{ $customer->id }}</span>
                            </td>
                            <td>
                                <div class="d-flex">
                                    <div class="mr-2">
                                        @if($customer->type == 'customer')
                                        <span class="badge bg-info p-2">
                                            <i class="fas fa-user"></i>
                                        </span>
                                        @elseif($customer->type == 'supplier')
                                        <span class="badge bg-warning p-2">
                                            <i class="fas fa-truck"></i>
                                        </span>
                                        @else
                                        <span class="badge bg-primary p-2">
                                            <i class="fas fa-user-tie"></i>
                                        </span>
                                        @endif
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.customers.show', $customer) }}" 
                                           class="text-dark font-weight-normal customer-name" 
                                           style="font-size: 0.95em; text-decoration: none;">
                                            {{ $customer->name }}
                                        </a>
                                        @if($customer->contact_person)
                                        <div>
                                            <small class="text-muted">
                                                <i class="fas fa-user-circle fa-xs mr-1"></i>{{ $customer->contact_person }}
                                            </small>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($customer->type == 'customer')
                                <span class="badge bg-info">Клиент</span>
                                @elseif($customer->type == 'supplier')
                                <span class="badge bg-warning">Доставчик</span>
                                @else
                                <span class="badge bg-primary">И двата</span>
                                @endif
                            </td>
                            <td>
                                @if($customer->phone)
                                <div class="mb-1">
                                    <i class="fas fa-phone text-success mr-1"></i>
                                    <small>{{ $customer->phone }}</small>
                                </div>
                                @endif
                                @if($customer->email)
                                <div>
                                    <i class="fas fa-envelope text-primary mr-1"></i>
                                    <small>{{ \Illuminate\Support\Str::limit($customer->email, 20) }}</small>
                                </div>
                                @endif
                            </td>
                            <td>
                                @if($customer->is_active)
                                <span class="badge bg-success">
                                    <i class="fas fa-check mr-1"></i>Активен
                                </span>
                                @else
                                <span class="badge bg-secondary">
                                    <i class="fas fa-times mr-1"></i>Неактивен
                                </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="{{ route('admin.customers.show', $customer) }}" 
                                       class="btn btn-sm btn-info" 
                                       title="Преглед">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.customers.edit', $customer) }}" 
                                       class="btn btn-sm btn-primary" 
                                       title="Редактирай">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.customers.destroy', $customer) }}" 
                                          method="POST" 
                                          class="d-inline"
                                          onsubmit="return confirm('Сигурни ли сте, че искате да деактивирате този клиент?')">
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
                
                @if($customers->isEmpty())
                <div class="text-center py-4">
                    <i class="fas fa-users fa-2x text-muted mb-3"></i>
                    <h5 class="text-muted">Няма намерени клиенти</h5>
                    <p class="text-muted mb-0">Създайте първия клиент</p>
                    <a href="{{ route('admin.customers.create') }}" class="btn btn-primary mt-3">
                        <i class="fas fa-plus mr-1"></i>Добави клиент
                    </a>
                </div>
                @endif
            </div>
            
            @if($customers->hasPages())
            <div class="card-footer clearfix">
                <div class="float-right">
                    {{ $customers->links() }}
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
</style>
@endpush

@push('js')
<script>
$(document).ready(function() {
    // Съхраняваме оригиналните редове
    const originalRows = $('#customersTableBody tr').toArray();
    const originalCustomers = [];
    
    // Подготвяме данните
    originalRows.forEach(row => {
        const nameElement = $(row).find('.customer-name');
        originalCustomers.push({
            element: row,
            name: nameElement.text().toLowerCase(),
            originalName: nameElement.text()
        });
    });
    
    // Функция за търсене по "започва с"
    function searchCustomers(searchText) {
        const searchLower = searchText.toLowerCase().trim();
        const tbody = $('#customersTableBody');
        
        // Ако няма текст, показваме всички
        if (searchText.length === 0) {
            tbody.empty();
            originalRows.forEach(row => {
                tbody.append(row);
            });
            return;
        }
        
        // Филтрираме по "започва с"
        const filtered = originalCustomers.filter(customer => {
            return customer.name.startsWith(searchLower);
        });
        
        // Изчистваме и показваме резултатите
        tbody.empty();
        
        if (filtered.length > 0) {
            filtered.forEach(customer => {
                const newRow = $(customer.element).clone();
                
                // Подсветяваме съвпадението
                if (searchLower.length > 0) {
                    const nameElement = newRow.find('.customer-name');
                    const originalText = customer.originalName;
                    const highlightedText = 
                        '<span class="highlight">' + 
                        originalText.substring(0, searchLower.length) + 
                        '</span>' + 
                        originalText.substring(searchLower.length);
                    nameElement.html(highlightedText);
                }
                
                tbody.append(newRow);
            });
        } else {
            tbody.html(`
                <tr>
                    <td colspan="6" class="text-center py-4">
                        <i class="fas fa-search fa-2x text-muted mb-3"></i>
                        <h5 class="text-muted">Няма намерени резултати</h5>
                        <p class="text-muted mb-0">Не бяха намерени клиенти, чиито имена започват с ${searchText}</p>
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
            searchCustomers(searchValue);
        }, 200);
    });
    
    // Enter в полето за търсене
    $('#quickSearch').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            searchCustomers($(this).val());
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