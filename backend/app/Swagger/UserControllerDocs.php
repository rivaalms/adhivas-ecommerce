<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

class UserControllerDocs
{
    #[OA\Get(path: '/api/users', summary: 'Get All Users', security: [['bearerAuth' => []]], tags: ['users'])]
    #[OA\Parameter(name: 'search', in: 'query', required: false, description: 'Search query for name or email', schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'role', in: 'query', required: false, description: 'Filter by role', schema: new OA\Schema(type: 'string', enum: ['admin', 'customer']))]
    #[OA\Parameter(name: 'per_page', in: 'query', required: false, description: 'Number of items per page', schema: new OA\Schema(type: 'integer', default: 10))]
    #[OA\Response(
        response: 200,
        description: 'Users retrieved successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'string', example: 'success'),
                new OA\Property(property: 'message', type: 'string', example: 'Users retrieved successfully'),
                new OA\Property(property: 'data', type: 'object', properties: [
                    new OA\Property(property: 'data', type: 'array', items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'id', type: 'integer', example: 1),
                            new OA\Property(property: 'full_name', type: 'string', example: 'John Doe'),
                            new OA\Property(property: 'email', type: 'string', example: 'john@example.com'),
                            new OA\Property(property: 'role', type: 'string', example: 'customer'),
                            new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
                        ]
                    )),
                    new OA\Property(property: 'links', type: 'object', example: ['first' => '...', 'last' => '...']),
                    new OA\Property(property: 'meta', type: 'object', example: ['current_page' => 1, 'per_page' => 10, 'total' => 50]),
                ])
            ]
        )
    )]
    public function index() {}

    #[OA\Get(path: '/api/users/{id}', summary: 'Get User Detail', security: [['bearerAuth' => []]], tags: ['users'])]
    #[OA\Parameter(name: 'id', in: 'path', required: true, description: 'User ID', schema: new OA\Schema(type: 'integer', example: 1))]
    #[OA\Response(
        response: 200,
        description: 'User retrieved successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'string', example: 'success'),
                new OA\Property(property: 'message', type: 'string', example: 'User retrieved successfully'),
                new OA\Property(property: 'data', type: 'object', properties: [
                    new OA\Property(property: 'id', type: 'integer', example: 1),
                    new OA\Property(property: 'full_name', type: 'string', example: 'John Doe'),
                    new OA\Property(property: 'email', type: 'string', example: 'john@example.com'),
                    new OA\Property(property: 'role', type: 'string', example: 'customer'),
                    new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
                ])
            ]
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'User not found',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'string', example: 'error'),
                new OA\Property(property: 'message', type: 'string', example: 'User not found')
            ]
        )
    )]
    public function show() {}
}
