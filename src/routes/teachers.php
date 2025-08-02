<?php
// src/routes/teachers.php

use Slim\App;
use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Teacher;

return function (App $app) {

    $app->get('/teachers', function (Request $request, Response $response) {
        $teachers = Teacher::with('qualifiedSubjects')->get();
        $response->getBody()->write($teachers->toJson());
        return $response->withHeader('Content-Type', 'application/json');
    });

    $app->post('/teachers', function (Request $request, Response $response) {
        $data = $request->getParsedBody();
        $teacher = Teacher::create($data);
        $response->getBody()->write($teacher->toJson());
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    });

    $app->put('/teachers/{id}', function (Request $request, Response $response, array $args) {
        $data = $request->getParsedBody();
        $teacher = Teacher::find($args['id']);
        if (!$teacher) {
            return $response->withStatus(404);
        }
        $teacher->update($data);
        $response->getBody()->write($teacher->toJson());
        return $response->withHeader('Content-Type', 'application/json');
    });

    $app->delete('/teachers/{id}', function (Request $request, Response $response, array $args) {
        $teacher = Teacher::find($args['id']);
        if (!$teacher) {
            return $response->withStatus(404);
        }
        $teacher->delete();
        return $response->withStatus(204);
    });
};



