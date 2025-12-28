<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="utf-8">
    <title>Продукти – филтриран експорт</title>
    <style>
        body        { font-family: DejaVu Sans, sans-serif; font-size: 10px; margin: 20px; }
        h1          { margin: 0 0 10px; font-size: 18px; }
        .header     { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
        .logo       { width: 120px; height: auto; }
        .filters    { font-size: 9px; color: #555; }
        table       { width: 100%; border-collapse: collapse; }
        th, td      { border: 1px solid #ccc; padding: 6px 4px; text-align: left; }
        th          { background-color: #f5f5f5; font-weight: bold; }
        .center     { text-align: center; }
        .right      { text-align: right; }
        tfoot       { font-weight: bold; }
        .page-break { page-break-after: always; }
        .footer     { margin-top: 15px; font-size: 8px; color: #777; text-align: center; }
    </style>
</head>
<body>
@php
    /* === вземаме само ID-тата от сесията === */
    $ids = session('filtered_products', []);
    if (empty($ids)) {
        /* ако няма нищо – връщаме празен документ */
        $products = collect();
    } else {
        /* вземаме ОБЕКТИТЕ по ID, но само нужните колони – пестим памет */
        $products = \App\Models\Product::whereIn('id', $ids)
            ->orderBy('name')
            ->get([
                'id','name','code','plu','barcode','description',
                'quantity','unit_of_measure','min_stock','price','cost_price',
                'is_service','is_active','location'
            ]);
    }
    $perPage = 30;
    $chunks  = $products->chunk($perPage);
@endphp

@if($chunks->isEmpty())
    <h1>Няма продукти по зададените филтри</h1>
@else
    @foreach ($chunks as $page => $chunk)
        <div class="header">
            <div>
                <h1>Списък продукти</h1>
                <div class="filters">
                    Дата на изготвяне: {{ now()->format('d.m.Y H:i') }} |
                    @if(request('search'))         Търсене: {{ request('search') }}; @endif
                    @if(request('status'))         Статус: {{ request('status') }}; @endif
                    @if(request('stock_status'))   Наличност: {{ request('stock_status') }}; @endif
                    @if(request('type'))           Тип: {{ request('type') }}; @endif
                    <br>Брой редове: {{ $products->count() }}
                </div>
            </div>
            <div>
                <img src="{{ public_path('images/logo.png') }}" alt="Logo" class="logo">
            </div>
        </div>

        <table>
            <thead>
            <tr>
                <th width="6%"  class="center">№</th>
                <th width="8%"  class="center">ID</th>
                <th width="25%">Продукт / Услуга</th>
                <th width="12%">Кодове</th>
                <th width="10%" class="right">Наличност</th>
                <th width="10%" class="right">Цена</th>
                <th width="8%"  class="center">Тип</th>
                <th width="8%"  class="center">Статус</th>
                <th width="10%">Местоположение</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($chunk as $product)
                @php
                    $stockStatus = 'normal'; $stockClass = 'success';
                    if ($product->quantity <= 0) {
                        $stockStatus = 'out'; $stockClass = 'danger';
                    } elseif ($product->min_stock > 0 && $product->quantity <= $product->min_stock) {
                        $stockStatus = 'low'; $stockClass = 'warning';
                    }
                @endphp
                <tr>
                    <td class="center">{{ $page * $perPage + $loop->iteration }}</td>
                    <td class="center">#{{ $product->id }}</td>
                    <td>
                        <strong>{{ $product->name }}</strong>
                        @if($product->description)
                            <br><small>{{ \Illuminate\Support\Str::limit($product->description, 40) }}</small>
                        @endif
                    </td>
                    <td>
                        @if($product->plu)
                            <small>PLU: <b>{{ $product->plu }}</b></small><br>
                        @endif
                        @if($product->code)
                            <small>Код: <b>{{ $product->code }}</b></small><br>
                        @endif
                        @if($product->barcode)
                            <small>Баркод: <code>{{ $product->barcode }}</code></small>
                        @endif
                    </td>
                    <td class="right">
                        <span style="color: {{ $stockClass }}">
                            {{ number_format($product->quantity, 2) }} {{ $product->unit_of_measure }}
                        </span>
                        @if($product->min_stock > 0 && $stockStatus === 'low')
                            <br><small>мин: {{ $product->min_stock }}</small>
                        @endif
                    </td>
                    <td class="right">
                        {{ number_format($product->price, 2) }} лв.
                        @if($product->cost_price > 0)
                            <br><small>с/у: {{ number_format($product->cost_price, 2) }}</small>
                        @endif
                    </td>
                    <td class="center">
                        {!! $product->is_service
                            ? '<span style="background:#6c7576;color:#fff;padding:2px 6px;border-radius:3px;font-size:9px;">Услуга</span>'
                            : '<span style="background:#007bff;color:#fff;padding:2px 6px;border-radius:3px;font-size:9px;">Продукт</span>' !!}
                    </td>
                    <td class="center">
                        {!! $product->is_active
                            ? '<span style="color:#28a745">Активен</span>'
                            : '<span style="color:#6c757d">Неактивен</span>' !!}
                    </td>
                    <td>{{ $product->location ?? '–' }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <div class="footer">
            Страница {{ $page + 1 }} / {{ $chunks->count() }} | Аутосервиз &copy; {{ date('Y') }}
        </div>

        @if (!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach
@endif
</body>
</html>