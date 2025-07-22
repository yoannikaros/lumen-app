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
    
    // Tandon Routes
    $router->group(['prefix' => 'tandon'], function () use ($router) {
        $router->get('/', 'TandonController@index');
        $router->post('/', 'TandonController@store');
        $router->get('/area/{areaId}', 'TandonController@getByArea');
        $router->get('/{id}', 'TandonController@show');
        $router->put('/{id}', 'TandonController@update');
        $router->delete('/{id}', 'TandonController@destroy');
    });
    
    // Nutrisi Pupuk Detail Routes
    $router->group(['prefix' => 'nutrisi-pupuk-detail'], function () use ($router) {
        $router->get('/', 'NutrisiPupukDetailController@index');
        $router->post('/', 'NutrisiPupukDetailController@store');
        $router->get('/nutrisi-pupuk/{nutrisiPupukId}', 'NutrisiPupukDetailController@getByNutrisiPupuk');
        $router->get('/{id}', 'NutrisiPupukDetailController@show');
        $router->put('/{id}', 'NutrisiPupukDetailController@update');
        $router->delete('/{id}', 'NutrisiPupukDetailController@destroy');
    });
    
    // Seed Log Routes
    $router->group(['prefix' => 'seed-log'], function () use ($router) {
        $router->get('/', 'SeedLogController@index');
        $router->post('/', 'SeedLogController@store');
        $router->get('/summary', 'SeedLogController@summary');
        $router->get('/by-data-sayur/{dataSayurId}', 'SeedLogController@getByDataSayur');
        $router->get('/{id}', 'SeedLogController@show');
        $router->put('/{id}', 'SeedLogController@update');
        $router->delete('/{id}', 'SeedLogController@destroy');
    });
    
    // Plant Health Log Routes
    $router->group(['prefix' => 'plant-health-log'], function () use ($router) {
        $router->get('/', 'PlantHealthLogController@index');
        $router->post('/', 'PlantHealthLogController@store');
        $router->get('/summary', 'PlantHealthLogController@summary');
        $router->get('/health-stats', 'PlantHealthLogController@getHealthStats');
        $router->get('/by-data-sayur/{dataSayurId}', 'PlantHealthLogController@getByDataSayur');
        $router->get('/{id}', 'PlantHealthLogController@show');
        $router->put('/{id}', 'PlantHealthLogController@update');
        $router->delete('/{id}', 'PlantHealthLogController@destroy');
    });

    // Perlakuan Master Routes
    $router->group(['prefix' => 'perlakuan-master'], function () use ($router) {
        $router->get('/', 'PerlakuanMasterController@index');
        $router->post('/', 'PerlakuanMasterController@store');
        $router->get('/summary', 'PerlakuanMasterController@summary');
        $router->get('/by-tipe/{tipe}', 'PerlakuanMasterController@getByTipe');
        $router->get('/{id}', 'PerlakuanMasterController@show');
        $router->put('/{id}', 'PerlakuanMasterController@update');
        $router->delete('/{id}', 'PerlakuanMasterController@destroy');
    });

    // Jadwal Perlakuan Routes
    $router->group(['prefix' => 'jadwal-perlakuan'], function () use ($router) {
        $router->get('/', 'JadwalPerlakuanController@index');
        $router->post('/', 'JadwalPerlakuanController@store');
        $router->get('/summary', 'JadwalPerlakuanController@summary');
        $router->get('/rotation-schedule', 'JadwalPerlakuanController@getRotationSchedule');
        $router->get('/by-month/{year}/{month}', 'JadwalPerlakuanController@getByMonth');
        $router->get('/by-area/{areaId}', 'JadwalPerlakuanController@getByArea');
        $router->get('/by-perlakuan/{perlakuanId}', 'JadwalPerlakuanController@getByPerlakuan');
        $router->get('/{id}', 'JadwalPerlakuanController@show');
        $router->put('/{id}', 'JadwalPerlakuanController@update');
        $router->delete('/{id}', 'JadwalPerlakuanController@destroy');
    });
    
    // Pembelian Benih Detail Routes
    $router->group(['prefix' => 'pembelian-benih-detail'], function () use ($router) {
        $router->get('/', 'PembelianBenihDetailController@index');
        $router->post('/', 'PembelianBenihDetailController@store');
        $router->get('/summary', 'PembelianBenihDetailController@summary');
        $router->get('/price-analysis', 'PembelianBenihDetailController@getPriceAnalysis');
        $router->get('/belanja-modal/{belanja_modal_id}', 'PembelianBenihDetailController@getByBelanjaModal');
        $router->get('/{id}', 'PembelianBenihDetailController@show');
        $router->put('/{id}', 'PembelianBenihDetailController@update');
        $router->delete('/{id}', 'PembelianBenihDetailController@destroy');
    });

    // Penjualan Detail Batch Routes
    $router->group(['prefix' => 'penjualan-detail-batch'], function () use ($router) {
        $router->get('/', 'PenjualanDetailBatchController@index');
        $router->post('/', 'PenjualanDetailBatchController@store');
        $router->get('/summary', 'PenjualanDetailBatchController@summary');
        $router->get('/batch-performance', 'PenjualanDetailBatchController@getBatchPerformance');
        $router->get('/penjualan/{penjualan_id}', 'PenjualanDetailBatchController@getByPenjualan');
        $router->get('/data-sayur/{data_sayur_id}', 'PenjualanDetailBatchController@getByDataSayur');
        $router->get('/{id}', 'PenjualanDetailBatchController@show');
        $router->put('/{id}', 'PenjualanDetailBatchController@update');
        $router->delete('/{id}', 'PenjualanDetailBatchController@destroy');
    });
    
});