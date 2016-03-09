<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

/*Route::get('/', function (\Pheal\Pheal $pheal) {
	return view('welcome');
})->name('index');*/

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => ['web']], function () {
	Route::group(['prefix' => 'user'], function () {
        Route::get('/login' , 'UserController@login' )->name('login' );
        Route::get('/logout', 'UserController@logout')->name('logout');
	});

	Route::group(['middleware' => ['auth', \App\Http\Middleware\Administrator::class]], function () {
		Route::get ('/config'     , 'ManageController@config')->name('config'     );
		Route::post('/config/motd', 'ManageController@motd'  )->name('config.motd');
	});

	Route::group(['middleware' => ['auth', \App\Http\Middleware\Contractor::class]], function () {
		Route::get('/contract', 'ManageController@contract')->name('contract');
	});

	Route::get ('/', 'HomeController@index')->name('index');
	Route::post('/', 'HomeController@paste')->name('paste');
});
