<?php

return [
    // Development module mainly for easier debugging.
    'Development' => [
        // Display the error message.
        // Default:
        // Development: true
        // Production: false
        'ShowError' => true,

        // Display stacktrace leading to the error.
        // Default:
        // Development: true
        // Production: false
        'ShowStackTrace' => true,
    ],

    // This is where all the routing handling goes.
    // Routes can contain named parameters using /{name}/
    // Each route must have a controller and their corresponding action.
    'Routes' => [
        'Login' => [
            'Route' => '/',
            'Controller' => 'AuthenticationController',
            'Action' => 'Index',
        ],

        'Register' => [
            'Route' => '/register',
            'Controller' => 'AuthenticationController',
            'Action' => 'Register',
        ],

        'Home' => [
            'Route' => '/home',
            'Controller' => 'HomeController',
            'Action' => 'Index',
        ],
    ],

    // Default configuration for some bindings...
    'Default' => [
        // Controller action prefix: function action{RouteAction}()
        'ActionPrefix' => 'action',

        // View page template file.
        'PageViewTemplatePath' => 'defaultViewTemplate.php',

        // Error page template file.
        'ErrorViewTemplatePath' => 'defaultErrorViewTemplate.php',
    ],

    // MySQL Database configuration.
    'Mysqli' => [
        'Host' => 'database',
        'Username' => getenv('MYSQL_USERNAME'),
        'Password' => getenv('MYSQL_PASSWORD'),
        'Schema' => getenv('MYSQL_DATABASE'),
        'Port' => 3306,
    ],
];
