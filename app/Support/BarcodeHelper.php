<?php

namespace App\Support;

use Milon\Barcode\DNS1D;

class BarcodeHelper
{
    /**
     * Връща PNG баркод като binary string (готов за <img>)
     */
    public static function png(string $code): string
    {
        $dns = new DNS1D();
        $base64 = $dns->getBarcodePNG($code, 'C39', 2, 30);

        return base64_decode($base64); // ← правилно!
    }
}
