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

Route::get('/', function () { return view('main'); })->name("home");

Route::match(['post', 'get'], 'schedule', 'Pages\SchedulePageController@getPage');
Route::post('scheduleUpdate', 'Pages\SchedulePageController@update');
Route::post('getSchedule', 'Pages\SchedulePageController@getSchedule');

Route::match(['post', 'get'], 'group', 'Pages\GroupPageController@getPage');
Route::post('getGroup', 'Pages\GroupPageController@getGroup');
Route::post('groupUpdate', 'Pages\GroupPageController@update');
Route::post('userRemove', 'Pages\GroupPageController@userRemove');
Route::post('chnggrname', 'Pages\GroupPageController@changeGroupName');
Route::post('groupRemove', 'Pages\GroupPageController@groupRemove');

Route::match(['post', 'get'], 'timetable', 'Pages\ListPageController@getPage');
Route::post('tableUpdate', 'Pages\ListPageController@tableUpdate');
Route::post('getWeekTable', 'Pages\ListPageController@getWeekTable');
Route::post('getMonthTable', 'Pages\ListPageController@getMonthTable');
Route::post('getYearTable', 'Pages\ListPageController@getYearTable');

Route::post('getExcel/{type}', 'ExcelController@getExcel')->name("getExcel");
Route::get('getExcel/{type}', 'ExcelController@getExcel')->name("getExcel");

//--------------------------- AUTH ---------------------------
Route::post('register', 'Auth\RegisterController@register');
Route::post('login', 'Auth\LoginController@login');
Route::post('sendRestore', 'Auth\ForgotPasswordController@sendRestore');
Route::post('restore', 'Auth\ForgotPasswordController@restore');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');
