<?php

namespace App\WebAPI;

use Laminas\Diactoros\Response;

class ApiResponse
{
    public static function success(mixed $data): Response
    {
        $response = new Response();
        $response->getBody()->write(json_encode($data));
        return $response->withStatus(200)->withAddedHeader('Content-Type', 'application/json');
    }

    public static function badRequest(string $message): Response
    {
        $response = new Response();
        $response->getBody()->write(json_encode(['error' => $message]));
        return $response->withStatus(400)->withAddedHeader('Content-Type', 'application/json');
    }

    public static function serverError(string $message = 'Server error'): Response
    {
        $response = new Response();
        $response->getBody()->write(json_encode(['error' => $message]));
        return $response->withStatus(500)->withAddedHeader('Content-Type', 'application/json');
    }

    public static function notFound(string $message = 'Not found'): Response
    {
        $response = new Response();
        $response->getBody()->write(json_encode(['error' => $message]));
        return $response->withStatus(404)->withAddedHeader('Content-Type', 'application/json');
    }
}