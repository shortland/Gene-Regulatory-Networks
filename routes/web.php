<?php
use Laravel\Lumen\Routing\Router;

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
/** @var \Laravel\Lumen\Routing\Router $router */
$router->get('/', function () {
    return "API Framework: " . app()->version();
});

$router->get('authorize',  [
    'uses'       => 'NetworkController@newCode'
]);

$router->post('token',  [
    'uses'       => 'NetworkController@newToken'
]);

$router->get('list_networks',  [
    'uses'       => 'PublicNetworkController@list'
]);

$router->get('get_network_changes',  [
    'uses'       => 'PublicNetworkController@changesAfterEpoch'
]);

$router->post('modify_network',  [
    'uses'       => 'PrivateNetworkController@modify'
]);

$router->post('delete_network',  [
    'uses'       => 'PrivateNetworkController@deleteFile'
]);

$router->post('create_network',  [
    'uses'       => 'PrivateNetworkController@createFile'
]);

$router->post('rename_network', [
    'uses'       => 'PrivateNetworkController@renameFile'
]);

$router->get('export_csv', [
    'uses'       => 'PrivateNetworkController@csvExport'
]);

$router->get('export_json', [
    'uses'       => 'PrivateNetworkController@jsonExport'
]);

$router->get('network_diffs', [
    'uses'       => 'PrivateNetworkController@nodeDiffs'
]);

/**
*    Need to figure this out...
*    Generate random string
*/
$router->get('appKey', function () {
    return str_random('32');
});

// route for creating access_token
$router->post('accessToken', 'AccessTokenController@createAccessToken');

$router->group(['middleware' => ['auth:api', 'throttle:60']], function () use ($router) {
    $router->post('users', [
        'uses'       => 'UserController@store',
        'middleware' => "scope:users,users:create"
    ]);
    $router->get('users',  [
        'uses'       => 'UserController@index',
        'middleware' => "scope:users,users:list"
    ]);
    $router->get('users/{id}', [
        'uses'       => 'UserController@show',
        'middleware' => "scope:users,users:read"
    ]);
    $router->put('users/{id}', [
        'uses'       => 'UserController@update',
        'middleware' => "scope:users,users:write"
    ]);
    $router->delete('users/{id}', [
        'uses'       => 'UserController@destroy',
        'middleware' => "scope:users,users:delete"
    ]);
});

