@extends('adminlte::page')

@section('title', 'Детайли за автомобил')

@push('css')
    <link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css" rel="stylesheet"/>
@endpush

@section('content_header')
    <h1>Детайли за автомобил</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="btn-group" role="group">
                <button onclick="window.print()" class="btn btn-sm btn-secondary">
                    <i class="fas fa-print"></i> Печат
                </button>
                <a href="{{ route('admin.vehicles.export.pdf', $vehicle) }}" class="btn btn-sm btn-danger">
                    <i class="fas fa-file-pdf"></i> PDF
                </a>
                <a href="{{ route('admin.vehicles.export.excel', $vehicle) }}" class="btn btn-sm btn-success">
                    <i class="fas fa-file-excel"></i> Excel
                </a>
                <a href="{{ route('admin.vehicles.export.csv', $vehicle) }}" class="btn btn-sm btn-info">
                    <i class="fas fa-file-csv"></i> CSV
                </a>
            </div>
        </div>

        <div class="card-body">
            <table class="table table-sm table-bordered">
                <tr><th>ID</th><td>{{ $vehicle->id }}</td></tr>
                <tr><th>Собственик</th><td>{{ $vehicle->customer->name }}</td></tr>
                <tr><th>Рег. №</th><td>{{ $vehicle->plate }}</td></tr>
                <tr><th>VIN</th><td>{{ $vehicle->vin }}</td></tr>
                <tr><th>Марка</th><td>{{ $vehicle->make }}</td></tr>
                <tr><th>Модел</th><td>{{ $vehicle->model }}</td></tr>
                <tr><th>Година</th><td>{{ $vehicle->year }}</td></tr>
                <tr><th>Пробег</th><td>{{ $vehicle->mileage }} км</td></tr>
                <tr><th>ДК №</th><td>{{ $vehicle->dk_no ?? '-' }}</td></tr>
                <tr><th>Бележки</th><td>{{ $vehicle->notes ?? '-' }}</td></tr>
            </table>

            <h4 class="mt-4">История на поръчки</h4>
            <table class="table table-sm table-striped" id="ordersTable">
                <thead>
                    <tr>
                        <th>№</th>
                        <th>Статус</th>
                        <th>Приета на</th>
                        <th>Общо</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vehicle->workOrders as $wo)
                        <tr>
                            <td>{{ $wo->number }}</td>
                            <td>{{ $wo->status }}</td>
                            {{-- правилно форматиране на дата --}}
                            <td>{{ optional($wo->received_at)->format('d.m.Y') ?? '-' }}</td>
                            <td>{{ number_format($wo->total, 2) }} лв.</td>
                            <td>
                                <a href="{{ route('admin.work-orders.show', $wo) }}" class="btn btn-xs btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer">
            <a href="{{ route('admin.vehicles.edit', $vehicle) }}" class="btn btn-primary">Редактирай</a>
            <a href="{{ route('admin.vehicles.index') }}" class="btn btn-secondary">Назад</a>
        </div>
    </div>
@stop

@push('js')
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

    <script>
        $(function () {
            $('#ordersTable').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    { extend: 'print', text: 'Печат' },
                    { extend: 'excelHtml5', text: 'Excel' },
                    { extend: 'csvHtml5', text: 'CSV' },
                    { extend: 'pdfHtml5', text: 'PDF' }
                ],
                language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/bg.json' }
            });
        });
    </script>
@endpush