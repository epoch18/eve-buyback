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
		Route::get ('/manage/resource/categories', 'Manage\ResourceController@getCategories')->name('manage.resource.categories');
		Route::get ('/manage/resource/groups'    , 'Manage\ResourceController@getGroups'    )->name('manage.resource.groups'    );
		Route::get ('/manage/resource/types'     , 'Manage\ResourceController@getTypes'     )->name('manage.resource.types'     );

		Route::post('/manage/motd/edit'          , 'Manage\MotdController@postEditMotd'     )->name('manage.motd.edit'          );

		Route::get ('/manage/item/get'           , 'Manage\ItemController@getGetItems'      )->name('manage.item.get'           );
		Route::post('/manage/item/add'           , 'Manage\ItemController@postAddItems'     )->name('manage.item.add'           );
		Route::post('/manage/item/edit'          , 'Manage\ItemController@postEditItems'    )->name('manage.item.edit'          );
		Route::post('/manage/item/remove'        , 'Manage\ItemController@postRemoveItems'  )->name('manage.item.remove'        );
		Route::post('/manage/item/update'        , 'Manage\ItemController@postUpdateItems'  )->name('manage.item.update'        );

		Route::get ('/manage'                    , 'Manage\ManageController@getIndex'       )->name('manage.index'              );
	});

	Route::group(['middleware' => ['auth', \App\Http\Middleware\Contractor::class]], function () {
		Route::post('/contract/update', 'Contract\ContractController@postUpdateContracts')->name('contract.update');
		Route::get ('/contract'       , 'Contract\ContractController@getIndex'           )->name('contract.index' );
	});

	Route::get ('/'                , 'HomeController@index'         )->name('index'                );
	Route::post('/'                , 'HomeController@paste'         )->name('paste'                );
	Route::get ('/mining'          , 'HomeController@getMiningTable')->name('home.mining'          );
	Route::get ('/mining/asteroids', 'HomeController@getAsteroids'  )->name('home.mining.asteroids');
});
