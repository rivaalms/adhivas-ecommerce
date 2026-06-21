<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: "Adhivas E-Commerce API",
    version: "1.0.0",
    description: "API for Adhivas E-Commerce Platform",
)]
#[OA\Server(
    url: "http://localhost:8000/api",
    description: "Adhivas E-Commerce API Server"
)]
#[OA\SecurityScheme(
    type: "http",
    securityScheme: "bearerAuth",
    scheme: "bearer",
    bearerFormat: "JWT"
)]
abstract class Controller
{
    /**
     * @template T
     *
     * @param  T  $data
     * @param  string  $message
     * @param  int  $code
     */
    protected function response($data, $message = '', $code = 200)
    {
        $status = $code >= 200 && $code < 400 ? 'success' : 'error';

        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ], $code);
    }
}
