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

Route::get('/', function () {
    return redirect()->route('voyager.login');;
});


Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
    Route::get('companyUsers/{id}', ['uses' => 'CompanyController@companyUsers', 'as' => 'companyUsers']);
    Route::get('usersbycompany/{id}', ['uses' => 'UserController@usersbycompany', 'as' => 'usersbycompany']);
    Route::get('attachmentsbycompany/{id}', ['uses' => 'CompanyAttachmentController@attachmentsbycompany', 'as' => 'attachmentsbycompany']);
    Route::get('generateattachments/{id}', ['uses' => 'CompanyAttachmentController@generateattachments', 'as' => 'generateattachments']); 
	Auth::routes();
});



Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});

 
