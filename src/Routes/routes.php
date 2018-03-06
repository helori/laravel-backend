<?php

Route::middleware('web')->group(function () {

    Route::group(['prefix' => 'admin', 'middleware' => 'auth:admin'], function ()
    {
        Route::get('/', ['uses' => '\Helori\LaravelBackend\Controllers\FrontController@home', 'as' => 'backend-home']);
    });
});


