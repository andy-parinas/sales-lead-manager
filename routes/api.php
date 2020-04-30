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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


/**
 * Authentication Test Route
 */
Route::get('auth-test', 'HomeController@index');

/**
 * Authentication Route
 */
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout');

/**
 * Franchise Route
 */
Route::resource('franchises', 'Franchise\FranchiseController', ['except' => ['create', 'edit']]);
Route::resource('franchises.postcodes', 'Franchise\FranchisePostcodeController', ['only' => ['index', 'store', 'destroy']]);
Route::resource('franchises.children', 'Franchise\FranchiseChildrenController', ['only' => ['index', 'store']]);
Route::resource('franchises.leads', 'Franchise\FranchiseLeadController', ['except' => ['create', 'edit']]);
Route::put('franchises/{franchise}/leads/{lead}/franchise', 'Franchise\FranchiseLeadFranchiseController@update');


/**
 * Lead Route
 */
Route::resource('leads', 'Lead\LeadController', ['only' => ['index', 'show', 'destroy']]);

/**
 *  User Routes
 */
Route::resource('users', 'User\UserController', ['except' => ['create', 'edit']]);
Route::resource('users.franchises', 'User\UserFranchiseController', ['only' => ['index', 'store', 'destroy']]);

/**
 *  SalesContact Routes
 */
Route::resource('contacts', 'SalesContact\SalesContactController', ['except' => ['edit', 'create']]);

/**
 *  LeadSource Routes
 */
Route::resource('lead-sources', 'LeadSource\LeadSourceController', ['except' => ['edit', 'create', 'show']]);

/**
 * Product Routes
 */
Route::resource('products', 'Product\ProductController', ['except' => ['edit', 'create', 'show']]);

/**
 * TradeType Routes
 */
Route::resource('trade-types', 'TradeType\TradeTypeController', ['except' => ['edit', 'create', 'show']]);

/**
 * Postcode Routes
 */
Route::resource('postcodes', 'Postcode\PostcodeController', ['only' => ['index']]);
















