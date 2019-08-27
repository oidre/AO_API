<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group([
    'prefix' => 'auth',
], function () use ($router) {
    $router->post('login', [
        'as' => 'auth.login',
        'uses' => 'AuthController@login',
    ]);
    $router->post('logout', [
        'as' => 'auth.logout',
        'uses' => 'AuthController@logout',
    ]);
    $router->get('refresh', [
        'as' => 'auth.refresh',
        'uses' => 'AuthController@refresh',
    ]);
    $router->get('self', [
        'as' => 'auth.self',
        'uses' => 'AuthController@self',
    ]);
});

$router->group([
    'prefix' => 'modules',
], function () use ($router) {
    $router->get('/', [
        'as' => 'modules.index',
        'uses' => 'ModulesController@index',
    ]);
    $router->get('/{id:[0-9]+}', [
        'as' => 'modules.show',
        'uses' => 'ModulesController@show',
    ]);
    $router->post('/', [
        'as' => 'modules.store',
        'uses' => 'ModulesController@store',
    ]);
    $router->put('/{id:[0-9]+}', [
        'as' => 'modules.update',
        'uses' => 'ModulesController@update',
    ]);
    $router->delete('/{id:[0-9]+}', [
        'as' => 'modules.destroy',
        'uses' => 'ModulesController@destroy',
    ]);
});


$router->get('/users', 'UsersController@index');