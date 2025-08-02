<?php

use DI\Container;
use Slim\App;
use Illuminate\Database\Capsule\Manager as Capsule;
use App\Services\ScheduleEngine;
use Firebase\JWT\JWT;

return function (App $app) {
    $container = $app->getContainer();

    // Eloquent ORM
    $capsule = new Capsule;
    $capsule->addConnection([
        'driver'    => 'mysql',
        'host'      => $_ENV['DB_HOST'] ?? 'mysql',
        'database'  => $_ENV['DB_DATABASE'] ?? 'school_schedule',
        'username'  => $_ENV['DB_USERNAME'] ?? 'scheduler_user',
        'password'  => $_ENV['DB_PASSWORD'] ?? 'StrongPassword123',
        'charset'   => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'prefix'    => '',
    ]);

    $capsule->setAsGlobal();
    $capsule->bootEloquent();

    // Bind Capsule (DB) to Container
    $container->set('db', function() use ($capsule) {
        return $capsule;
    });

    // ScheduleEngine
    $container->set(ScheduleEngine::class, function() {
        return new ScheduleEngine();
    });

    // JWT Secret Key
    $container->set('jwt_secret', function() {
        return $_ENV['JWT_SECRET'] ?? 'defaultsecret';
    });
};
