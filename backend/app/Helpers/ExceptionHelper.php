<?php

namespace App\Helpers;

class ExceptionHelper
{
    /**
     * Format an exception to be more easily machine readable.
     *
     * @param  \Exception  $e
     * @return array
     */
    public static function formatException($e): array
    {
        $context = [
            'file'      => $e->getFile(),
            'line'      => $e->getLine(),
            'trace'     => $e->getTrace(),
        ];

        $templates = [
            \Illuminate\Auth\AuthenticationException::class => [
                'message'       => $e->getMessage(),
                'code'          => 401
            ],
            \Illuminate\Validation\ValidationException::class => [
                'message'       => $e->getMessage(),
                'code'          => 422,
            ]
        ];

        if (in_array(get_class($e), array_keys($templates))) {
            return array_merge($templates[get_class($e)], compact('context'));
        }

        return [
            'message'       => $e->getMessage(),
            'code'          => $e->getCode(),
            'context'       => $context
        ];
    }

    /**
     * Send an error response
     *
     * @param array $error
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public static function sendErrorResponse(array $error): \Illuminate\Http\JsonResponse
    {
        $code = (is_numeric($error['code']) && ($error['code'] >= 400 && $error['code'] <= 599))
            ? $error['code']
            : 500;

        return response()->json([
            'status' => 'error',
            'message' => $error['message'],
            'data' => \Illuminate\Support\Facades\App::environment('local') ? $error['context'] : null
        ], $code);
    }
}
