<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ──────────────────────────────────────────
// PUBLIC ROUTES (no auth required)
// ──────────────────────────────────────────
$routes->get('/',          'Auth::login');
$routes->get('/login',     'Auth::login');
$routes->post('/login',    'Auth::attemptLogin');
$routes->get('/register',  'Auth::register');
$routes->post('/register', 'Auth::attemptRegister');
$routes->get('/logout',    'Auth::logout');

// ──────────────────────────────────────────
// PROTECTED ROUTES (AuthFilter applied)
// ──────────────────────────────────────────
$routes->group('', ['filter' => 'auth'], static function (RouteCollection $routes) {
    $routes->get('/dashboard', 'Dashboard::index');
});

// ──────────────────────────────────────────
// API ROUTES (ApiFilter: JSON + session auth)
// ──────────────────────────────────────────
$routes->group('api', ['filter' => 'api'], static function (RouteCollection $routes) {

    // Settings
    $routes->post('settings/save',     'Settings::save');
    $routes->post('settings/test-key', 'Settings::testKey');

    // Prospects (Tab 1)
    $routes->get('prospects',           'Api\ProspectController::index');
    $routes->post('prospects/save',     'Api\ProspectController::save');
    $routes->delete('prospects/(:num)', 'Api\ProspectController::delete/$1');

    // Packages (Tab 2)
    $routes->get('packages',           'Api\PackageController::index');
    $routes->post('packages/save',     'Api\PackageController::save');
    $routes->delete('packages/(:num)', 'Api\PackageController::delete/$1');

    // Proposals (Tab 3)
    $routes->get('proposals',              'Api\ProposalController::index');
    $routes->get('proposals/(:num)',       'Api\ProposalController::show/$1');
    $routes->post('proposals/generate',    'Api\ProposalController::generate');
    $routes->post('proposals/save',        'Api\ProposalController::save');
    $routes->delete('proposals/(:num)',    'Api\ProposalController::delete/$1');

    // UGC Calculator (Tab 4)
    $routes->post('ugc/calculate', 'Api\UgcController::calculate');
});
