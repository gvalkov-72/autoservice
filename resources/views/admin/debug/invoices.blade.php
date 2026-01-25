@extends('adminlte::page')

@section('title', 'Debug Invoices')

@section('content_header')
    <h1>Debug Invoices (Access Import Validation)</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body table-responsive p-0">
        <table class="table table-sm table-hover text-nowrap">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Old ID</th>
                    <th>Client</th>
                    <th>Type</th>
                    <th>Date</th>
                    <th class="text-center">Items</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoices as $inv)
                    <tr>
                        <td>{{ $inv->id }}</td>
                        <td>{{ $inv->old_id }}</td>
                        <td>
                            {{ $inv->customer->name ?? '—' }}
                        </td>
                        <td>
                            {{ $inv->doctype->name ?? $inv->invoice_type }}
                        </td>
                        <td>
                            {{ optional($inv->invoice_date)->format('d.m.Y') }}
                        </td>
                        <td class="text-center">
                            {{ $inv->items->count() }}
                        </td>
                        <td class="text-right">
                            <strong>{{ number_format($inv->total, 2, '.', ' ') }}</strong>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@stop
