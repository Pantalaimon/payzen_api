<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/
Route::get('/', function () {
    return View::make('hello');
});

/*
  root 'doc#index'
  get 'redirect/:id' => 'redirect#go'
  get 'return' => 'redirect#back'
  post 'return' => 'redirect#back'
  mount BaseApi => '/'
  */

// RedirectController
Route::get('redirect/{chargeId}', [
    'as' => 'redirectClient',
    'before' => 'secure',
    'uses' => "RedirectController@go"
])->where('chargeId', '\d+');
Route::controller('redirect', 'RedirectController'); // auto-map methods "getX", "postX", "putX" and "deleteX"

// Charge creation with shop id in url
Route::post("pos/{urlShopId}/charges", [
    'as' => 'postChargeForPos',
    'before' => 'secure|identify_shop',
    'uses' => "ChargesController@postChargeForPos"
]);

/*
 * 	Verb		Path					Action		Route name
	GET 		/charge 				index 		charge.index
	GET 		/charge/create 			create 		charge.create
	POST 		/charge 				store 		charge.store
	GET 		/charge/{charge} 		show 		charge.show
	GET 		/charge/{charge}/edit 	edit 		charge.edit
	PUT/PATCH 	/charge/{charge} 		update 		charge.update
	DELETE 		/charge/{charge} 		destroy 	charge.destroy
 */
Route::resource('charges', 'ChargesController');

Route::resource('availablemethods', 'AvailablemethodsController');

Route::resource('contexts', 'ContextsController');

Route::resource('messages', 'MessagesController');

Route::resource('usedmethods', 'UsedmethodsController');

Route::resource('currencies', 'CurrenciesController');