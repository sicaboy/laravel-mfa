<?php

use Illuminate\Http\Middleware\CheckResponseForModifications;
use Illuminate\Support\Facades\Route;

// Scripts & Styles...
//Route::get('/scripts/{script}', 'ScriptController@show')->middleware(CheckResponseForModifications::class);
//Route::get('/styles/{style}', 'StyleController@show')->middleware(CheckResponseForModifications::class);

Route::get('/mfa', 'MFAController@getIndex')->name('mfa');
Route::get('/mfa/form', 'MFAController@getForm')->name('mfa-form');
Route::post('/mfa/form', 'MFAController@postForm');

