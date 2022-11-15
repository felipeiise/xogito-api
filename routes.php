<?php

declare(strict_types=1);

/**
 * @return array
 */
function get_routes(): array {
    return [
        ## views
        'view::/' => ['controller' => 'check'],
        'view::/register' => ['controller' => 'register'],
        'view::/qrcode' => ['controller' => 'qrcode'],
        'view::/signin' => ['controller' => 'signin'],
        'view::/mfa' => ['controller' => 'mfa'],
        'view::/signout' => ['controller' => 'signout'],

        ## protected views
        'view::/dashboard' => ['controller' => 'dashboard', 'protected' => true],
        'view::/my-account' => ['controller' => 'my_account', 'protected' => true],
        'view::/users' => ['controller' => 'users', 'protected' => true],
        'view::/users-add' => ['controller' => 'users-add', 'protected' => true],

        ## GET api routes
        'get::/api/qrcode/mfa' => ['controller' => 'UserMfa@getQrCodeMfa'], // Get QR code MFA
        'get::/api/user' => ['controller' => 'User@getUserResponse'], // Get user - works after "User MFA Token login"
        'get::/api/users' => ['controller' => 'User@getUsers'], // Get a list of users
        'get::/api/roles' => ['controller' => 'User@getRoles'], // Get a list of roles

        'get::/api/user/list/{id?}' => ['controller' => 'User@sampleRouteGetById'], // Optional parameters
        'get::/api/{name}/{id}' => ['controller' => 'User@sampleRouteCustom'], // Custom routes and parameters

        ## POST api routes
        'post::/api/user' => ['controller' => 'User@register'], // Administrator User / Register User
        'post::/api/login' => ['controller' => 'User@login'], // Login Administrator / Login User
        'post::/api/token' => ['controller' => 'UserMfa@verifyTokenMfa'], // User MFA Token login

        ## PATCH api routes
        'patch::/api/user' => ['controller' => 'User@updateUser'], // Update a user
        'patch::/api/user/deactivate' => ['controller' => 'User@deactivateUser'], // Deactivate a user
    ];
}
