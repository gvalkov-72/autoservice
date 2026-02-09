@extends('adminlte::page')

@section('title', 'Начало – Автосервиз')

@section('content_header')
    <h1>Дашборд</h1>
@stop

@section('content')
    @php
        $rate = 1.95583;
        $showBgn = now()->lte('2026-03-31'); // показваме левове само до тази дата

        // Функция за конвертиране от евро към левове
        function toBgn($amountEur, $rate = 1.95583, $decimals = 2)
        {
            return number_format($amountEur * $rate, $decimals, ',', ' ');
        }

        // Функция за форматиране на евро (което вече е в базата)
        function formatEur($amountEur, $decimals = 2)
        {
            return number_format($amountEur, $decimals, ',', ' ') . ' €';
        }
    @endphp
    <div class="row">

        {{-- Клиенти (реално имаме само имена в Work Orders) --}}
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ \App\Models\WorkOrder::distinct('client_name')->count('client_name') }}</h3>
                    <p>Клиенти (от поръчки)</p>
                </div>
                <div class="icon"><i class="fas fa-users"></i></div>
            </div>
        </div>

        {{-- Поръчки --}}
        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ \App\Models\WorkOrder::count() }}</h3>
                    <p>Поръчки</p>
                </div>
                <div class="icon"><i class="fas fa-clipboard-list"></i></div>
            </div>
        </div>

        {{-- Оборот --}}
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    @php
                        $totalEur = \App\Models\WorkOrder::all()->sum->total;
                    @endphp
                    <h3>{{ formatEur($totalEur) }}</h3>
                    <p>Общ оборот</p>
                    @if ($showBgn)
                        <small class="text-white" style="opacity: 0.8; font-size: 0.85rem;">
                            {{ toBgn($totalEur, $rate) }} лв
                        </small>
                    @endif
                </div>
                <div class="icon"><i class="fas fa-coins"></i></div>
            </div>
        </div>

        {{-- Потребители --}}
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ \App\Models\User::count() }}</h3>
                    <p>Потребители</p>
                </div>
                <div class="icon"><i class="fas fa-user-shield"></i></div>
            </div>
        </div>

    </div>

    {{-- Последни поръчки --}}
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-clock mr-1"></i>
                        Последни поръчки
                    </h3>
                </div>

                <div class="card-body table-responsive p-0">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Клиент</th>
                                <th>Дата</th>
                                <th class="text-right">Общо</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(\App\Models\WorkOrder::orderByDesc('id')->limit(10)->get()
                                                            as $wo)
                                <tr>
                                    <td>{{ $wo->id }}</td>
                                    <td>{{ $wo->client_name ?: '—' }}</td>
                                    <td>{{ $wo->order_date?->format('d.m.Y') }}</td>
                                    <td class="text-right">
                                        <div class="font-weight-bold">{{ formatEur($wo->total) }}</div>
                                        @if ($showBgn)
                                            <small class="text-muted">{{ toBgn($wo->total, $rate) }} лв</small>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">
                                        Няма поръчки
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop