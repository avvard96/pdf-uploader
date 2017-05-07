<?php

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

//Route::get('/', function () {
//    return view('welcome');
//});

Route::get('/', 'HomeController@index')->name('home.index');
Route::get('/test', 'HomeController@test')->name('home.test');
Route::post('/upload-config', 'HomeController@uploadConfig')->name('home.uploadConfig');
Route::post('/upload-from-mail', 'HomeController@uploadFromMail')->name('home.uploadFromMail');