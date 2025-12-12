@extends('adminlte::page')

@section('title', 'Артикули')

@section('content_header')
    <h1>Артикули</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <a href="{{ route('admin.products.create') }}" class="btn btn-success btn-sm float-right">Добави артикул</a>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Код</th>
                        <th>Наименование</th>
                        <th>Марка</th>
                        <th>Цена (без ДДС)</th>
                        <th>Наличност</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $p)
                        <tr>
                            <td>{{ $p->sku }}</td>
                            <td>{{ $p->name }}</td>
                            <td>{{ $p->brand }}</td>
                            <td>{{ number_format($p->price, 2, '.', '') }} лв.</td>
                            <td>{{ $p->stock_quantity }} {{ $p->unit }}</td>
                            <td>
                                <a href="{{ route('admin.products.barcode', $p) }}" class="btn btn-sm btn-secondary" target="_blank">Баркод</a>
                                <a href="{{ route('admin.products.edit', $p) }}" class="btn btn-sm btn-primary">Редактирай</a>
                                <form action="{{ route('admin.products.destroy', $p) }}" method="POST" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Изтриване?')">Изтрий</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Пагинация с малки стрелки и без текст -->
            <div class="d-flex justify-content-center mt-3">
                {{ $products->onEachSide(1)->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
@stop

@section('css')
    {{-- Махаме надписите и смаляваме стрелките --}}
    <style>
        .page-link {
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
            line-height: 1;
        }
        .page-item span.sr-only {
            display: none;
        }
        .pagination .page-item .page-link .fas {
            font-size: 0.9rem;
        }
    </style>
@stop