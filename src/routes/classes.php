<?php
// src/routes/classes.php

use Slim\App;
use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\ClassModel;

return function (App $app) {

    $app->get('/classes', function (Request $request, Response $response) {
        $classes = ClassModel::with(['subject', 'teacher', 'room'])->get();
        $response->getBody()->write($classes->toJson());
        return $response->withHeader('Content-Type', 'application/json');
    });

    $app->post('/classes', function (Request $request, Response $response) {
        $data = $request->getParsedBody();
        $class = ClassModel::create($data);
        $response->getBody()->write($class->toJson());
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    });

    $app->put('/classes/{id}', function (Request $request, Response $response, array $args) {
        $data = $request->getParsedBody();
        $class = ClassModel::find($args['id']);
        if (!$class) {
            return $response->withStatus(404);
        }
        $class->update($data);
        $response->getBody()->write($class->toJson());
        return $response->withHeader('Content-Type', 'application/json');
    });

    $app->delete('/classes/{id}', function (Request $request, Response $response, array $args) {
        $class = ClassModel::find($args['id']);
        if (!$class) {
            return $response->withStatus(404);
        }
        $class->delete();
        return $response->withStatus(204);
    });
};