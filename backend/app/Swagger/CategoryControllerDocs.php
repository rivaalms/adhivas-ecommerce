<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

class CategoryControllerDocs
{
    #[OA\Get(path: '/api/categories', summary: 'Get All Categories', security: [['bearerAuth' => []]], tags: ['categories'])]
    #[OA\Parameter(name: 'search', in: 'query', required: false, description: 'Search query for category name', schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'per_page', in: 'query', required: false, description: 'Number of items per page', schema: new OA\Schema(type: 'integer', default: 10))]
    #[OA\Response(
        response: 200,
        description: 'Categories retrieved successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'string', example: 'success'),
                new OA\Property(property: 'message', type: 'string', example: 'Categories retrieved successfully'),
                new OA\Property(property: 'data', type: 'object', properties: [
                    new OA\Property(property: 'data', type: 'array', items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'id', type: 'integer', example: 1),
                            new OA\Property(property: 'name', type: 'string', example: 'Electronics'),
                            new OA\Property(property: 'slug', type: 'string', example: 'electronics'),
                            new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
                            new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
                        ]
                    )),
                    new OA\Property(property: 'links', type: 'object', example: ['first' => '...', 'last' => '...', 'prev' => null, 'next' => null]),
                    new OA\Property(property: 'meta', type: 'object', example: ['current_page' => 1, 'from' => 1, 'last_page' => 1, 'path' => '...', 'per_page' => 10, 'to' => 10, 'total' => 50]),
                ])
            ]
        )
    )]
    public function index() {}

    #[OA\Post(path: '/api/categories', summary: 'Create Category', security: [['bearerAuth' => []]], tags: ['categories'])]
    #[OA\RequestBody(required: true, content: [new OA\MediaType(
        mediaType: 'application/json',
        schema: new OA\Schema(
            properties: [
                new OA\Property(property: 'name', type: 'string', example: 'Electronics'),
            ],
            required: ['name']
        )
    )])]
    #[OA\Response(
        response: 201,
        description: 'Category created successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'string', example: 'success'),
                new OA\Property(property: 'message', type: 'string', example: 'Category created successfully'),
                new OA\Property(property: 'data', type: 'object', properties: [
                    new OA\Property(property: 'id', type: 'integer', example: 1),
                    new OA\Property(property: 'name', type: 'string', example: 'Electronics'),
                    new OA\Property(property: 'slug', type: 'string', example: 'electronics'),
                    new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
                    new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
                ])
            ]
        )
    )]
    public function store() {}

    #[OA\Get(path: '/api/categories/{id}', summary: 'Get Category Detail', security: [['bearerAuth' => []]], tags: ['categories'])]
    #[OA\Parameter(name: 'id', in: 'path', required: true, description: 'Category ID', schema: new OA\Schema(type: 'integer', example: 1))]
    #[OA\Response(
        response: 200,
        description: 'Category retrieved successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'string', example: 'success'),
                new OA\Property(property: 'message', type: 'string', example: 'Category retrieved successfully'),
                new OA\Property(property: 'data', type: 'object', properties: [
                    new OA\Property(property: 'id', type: 'integer', example: 1),
                    new OA\Property(property: 'name', type: 'string', example: 'Electronics'),
                    new OA\Property(property: 'slug', type: 'string', example: 'electronics'),
                    new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
                    new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
                ])
            ]
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'Category not found',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'string', example: 'error'),
                new OA\Property(property: 'message', type: 'string', example: 'Category not found')
            ]
        )
    )]
    public function show() {}

    #[OA\Put(path: '/api/categories/{id}', summary: 'Update Category', security: [['bearerAuth' => []]], tags: ['categories'])]
    #[OA\Parameter(name: 'id', in: 'path', required: true, description: 'Category ID', schema: new OA\Schema(type: 'integer', example: 1))]
    #[OA\RequestBody(required: true, content: [new OA\MediaType(
        mediaType: 'application/json',
        schema: new OA\Schema(
            properties: [
                new OA\Property(property: 'name', type: 'string', example: 'New Electronics'),
            ],
            required: ['name']
        )
    )])]
    #[OA\Response(
        response: 200,
        description: 'Category updated successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'string', example: 'success'),
                new OA\Property(property: 'message', type: 'string', example: 'Category updated successfully'),
                new OA\Property(property: 'data', type: 'object', properties: [
                    new OA\Property(property: 'id', type: 'integer', example: 1),
                    new OA\Property(property: 'name', type: 'string', example: 'New Electronics'),
                    new OA\Property(property: 'slug', type: 'string', example: 'new-electronics'),
                    new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
                    new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
                ])
            ]
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'Category not found',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'string', example: 'error'),
                new OA\Property(property: 'message', type: 'string', example: 'Category not found')
            ]
        )
    )]
    public function update() {}

    #[OA\Delete(path: '/api/categories/{id}', summary: 'Delete Category', security: [['bearerAuth' => []]], tags: ['categories'])]
    #[OA\Parameter(name: 'id', in: 'path', required: true, description: 'Category ID', schema: new OA\Schema(type: 'integer', example: 1))]
    #[OA\Response(
        response: 200,
        description: 'Category deleted successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'string', example: 'success'),
                new OA\Property(property: 'message', type: 'string', example: 'Category deleted successfully'),
                new OA\Property(property: 'data', type: 'object', nullable: true, example: null)
            ]
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'Category not found',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'string', example: 'error'),
                new OA\Property(property: 'message', type: 'string', example: 'Category not found')
            ]
        )
    )]
    public function destroy() {}
}
