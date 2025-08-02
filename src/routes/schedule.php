<?php
// routes/schedule.php

use App\Services\ScheduleEngine;
use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;

// Get Scheduling Conflicts
$app->get('/schedule/conflicts', function (Request $request, Response $response) {
    $result = ScheduleEngine::generateSchedule('Term 1');

    $response->getBody()->write(json_encode([
        'conflicts' => $result['conflicts']
    ]));
    return $response->withHeader('Content-Type', 'application/json');
});

// Force Apply Schedule (Override Conflicts)
$app->post('/schedule/force', function (Request $request, Response $response) {
    $body = $request->getParsedBody();
    $allowConflict = $body['allow_conflict'] ?? false;

    $result = ScheduleEngine::generateSchedule('Term 1', ['allow_conflict' => $allowConflict]);

    $response->getBody()->write(json_encode([
        'message' => 'Forced Schedule Applied',
        'scheduled' => count($result['schedule']),
        'conflicts' => $result['conflicts']
    ]));
    return $response->withHeader('Content-Type', 'application/json');
});
