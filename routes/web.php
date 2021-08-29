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

Route::get('/', 'MicropostsController@index');


// ユーザ登録
Route::get('signup', 'Auth\RegisterController@showRegistrationForm')->name('signup.get');
Route::post('signup', 'Auth\RegisterController@register')->name('signup.post');

//認証
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login')->name('login.post');
Route::get('logout', 'Auth\LoginController@logout')->name('logout.get');

// 認証付きのルーティング
Route::group(['middleware' => ['auth']], function () {
    // このグループ内のルーティングではURLの最初に/user/{id}/が付与される。
    Route::group(['prefix'=> 'user/{id}'], function () {
        Route::post('follow', 'UserFollowController@store')->name('user.follow');           //フォローを操作可能にするルーティング
        Route::delete('unfollow', 'UserFollowController@destroy')->name('user.unfollow');   //アンフォローを操作可能にするルーティング
        Route::get('followings', 'UserController@followings')->name('user.followings');     //フォローしているUser一覧を表示するルーティング
        Route::get('followers', 'UserController@followers')->name('user.followers');        //フォローされているUser一覧を表示するルーティング
    });
    
    Route::resource('users', 'UsersController', ['only' => ['index', 'show']]);
    
    Route::resource('microposts', 'MicropostsController', ['only' => ['store', 'destroy']]);
});
