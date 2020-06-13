<?php
Route::group(['prefix' => 'auth'], function() {
    Route::post('sign-up', 'AuthController@signUp')->name('auth.sign-up');
    Route::post('sign-in', 'AuthController@signIn')->name('auth.sign-in');
    Route::post('refresh-token', 'AuthController@refreshToken')->name('auth.refresh-token');
    Route::get('user', 'AuthController@user')->name('auth.user')->middleware('auth:api');
});
