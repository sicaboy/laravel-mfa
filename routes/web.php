<?php

use Illuminate\Http\Middleware\CheckResponseForModifications;
use Illuminate\Support\Facades\Route;

// Scripts & Styles...
//Route::get('/scripts/{script}', 'ScriptController@show')->middleware(CheckResponseForModifications::class);
//Route::get('/styles/{style}', 'StyleController@show')->middleware(CheckResponseForModifications::class);

Route::get('/mfa', function() {return 'hihihi';})->name('mfa');
Route::post('/mfa', function() {return 'hihihi';});
Route::post('/d', function() {return 'hihihi';});
