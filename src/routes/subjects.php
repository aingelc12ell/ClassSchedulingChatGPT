
<?php
// src/routes/subjects.php

use Slim\App;
use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Subject;

return function (App $app) {

    $app->get('/subjects', function (Request $request, Response $response) {
        $subjects = Subject::all();
        $response->getBody()->write($subjects->toJson());
        return $response->withHeader('Content-Type', 'application/json');
    });

    $app->post('/subjects', function (Request $request, Response $response) {
        $data = $request->getParsedBody();
        $subject = Subject::create($data);
        $response->getBody()->write($subject->toJson());
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    });

    $app->put('/subjects/{id}', function (Request $request, Response $response, array $args) {
        $data = $request->getParsedBody();
        $subject = Subject::find($args['id']);
        if (!$subject) {
            return $response->withStatus(404);
        }
        $subject->update($data);
        $response->getBody()->write($subject->toJson());
        return $response->withHeader('Content-Type', 'application/json');
    });

    $app->delete('/subjects/{id}', function (Request $request, Response $response, array $args) {
        $subject = Subject::find($args['id']);
        if (!$subject) {
            return $response->withStatus(404);
        }
        $subject->delete();
        return $response->withStatus(204);
    });
};