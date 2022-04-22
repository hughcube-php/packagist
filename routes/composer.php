<?php

use App\Http\Composer\CatchAllController as CatchAllController;
use Illuminate\Support\Facades\Route;

Route::any('composer/{any}', CatchAllController::class)->where('any', '.*');
