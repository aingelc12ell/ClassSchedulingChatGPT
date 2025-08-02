<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Illuminate\Database\Capsule\Manager as Capsule;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

// Database setup
$capsule = new Capsule;
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => '127.0.0.1',
    'database' => 'school_schedule',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix' => '',
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

// Middleware for JSON parsing
$app->addBodyParsingMiddleware();

// JWT Auth Middleware Placeholder
$app->add(function (Request $request, Response $response, $next) {
    // JWT Authentication Logic Here
    return $next($request, $response);
});

// Modular Route Includes
(require __DIR__ . '/../src/routes/students.php')($app);
(require __DIR__ . '/../src/routes/teachers.php')($app);
(require __DIR__ . '/../src/routes/rooms.php')($app);
(require __DIR__ . '/../src/routes/subjects.php')($app);
(require __DIR__ . '/../src/routes/curriculums.php')($app);
(require __DIR__ . '/../src/routes/classes.php')($app);
(require __DIR__ . '/../src/routes/schedule.php')($app);

$app->run();
