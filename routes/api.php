<?php

Route::group(['prefix' => 'v1', 'namespace' => 'Api\V1'], function() {

    Route::group(['prefix' => 'auth', 'namespace' => 'Auth'], function() {
        Route::post('sign-up', 'SignUpController@signUp')->name('auth.sign-up');
        Route::post('sign-in', 'SignInController@signIn')->name('auth.sign-in');
        Route::post('refresh-token', 'RefreshTokenController@refreshToken')->name('auth.refresh-token');

        Route::get('user', 'UserController@user')->name('auth.user')->middleware('auth:api');
        Route::get('verify-email/{user}', 'VerificationController@verify')->middleware('signed')->name('auth.verify');
    });


    Route::group(['middleware' => 'auth:api', 'namespace' => 'Todo'], function () {
        Route::apiResource('todo-category', 'TodoCategoryController')->names('todo.category');
    });

});
