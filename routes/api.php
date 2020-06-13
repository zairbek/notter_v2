<?php
Route::group(['prefix' => 'auth'], function() {
    Route::post('sign-up', 'AuthController@signUp')->name('auth.sign-up');
    Route::post('sign-in', 'AuthController@signIn')->name('auth.sign-in');
    Route::get('user', 'AuthController@user')->name('auth.user')->middleware('auth:api');
});
