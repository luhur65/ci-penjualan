<?php

use CodeIgniter\Router\RouteCollection;
use App\Controllers\Auth\LoginController;
use App\Controllers\DashboardController;
use App\Controllers\MenuController;
use App\Controllers\UserController;
use App\Controllers\RoleController;

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

// Protected Routes
$routes->group('', ['filter' => 'authenticated', 'csrf' => true], function ($routes) {
    $routes->get('dashboard', [DashboardController::class, 'index'], ['as' => 'dashboard.index']);

    $routes->get('/user', [UserController::class, 'index'], ['as' => 'users.index']);
    $routes->get('/role', [RoleController::class, 'index'], ['as' => 'roles.index']);
    $routes->get('/menu', [MenuController::class, 'index'], ['as' => 'menu.index']);

});
