@forelse($orders as $wo)
<tr>
    <td>{{ $wo->old_id }}</td>

    <td>
        <strong>{{ $wo->client_name ?: '—' }}</strong><br>
        <small class="text-muted">{{ $wo->phone }}</small>
    </td>

    <td>
        {{ $wo->vehicle }}<br>
        <small class="text-muted">{{ $wo->plate_number }}</small>
    </td>

    <td>{{ $wo->order_date?->format('d.m.Y') }}</td>

    <td class="text-right font-weight-bold">
        {{ number_format($wo->total, 2) }} лв
    </td>

    <td class="text-right">
        <a href="{{ route('admin.work-orders.show', $wo->id) }}"
           class="btn btn-sm btn-outline-primary"
           title="Преглед">
            <i class="fas fa-eye"></i>
        </a>
    </td>
</tr>
@empty
<tr>
    <td colspan="6" class="text-center text-muted py-3">
        Няма резултати
    </td>
</tr>
@endforelse
