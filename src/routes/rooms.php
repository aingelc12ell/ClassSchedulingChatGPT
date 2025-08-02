<?php
// src/routes/rooms.php

use Slim\App;
use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Room;

return function (App $app) {

    $app->get('/rooms', function (Request $request, Response $response) {
        $rooms = Room::all();
        $response->getBody()->write($rooms->toJson());
        return $response->withHeader('Content-Type', 'application/json');
    });

    $app->post('/rooms', function (Request $request, Response $response) {
        $data = $request->getParsedBody();
        $room = Room::create($data);
        $response->getBody()->write($room->toJson());
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    });

    $app->put('/rooms/{id}', function (Request $request, Response $response, array $args) {
        $data = $request->getParsedBody();
        $room = Room::find($args['id']);
        if (!$room) {
            return $response->withStatus(404);
        }
        $room->update($data);
        $response->getBody()->write($room->toJson());
        return $response->withHeader('Content-Type', 'application/json');
    });

    $app->delete('/rooms/{id}', function (Request $request, Response $response, array $args) {
        $room = Room::find($args['id']);
        if (!$room) {
            return $response->withStatus(404);
        }
        $room->delete();
        return $response->withStatus(204);
    });
};