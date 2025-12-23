<?php

use CodeIgniter\Router\RouteCollection;
use App\Controllers\Auth\LoginController;
use App\Controllers\DashboardController;
use App\Controllers\BerandaController;
use App\Controllers\MenuController;

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

    $routes->get('/users', [BerandaController::class, 'index'], ['as' => 'users.index']);
    $routes->get('/users/getUsers', [BerandaController::class, 'getUsers'], ['as' => 'users.getUsers']);
    $routes->post('/users/createUser', [BerandaController::class, 'createUser'], ['as' => 'users.createUser']);
    // $routes->post('/users/updateUser/:id', [BerandaController::class, 'updateUser'], ['as' => 'users.updateUser']);
    // $routes->post('/users/deleteUser/:id', [BerandaController::class, 'deleteUser'], ['as' => 'users.deleteUser']);

    $routes->get('/menu', [MenuController::class, 'index'], ['as' => 'menu.index']);


    $routes->get('/about', [BerandaController::class, 'about'], ['as' => 'about.index']);
    $routes->get('/contact', [BerandaController::class, 'contact'], ['as' => 'contact.index']);
});
