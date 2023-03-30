<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Check If The Application Is Under Maintenance
|--------------------------------------------------------------------------
|
| If the application is in maintenance / demo mode via the "down" command
| we will load this file so that any pre-rendered content can be shown
| instead of starting the framework, which could cause an exception.
|
*/

if (file_exists($maintenance = __DIR__ . '/../storage/framework/maintenance.php')) {
    require $maintenance;
}

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| this application. We just need to utilize it! We'll simply require it
| into the script here so we don't need to manually load our classes.
|
*/

require __DIR__ . '/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request using
| the application's HTTP kernel. Then, we will send the response back
| to this client's browser, allowing them to enjoy our application.
|
*/

$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);


// server {
//     listen 80;
//     listen [::]:80;
//     server_name example.com;
//     root /srv/example.com/public;

//     add_header X-Frame-Options "SAMEORIGIN";
//     add_header X-Content-Type-Options "nosniff";

//     index index.php;

//     charset utf-8;

//     location / {
//         try_files $uri $uri/ /index.php?$query_string;
//     }

//     location = /favicon.ico { access_log off; log_not_found off; }
//     location = /robots.txt  { access_log off; log_not_found off; }

//     error_page 404 /index.php;

//     location ~ \.php$ {
//         fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
//         fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
//         include fastcgi_params;
//     }

//     location ~ /\.(?!well-known).* {
//         deny all;
//     }
// }
