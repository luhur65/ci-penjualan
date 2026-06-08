<?php

use CodeIgniter\Router\RouteCollection;
use App\Controllers\Auth\LoginController;
use App\Controllers\DashboardController;
use App\Controllers\ErrorController;
use App\Controllers\MenuController;
use App\Controllers\UserController;
use App\Controllers\RoleController;
use App\Controllers\PenjualanController;
use App\Controllers\ParameterController;
use App\Controllers\OverviewController;

/**
 * @var RouteCollection $routes
 */


$routes->get('/', function() {
    return redirect()->to('/login');
});

// Authentication Routes
$routes->group('', ['filter' => 'guest'], function ($routes) {
    $routes->get('/login', [LoginController::class, 'index'], ['as' => 'login.index']);
    $routes->post('/login', [LoginController::class, 'authenticate'], ['as' => 'login.authenticate']);
});

$routes->get('/logout', [LoginController::class, 'logout'], ['filter' => 'authenticated'], ['as' => 'logout.index']);
$routes->get('/refresh', [LoginController::class, 'refreshToken'], ['as' => 'token.refresh']);
$routes->get('/reset-password', [LoginController::class, 'resetPasswordForm'], ['as' => 'reset-password.index']);
$routes->get('/reset-password/success', function() {
    return view('reset-password/success');
});

// Protected Routes without ACL Filter but must be authenticated
$routes->group('', ['filter' => 'authenticated'], function($routes) {
    $routes->get('dashboard', [DashboardController::class, 'index'], ['as' => 'dashboard.index']);
    $routes->get('/transaksi', [OverviewController::class, 'transaksi'], ['as' => 'overview.transaksi']);
});

// Protected Routes with ACL Filter
$routes->group('', ['filter' => ['authenticated', 'acl'], 'csrf' => true], function ($routes) {

    $routes->get('/user', [UserController::class, 'index'], ['as' => 'users.index']);
    $routes->get('/role', [RoleController::class, 'index'], ['as' => 'roles.index']);
    $routes->get('/menu', [MenuController::class, 'index'], ['as' => 'menu.index']);
    $routes->get('/penjualan', [PenjualanController::class, 'index'], ['as' => 'penjualan.index']);
    $routes->get('/parameter', [ParameterController::class, 'index'], ['as' => 'parameter.index']);
    $routes->get('/error', [ErrorController::class, 'index'], ['as' => 'error.index']);
    
    $routes->get('testingmasterdetail', [\App\Controllers\TestingmasterdetailController::class, 'index'], ['as' => 'testingmasterdetail.index']);
});
    