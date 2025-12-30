<?php

use Illuminate\Foundation\Application;

$app = new Application(
    $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
);

$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

// Register Spatie permission middleware aliases to match route usage.
$app->router->aliasMiddleware('role', \Spatie\Permission\Middlewares\RoleMiddleware::class);
$app->router->aliasMiddleware('permission', \Spatie\Permission\Middlewares\PermissionMiddleware::class);
$app->router->aliasMiddleware('role_or_permission', \Spatie\Permission\Middlewares\RoleOrPermissionMiddleware::class);

return $app;
