@php
    $rate = 1.95583;
    $showBgn = now()->lte('2026-01-31');

    function toBgn($amountEur, $rate = 1.95583, $decimals = 2)
    {
        return number_format($amountEur * $rate, $decimals, ',', ' ');
    }

    function formatEur($amountEur, $decimals = 2)
    {
        return number_format($amountEur, $decimals, ',', ' ');
    }
@endphp

@forelse($orders as $wo)
<tr>
    <td>{{ $wo->old_id }}</td>

    <td>
        <strong>{{ $wo->client_name ?: '—' }}</strong><br>
        <small class="text-muted">{{ $wo->phone }}</small>
    </td>

    <td>
        <div class="font-weight-bold">{{ $wo->vehicle ?: '—' }}</div>
        <div class="text-muted">
            <small>
                @if ($wo->plate_number)
                    <i class="fas fa-car mr-1"></i>{{ $wo->plate_number }}
                @endif
                @if ($wo->chassis_number)
                    @if ($wo->plate_number)
                        •
                    @endif
                    <i class="fas fa-id-card mr-1"></i>{{ $wo->chassis_number }}
                @endif
                @if (!$wo->plate_number && !$wo->chassis_number)
                    —
                @endif
            </small>
        </div>
    </td>

    <td>{{ $wo->order_date?->format('d.m.Y') }}</td>

    <td class="text-right font-weight-bold">
        <div>{{ formatEur($wo->total) }} €</div>
        @if ($showBgn)
            <small class="text-muted">{{ toBgn($wo->total, $rate) }} лв</small>
        @endif
    </td>

    <td class="text-center">
        <div class="btn-group btn-group-sm" role="group">
            <a href="{{ route('admin.work-orders.show', $wo->id) }}"
               class="btn btn-outline-primary" title="Преглед">
                <i class="fas fa-eye"></i>
            </a>
            <a href="{{ route('admin.work-orders.edit', $wo->id) }}"
               class="btn btn-outline-warning" title="Редактиране">
                <i class="fas fa-edit"></i>
            </a>
            <form action="{{ route('admin.work-orders.destroy', $wo->id) }}" 
                  method="POST" 
                  class="d-inline"
                  onsubmit="return confirm('Сигурни ли сте, че искате да изтриете поръчка #{{ $wo->old_id }}?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger" title="Изтриване">
                    <i class="fas fa-trash"></i>
                </button>
            </form>
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="6" class="text-center text-muted py-3">
        Няма резултати
    </td>
</tr>
@endforelse