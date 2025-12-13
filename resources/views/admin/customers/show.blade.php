@extends('adminlte::page')

@section('title', 'Детайли за клиент')

@push('css')
    <link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css" rel="stylesheet"/>
@endpush

@section('content_header')
    <h1>Детайли за клиент</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="btn-group" role="group">
                <button onclick="window.print()" class="btn btn-sm btn-secondary">
                    <i class="fas fa-print"></i> Печат
                </button>
                <a href="{{ route('admin.customers.export.pdf', $customer) }}" class="btn btn-sm btn-danger">
                    <i class="fas fa-file-pdf"></i> PDF
                </a>
                <a href="{{ route('admin.customers.export.excel', $customer) }}" class="btn btn-sm btn-success">
                    <i class="fas fa-file-excel"></i> Excel
                </a>
                <a href="{{ route('admin.customers.export.csv', $customer) }}" class="btn btn-sm btn-info">
                    <i class="fas fa-file-csv"></i> CSV
                </a>
            </div>
        </div>

        <div class="card-body">
            {{-- Основни данни --}}
            <table class="table table-sm table-bordered">
                <tr><th>ID</th><td>{{ $customer->id }}</td></tr>
                <tr><th>Име / Фирма</th><td>{{ $customer->name }}</td></tr>
                <tr><th>Тип</th><td>{{ $customer->type == 'company' ? 'Фирма' : 'Физ. лице' }}</td></tr>
                <tr><th>ДДС №</th><td>{{ $customer->vat_number ?? '-' }}</td></tr>
                <tr><th>Контактно лице</th><td>{{ $customer->contact_person ?? '-' }}</td></tr>
                <tr><th>Телефон</th><td>{{ $customer->phone ?? '-' }}</td></tr>
                <tr><th>Имейл</th><td>{{ $customer->email ?? '-' }}</td></tr>
                <tr><th>Адрес</th><td>{{ $customer->address ?? '-' }}</td></tr>
                <tr><th>Град</th><td>{{ $customer->city ?? '-' }}</td></tr>
                <tr><th>Бележки</th><td>{{ $customer->notes ?? '-' }}</td></tr>
            </table>

            {{-- Автомобили на клиента --}}
            <h4 class="mt-4">Автомобили</h4>
            <table class="table table-sm table-striped" id="vehiclesTable">
                <thead>
                    <tr>
                        <th>Рег. №</th>
                        <th>VIN</th>
                        <th>Марка/Модел</th>
                        <th>Година</th>
                        <th>Пробег</th>
                        <th></th> {{-- Подробности бутон --}}
                    </tr>
                </thead>
                <tbody>
                    @forelse($customer->vehicles as $v)
                        <tr>
                            <td>{{ $v->plate }}</td>
                            <td>{{ $v->vin }}</td>
                            <td>{{ $v->make }} / {{ $v->model }}</td>
                            <td>{{ $v->year }}</td>
                            <td>{{ $v->mileage }} км</td>
                            <td>
                                <a href="{{ route('admin.vehicles.show', $v) }}" class="btn btn-xs btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td></tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Поръчки на клиента --}}
            <h4 class="mt-4">Поръчки</h4>
            <table class="table table-sm table-striped" id="ordersTable">
                <thead>
                    <tr>
                        <th>№</th>
                        <th>Статус</th>
                        <th>Приета на</th>
                        <th>Общо</th>
                        <th></th> {{-- Подробности бутон --}}
                    </tr>
                </thead>
                <tbody>
                    @forelse($customer->workOrders as $wo)
                        <tr>
                            <td>{{ $wo->number }}</td>
                            <td>{{ $wo->status }}</td>
                            {{-- КАСТВАМЕ КЪМ Carbon преди format() --}}
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
            <a href="{{ route('admin.customers.edit', $customer) }}" class="btn btn-primary">Редактирай</a>
            <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary">Назад</a>
        </div>
    </div>
@stop

@section('css')
    <link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css" rel="stylesheet"/>
@stop

@push('js')
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

    <script>
        $(function () {
            $('#vehiclesTable, #ordersTable').DataTable({
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