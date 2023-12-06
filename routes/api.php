<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| afyaPacs Dev
| Backend Developer : ibudirsan
| Email             : ibnudirsan@gmail.com
| Copyright © AfyaPacs 2022
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
