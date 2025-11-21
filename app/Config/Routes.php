<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */


// $routes->get('/', 'Home::index');
$routes->get('/', 'BerandaController::index');
$routes->get('/about', 'BerandaController::about');
$routes->get('/contact', 'BerandaController::contact');
