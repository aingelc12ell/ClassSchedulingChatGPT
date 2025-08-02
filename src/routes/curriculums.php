<?php
// src/routes/curriculums.php

use Slim\App;
use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Curriculum;

return function (App $app) {

    $app->get('/curriculums', function (Request $request, Response $response) {
        $curriculums = Curriculum::with('subjects')->get();
        $response->getBody()->write($curriculums->toJson());
        return $response->withHeader('Content-Type', 'application/json');
    });

    $app->post('/curriculums', function (Request $request, Response $response) {
        $data = $request->getParsedBody();
        $curriculum = Curriculum::create($data);
        $response->getBody()->write($curriculum->toJson());
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    });

    $app->put('/curriculums/{id}', function (Request $request, Response $response, array $args) {
        $data = $request->getParsedBody();
        $curriculum = Curriculum::find($args['id']);
        if (!$curriculum) {
            return $response->withStatus(404);
        }
        $curriculum->update($data);
        $response->getBody()->write($curriculum->toJson());
        return $response->withHeader('Content-Type', 'application/json');
    });

    $app->delete('/curriculums/{id}', function (Request $request, Response $response, array $args) {
        $curriculum = Curriculum::find($args['id']);
        if (!$curriculum) {
            return $response->withStatus(404);
        }
        $curriculum->delete();
        return $response->withStatus(204);
    });
};


