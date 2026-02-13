@forelse($customers as $customer)
    <tr>
        <td>{{ $customer->old_id ?? $customer->id }}</td>
        <td>
            <strong>{!! $customer->name !!}</strong><br>
            <small class="text-muted">
                <i class="fas fa-phone mr-1"></i>{{ $customer->phone ?? '—' }}<br>
                <i class="fas fa-envelope mr-1"></i>{{ $customer->email ?? '—' }}
            </small>
        </td>
        <td>
            @if ($customer->bulstat)
                <div><strong>ЕИК/БУЛСТАТ:</strong> {{ $customer->bulstat }}</div>
            @endif
            @if ($customer->tax_number)
                <div><strong>ДДС №:</strong> {{ $customer->tax_number }}</div>
            @endif
            @if ($customer->mol)
                <div><small>МОЛ: {!! $customer->mol !!}</small></div>
            @endif
            @unless ($customer->bulstat || $customer->tax_number || $customer->mol)
                —
            @endunless
        </td>
        <td>
            @if ($customer->address)
                {!! $customer->address !!}<br>
            @endif
            @if ($customer->address_2)
                <small>{!! $customer->address_2 !!}</small>
            @endif
            @unless ($customer->address || $customer->address_2)
                —
            @endunless
        </td>
        <td>
            @php
                $vehicles = $customer->vehicles->take(3);
            @endphp
            @if ($vehicles->count())
                @foreach ($vehicles as $v)
                    <div><i class="fas fa-car mr-1"></i>{{ $v->plate_number ?? 'без рег.№' }} ({!! $v->vehicle ?? '?' !!})
                    </div>
                @endforeach
                @if ($customer->vehicles->count() > 3)
                    <div><small class="text-muted">и още {{ $customer->vehicles->count() - 3 }}</small></div>
                @endif
            @else
                <span class="text-muted">— няма —</span>
            @endif
        </td>
        <td>
            @if ($customer->is_active)
                <span class="badge badge-success">Активен</span>
            @else
                <span class="badge badge-secondary">Неактивен</span>
            @endif
            <br>
            @if ($customer->is_customer && $customer->is_supplier)
                <span class="badge badge-info">Клиент/Доставчик</span>
            @elseif($customer->is_customer)
                <span class="badge badge-primary">Клиент</span>
            @elseif($customer->is_supplier)
                <span class="badge badge-warning">Доставчик</span>
            @endif
        </td>
        <td class="text-center">
            <div class="btn-group btn-group-sm" role="group">
                <a href="{{ route('admin.customers.show', $customer->id) }}" class="btn btn-outline-primary"
                    title="Преглед">
                    <i class="fas fa-eye"></i>
                </a>
                <a href="{{ route('admin.customers.pdf', $customer->id) }}" class="btn btn-outline-danger"
                    title="PDF" target="_blank">
                    <i class="fas fa-file-pdf"></i>
                </a>
                <a href="{{ route('admin.customers.edit', $customer->id) }}" class="btn btn-outline-warning"
                    title="Редактиране">
                    <i class="fas fa-edit"></i>
                </a>
                <form action="{{ route('admin.customers.destroy', $customer->id) }}" method="POST" class="d-inline"
                    onsubmit="return confirm('Сигурни ли сте, че искате да изтриете клиент {!! $customer->name !!}?');">
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
        <td colspan="7" class="text-center text-muted py-4">
            Няма намерени клиенти
        </td>
    </tr>
@endforelse
