@extends('adminlte::page')

@section('title', 'Детайли за данни на автосервиза')

@push('css')
    <link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css" rel="stylesheet"/>
@endpush

@section('content_header')
    <h1>Детайли за данни на автосервиза</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="btn-group" role="group">
                <button onclick="window.print()" class="btn btn-sm btn-secondary">
                    <i class="fas fa-print"></i> Печат
                </button>
                <a href="{{ route('admin.company-settings.export.pdf', $companySetting) }}" class="btn btn-sm btn-danger">
                    <i class="fas fa-file-pdf"></i> PDF
                </a>
                <a href="{{ route('admin.company-settings.export.excel', $companySetting) }}" class="btn btn-sm btn-success">
                    <i class="fas fa-file-excel"></i> Excel
                </a>
                <a href="{{ route('admin.company-settings.export.csv', $companySetting) }}" class="btn btn-sm btn-info">
                    <i class="fas fa-file-csv"></i> CSV
                </a>
            </div>
        </div>

        <div class="card-body">
            <table class="table table-sm table-bordered" id="companyDetailsTable">
                <thead>
                    <tr>
                        <th>Поле</th>
                        <th>Стойност</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td>ID</td><td>{{ $companySetting->id }}</td></tr>
                    <tr><td>Име на фирма</td><td>{{ $companySetting->name }}</td></tr>
                    <tr><td>МОЛ</td><td>{{ $companySetting->contact_person ?? '-' }}</td></tr>
                    <tr><td>Град</td><td>{{ $companySetting->city ?? '-' }}</td></tr>
                    <tr><td>Адрес</td><td>{{ $companySetting->address ?? '-' }}</td></tr>
                    <tr><td>ЕИК / ЕГН / ЗДДС номер</td><td>{{ $companySetting->vat_number ?? '-' }}</td></tr>
                    <tr><td>Телефон</td><td>{{ $companySetting->phone ?? '-' }}</td></tr>
                    <tr><td>Имейл</td><td>{{ $companySetting->email ?? '-' }}</td></tr>
                    <tr><td>IBAN</td><td>{{ $companySetting->iban ?? '-' }}</td></tr>
                    <tr><td>Име на банката</td><td>{{ $companySetting->bank_name ?? '-' }}</td></tr>
                    <tr><td>BIC код</td><td>{{ $companySetting->bic ?? '-' }}</td></tr>
                    <tr><td>Уебсайт</td><td>{{ $companySetting->website ?? '-' }}</td></tr>
                    <tr><td>Статус</td><td>
                        @if($companySetting->is_active)
                            <span class="badge badge-success">Активен</span>
                        @else
                            <span class="badge badge-secondary">Неактивен</span>
                        @endif
                    </td></tr>
                    @if($companySetting->logo_path)
                    <tr><td>Лого</td><td>
                        <img src="{{ Storage::url($companySetting->logo_path) }}" alt="Лого" style="max-height: 100px;">
                    </td></tr>
                    @endif
                    @if($companySetting->invoice_footer)
                    <tr><td>Текст за фактури</td><td>{{ $companySetting->invoice_footer }}</td></tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="card-footer">
            <a href="{{ route('admin.company-settings.edit', $companySetting) }}" class="btn btn-primary">Редактирай</a>
            <a href="{{ route('admin.company-settings.index') }}" class="btn btn-secondary">Назад</a>
        </div>
    </div>
@stop

@section('css')
    <link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css" rel="stylesheet"/>
@stop

@push('js')
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>

    <script>
        $(function () {
            $('#companyDetailsTable').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    { 
                        extend: 'print', 
                        text: '<i class="fas fa-print"></i> Печат',
                        title: 'Данни на автосервиза - {{ $companySetting->name }}',
                        customize: function (win) {
                            $(win.document.body).find('h1').css('text-align', 'center');
                        }
                    },
                    { 
                        extend: 'excelHtml5', 
                        text: '<i class="fas fa-file-excel"></i> Excel',
                        title: 'Данни на автосервиза - {{ $companySetting->name }}'
                    },
                    { 
                        extend: 'pdfHtml5', 
                        text: '<i class="fas fa-file-pdf"></i> PDF',
                        title: 'Данни на автосервиза - {{ $companySetting->name }}'
                    },
                    { 
                        extend: 'csvHtml5', 
                        text: '<i class="fas fa-file-csv"></i> CSV',
                        title: 'Данни на автосервиза - {{ $companySetting->name }}'
                    }
                ],
                language: { 
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/bg.json' 
                },
                paging: false,      // Без странициране (всички редове на една страница)
                searching: false,   // Без търсачка (не е нужна за детайли)
                info: false,        // Без "Showing 1 to X of Y entries"
                ordering: false,    // Без сортиране (редовете са фиксирани)
                autoWidth: false,   // По-добър контрол над ширините
                columns: [
                    { width: "30%" }, // Първа колона - 30% ширина
                    { width: "70%" }  // Втора колона - 70% ширина
                ]
            });
        });
    </script>
@endpush