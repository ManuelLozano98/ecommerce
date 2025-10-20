<?php

namespace App\Utils;

use Psr\Http\Message\ResponseInterface;

class ApiHelper
{
    public static function success(ResponseInterface $response, $data = [], $statusCode = 200)
    {
        $payload = [
            'status' => 'success',
            'data' => $data
        ];

        return self::buildResponse($response, $payload, $statusCode);
    }

    public static function error(ResponseInterface $response, array $error, int $statusCode = 400)
    {
        $payload = [
            'status' => 'error',
            'message' => $error['message'] ?? 'An error occurred',
            'details' => $error['details'] ?? null,
        ];

        return self::buildResponse($response, $payload, $statusCode);
    }


    private static function buildResponse(ResponseInterface $response, array $payload, int $statusCode)
    {
        $response->getBody()->write(json_encode($payload, JSON_UNESCAPED_UNICODE));
        return $response->withStatus($statusCode)
            ->withHeader('Content-Type', 'application/json');
    }
}
