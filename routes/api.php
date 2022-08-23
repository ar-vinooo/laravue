<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('companies', 'Api\CompaniesController@get');
Route::get('companies/{id}', 'Api\CompaniesController@find');
Route::post('companies', 'Api\CompaniesController@post');
Route::put('companies', 'Api\CompaniesController@put');
Route::delete('companies/{id}', 'Api\CompaniesController@delete');

Route::get('images', 'Api\ImagesController@get');
