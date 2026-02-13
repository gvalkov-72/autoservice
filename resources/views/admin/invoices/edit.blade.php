@extends('adminlte::page')

@section('title', 'Редактиране на фактура №' . $invoice->old_id)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Редактиране на фактура №{{ $invoice->old_id }}</h1>
        <div>
            <a href="{{ route('admin.invoices.print', $invoice->id) }}" class="btn btn-default btn-sm" target="_blank">
                <i class="fas fa-print"></i> Печат
            </a>
            <a href="{{ route('admin.invoices.pdf', $invoice->id) }}" class="btn btn-danger btn-sm" target="_blank">
                <i class="fas fa-file-pdf"></i> PDF
            </a>
            <a href="{{ route('admin.invoices.index') }}" class="btn btn-default btn-sm ml-2">
                <i class="fas fa-arrow-left"></i> Назад
            </a>
        </div>
    </div>
@stop

@section('content')
    <form action="{{ route('admin.invoices.update', $invoice->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Номер на фактура (само за четене) --}}
        <div class="row">
            <div class="col-md-3">
                <x-adminlte-input name="old_id" label="Номер на фактура"
                    value="{{ html_entity_decode(old('old_id', $invoice->old_id), ENT_QUOTES, 'UTF-8') }}"
                    placeholder="—" readonly />
            </div>
        </div>

        {{-- ОСНОВНА ИНФОРМАЦИЯ --}}
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">Основна информация</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    {{-- Клиент (търсене по old_id) --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="customer_select">Клиент <span class="text-danger">*</span></label>
                            <select name="customer_old_id" id="customer_select" class="form-control form-control-sm select2"
                                    style="width: 100%;" required>
                                @if($invoice->customer)
                                    <option value="{{ $invoice->customer->old_id }}" selected>
                                        {{ $invoice->customer->name }} ({{ $invoice->customer->customer_number ?? 'без №' }}) - {{ $invoice->customer->phone ?? '' }}
                                    </option>
                                @endif
                            </select>
                            @error('customer_old_id')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- Тип фактура (Doctype) --}}
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="invoice_type">Тип фактура <span class="text-danger">*</span></label>
                            <select name="invoice_type" id="invoice_type" class="form-control form-control-sm" required>
                                <option value="">-- Избери --</option>
                                @foreach($doctypes as $doctype)
                                    <option value="{{ $doctype->type }}" 
                                        {{ old('invoice_type', $invoice->invoice_type) == $doctype->type ? 'selected' : '' }}>
                                        {{ $doctype->name }} ({{ $doctype->short ?? $doctype->type }})
                                    </option>
                                @endforeach
                            </select>
                            @error('invoice_type')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    {{-- Дата фактура --}}
                    <div class="col-md-3">
                        <x-adminlte-input name="invoice_date" label="Дата фактура" type="date"
                            value="{{ old('invoice_date', optional($invoice->invoice_date)->format('Y-m-d')) }}" />
                    </div>

                    {{-- Дата на падеж --}}
                    <div class="col-md-3">
                        <x-adminlte-input name="date_due" label="Падежна дата" type="date"
                            value="{{ old('date_due', optional($invoice->date_due)->format('Y-m-d')) }}" />
                    </div>

                    {{-- Дата на получаване --}}
                    <div class="col-md-3">
                        <x-adminlte-input name="invoice_received_date" label="Дата на получаване" type="date"
                            value="{{ old('invoice_received_date', optional($invoice->invoice_received_date)->format('Y-m-d')) }}" />
                    </div>
                </div>

                <div class="row">
                    {{-- Получил --}}
                    <div class="col-md-4">
                        <x-adminlte-input name="invoice_received_person" label="Получил"
                            value="{{ html_entity_decode(old('invoice_received_person', $invoice->invoice_received_person), ENT_QUOTES, 'UTF-8') }}"
                            placeholder="Име на получилия" />
                    </div>

                    {{-- Създадена от --}}
                    <div class="col-md-4">
                        <x-adminlte-input name="invoice_created_by" label="Създадена от"
                            value="{{ html_entity_decode(old('invoice_created_by', $invoice->invoice_created_by), ENT_QUOTES, 'UTF-8') }}"
                            placeholder="..." />
                    </div>
                </div>

                <div class="row">
                    {{-- Отговорник получаване --}}
                    <div class="col-md-4">
                        <x-adminlte-input name="invoice_rec_responsible" label="Отговорник (получаване)"
                            value="{{ html_entity_decode(old('invoice_rec_responsible', $invoice->invoice_rec_responsible), ENT_QUOTES, 'UTF-8') }}"
                            placeholder="..." />
                    </div>

                    {{-- Отговорник създаване --}}
                    <div class="col-md-4">
                        <x-adminlte-input name="invoice_cre_responsible" label="Отговорник (създаване)"
                            value="{{ html_entity_decode(old('invoice_cre_responsible', $invoice->invoice_cre_responsible), ENT_QUOTES, 'UTF-8') }}"
                            placeholder="..." />
                    </div>
                </div>
            </div>
        </div>

        {{-- АРТИКУЛИ И УСЛУГИ --}}
        <div class="card card-info card-outline" id="items-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-boxes"></i> Артикули и услуги
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-sm btn-success" id="add-item-row">
                        <i class="fas fa-plus"></i> Добави ред
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm table-bordered mb-0" id="items-table">
                    <thead>
                        <tr>
                            <th style="width:5%;">№</th>
                            <th style="width:15%;">Код</th>
                            <th style="width:30%;">Наименование <span class="text-danger">*</span></th>
                            <th style="width:8%;">Мярка</th>
                            <th style="width:10%;">К-во <span class="text-danger">*</span></th>
                            <th style="width:12%;">Ед. цена (€) <span class="text-danger">*</span></th>
                            <th style="width:12%;">Сума (€)</th>
                            <th style="width:8%;"></th>
                        </tr>
                    </thead>
                    <tbody id="items-container">
                        @foreach($invoice->items as $index => $item)
                            <tr class="item-row">
                                <td class="text-center row-number">{{ $index + 1 }}</td>
                                <td>
                                    <input type="text" name="items[][item_code]" class="form-control form-control-sm" 
                                           placeholder="Код" value="{{ html_entity_decode($item->item_code, ENT_QUOTES, 'UTF-8') }}">
                                    <input type="hidden" name="items[][id]" value="{{ $item->id }}">
                                </td>
                                <td>
                                    <input type="text" name="items[][item_name]" class="form-control form-control-sm" 
                                           placeholder="Наименование" required 
                                           value="{{ html_entity_decode($item->item_name, ENT_QUOTES, 'UTF-8') }}">
                                </td>
                                <td>
                                    <input type="text" name="items[][item_measure]" class="form-control form-control-sm" 
                                           placeholder="бр." value="{{ html_entity_decode($item->item_measure, ENT_QUOTES, 'UTF-8') }}">
                                </td>
                                <td>
                                    <input type="number" step="0.01" name="items[][quantity]" class="form-control form-control-sm item-quantity" 
                                           placeholder="К-во" value="{{ $item->quantity }}" required>
                                </td>
                                <td>
                                    <input type="number" step="0.01" name="items[][price_each]" class="form-control form-control-sm item-price" 
                                           placeholder="Цена" value="{{ $item->price_each }}" required>
                                </td>
                                <td class="text-right align-middle">
                                    <span class="item-total">{{ number_format($item->row_total, 2, '.', '') }}</span>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-danger remove-item-row" title="Премахни">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="6" class="text-right">Обща сума:</th>
                            <th class="text-right" id="total-amount">{{ number_format($invoice->total, 2, '.', '') }}</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="card-footer">
                <small class="text-muted">Въведете артикулите/услугите по фактурата. Сумата се изчислява автоматично.</small>
            </div>
        </div>

        {{-- ДОПЪЛНИТЕЛНИ НАСТРОЙКИ --}}
        <div class="card card-secondary card-outline">
            <div class="card-header">
                <h3 class="card-title">Допълнителна информация</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <x-adminlte-textarea name="note" label="Бележки" rows="2"
                            placeholder="Вътрешни бележки...">{{ old('note', $invoice->note) }}</x-adminlte-textarea>
                    </div>
                    <div class="col-md-6">
                        <x-adminlte-textarea name="zeroexplain" label="Обяснение за нулева ставка" rows="2"
                            placeholder="Ако фактурата е с нулево ДДС...">{{ old('zeroexplain', $invoice->zeroexplain) }}</x-adminlte-textarea>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <x-adminlte-input name="tipsdelka" label="Tipsdelka" type="number"
                            value="{{ old('tipsdelka', $invoice->tipsdelka) }}" placeholder="0" />
                    </div>
                    <div class="col-md-3">
                        <x-adminlte-input name="sale_type" label="Тип продажба" type="number"
                            value="{{ old('sale_type', $invoice->sale_type) }}" placeholder="0" />
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Метод на плащане</label>
                            <select name="pay_method" class="form-control form-control-sm">
                                @foreach($paymentMethods as $key => $method)
                                    <option value="{{ $key }}" {{ old('pay_method', $invoice->pay_method) == $key ? 'selected' : '' }}>
                                        {{ $method }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <x-adminlte-input-switch name="payment_cash" label="Плащане в брой"
                            :checked="old('payment_cash', $invoice->payment_cash)" />
                    </div>
                    <div class="col-md-3">
                        <x-adminlte-input-switch name="paid" label="Платена"
                            :checked="old('paid', $invoice->paid)" />
                    </div>
                    <div class="col-md-3">
                        <x-adminlte-input-switch name="printed" label="Отпечатана"
                            :checked="old('printed', $invoice->printed)" />
                    </div>
                    <div class="col-md-3">
                        <x-adminlte-input-switch name="is_void" label="Анулирана"
                            :checked="old('is_void', $invoice->is_void)" />
                    </div>
                </div>
            </div>
        </div>

        {{-- БУТОНИ --}}
        <div class="row mb-4">
            <div class="col-12 text-right">
                <x-adminlte-button type="submit" label="Запази промените" theme="success" icon="fas fa-save" />
                <a href="{{ route('admin.invoices.index') }}" class="btn btn-default ml-2">Отказ</a>
            </div>
        </div>
    </form>
@stop

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container--default .select2-selection--single {
            height: calc(1.8125rem + 2px);
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            line-height: 1.5;
            border-radius: 0.2rem;
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(function() {
            // ========== SELECT2 ЗА ТЪРСЕНЕ НА КЛИЕНТ ==========
            $('#customer_select').select2({
                ajax: {
                    url: "{{ route('admin.customers.search') }}",
                    dataType: 'json',
                    delay: 300,
                    data: function(params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.map(function(customer) {
                                return {
                                    id: customer.old_id,
                                    text: customer.name + (customer.customer_number ? ' (' + customer.customer_number + ')' : '') + ' - ' + (customer.phone || ''),
                                    customer: customer
                                };
                            })
                        };
                    },
                    cache: true
                },
                placeholder: '-- Търсене на клиент --',
                minimumInputLength: 2,
                allowClear: true
            });

            // Ако има текущ клиент, задаваме опцията ръчно
            @if($invoice->customer)
                var currentCustomer = {
                    id: "{{ $invoice->customer->old_id }}",
                    text: "{{ $invoice->customer->name }}" + 
                          ({{ $invoice->customer->customer_number ? ' ("' . $invoice->customer->customer_number . '")' : '' }}) + 
                          " - {{ $invoice->customer->phone ?? '' }}"
                };
                var option = new Option(currentCustomer.text, currentCustomer.id, true, true);
                $('#customer_select').append(option).trigger('change');
            @endif

            // ========== ДИНАМИЧНИ РЕДОВЕ ЗА АРТИКУЛИ ==========
            function calculateRowTotal(row) {
                const qty = parseFloat(row.find('.item-quantity').val()) || 0;
                const price = parseFloat(row.find('.item-price').val()) || 0;
                const total = qty * price;
                row.find('.item-total').text(total.toFixed(2));
                return total;
            }

            function calculateGrandTotal() {
                let total = 0;
                $('.item-total').each(function() {
                    total += parseFloat($(this).text()) || 0;
                });
                $('#total-amount').text(total.toFixed(2));
            }

            const itemRowTemplate = `
                <tr class="item-row">
                    <td class="text-center row-number">${$('#items-container tr').length + 1}</td>
                    <td>
                        <input type="text" name="items[][item_code]" class="form-control form-control-sm" placeholder="Код">
                    </td>
                    <td>
                        <input type="text" name="items[][item_name]" class="form-control form-control-sm" placeholder="Наименование" required>
                    </td>
                    <td>
                        <input type="text" name="items[][item_measure]" class="form-control form-control-sm" placeholder="бр.">
                    </td>
                    <td>
                        <input type="number" step="0.01" name="items[][quantity]" class="form-control form-control-sm item-quantity" placeholder="К-во" value="1.00" required>
                    </td>
                    <td>
                        <input type="number" step="0.01" name="items[][price_each]" class="form-control form-control-sm item-price" placeholder="Цена" value="0.00" required>
                    </td>
                    <td class="text-right align-middle">
                        <span class="item-total">0.00</span>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-danger remove-item-row" title="Премахни">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;

            $('#add-item-row').on('click', function() {
                $('#items-container').append(itemRowTemplate);
            });

            $(document).on('click', '.remove-item-row', function() {
                const row = $(this).closest('tr');
                // Ако има скрито поле с ID, значи е съществуващ запис – маркираме за изтриване
                if (row.find('input[name*="[id]"]').length) {
                    if (row.find('input[name*="[_delete]"]').length === 0) {
                        row.append('<input type="hidden" name="items[][_delete]" value="1">');
                    }
                    row.hide();
                } else {
                    row.remove();
                }
                // Преномериране
                $('#items-container tr:visible').each(function(index) {
                    $(this).find('.row-number').text(index + 1);
                });
                calculateGrandTotal();
            });

            $(document).on('input', '.item-quantity, .item-price', function() {
                const row = $(this).closest('tr');
                calculateRowTotal(row);
                calculateGrandTotal();
            });

            // Първоначално изчисление на общата сума
            calculateGrandTotal();
        });
    </script>
@stop