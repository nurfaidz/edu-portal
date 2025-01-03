<?php

use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('qr-code/{schedule_id}', App\Http\Controllers\QrCodeController::class)->name('qr-code');
