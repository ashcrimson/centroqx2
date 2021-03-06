<?php

Route::group(['as'=>'api.','namespace' => 'API'], function () {

    Route::resource('options', 'OptionAPIController');



    Route::group(['middleware' => 'auth:api'], function () {

        Route::resource('permissions', 'PermissionAPIController');

        Route::resource('roles', 'RoleAPIController');

        Route::resource('users', 'UserAPIController');
        Route::get('user/add/shortcut/{user}', 'UserAPIController@addShortcut')->name('users.add_shortcut');
        Route::get('user/remove/shortcut/{user}', 'UserAPIController@removeShortcut')->name('users.remove_shortcut');

        Route::resource('drogas', 'DrogaAPIController');

        Route::resource('cargos', 'CargoAPIController');

        Route::resource('dilucions', 'DilucionAPIController');

        Route::resource('empleados', 'EmpleadoAPIController');

        Route::resource('protocolos', 'ProtocoloAPIController');

        Route::resource('servicios', 'ServicioAPIController');
    });


});

