@extends('adminlte::page')

@section('title', 'Поръчки')

@section('content_header')
    <h1>Поръчки</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <a href="{{ route('admin.work-orders.create') }}" class="btn btn-success btn-sm float-right">Нова поръчка</a>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Номер</th>
                        <th>Клиент</th>
                        <th>Автомобил</th>
                        <th>Статус</th>
                        <th>Приета на</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($workOrders as $wo)
                        <tr>
                            <td>{{ $wo->number }}</td>
                            <td>{{ $wo->customer->name }}</td>
                            <td>{{ $wo->vehicle->plate ?? '-' }}</td>
                            <td>{{ $wo->status }}</td>
                            <td>{{ $wo->received_at ? \Carbon\Carbon::parse($wo->received_at)->format('d.m.Y') : '-' }}</td>
                            <td>
                                <a href="{{ route('admin.work-orders.show', $wo) }}" class="btn btn-sm btn-info">Преглед</a>
                                <a href="{{ route('admin.work-orders.edit', $wo) }}" class="btn btn-sm btn-primary">Редактирай</a>

                                <!-- БУТОН ИЗТРИВАНЕ -->
                                <form action="{{ route('admin.work-orders.destroy', $wo) }}" method="POST" style="display:inline;"
                                      onsubmit="return confirm('Сигурни ли сте, че искате да изтриете тази поръчка?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Изтрий</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $workOrders->links() }}
        </div>
    </div>
@stop