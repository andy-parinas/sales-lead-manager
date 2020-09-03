<?php

use App\Mail\LeadCreatedMail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
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
    return view('welcome');
});

Route::get('/email', function () {
    $lead = \App\Lead::with(['salesContact', 'jobType.salesStaff'])->first();
    return  new LeadCreatedMail($lead);
});


Auth::routes();

// Route::get('/home', 'HomeController@index')->name('home');
