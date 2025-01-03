<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Milon\Barcode\DNS2D;

class QrCodeController extends Controller
{
    public function __invoke($schedule_id)
    {
        $barcodeGenerator = new DNS2D();
        $barcode = $barcodeGenerator->getBarcodePNG($schedule_id, 'QRCODE', 100, 100);

        return view('qrcode', compact('barcode'));
    }
}
