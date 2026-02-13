@forelse($invoices as $invoice)
    @php
        $rate = 1.95583;
        $totalEur = $invoice->total;
        $totalBgn = $totalEur * $rate;
    @endphp
    <tr>
        <td><strong>{{ $invoice->old_id }}</strong></td>
        <td>{{ $invoice->invoice_date ? $invoice->invoice_date->format('d.m.Y') : '—' }}</td>
        <td>
            @if($invoice->customer)
                <strong>{!! $invoice->customer->name !!}</strong><br>
                <small class="text-muted">{{ $invoice->customer->phone ?? '—' }}</small>
            @else
                <span class="text-muted">—</span>
            @endif
        </td>
        <td>
            @if($invoice->doctype)
                <span class="badge badge-info">{{ $invoice->doctype->short ?? $invoice->doctype->name }}</span>
            @else
                —
            @endif
        </td>
        <td>
            @if($invoice->date_due)
                {{ $invoice->date_due->format('d.m.Y') }}
                @if($invoice->date_due < now() && !$invoice->paid && !$invoice->is_void)
                    <br><small class="text-danger">Просрочена</small>
                @endif
            @else
                —
            @endif
        </td>
        <td class="text-right">{{ number_format($totalEur, 2, ',', ' ') }}</td>
        <td class="text-right">{{ number_format($totalBgn, 2, ',', ' ') }}</td>
        <td>
            @if($invoice->is_void)
                <span class="badge badge-secondary">Анулирана</span>
            @elseif($invoice->paid)
                <span class="badge badge-success">Платена</span>
            @else
                <span class="badge badge-warning">Неплатена</span>
            @endif
            @if($invoice->payment_cash)
                <br><small class="text-muted">в брой</small>
            @else
                <br><small class="text-muted">безкасово</small>
            @endif
        </td>
        <td class="text-center">
            <div class="btn-group btn-group-sm" role="group">
                <a href="{{ route('admin.invoices.show', $invoice->id) }}" class="btn btn-outline-primary" title="Преглед">
                    <i class="fas fa-eye"></i>
                </a>
                @if(!$invoice->is_void)
                    <a href="{{ route('admin.invoices.edit', $invoice->id) }}" class="btn btn-outline-warning" title="Редактиране">
                        <i class="fas fa-edit"></i>
                    </a>
                @endif
                <a href="{{ route('admin.invoices.print', $invoice->id) }}" class="btn btn-outline-secondary" title="Печат" target="_blank">
                    <i class="fas fa-print"></i>
                </a>
                <a href="{{ route('admin.invoices.pdf', $invoice->id) }}" class="btn btn-outline-danger" title="PDF" target="_blank">
                    <i class="fas fa-file-pdf"></i>
                </a>
                @if(!$invoice->is_void)
                    <form action="{{ route('admin.invoices.destroy', $invoice->id) }}" method="POST" class="d-inline"
                          onsubmit="return confirm('Сигурни ли сте, че искате да изтриете фактура №{{ $invoice->old_id }}?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger" title="Изтриване">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                @endif
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="9" class="text-center text-muted py-4">
            Няма намерени фактури
        </td>
    </tr>
@endforelse