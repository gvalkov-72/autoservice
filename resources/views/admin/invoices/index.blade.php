@extends('adminlte::page')

@section('title', 'Фактури')

@section('content_header')
    <h1>Фактури</h1>
@endsection

@section('content')

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <a href="{{ route('admin.invoices.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Нова фактура
            </a>

            <div style="width: 300px;">
                <input type="text" id="invoice-search" class="form-control" placeholder="Търси във всички колони..."
                    autocomplete="off">
            </div>
        </div>

        <div class="card-body p-0">
            <div id="invoice-table">
                <table class="table table-bordered table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Фактура №</th>
                            <th>Клиент</th>
                            <th>Дата</th>
                            <th>Статус</th>
                            <th>Плащане</th>
                            <th class="text-right">Сума</th>
                            <th class="text-center">Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($invoices as $invoice)
                            <tr>
                                <td>{{ $invoice->id }}</td>
                                <td>{{ $invoice->invoice_number }}</td>
                                <td>
                                    {{ $invoice->customer->name ?? '-' }}
                                </td>
                                <td>{{ $invoice->invoice_date }}</td>
                                <td>
                                    <span class="badge badge-info">
                                        {{ $invoice->status }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-secondary">
                                        {{ $invoice->payment_status }}
                                    </span>
                                </td>
                                <td class="text-right">
                                    {{ number_format($invoice->grand_total, 2) }} лв.
                                </td>
                                <td class="text-center">

                                    <a href="{{ route('invoices.pdf', $invoice) }}" class="btn btn-sm btn-outline-danger"
                                        target="_blank" title="PDF">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                    <a href="{{ route('admin.invoices.show', $invoice) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <a href="{{ route('admin.invoices.edit', $invoice) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">
                                    Няма намерени фактури
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-3 d-flex justify-content-center pagination-sm">
                    {{ $invoices->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')
    <script>
        let timer = null;

        document.getElementById('invoice-search').addEventListener('input', function() {
            clearTimeout(timer);

            const query = this.value;

            timer = setTimeout(() => {
                fetch(`{{ route('admin.invoices.index') }}?q=${encodeURIComponent(query)}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.text())
                    .then(html => {
                        document.getElementById('invoice-table').innerHTML = html;
                    });
            }, 300);
        });
    </script>
@endsection
