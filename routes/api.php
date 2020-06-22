<?php
Route::group(['prefix' => 'auth', 'namespace' => 'Api\V1\Auth'], function() {
    Route::post('sign-up', 'AuthController@signUp')->name('auth.sign-up');
    Route::post('sign-in', 'AuthController@signIn')->name('auth.sign-in');
    Route::post('refresh-token', 'AuthController@refreshToken')->name('auth.refresh-token');
    Route::get('user', 'AuthController@user')->name('auth.user')->middleware('auth:api');
    Route::get('verify-email/{user}', 'AuthController@verify')->middleware('signed')->name('auth.verify');
});


Route::group(['prefix' => 'v1', 'middleware' => 'auth:api', 'namespace' => 'Api\V1\Todo'], function () {

    Route::apiResource('todo-category', 'TodoCategoryController')->names('todo.category');


});
