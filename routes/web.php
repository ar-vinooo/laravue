<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'AuthController@formLogin');
Route::post('/login', 'AuthController@prosesLogin')->name('login');

Route::middleware('auth')->group(function () {
    Route::get('/beranda', 'BerandaController@index');
    Route::get('/companies', 'CompaniesController@index');

    Route::get('/logout', 'AuthController@prosesLogout');
});
