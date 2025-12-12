<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Баркод {{ $product->sku }}</title>
    <style>
        body { margin: 0; text-align: center; font-family: DejaVu Sans, sans-serif; }
        .sticker { display: inline-block; padding: 10px; border: 1px dashed #ccc; margin: 5mm; }
        .sku { font-size: 12px; font-weight: bold; }
        .price { font-size: 14px; color: green; }
        @media print { body { margin: 0; } }
    </style>
</head>
<body onload="window.print()">
    <div class="sticker">
        <div class="sku">{{ $product->sku }}</div>
        
        {{-- ЕТО ПРАВИЛНИЯТ РЕД --}}
        <img src="{{ route('admin.barcode.png', ['code' => $product->sku]) }}" alt="barcode">

        <div class="price">{{ number_format($product->price, 2) }} лв.</div>
    </div>
</body>
</html>
