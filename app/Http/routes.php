<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});  // 网站首页

Route::controller('/demo', 'Api\DemoController');
Route::controller('/oauth','Api\OauthController'); //OAuth2.0
Route::controller('/region', 'Api\RegionController');
Route::controller('/user', 'Api\UserController');
Route::controller('/userextend', 'Api\UserextendController');
Route::controller('/useropened', 'Api\UseropenedController');

