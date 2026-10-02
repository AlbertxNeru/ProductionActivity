<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

/** @var object $router */

$router->get('/', 'Welcome::index');

// ---------------------------------------------------------------
// Migration routes (required by the laboratory migration activity)
// ---------------------------------------------------------------
$router->get('create-migration/{migration_class}', 'MigrationController::create_migration');
$router->get('migrate', 'MigrationController::migrate');
$router->get('rollback', 'MigrationController::rollback');
$router->get('rollback-all', 'MigrationController::rollback_all');
$router->get('refresh', 'MigrationController::refresh');
$router->get('status', 'MigrationController::status');

// ---------------------------------------------------------------
// Authentication API
// ---------------------------------------------------------------
$router->post('api/register', 'AuthController::register');
$router->post('api/login', 'AuthController::login');
$router->post('api/refresh', 'AuthController::refresh');
$router->post('api/logout', 'AuthController::logout');
$router->get('api/me', 'AuthController::me');

// ---------------------------------------------------------------
// Product CRUD API
// ---------------------------------------------------------------
$router->get('api/products', 'ProductController::index');
$router->get('api/products/{id}', 'ProductController::show')->where_number('id');
$router->post('api/products', 'ProductController::store');
$router->put('api/products/{id}', 'ProductController::update')->where_number('id');
$router->patch('api/products/{id}', 'ProductController::update')->where_number('id');
$router->delete('api/products/{id}', 'ProductController::destroy')->where_number('id');
