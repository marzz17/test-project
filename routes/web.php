<?php

use Illuminate\Support\Facades\Route;

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
Route::post('createcontacttable', 'GlobalController@createcontactstable');
Route::post('createcontacts', 'GlobalController@savecontacts');
Route::get('getcontacts/{id}', 'GlobalController@getcontacts');

Route::get('getcampaigns', 'CampaignController@getcampaigns');
Route::post('createcampaigns', 'CampaignController@savecampaigns');
Route::delete('destroycampaigns/{id}', 'CampaignController@destroycampaigns');

Route::get('gettemplates', 'TemplatesController@gettemplates');
Route::post('createtemplate', 'TemplatesController@savetemplates');