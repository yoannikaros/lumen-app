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
    return response()->json([
        'message' => 'SB Farm API',
        'version' => $router->app->version(),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
});

// Auth Routes
$router->group(['prefix' => 'api'], function () use ($router) {
    $router->post('register', 'AuthController@register');
    $router->post('login', 'AuthController@login');
    
    // Protected routes
    $router->group(['middleware' => 'jwt.auth'], function () use ($router) {
        $router->get('me', 'AuthController@me');
        $router->post('logout', 'AuthController@logout');
    });
});

// Protected API Routes
$router->group(['prefix' => 'api', 'middleware' => 'jwt.auth'], function () use ($router) {
    
    // Pencatatan Pupuk Routes
    $router->group(['prefix' => 'pencatatan-pupuk'], function () use ($router) {
        $router->get('/', 'PencatatanPupukController@index');
        $router->post('/', 'PencatatanPupukController@store');
        $router->get('/summary', 'PencatatanPupukController@summary');
        $router->get('/{id}', 'PencatatanPupukController@show');
        $router->put('/{id}', 'PencatatanPupukController@update');
        $router->delete('/{id}', 'PencatatanPupukController@destroy');
    });
    
    // Penjualan Sayur Routes
    $router->group(['prefix' => 'penjualan-sayur'], function () use ($router) {
        $router->get('/', 'PenjualanSayurController@index');
        $router->post('/', 'PenjualanSayurController@store');
        $router->get('/summary', 'PenjualanSayurController@summary');
        $router->get('/{id}', 'PenjualanSayurController@show');
        $router->put('/{id}', 'PenjualanSayurController@update');
        $router->delete('/{id}', 'PenjualanSayurController@destroy');
    });
    
    // Belanja Modal Routes
    $router->group(['prefix' => 'belanja-modal'], function () use ($router) {
        $router->get('/', 'BelanjaModalController@index');
        $router->post('/', 'BelanjaModalController@store');
        $router->get('/summary', 'BelanjaModalController@summary');
        $router->get('/kategori', 'BelanjaModalController@getKategori');
        $router->get('/{id}', 'BelanjaModalController@show');
        $router->put('/{id}', 'BelanjaModalController@update');
        $router->delete('/{id}', 'BelanjaModalController@destroy');
    });
    
    // Nutrisi Pupuk Routes
    $router->group(['prefix' => 'nutrisi-pupuk'], function () use ($router) {
        $router->get('/', 'NutrisiPupukController@index');
        $router->post('/', 'NutrisiPupukController@store');
        $router->get('/summary', 'NutrisiPupukController@summary');
        $router->get('/areas', 'NutrisiPupukController@getAreas');
        $router->get('/{id}', 'NutrisiPupukController@show');
        $router->put('/{id}', 'NutrisiPupukController@update');
        $router->delete('/{id}', 'NutrisiPupukController@destroy');
    });
    
    // Data Sayur Routes
    $router->group(['prefix' => 'data-sayur'], function () use ($router) {
        $router->get('/', 'DataSayurController@index');
        $router->post('/', 'DataSayurController@store');
        $router->get('/summary', 'DataSayurController@summary');
        $router->get('/areas', 'DataSayurController@getAreas');
        $router->get('/{id}', 'DataSayurController@show');
        $router->put('/{id}', 'DataSayurController@update');
        $router->delete('/{id}', 'DataSayurController@destroy');
    });
    
    // Area Kebun Routes
    $router->group(['prefix' => 'area-kebun'], function () use ($router) {
        $router->get('/', 'AreaKebunController@index');
        $router->post('/', 'AreaKebunController@store');
        $router->get('/summary', 'AreaKebunController@summary');
        $router->get('/{id}', 'AreaKebunController@show');
        $router->put('/{id}', 'AreaKebunController@update');
        $router->delete('/{id}', 'AreaKebunController@destroy');
    });
    
    // Jenis Pupuk Routes
    $router->group(['prefix' => 'jenis-pupuk'], function () use ($router) {
        $router->get('/', 'JenisPupukController@index');
        $router->post('/', 'JenisPupukController@store');
        $router->get('/summary', 'JenisPupukController@summary');
        $router->get('/active', 'JenisPupukController@getActive');
        $router->get('/{id}', 'JenisPupukController@show');
        $router->put('/{id}', 'JenisPupukController@update');
        $router->delete('/{id}', 'JenisPupukController@destroy');
    });
    
});