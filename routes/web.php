<?php

use Illuminate\Support\Facades\Route;

Route::get('/mfa', 'MFAController@getIndex')->name('mfa');
Route::get('/mfa/form', 'MFAController@getForm')->name('mfa-form');
Route::post('/mfa/form', 'MFAController@postForm');

