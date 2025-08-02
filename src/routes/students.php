<?php
// src/routes/students.php

use Slim\App;
use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Student;

return function (App $app) {

    $app->get('/students', function (Request $request, Response $response) {
        $students = Student::with('curriculum')->get();
        $response->getBody()->write($students->toJson());
        return $response->withHeader('Content-Type', 'application/json');
    });

    $app->post('/students', function (Request $request, Response $response) {
        $data = $request->getParsedBody();
        $student = Student::create($data);
        $response->getBody()->write($student->toJson());
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    });

    $app->put('/students/{id}', function (Request $request, Response $response, array $args) {
        $data = $request->getParsedBody();
        $student = Student::find($args['id']);
        if (!$student) {
            return $response->withStatus(404);
        }
        $student->update($data);
        $response->getBody()->write($student->toJson());
        return $response->withHeader('Content-Type', 'application/json');
    });

    $app->delete('/students/{id}', function (Request $request, Response $response, array $args) {
        $student = Student::find($args['id']);
        if (!$student) {
            return $response->withStatus(404);
        }
        $student->delete();
        return $response->withStatus(204);
    });
};
