<?php

/**
 * This file has been auto-generated
 * by the Symfony Routing Component.
 */

return [
    false, // $matchHost
    [ // $staticRoutes
        '/api/country' => [
            [['_route' => 'get_all_countries', '_controller' => 'App\\Controller\\CountryController::getAll'], null, ['GET' => 0], null, false, false, null],
            [['_route' => 'add_country', '_controller' => 'App\\Controller\\CountryController::addCountry'], null, ['POST' => 0], null, false, false, null],
        ],
        '/api' => [[['_route' => 'api_status', '_controller' => 'App\\Controller\\StatusController::status'], null, ['GET' => 0], null, false, false, null]],
        '/api/ping' => [[['_route' => 'api_ping', '_controller' => 'App\\Controller\\StatusController::ping'], null, ['GET' => 0], null, false, false, null]],
    ],
    [ // $regexpList
        0 => '{^(?'
                .'|/_error/(\\d+)(?:\\.([^/]++))?(*:35)'
                .'|/api/country/([^/]++)(?'
                    .'|(*:66)'
                .')'
            .')/?$}sDu',
    ],
    [ // $dynamicRoutes
        35 => [[['_route' => '_preview_error', '_controller' => 'error_controller::preview', '_format' => 'html'], ['code', '_format'], null, null, false, true, null]],
        66 => [
            [['_route' => 'get_country_by_code', '_controller' => 'App\\Controller\\CountryController::get'], ['code'], ['GET' => 0], null, false, true, null],
            [['_route' => 'edit_country', '_controller' => 'App\\Controller\\CountryController::edit'], ['code'], ['PATCH' => 0], null, false, true, null],
            [['_route' => 'delete_country', '_controller' => 'App\\Controller\\CountryController::delete'], ['code'], ['DELETE' => 0], null, false, true, null],
            [null, null, null, null, false, false, 0],
        ],
    ],
    null, // $checkCondition
];
