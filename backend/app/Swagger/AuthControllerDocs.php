<?php

namespace App\Swagger;

use App\Models\User;
use OpenApi\Attributes as OA;

class AuthControllerDocs
{
    #[OA\Post(path: '/api/auth/login', summary: 'User Login', tags: ['auth'])]
    #[OA\RequestBody(required: true, content: [new OA\MediaType(
        mediaType: 'application/json',
        schema: new OA\Schema(
            properties: [
                new OA\Property(property: 'email', type: 'string', example: 'user@example.com'),
                new OA\Property(property: 'password', type: 'string', example: 'password'),
            ],
            required: ['email', 'password']
        )
    )])]
    #[OA\Response(
        response: 200,
        description: 'Successful operation',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'string', example: 'success'),
                new OA\Property(property: 'message', type: 'string', example: 'Login successful'),
                new OA\Property(property: 'data', type: 'object', properties: [
                    new OA\Property(property: 'access_token', type: 'string', example: 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...'),
                    new OA\Property(property: 'token_type', type: 'string', example: 'bearer'),
                    new OA\Property(property: 'expires_in', type: 'integer', example: 3600),
                    new OA\Property(
                        property: 'user',
                        type: 'object',
                        ref: User::class,
                    )
                ])
            ]
        )
    )]
    public function login() {}

    #[OA\Post(path: '/api/auth/register', summary: 'User Registration', tags: ['auth'])]
    #[OA\RequestBody(required: true, content: [new OA\MediaType(
        mediaType: 'application/json',
        schema: new OA\Schema(
            properties: [
                new OA\Property(property: 'full_name', type: 'string', example: 'John Doe'),
                new OA\Property(property: 'email', type: 'string', example: 'johndoe@example.com'),
                new OA\Property(property: 'password', type: 'string', example: 'password'),
                new OA\Property(property: 'password_confirmation', type: 'string', example: 'password'),
            ],
            required: ['full_name', 'email', 'password', 'password_confirmation']
        )
    )])]
    #[OA\Response(
        response: 200,
        description: 'User registered successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'string', example: 'success'),
                new OA\Property(property: 'message', type: 'string', example: 'User registered successfully'),
                new OA\Property(property: 'data', type: 'object', properties: [
                    new OA\Property(
                        property: 'user',
                        type: 'object',
                        ref: User::class,
                    ),
                    new OA\Property(property: 'access_token', type: 'string', example: 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...'),
                    new OA\Property(property: 'token_type', type: 'string', example: 'bearer'),
                    new OA\Property(property: 'expires_in', type: 'integer', example: 3600),
                ])
            ]
        )
    )]
    public function register() {}

    #[OA\Post(path: '/api/auth/logout', summary: 'User Logout', security: [['bearerAuth' => []]], tags: ['auth'])]
    #[OA\Response(
        response: 200,
        description: 'Successfully logged out',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'string', example: 'success'),
                new OA\Property(property: 'message', type: 'string', example: 'Successfully logged out'),
                new OA\Property(property: 'data', type: 'object', nullable: true, example: null)
            ]
        )
    )]
    public function logout() {}

    #[OA\Post(path: '/api/auth/refresh', summary: 'Refresh Token', security: [['bearerAuth' => []]], tags: ['auth'])]
    #[OA\Response(
        response: 200,
        description: 'Token refreshed successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'string', example: 'success'),
                new OA\Property(property: 'message', type: 'string', example: 'Token refreshed successfully'),
                new OA\Property(property: 'data', type: 'object', properties: [
                    new OA\Property(property: 'access_token', type: 'string', example: 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...'),
                    new OA\Property(property: 'token_type', type: 'string', example: 'bearer'),
                    new OA\Property(property: 'expires_in', type: 'integer', example: 3600),
                    new OA\Property(
                        property: 'user',
                        type: 'object',
                        ref: User::class,
                    )
                ])
            ]
        )
    )]
    public function refresh() {}
}
