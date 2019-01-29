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
    return view('welcome');
});

Route::get('init', 'AppController@init');
Route::post('login', 'AppController@login');
Route::post('register', 'AppController@register');
Route::post('logout', 'AppController@logout');
Route::post('install', 'AppController@install');

Route::prefix('categories')->group(function () {
    Route::get('/', 'CategoryController@getCats');
    Route::post('create', 'CategoryController@create');
    Route::post('delete', 'CategoryController@delete');
});

Route::prefix('forum')->group(function () {
    Route::get('/{id}', 'ForumController@getForumById');
    Route::post('create', 'ForumController@create');
});

Route::prefix('post')->group(function () {
    Route::post('create', 'PostController@create');
    Route::post('increase-rating', 'PostController@increaseRating');
    Route::post('decrease-rating', 'PostController@decreaseRating');

});

Route::prefix('thread')->group(function () {
    Route::get('search/{searchQuery}', 'ThreadController@search');
    Route::post('create', 'ThreadController@create');
    Route::post('subscribe', 'ThreadController@subscribe');
    Route::post('unsubscribe', 'ThreadController@unsubscribe');

    Route::get('/{id}', 'ThreadController@getThreadById');
});

Route::prefix('notification')->group(function () {
    Route::post('read', 'NotificationController@read');
    Route::post('has-new-activate', 'NotificationController@hasNewActivate');
    Route::post('has-new-deactivate', 'NotificationController@hasNewDeactivate');
    Route::get('get', 'NotificationController@get');

});

