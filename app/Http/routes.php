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
		Route::get ('/config'             , 'ManageController@config'       )->name('config'               );
		Route::post('/config/motd'        , 'ManageController@motd'         )->name('config.motd'          );
		Route::post('/config/add-items'   , 'ManageController@addItems'     )->name('config.items.add'     );
		Route::get ('/config/items'       , 'ManageController@getItems'     )->name('config.items.get'     );
		Route::post('/config/update-items', 'ManageController@updateItems'  )->name('config.items.update'  );
		Route::get ('/config/types'       , 'ManageController@getTypes'     )->name('config.types.get'     );
		Route::get ('/config/groups'      , 'ManageController@getGroups'    )->name('config.groups.get'    );
		Route::get ('/config/categories'  , 'ManageController@getCategories')->name('config.categories.get');

		Route::post('/config/items', 'ManageController@items'   )->name('config.items'       );
	});

	Route::group(['middleware' => ['auth', \App\Http\Middleware\Contractor::class]], function () {
		Route::get('/contract', 'ManageController@contract')->name('contract');
	});

	Route::get ('/', 'HomeController@index')->name('index');
	Route::post('/', 'HomeController@paste')->name('paste');
});
