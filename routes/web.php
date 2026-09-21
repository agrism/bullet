<?php

use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

Route::redirect('/contacts', '/kontakti/');
Route::get('/', [PageController::class, 'show']);
Route::get('/{any}', [PageController::class, 'show'])->where('any', '.*');


