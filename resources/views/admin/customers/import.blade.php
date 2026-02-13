@extends('adminlte::page')

@section('title', 'Импорт на клиенти')

@section('content_header')
    <h1>Импорт на клиенти</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Начало</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.customers.index') }}">Клиенти</a></li>
        <li class="breadcrumb-item active">Импорт</li>
    </ol>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Импортиране на клиенти от файл</h3>
                </div>
                
                <form action="{{ route('admin.customers.import.process') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="card-body">
                        <!-- Инструкции -->
                        <div class="alert alert-info">
                            <h5><i class="fas fa-info-circle"></i> Инструкции за импорт:</h5>
                            <ol>
                                <li>Изтеглете шаблона от бутона по-долу</li>
                                <li>Попълнете данните във файла, като спазвате формата</li>
                                <li>Задължително поле: <strong>Име/Фирма</strong></li>
                                <li>Датата трябва да е във формат: <strong>YYYY-MM-DD</strong></li>
                                <li>Булевите полета (да/не) се попълват с: <strong>1 (за да) или 0 (за не)</strong></li>
                                <li>Запазете файла като CSV или Excel</li>
                                <li>Качете файла тук</li>
                            </ol>
                        </div>
                        
                        <!-- Резултати от предишен импорт -->
                        @if(session('import_results'))
                            @php $results = session('import_results'); @endphp
                            <div class="alert alert-{{ empty($results['errors']) ? 'success' : 'warning' }}">
                                <h5><i class="fas fa-chart-bar"></i> Резултати от импорта:</h5>
                                <ul class="mb-0">
                                    <li>Нови клиенти: <strong>{{ $results['imported'] ?? 0 }}</strong></li>
                                    <li>Обновени клиенти: <strong>{{ $results['updated'] ?? 0 }}</strong></li>
                                    @if(!empty($results['errors']))
                                        <li>Грешки: <strong>{{ count($results['errors']) }}</strong></li>
                                    @endif
                                </ul>
                                
                                @if(!empty($results['errors']))
                                    <div class="mt-3">
                                        <button type="button" class="btn btn-sm btn-warning" data-toggle="collapse" data-target="#errorsCollapse">
                                            Покажи грешките
                                        </button>
                                        <div class="collapse mt-2" id="errorsCollapse">
                                            <div class="card card-body">
                                                @foreach($results['errors'] as $error)
                                                    <div class="text-danger small">{{ $error }}</div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif
                        
                        <!-- Форма за качване -->
                        <div class="form-group">
                            <label for="import_file">Изберете файл за импорт</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input @error('import_file') is-invalid @enderror" 
                                       id="import_file" name="import_file" accept=".csv,.xlsx,.xls" required>
                                <label class="custom-file-label" for="import_file">Изберете CSV или Excel файл</label>
                                @error('import_file')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <small class="text-muted">
                                Максимален размер: 5MB. Поддържани формати: .csv, .xlsx, .xls
                            </small>
                        </div>
                        
                        <!-- Настройки -->
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" name="update_existing" id="update_existing" value="1">
                                <label class="custom-control-label" for="update_existing">
                                    Обнови съществуващи записи
                                </label>
                                <small class="form-text text-muted">
                                    Ако е отметнато, клиенти със същия клиентски номер или булстат ще бъдат обновени
                                </small>
                            </div>
                        </div>
                        
                        <!-- Шаблон -->
                        <div class="form-group">
                            <div class="d-flex justify-content-between align-items-center">
                                <span>Изтеглете шаблон за попълване:</span>
                                <a href="{{ route('admin.customers.import.template') }}" class="btn btn-success btn-sm">
                                    <i class="fas fa-download mr-2"></i>Изтегли шаблон
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Карта на полетата -->
                    <div class="card-header">
                        <h4 class="card-title mb-0">Описание на полетата</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead>
                                    <tr>
                                        <th width="30%">Поле</th>
                                        <th width="30%">Тип</th>
                                        <th>Описание</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><code>customer_number</code></td>
                                        <td>Текст</td>
                                        <td>Клиентски номер (незадължително)</td>
                                    </tr>
                                    <tr>
                                        <td><code>name</code></td>
                                        <td>Текст</td>
                                        <td><strong>Име/Фирма (задължително)</strong></td>
                                    </tr>
                                    <tr>
                                        <td><code>email</code></td>
                                        <td>Имейл</td>
                                        <td>Имейл адрес</td>
                                    </tr>
                                    <tr>
                                        <td><code>phone</code></td>
                                        <td>Текст</td>
                                        <td>Телефон</td>
                                    </tr>
                                    <tr>
                                        <td><code>bulstat</code></td>
                                        <td>Текст</td>
                                        <td>Булстат/ЕИК</td>
                                    </tr>
                                    <tr>
                                        <td><code>mol</code></td>
                                        <td>Текст</td>
                                        <td>МОЛ (Молително отговорно лице)</td>
                                    </tr>
                                    <tr>
                                        <td><code>address</code></td>
                                        <td>Текст</td>
                                        <td>Адрес</td>
                                    </tr>
                                    <tr>
                                        <td><code>is_customer</code></td>
                                        <td>Булево (1/0)</td>
                                        <td>Клиент (1=да, 0=не)</td>
                                    </tr>
                                    <tr>
                                        <td><code>is_supplier</code></td>
                                        <td>Булево (1/0)</td>
                                        <td>Доставчик (1=да, 0=не)</td>
                                    </tr>
                                    <tr>
                                        <td><code>is_active</code></td>
                                        <td>Булево (1/0)</td>
                                        <td>Активен (1=да, 0=не)</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Бутони -->
                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.customers.index') }}" class="btn btn-default">
                                <i class="fas fa-arrow-left mr-2"></i>Назад
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-upload mr-2"></i>Импортирай
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('css')
<style>
    .custom-file-label::after {
        content: "Прегледай";
    }
    code {
        background-color: #f8f9fa;
        padding: 2px 5px;
        border-radius: 3px;
        border: 1px solid #dee2e6;
    }
</style>
@stop

@section('js')
<script>
$(document).ready(function() {
    // Показване на името на файла при избор
    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });
    
    // Валидация на файла
    $('form').submit(function(e) {
        const fileInput = $('#import_file')[0];
        if (!fileInput.files.length) {
            e.preventDefault();
            toastr.error('Моля изберете файл за импорт');
            return false;
        }
        
        const file = fileInput.files[0];
        const validExtensions = ['.csv', '.xlsx', '.xls'];
        const fileExtension = file.name.substring(file.name.lastIndexOf('.')).toLowerCase();
        
        if (!validExtensions.includes(fileExtension)) {
            e.preventDefault();
            toastr.error('Невалиден файлов формат. Моля изберете CSV или Excel файл.');
            return false;
        }
        
        if (file.size > 5 * 1024 * 1024) { // 5MB
            e.preventDefault();
            toastr.error('Файлът е твърде голям. Максималният размер е 5MB.');
            return false;
        }
        
        return true;
    });
});
</script>
@stop