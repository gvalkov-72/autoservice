@extends('adminlte::page')

@section('title', 'Начало – Автосервиз')

@section('content_header')
    <h1>Дашборд</h1>
@stop

@section('content')
    <div class="row">
        <!-- Останалите картички (Customers, Vehicles, Products, Work Orders) остават същите -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ \App\Models\Customer::count() }}</h3>
                    <p>Клиенти</p>
                </div>
                <div class="icon"><i class="fas fa-users"></i></div>
                <a href="{{ route('admin.customers.index') }}" class="small-box-footer">Повече <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ... -->
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
            <!-- Картичката "Последни поръчки" остава същата -->
            <div class="card card-primary card-outline">
                <!-- ... съдържание ... -->
            </div>
        </div>

        <div class="col-md-6">
            <div class="card card-warning card-outline">
                <div class="card-header border-0">
                    <h3 class="card-title">
                        <i class="fas fa-exclamation-triangle mr-2"></i>Ниски наличности
                    </h3>
                    <div class="card-tools">
                        @php
                            // Използваме scope lowStock от Product модела
                            $lowStockCount = \App\Models\Product::lowStock()->count();
                        @endphp
                        <span class="badge badge-warning">{{ $lowStockCount }} артикула</span>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Код</th>
                                <th>Артикул</th>
                                <th>Наличност</th>
                                <th>Минимум</th>
                                <th style="width: 60px"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                // Вземаме продуктите с ниски наличности, използвайки scope-a
                                $lowStockProducts = \App\Models\Product::lowStock()
                                    ->orderBy('quantity', 'asc')
                                    ->limit(6)
                                    ->get();
                            @endphp
                            @forelse($lowStockProducts as $p)
                                @php
                                    // Изчисляваме колко е критична наличността
                                    if ($p->min_stock > 0) {
                                        $percentage = ($p->quantity / $p->min_stock) * 100;
                                    } else {
                                        $percentage = $p->quantity > 0 ? 100 : 0;
                                    }
                                    $progressColor = $percentage <= 30 ? 'danger' : ($percentage <= 60 ? 'warning' : 'info');
                                @endphp
                                <tr>
                                    <td><code>{{ $p->primary_code }}</code></td>
                                    <td>
                                        <div class="font-weight-bold">{{ \Illuminate\Support\Str::limit($p->name, 25) }}</div>
                                        <div class="text-xs text-muted">{{ $p->location ?? 'Без местоположение' }}</div>
                                    </td>
                                    <td>
                                        <div class="progress progress-xs mb-1">
                                            <div class="progress-bar bg-{{ $progressColor }}"
                                                 style="width: {{ min($percentage, 100) }}%"
                                                 title="{{ round($percentage) }}% от минималната наличност">
                                            </div>
                                        </div>
                                        <span class="badge bg-{{ $progressColor }}">
                                            {{ number_format($p->quantity, ($p->unit_of_measure == 'бр.' ? 0 : 2)) }} {{ $p->unit_of_measure }}
                                        </span>
                                    </td>
                                    <td class="text-muted small">
                                        мин: {{ $p->min_stock }}
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.products.edit', $p->id) }}"
                                           class="btn btn-xs btn-outline-warning"
                                           title="Редактирай наличност">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <i class="fas fa-check-circle fa-2x mb-2 text-success"></i><br>
                                        Всички наличности са в норма
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer text-center">
                    <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-outline-warning mr-2">
                        <i class="fas fa-boxes mr-1"></i>Складова наличност
                    </a>
                    <a href="{{ route('admin.stock.create-purchase') }}" class="btn btn-sm btn-success">
                        <i class="fas fa-cart-plus mr-1"></i>Нова доставка
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Останалата част от кода (Financial Overview и CSS) остава същата -->
    <!-- ... -->

@stop