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