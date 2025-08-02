<?php

use Slim\App;
use Slim\Middleware\ErrorMiddleware;
use Slim\Middleware\RoutingMiddleware;
use Slim\Middleware\BodyParsingMiddleware;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

return function (App $app) {

    // Parse JSON Bodies
    $app->addBodyParsingMiddleware();

    // CORS Middleware
    $app->add(function (Request $request, RequestHandler $handler): Response {
        $response = $handler->handle($request);
        return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    });

    // JWT Authentication Middleware
    $app->add(function (Request $request, RequestHandler $handler) use ($app): Response {
        $route = $request->getAttribute('route');
        $publicRoutes = ['/login', '/register']; // Adjust accordingly

        if (!$route || in_array($route->getPattern(), $publicRoutes)) {
            return $handler->handle($request);
        }

        $authHeader = $request->getHeaderLine('Authorization');
        if (!$authHeader || !preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }

        $jwt = $matches[1];
        try {
            $secret = $app->getContainer()->get('jwt_secret');
            $decoded = JWT::decode($jwt, new Key($secret, 'HS256'));
            $request = $request->withAttribute('jwt', $decoded);
        } catch (Exception $e) {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode(['error' => 'Invalid Token']));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }

        return $handler->handle($request);
    });

    // Error Middleware
    $app->addErrorMiddleware(true, true, true);
};
