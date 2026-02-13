@forelse($vehicles as $vehicle)
<tr>
    <td>{{ $vehicle->id }}</td>
    <td>
        <strong>{{ $vehicle->vehicle ?: '—' }}</strong>
        @if($vehicle->notes)
            <br><small class="text-muted">{{ Str::limit($vehicle->notes, 50) }}</small>
        @endif
    </td>
    <td>
        @if($vehicle->customer)
            <a href="{{ route('admin.customers.show', $vehicle->customer_id) }}">
                {{ $vehicle->customer->name }}
            </a>
        @else
            —
        @endif
    </td>
    <td>
        @if($vehicle->plate_number)
            <span class="badge badge-dark">{{ $vehicle->plate_number }}</span>
        @else
            —
        @endif
    </td>
    <td>
        @if($vehicle->chassis_number)
            <small class="text-muted font-monospace">{{ $vehicle->chassis_number }}</small>
        @else
            —
        @endif
    </td>
    <td>
        @if($vehicle->last_mileage)
            {{ number_format($vehicle->last_mileage, 0, ',', ' ') }} км
        @else
            —
        @endif
    </td>
    <td>
        <span class="badge {{ $vehicle->is_active ? 'bg-success' : 'bg-secondary' }}">
            {{ $vehicle->is_active ? 'Активен' : 'Неактивен' }}
        </span>
    </td>
    <td class="text-center">
        <div class="btn-group btn-group-sm" role="group">
            <a href="{{ route('admin.vehicles.show', $vehicle->id) }}"
               class="btn btn-outline-primary" title="Преглед">
                <i class="fas fa-eye"></i>
            </a>
            <a href="{{ route('admin.vehicles.edit', $vehicle->id) }}"
               class="btn btn-outline-warning" title="Редактиране">
                <i class="fas fa-edit"></i>
            </a>
            <form action="{{ route('admin.vehicles.destroy', $vehicle->id) }}" 
                  method="POST" 
                  class="d-inline"
                  onsubmit="return confirm('Сигурни ли сте, че искате да изтриете автомобил {{ $vehicle->plate_number }}?');">
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
    <td colspan="8" class="text-center text-muted py-3">
        Няма резултати
    </td>
</tr>
@endforelse