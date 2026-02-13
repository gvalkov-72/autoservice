@forelse($companySettings as $company)
    <tr>
        <td>{{ $company->id }}</td>
        <td>
            <strong>{!! $company->name !!}</strong>
            @if($company->is_active)
                <span class="badge badge-success ml-1">Активен</span>
            @endif
            @if($company->website)
                <br><small><a href="{{ $company->website }}" target="_blank">{{ $company->website }}</a></small>
            @endif
        </td>
        <td>
            {{ $company->city ?? '—' }}<br>
            <small class="text-muted">{{ $company->address ?? '—' }}</small>
        </td>
        <td>
            @if($company->vat_number)
                <div><strong>ЕИК:</strong> {{ $company->vat_number }}</div>
                <div><small>ДДС: BG{{ $company->vat_number }}</small></div>
            @else
                —
            @endif
        </td>
        <td>
            @if($company->phone)
                <i class="fas fa-phone mr-1"></i>{{ $company->phone }}<br>
            @endif
            @if($company->email)
                <i class="fas fa-envelope mr-1"></i>{{ $company->email }}
            @endif
            @unless($company->phone || $company->email)
                —
            @endunless
            @if($company->contact_person)
                <br><small>Контакт: {!! $company->contact_person !!}</small>
            @endif
        </td>
        <td>
            @if($company->is_active)
                <span class="badge badge-success">Активен</span>
            @else
                <span class="badge badge-secondary">Неактивен</span>
            @endif
        </td>
        <td class="text-center">
            <div class="btn-group btn-group-sm" role="group">
                <a href="{{ route('admin.company-settings.show', $company->id) }}" 
                   class="btn btn-outline-primary" title="Преглед">
                    <i class="fas fa-eye"></i>
                </a>
                <a href="{{ route('admin.company-settings.edit', $company->id) }}" 
                   class="btn btn-outline-warning" title="Редактиране">
                    <i class="fas fa-edit"></i>
                </a>
                <a href="{{ route('admin.company-settings.print', $company->id) }}" 
                   class="btn btn-outline-secondary" title="Печат" target="_blank">
                    <i class="fas fa-print"></i>
                </a>
                <a href="{{ route('admin.company-settings.pdf', $company->id) }}" 
                   class="btn btn-outline-danger" title="PDF" target="_blank">
                    <i class="fas fa-file-pdf"></i>
                </a>
                <form action="{{ route('admin.company-settings.destroy', $company->id) }}" 
                      method="POST" class="d-inline"
                      onsubmit="return confirm('Сигурни ли сте, че искате да изтриете фирмени данни за {{ $company->name }}?');">
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
            Няма въведени фирмени данни
        </td>
    </tr>
@endforelse