<table class="table table-bordered table-striped table-hover">
    <thead>
        <tr>
            <th width="30" class="text-center">
                <input type="checkbox" id="checkAll">
            </th>
            <th width="60" class="text-center">ID</th>
            <th>Фактура</th>
            <th>Клиент</th>
            <th width="120">Сума</th>
            <th width="120">Плащане</th>
            <th width="120">Статус</th>
            <th width="140" class="text-center">Действия</th>
        </tr>
    </thead>
    <tbody id="invoicesTableBody">
        @foreach ($invoices as $invoice)
            <tr>
                <td class="text-center">
                    <input type="checkbox" class="row-check" value="{{ $invoice->id }}">
                </td>
                <td class="text-center">#{{ $invoice->id }}</td>
                <td>{{ $invoice->invoice_number }}</td>
                <td>{{ $invoice->customer?->name }}</td>
                <td>{{ number_format($invoice->grand_total, 2) }} лв</td>
                <td>
                    <!-- ФИКС: Показване на статуса на плащане -->
                    @if($invoice->payment_status == 'paid')
                        <span class="badge badge-success">Платена</span>
                    @elseif(in_array($invoice->payment_status, ['pending', 'partial', 'overdue', 'refunded']))
                        <span class="badge badge-warning">{{ ucfirst($invoice->payment_status) }}</span>
                    @else
                        <span class="badge badge-secondary">Неясно</span>
                    @endif
                </td>
                <td>
                    <!-- ФИКС: Проверка по статус -->
                    @if(!in_array($invoice->status, ['voided', 'cancelled']))
                        <span class="badge badge-success">Активна</span>
                    @else
                        <span class="badge badge-secondary">Неактивна</span>
                    @endif
                </td>
                <td class="text-center">
                    <div class="btn-group btn-group-sm">
                        <a href="{{ route('admin.invoices.show', $invoice) }}" class="btn btn-info">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('admin.invoices.edit', $invoice) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i>
                        </a>
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

@if($invoices->hasPages())
<div class="card-footer clearfix">
    <div class="float-left">
        <small class="text-muted">
            Показване {{ $invoices->firstItem() }} – {{ $invoices->lastItem() }}
            от {{ $invoices->total() }} фактури
        </small>
    </div>
    <div class="float-right">
        {{ $invoices->appends(request()->except('page'))->links('pagination::bootstrap-4') }}
    </div>
</div>
@endif