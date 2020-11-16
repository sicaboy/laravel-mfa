<?php

use Illuminate\Support\Facades\Route;

Route::get('/generate', 'MFAController@getIndex')->name('generate');
Route::get('/form', 'MFAController@getForm')->name('form');
Route::post('/form', 'MFAController@postForm');

